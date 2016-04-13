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
       case 'getCriteria':
              $result = getCriteria($dbh,$customer_id);
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

function  getCriteriaEnabled($dbh, $customer_id){

  $query = "select distinct filter_cd from filter where customer_id = ? and field_cd = 'enabled' and the_value = 1";

  $types = 'i';  ## pass
  $params = array($customer_id);
  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);
  $where = "";
  foreach ($data as $primaryKey => $fieldValuePairs){
    foreach ($fieldValuePairs as $fieldKey => $value){
        $query = "select filter_cd, field_cd, the_value from filter where customer_id = ? and filter_cd = ?";
        $types = 'is';  ## pass
        $params = array($customer_id, $value);
        $dataCRIT = execSqlMultiRowPREPARED($dbh, $query, $types, $params);
        foreach ($dataCRIT as $primaryKey => $fieldValuePairsCRIT){
          if (  ('schoolSize' ==  $fieldValuePairsCRIT{'filter_cd'}) and
                ('options'    ==  $fieldValuePairsCRIT{'field_cd'})    ){
                $options = $fieldValuePairsCRIT{'the_value'};
                if (strlen($options)){
                  ##echo "These are the options:$options\n";
                  $optionArr = explode(",", $options);

                  foreach ($optionArr as $option){
                    if (0 == strlen($where)){
                      ##TODO: this wont work when handling multiple fields
                      $where = "and (INSTSIZE = $option";
                    } else {
                      $where = "$where or INSTSIZE = $option";
                    }
                  }
                  $where = "$where)";
                }
          }
        }
    }
  }
  return $where;
}

function  getColleges($dbh, $customer_id){
    $where = getCriteriaEnabled($dbh, $customer_id);
    $query = "select instnm as institution_nm from institutions where 1 = 1 $where and unitid in (
    select distinct sports.institutions_UNITID from sports where sport_cd = 'WCR')";
    ##$query = "select instnm as institution_nm from institutions where instnm like 'A%'$where";
    ### since types and params are defaulted to null in the called function you dont have to pass them
    $data = execSqlMultiRowPREPARED($dbh, $query);
    return $data;
}

function  getCollegeCount($dbh, $customer_id){
  getCriteriaEnabled($dbh, $customer_id);
  #count colleges

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
  $filter = $request_data->func;
  switch ($filter) {
     case 'schoolSize':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "options");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'schoolCost':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "min");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "max");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'home':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "zipCode");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "minDistanceAway");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "maxDistanceAway");
           break;
     case 'loc2':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "zipCode");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "minDistanceAway");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "maxDistanceAway");
           break;
     default:
           echo "Error:Invalid Request";
           break;
  }





  $college_count = array( "college_count" => 3);
  return $college_count;
}

##############################################################################
function  saveCriteriaFunc($dbh, $customer_id, $request_data, $field_cd){

  $filter_cd = $request_data->func;
  if (is_array($request_data->$field_cd)){
      $value = implode(",", $request_data->$field_cd);
  } else {
      $value = $request_data->$field_cd;
  }

  $query = "INSERT INTO filter (customer_id, filter_cd, field_cd, the_value) VALUES
           (?,          ?,      ?,          ?) ON DUPLICATE KEY UPDATE the_value = ? ";

  $types = 'issss';  ## pass
  $params = array($customer_id, $filter_cd, $field_cd, $value, $value);

  $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
  return $rowsAffected;
}

function  saveLocation(){
    return 1;
}




?>