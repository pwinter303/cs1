<?php
session_start();

include 'db.php';
//include 'functions.php';

//PLW Added 2016-04-06
date_default_timezone_set('America/New_York');

//if(isset($_SESSION['authenticated'])){
    $result = processRequest();
//} else {
//    ### must be logged in to use this...
//    header('HTTP/1.1 401 Unauthorized');
//    $response{'StatusCd'} = 401;
//    return $response;
//}
$json = json_encode($result);
echo $json;
########################################################
function processRequest(){
//  if (isset($_SESSION['customer_id'])) {
//    $customer_id = $_SESSION['customer_id'];
//  }   else  {
//     die ('invalid customer id');
//  }
    $customer_id = 2;  // TODO: temp hack
    switch ($_SERVER['REQUEST_METHOD']) {
       case 'POST':
             $result = processPost($customer_id);
             break;
       case 'GET':
             $result = processGet($customer_id);
             break;
       default:
             echo "Error:Invalid Request";
             break;
    }
    return $result;
}
####################  GETs ################################
function  processGet($customer_id){
    $dbh = createDatabaseConnection();
    $action = htmlspecialchars($_GET["action"]);
    switch ($action) {
       case 'getColleges':
             $result = getColleges($dbh,$customer_id);
             break;
       case 'getCollegeCount':
             $result = getCollegeCount($dbh,$customer_id);
             break;
       case 'getCriteriaForWeb':
              $result = getCriteriaForWeb($dbh,$customer_id);
              break;
       case 'getSize':
              $result = getSize($dbh);
              break;
       case 'getLocale':
              $result = getLocale($dbh);
              break;
       case 'getSchoolSizes':
              $result = getSchoolSizes($dbh);
              break;
       case 'getSports':
              $result = getSports($dbh);
              break;
      case 'getDirections':
             $result = getDirections($dbh,$customer_id);
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }
    return $result;
}
####################  POSTs ################################
function  processPost($customer_id){
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
//    $request = convertFromBoolean($request);
    $dbh = createDatabaseConnection();
    $action = $request->action;
    switch ($action) {
       case 'saveCriteria':
             $result = saveCriteria($dbh, $request, $customer_id);
             break;
       case 'saveCollege':
             $result = saveCollege($dbh, $request, $customer_id);
             break;
       case 'saveLocation':
             $result = saveLocation($dbh, $request, $customer_id);
             break;
       case 'getDirections':
             $result = getDirectionsAsPOST($dbh, $request);
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }
    return $result;
}

function  getLocale($dbh){
    $query = "select locale as id, locale_decode as name from decode_locale";
    $data = execSqlMultiRowPREPARED($dbh, $query);
    return $data;
}
function  getSchoolSizes($dbh){
    $query = "select instsize as id, instsize_decode as name from decode_instsize where instsize >0";
    $data = execSqlMultiRowPREPARED($dbh, $query);
    return $data;
}

function  getSports($dbh){
    $query = "select sport_cd as id, sport_nm as name from sports_decodes";
    $data = execSqlMultiRowPREPARED($dbh, $query);
    return $data;
}

function  getCriteriaForWeb($dbh, $customer_id){
    $data = getCriteria($dbh, $customer_id);

    foreach ($data as $criteria => $restOfArray){
      foreach ($restOfArray as $field => $value){
        if ('options' == $field){
          # Define array that will hold the final value
          $finalArr = array();
          # turn the value retrieved from the database into an array
          $values = explode(",", $value);
          # loop through each of the values and build an array (the ng-option requires an object with id in it)
          foreach($values as $single_value){
            $valArr = array("id" => $single_value);
            array_push($finalArr, $valArr);
          }
          $data{$criteria}{$field} = $finalArr;
        }
      }
    }
    return $data;
}

function  getCriteria($dbh, $customer_id){
    $query = "select criteria_cd, field_cd, the_value from criteria where customer_id = ?";
    $types = 'i';  ## pass
    $params = array($customer_id);
    $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);

    foreach ($data as $primaryKey => $fieldValuePairs){
        $criteria = $fieldValuePairs{'criteria_cd'};
        $field = $fieldValuePairs{'field_cd'};
        $value = $fieldValuePairs{'the_value'};
        $finalData{$criteria}{$field} = $value;
    }
    return $finalData;
}



function  createWhereClauseUsingCriteria($dbh, $customer_id){
  $data = getCriteria($dbh, $customer_id);
  $whereArr = array();
  foreach ($data as $criteria => $restOfArray){
      ##var_dump($restOfArray);
      if (1 == $restOfArray{'enabled'}){
          switch ($criteria) {
             case 'schoolSize':
                 $value = $restOfArray{'options'};
                 $where = " and institutions.instsize in ($value)";
                 array_push($whereArr, $where);
                 break;
             case 'schoolSetting':
                $value = $restOfArray{'options'};
                $where = " and institutions.locale in ($value)";
                array_push($whereArr, $where);
                break;
             case 'sports':
                $value = $restOfArray{'options'};
                $valueArr = explode(",", $value);
                ####var_dump($valueArr);
                array_walk($valueArr, create_function('&$str', '$str = "\"$str\"";'));
                ###var_dump($valueArr);
                $value = implode('", "', $valueArr);
                ####echo "this is value for sports $value\n";
                $where = " and institutions.unitid in (select distinct sports.unitid from sports where sport_cd in ($value)) ";
                array_push($whereArr, $where);
                break;
          }
      }
  }
  $finalWhere = implode(" ", $whereArr);
  ##echo "here is the finalWhere:$finalWhere\n";
  return $finalWhere;
}

