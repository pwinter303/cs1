<?php
session_start();

include 'db.php';
//include 'functions.php';

#### NOTE NOTE NOTE NOTE
# be VERY careful about $types passed into sql
# i = integer,  d = double,  s=string
# I've been burned using integer instead of double


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
       case 'getCustomersCollegeUnitIDs':
              $result = getCustomersCollegeUnitIDs($dbh, $customer_id);
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
      case 'getCollegesOnRoute':
             $result = getCollegesOnRoute($dbh,$request,$customer_id);
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

function  getLatLngForZipCode($dbh,$zipCode){
    $query = "select latitude, longitude from zip_codes where postal_code = ? ";
    $types = 's';  ## pass
    $params = array($zipCode);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);
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
  $distCols = "";
  $distHaving = "";

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
             case 'home':
                $zipCode = $restOfArray{'zipCode'};
                $min = $restOfArray{'minDistanceAway'};
                $max = $restOfArray{'maxDistanceAway'};
                $data = getLatLngForZipCode($dbh, $zipCode);
                $latitude = $data{'latitude'};
                $longitude = $data{'longitude'};
                $distCols = ",round((((acos(sin(($latitude*pi()/180)) * sin((`latitude`*pi()/180))+cos(($latitude*pi()/180))
                                 * cos((`latitude`*pi()/180)) * cos((($longitude- `longitude`)*pi()/180))))*180/pi())*60*1.1515))
                                 AS distance";
                $distHaving = "having distance < $max and $min < distance ";
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
  return array($finalWhere,$distCols,$distHaving);
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
    $componentsArr = createWhereClauseUsingCriteria($dbh, $customer_id);
    $where = $componentsArr[0];
    $distCols = $componentsArr[1];
    $distHaving = $componentsArr[2];

    ###echo "$where,$distCols,$distHaving\n";

    $where .= $distHaving;
    $selectCols = "instnm as name,
                             unitid as id,
                             locale_decode as locale,
                             city, stabbr as state_cd,
                             webaddr as url,
                             instsize_decode as school_size $distCols";

    $countSelect = "";
    $countEnd = "";
    if ($count){
      $countSelect = "SELECT count(*) as count FROM (";
      $countEnd = ") as count";
    }

    ## HACK: ICLEVEL = 1 is just 4 year colleges.. should be a drop down selection TODO
    $query = "$countSelect  select  $selectCols
    from institutions, decode_instsize, decode_locale
    where
      institutions.instsize = decode_instsize.instsize and
      institutions.locale = decode_locale.locale and
      iclevel = 1
      $where  $countEnd
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
    ### NOTE:  POST SEEMS BETTER
    $passedData = ($_GET);
    $passedDataDecoded = json_decode($passedData{'routePoints'});
    var_dump($passedDataDecoded{'name'});
}