function  getCollegeCount($dbh, $customer_id){
  $data = getCollegeFunc($dbh, $customer_id, 1);  #1 = return count
  return $data;
}
function  getColleges($dbh, $customer_id){
  $data = getCollegeFunc($dbh, $customer_id);  #1 = return count
  return $data;
}

function  getCollegeFunc($dbh, $customer_id, $count=0){
    $where = createWhereClauseUsingCriteria($dbh, $customer_id);

    $selectCols = "instnm as name,
                             locale_decode as locale,
                             city, stabbr as state_cd,
                             instsize_decode as school_size ";
    if ($count){
      $selectCols = "count(*) as count";
    }
    $query = "select  $selectCols
    from institutions, decode_instsize, decode_locale
    where
      institutions.instsize = decode_instsize.instsize and
      institutions.locale = decode_locale.locale
      $where
    ";
    #echo "this is the query:$query\n";

    ### since types and params are defaulted to null in the called function you dont have to pass them
    if ($count){
      $data = execSqlSingleRowPREPARED($dbh, $query);
    }else {
      $data = execSqlMultiRowPREPARED($dbh, $query);
    }
    return $data;
}


function  getCollegesSTATIC(){
    $colleges = array( array( name => "Georgetown University",
                          location => "3700 O St NW, Washington, DC"
                        ),
                   array( name => "Princeton University",
                          location => "One Nassau Hall, Princeton, NJ"
                        ),
                   array( name => "Miss Porters",
                          location => "Farmington,CT"
                        ),
                   array( name => "Pennsylvania State University",
                          location => "University Park, PA"
                        ),
                   array( name => "University of Pittsburgh",
                          location => "Pittsburgh,PA"
                        ),
                   array( name => "Ohio State University",
                          location => "Columbus,OH"
                        ),
                   array( name => "UMASS Amherst",
                          location => "Amherst, MA"
                        ),
                   array( name => "UMASS Boston",
                          location => "Boston, MA"
                        ),
                   array( name => "UVM",
                          location => "Burlington, VT"
                        ),
                   array( name => "Dartmouth",
                          location => "Hanover, NH"
                        ),
                   array( name => "Brown",
                          location => "Providence,RI"
                        )
                 );

    return $colleges;
//    array(['name' => "COLLEGE1"],
//                 ['name' => "COLLEGE2"],
//                 ['name' => "COLLEGE3"]);
}


function  getDirections(){
//    return 1;
    $passedData = ($_GET);

    $passedDataDecoded = json_decode($passedData{'routePoints'});
//    echo ("from" => $from);
//    var_dump($passedData{'routePoints'});
//    var_dump($passedDataDecoded);
//    var_dump($passedDataDecoded{'name'});
    var_dump($passedDataDecoded{'name'});

}

function  getDirectionsAsPOST($dbh, $request_data){
    $junk = $request_data->routePoints->start->name;
    return(array(name=>$junk));
}
function  saveCollege(){
    return 1;
}
function  saveCriteria($dbh, $request_data, $customer_id){
  $criteria = $request_data->func;
  switch ($criteria) {
     case 'schoolSize':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "options");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'schoolCost':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "min");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "max");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'schoolSetting':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "options");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'sports':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "options");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'home':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "zipCode");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "minDistanceAway");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "maxDistanceAway");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'loc2':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "zipCode");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "minDistanceAway");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "maxDistanceAway");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'testScore':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "SAT");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "ACT");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "options");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     default:
           echo "Error:Invalid Criteria Name";
           break;
  }





  $college_count = array( "college_count" => 3);
  return $college_count;
}

##############################################################################
function  saveCriteriaFunc($dbh, $customer_id, $request_data, $field_cd){

  $criteria_cd = $request_data->func;
  $value="";
  if (is_array($request_data->$field_cd)){
      ### HACK:  assumes all the OPTIONS fields are multiple select fields so value will be an object
      ### NOTE: the options MUST use 'id' as the key!
      if ('options' == $field_cd){
        $values = array();
        foreach ($request_data->$field_cd as $item){
          array_push($values,$item->id);
        }
      } else {
        $values = $request_data->$field_cd;
      }
      $value = implode(",", $values);
  } else {
      $value = $request_data->$field_cd;
  }
  ###echo "this is value:$value\n";

  $query = "INSERT INTO criteria (customer_id, criteria_cd, field_cd, the_value) VALUES
           (?,          ?,      ?,          ?) ON DUPLICATE KEY UPDATE the_value = ? ";

  $types = 'issss';  ## pass
  $params = array($customer_id, $criteria_cd, $field_cd, $value, $value);

  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
  return $rowsAffected;
}

function  saveLocation(){
    return 1;
}




?>