## ToDo: This reads the data from a file instead of hitting google
function  getDirectionsFROMFILE($dbh, $request_data){
    $myfile = fopen("c:/tmp/googleMapResults.txt", "r") or die("Unable to open file!");
    $data = fread($myfile,filesize("c:/tmp/googleMapResults.txt"));
    fclose($myfile);
    ##echo $data;
    return($data);
}
##function  getDirectionsAsPOST___REAL_ONE($dbh, $request_data){
function  getDirectionsAsPOST($dbh, $request_data){
    $orig = $request_data->routePoints->start->name;
    $dest = $request_data->routePoints->end->name;

//    var myKey = "&key=AIzaSyBJW90ZQrxG82XCEqDn9uxBlef8x7Oebkc";
    $myKey = "&key=AIzaSyBJW90ZQrxG82XCEqDn9uxBlef8x7Oebkc";
    #$parameters = "origin=" . encodeURI(orig) . "&destination=" . encodeURI(dest) . waypoints + myKey;
    $parameters = "origin=" . urlencode($orig) . "&destination=" . urlencode($dest) . $myKey;
//    var encodedParams = encodeURIComponent(parameters);
    $encodedParams = $parameters;

    $url = "https://maps.googleapis.com/maps/api/directions/json?" . $encodedParams;
    #echo "url is:$url\n";
    error_reporting(0);
    header('Content-Type: application/json');
    #echo file_get_contents($_GET["url"]);
    if (!$data = file_get_contents($url)) {
          $error = error_get_last();
          echo "HTTP request failed. Error was: " . $error['message'];
    }
      ## Temp code to write the results of the directions call to a file (to use for testing)
//    $myfile = fopen("c:/tmp/googleMapResults.txt", "w") or die("Unable to open file!");
//    fwrite($myfile, $data);

    return($data);
}
function getCollegesOnRoute($dbh, $request_data, $customer_id){
    $dataRouteJSON  = getDirectionsAsPOST($dbh, $request_data);
    ##var_dump($dataRouteJSON);
    $dataColleges = getColleges($dbh, $customer_id);
    $unitIDs = extractUnitIDs($dataColleges);
    ###var_dump($unitIDs);
    $finalUnitIDsArr = array();
    $dataResponse = json_decode($dataRouteJSON);
    $g = $dataResponse->routes;
    $distance = 25;
    foreach ($g as $item){
      foreach ($item->legs as $myLeg){
        foreach ($myLeg->steps as $myStep){
          ### USING STARTING POINT
          $lat1 = $myStep->start_location->lat;
          $lng1 = $myStep->start_location->lng;   ### no "O" in lng
          $dataCollegesNearRoute = getCollegesNearby($dbh, $lat1, $lng1, $distance, $unitIDs);
          $finalUnitIDsArr = updateFinalArrayWithResults($dataCollegesNearRoute,$finalUnitIDsArr,$lat1, $lng1);
          ### USING END POINT
          $lat2 = $myStep->end_location->lat;
          $lng2 = $myStep->end_location->lng;   ### no "O" in lng
          $dataCollegesNearRoute = getCollegesNearby($dbh, $lat2, $lng2, $distance, $unitIDs);
          $finalUnitIDsArr = updateFinalArrayWithResults($dataCollegesNearRoute,$finalUnitIDsArr,$lat2, $lng2);
          $arrExtra = array();
          #generateExtraWaypoints($lat1, $lng1, $lat2, $lng2,$arrExtra);

        }
      }
    }
    ##var_dump($finalUnitIDsArr);
    $finalArr = array();
    foreach ($finalUnitIDsArr as $unitID => $rest){
      $dataArr = getCollegeUsingUnitID($dbh, $unitID);
      $distanceOffRoute = $rest{'distance'};
      ##echo "distanceOffRoute:$distanceOffRoute\n";
      $dataArr{'distance'} = $distanceOffRoute;
      array_push($finalArr, $dataArr);
    }
    return $finalArr;
}

function generateExtraWaypoints($lat1, $lng1, $lat2, $lng2, $extraWaypoints){
    ### if the distance between two points is too far, calculate extra waypoints
    ### this is needed when calculating colleges near route
    ### if a route has a single leg that is 200 miles long.. you would only find college
    ### that were near the start and end. You need the extra points along the way

    #Steps
    # Calc distance between 2 main points
    $dist = distance($lat1, $lng1, $lat2, $lng2, ""); #### last param is unit (blank is miles)
    $dist = round($dist);
    echo "this is first lat/lng: $lat1 $lng1 and this is the second: $lat2 $lng2 and this is the distance: $dist\n";
    # if distance is too large, then calculate the mid point and use it as a waypoint
    if ($dist > 31){
        list ($latNew, $lngNew) = midpoint($lat1, $lng1, $lat2, $lng2);

        $dist = distance($lat1, $lng1, $latNew, $lngNew, ""); #### last param is unit (blank is miles)
        echo "generated a new wayPoint it is: $latNew  $lngNew\n";
        echo "distance between start and new point is $dist\n ";
        if ($dist > 31){
          list ($latNew, $lngNew) = midpoint($lat1, $lng1, $latNew, $lngNew);
          $dist = distance($lat1, $lng1, $latNew, $lngNew, ""); #### last param is unit (blank is miles)
          echo "generated a new wayPoint it is: $latNew  $lngNew\n";
          echo "distance between start and new point is $dist\n ";
        }
    }
}

function genWP ($lat1, $lng1, $lat2, $lng2) {

  foreach ($pointArr as $point){
    list ($begLat, $startLng) = $point[0];
    list ($endLat, $endLng) = $point[1];
    $dist = distance($begLat, $startLng, $endLat, $endLng, "");
    if ($dist > 31){
      list ($latNew, $lngNew) = midpoint($begLat, $startLng, $endLat, $endLng);
    }
  }
}
### credit for these two functions goes to:  http://stackoverflow.com/questions/5657194/need-help-calculating-longitude-and-latitude-midpoint-using-javascript-from-php
function midpoint ($lat1, $lng1, $lat2, $lng2) {

    $lat1= deg2rad($lat1);
    $lng1= deg2rad($lng1);
    $lat2= deg2rad($lat2);
    $lng2= deg2rad($lng2);

    $dlng = $lng2 - $lng1;
    $Bx = cos($lat2) * cos($dlng);
    $By = cos($lat2) * sin($dlng);
    $lat3 = atan2( sin($lat1)+sin($lat2),
    sqrt((cos($lat1)+$Bx)*(cos($lat1)+$Bx) + $By*$By ));
    $lng3 = $lng1 + atan2($By, (cos($lat1) + $Bx));
    $pi = pi();
//    return ($lat3*180)/$pi .' '. ($lng3*180)/$pi;
    $latNew = ($lat3*180)/$pi;
    $lngNew = ($lng3*180)/$pi;
    return array($latNew, $lngNew);
}

function distance($lat1, $lng1, $lat2, $lng2, $unit) {

  $theta = $lng1 - $lng2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
        return $miles;
      }
}


function updateFinalArrayWithResults($collegesFound, $finalArr, $lat, $lng){
    foreach ($collegesFound as $itemCollege){
      $id = $itemCollege{'id'};
      $dist = $itemCollege{'distance'};

      if (array_key_exists($id, $finalArr)){
        if ($dist < $finalArr{$id}{'distance'}){
          $finalArr{$id}{'distance'} = $dist;
          $finalArr{$id}{'lat'} = $lat;
          $finalArr{$id}{'lng'} = $lng;
        }
      } else {
        $finalArr{$id}{'distance'} = $dist;
        $finalArr{$id}{'lat'} = $lat;
        $finalArr{$id}{'lng'} = $lng;
      }
      #echo "id: $id distance:$dist\n";
    }
    return $finalArr;
}

function getCollegeUsingUnitID($dbh, $unitID){
    $query = "select instnm as name,
                     unitid as id,
                     locale_decode as locale,
                     city, stabbr as state_cd,
                     instsize_decode as school_size
    from institutions, decode_instsize, decode_locale
    where
      institutions.instsize = decode_instsize.instsize and
      institutions.locale = decode_locale.locale and
      unitID = ? ";

    $types = 'i';  ## pass
    $params = array($unitID);
    $data = execSqlSingleRowPREPARED($dbh, $query, $types, $params);
    return $data;
}

function getCustomersCollegeUnitIDs($dbh, $customer_id){
    $dataColleges = getColleges($dbh, $customer_id);
    $unitIDs = extractUnitIDs($dataColleges);
}

function extractUnitIDs($dataColleges){
  $unitIDsArr = array();
  foreach ($dataColleges as $College){
    array_push($unitIDsArr, $College{'id'});
  }
  return $unitIDsArr;
}

function getCollegesNearby($dbh, $lat, $lng, $distance, $unitIDs){
      $whereUnits = implode(",", $unitIDs);

//      $query = "select unitid as id,
//                round((((acos(sin(($lat *pi()/180)) * sin((`latitude`*pi()/180))+cos(($lat *pi()/180))
//                                   * cos((`latitude`*pi()/180)) * cos((($lng - `longitude`)*pi()/180))))*180/pi())*60*1.1515))
//                                   AS distance
//                from institutions
//                where unitID in ($whereUnits)
//                having distance < 15";
      $query = "select unitid as id,
                round((((acos(sin((? *pi()/180)) * sin((`latitude`*pi()/180))+cos((? *pi()/180))
                                   * cos((`latitude`*pi()/180)) * cos(((? - `longitude`)*pi()/180))))*180/pi())*60*1.1515))
                                   AS distance
                from institutions
                where unitID in ($whereUnits)
                having distance < ? ";
//      echo "this is the query:$query\n";
      $types = 'dddi';  ## pass
      $params = array($lat,$lat,$lng,$distance);
      $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);
//      $data = execSqlMultiRowPREPARED($dbh, $query);
      return $data;
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