<?php
session_start();

include 'db.php';
include 'googleMapFunctions.php';
//include 'functions.php';

#### NOTE NOTE NOTE NOTE
# be VERY careful about $types passed into sql
# i = integer,  d = double,  s=string
# I have been burned using integer instead of double


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
       case 'getCriteriaRefData':
              $result = getCriteriaRefData($dbh);
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
       case 'getDivisions':
              $result = getDivisions($dbh);
              break;
       case 'getStates':
              $result = getStates($dbh);
              break;
       case 'getTestScoreRelation':
              $result = getTestScoreRelation($dbh);
              break;
       case 'getYrsOfSchool':
             $result = getYrsOfSchool($dbh);
             break;
       case 'getRunBy':
             $result = getRunBy($dbh);
             break;
       case 'getColleges':
             $result = getColleges($dbh,$customer_id);
             break;
       case 'getCollegeCount':
             $result = getCollegeCount($dbh,$customer_id);
             break;
       case 'getCriteriaForWeb':
              $result = getCriteriaForWeb($dbh,$customer_id);
              break;
       case 'getTrips':
              $result = getTrips($dbh,$customer_id);
              break;
       case 'getCustomersCollegeUnitIDs':
              $result = getCustomersCollegeUnitIDs($dbh, $customer_id);
              break;
       case 'convertPolylineToLatLng':
              $result = convertPolylineToLatLng();
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
       case 'addTrip':
             $result = addTrip($dbh, $request, $customer_id);
             break;
       case 'deleteTrip':
             $result = deleteTrip($dbh, $request, $customer_id);
             break;
       case 'addCollegeToTrip':
             $result = addCollegeToTrip($dbh, $request, $customer_id);
             break;
       case 'deleteCollegeFromTrip':
             $result = deleteCollegeFromTrip($dbh, $request, $customer_id);
             break;
       #### did this as a POST to easily pass extra information
       case 'getTripDetails':
              $result = getTripDetails($dbh,$request,$customer_id);
              break;
       #### did this as a POST to easily pass extra information
       case 'getDirections':
             $result = getDirections($dbh, $request);
             break;
      #### did this as a POST to easily pass extra information
      case 'getCollegesOnRoute':
             $result = getCollegesOnRoute($dbh,$request,$customer_id);
             break;
       default:
             echo "Error:Invalid Request:Action not set properly";
             break;
    }
    return $result;
}

#################### GETS #####################################################
function  getCriteriaRefData($dbh){
    $finalResults = array();

    $data = getLocale($dbh);
    $finalResults{'locales'} = $data;

    $data = getSchoolSizes($dbh);
    $finalResults{'sizes'} = $data;

    $data = getYrsOfSchool($dbh);
    $finalResults{'yrsOfSchool'} = $data;

    $data = getTestScoreRelation($dbh);
    $finalResults{'TestScoreRelations'} = $data;

    $data = getSports($dbh);
    $finalResults{'sports'} = $data;

    $data = getDivisions($dbh);
    $finalResults{'divisions'} = $data;

    $data = getStates($dbh);
    $finalResults{'states'} = $data;

    $data = getRunBy($dbh);
    $finalResults{'runBy'} = $data;

    return $finalResults;
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

function  getDivisions($dbh){
    $data = array(
      array('id' => 'I',  'name' => 'I'),
      array('id' => 'II',  'name' => 'II'),
      array('id' => 'III',  'name' => 'III'),
      array('id' => 'Any',  'name' => 'Any')
    );
    return $data;
}

function  getStates($dbh){
    $data = array(
      array('id' => 'MA',  'name' => 'MA'),
      array('id' => 'VT',  'name' => 'VT'),
      array('id' => 'PA',  'name' => 'PA'),
      array('id' => 'CT',  'name' => 'CT'),
      array('id' => 'NY',  'name' => 'NY'),
      array('id' => 'NH',  'name' => 'NH'),
      array('id' => 'RI',  'name' => 'RI')
    );
    return $data;
}

function  getYrsOfSchool($dbh){
    $data = array(
      array('id' => 1,  'name' => 'Four or more years'),
      array('id' => 2,  'name' => 'At least 2 but less than 4 years'),
      array('id' => 3,  'name' => 'Less than 2 years (below associate)')
    );
    return $data;
}

function  getRunBy($dbh){
    $data = array(
      array('id' => 1,  'name' => 'Public'),
      array('id' => 2,  'name' => 'Private not-for-profit'),
      array('id' => 3,  'name' => 'Private for-profit')
    );
    return $data;
}

function  getTestScoreRelation($dbh){
    $data = array(
        array('id' => 'safety', 'name' => 'Safety Schools'),
        array('id' => 'match', 'name' => 'Good Matches'),
        array('id' => 'reach', 'name' => 'Reach Schools'),
        array('id' => 'notest', 'name' => 'No Test Info')
        );
    return $data;
}

function  getTrips($dbh, $customer_id){
    $query = "select trip_id as tripID, trip_name as name from trips where customer_id = $customer_id";
    $data = execSqlMultiRowPREPARED($dbh, $query);
    return $data;
}

function getTripDetails($dbh,$request_data,$customer_id){
  $tripID = $request_data->tripID;
  $dataTripPoints = getTripPoints($dbh,$tripID,$customer_id);
//  var_dump($dataTripPoints);
//  die;
  $start = $dataTripPoints{"start"};
  $end = $dataTripPoints{"end"};
  $stops = $dataTripPoints{"wayPts"};
  $dataDirections = getDirectionsBest($dbh, $start, $end, $stops);

  ### Get Colleges and determine how far off the route they are
  $dataColleges = getCollegesWithDistance($dbh, $customer_id, $dataDirections);

  $finalResults = array(
                  'tripPoints' => $dataTripPoints,
                  'googleDirections' => $dataDirections,
                  'colleges' => $dataColleges
                  );

  return $finalResults;
}

function getTripPoints($dbh,$tripID,$customer_id){

  $query = "select
  trip_point_id as tripPtID,
  address as addr,
  trip_points.unitID as schoolID,
  point_type_cd as pointTypeCd,
  addr_unitid_cd as addrUnitCd,
  instnm as collegeName,
  latitude as lat,
  longitude as lng,
  CONCAT(city,',', stabbr) as location
  from trip_points
       left join institutions on trip_points.unitid = institutions.unitid
  where
       trip_id = ?";
  $types = 'i';  ## pass
  $params = array($tripID);

  $data = execSqlMultiRowPREPARED($dbh, $query, $types, $params);
//  var_dump($data);
//  die;
  //  primary key is just 0, 1, 2 etc... integer counting the array items
  //  fieldValuePairs is the column name and the value
  $finalArr = array();
  foreach ($data as $primaryKey => $fieldValuePairs){
//      var_dump($fieldValuePairs);
      if ($fieldValuePairs{'pointTypeCd'} == "START"){
        $startAddr = $fieldValuePairs{'addr'};
      }
      if ($fieldValuePairs{'pointTypeCd'} == "END"){
        $endAddr = $fieldValuePairs{'addr'};
      }

      ## location: '42.8188000,-75.5350000'
      if ($fieldValuePairs{'pointTypeCd'} == "WAYPT"){
        $valArr = array(
        "tripPtID" => $fieldValuePairs{'tripPtID'},
        "schoolID" => $fieldValuePairs{'schoolID'},
        "name" => $fieldValuePairs{'collegeName'},
        "location" => $fieldValuePairs{'location'},
        "latLng" => $fieldValuePairs{'lat'} . "," . $fieldValuePairs{'lng'}
        );
        array_push($finalArr, $valArr);
      }
  }

  $finalData =
    array('start' => $startAddr, 'end' => $endAddr, 'wayPts' => $finalArr
  );

  return $finalData;
}

function addCollegeToTrip($dbh, $request_data, $customer_id){
    $tripID = $request_data->tripID;
    $schoolID = $request_data->schoolID;
    $pointTypeCd = "WAYPT";
    $addrUnitIDCd="U";
    $addr = "";
    $tripPointID = addTripPoint($dbh, $tripID, $pointTypeCd, $addrUnitIDCd, $addr, $schoolID);
    return array('tripPtID' => $tripPointID);
}


function deleteCollegeFromTrip($dbh, $request_data, $customer_id){
      $tripPtID = $request_data->tripPtID;
      $query = "DELETE from trip_points where trip_point_id = ?";
      $types = 'i';  ## pass
      $params = array($tripPtID);
      $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

      return array($rowsAffected);
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
        #### Division is a new multi-select
        #### FixMe ToDo:  Seems like this could be done better
        ###if ('options' == $field){
        if ('options' == $field  or  'divisions' == $field){
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
             case 'yrsOfSchool':
                 $value = $restOfArray{'options'};
                 $where = " and institutions.ICLEVEL in ($value)";
                 array_push($whereArr, $where);
                 break;
             case 'runBy':
                 $value = $restOfArray{'options'};
                 $where = " and institutions.CONTROL in ($value)";
                 array_push($whereArr, $where);
                 break;
             case 'states':
                 ### special logic to wrap the values in quotes (since they are alpha numeric)
                 $value = $restOfArray{'options'};
                 $valueArr = explode(",", $value);
                 # Iterate through the array and convert items to strings and wrap with quotes
                 array_walk($valueArr, create_function('&$str', '$str = "\"$str\"";'));
                 #### Changed this since strings are already quoted... Dont need more quotes
                 ####$value = implode('", "', $valueArr);
                 $value = implode(',', $valueArr);

                 $where = " and institutions.STABBR in ($value)";
                 array_push($whereArr, $where);
                 break;
             case 'schoolSize':
                 $value = $restOfArray{'options'};
                 $where = " and institutions.instsize in ($value)";
                 array_push($whereArr, $where);
                 break;
             case 'testScore':
                  $typesOfSchools = $restOfArray{'options'};
                  $typesOfSchoolsArr = explode(",", $typesOfSchools);

                  $sat = $restOfArray{'SAT'};
                  $act = $restOfArray{'ACT'};

                  $tempWhereArr = array();
                  $where = "";
                  foreach($typesOfSchoolsArr as $value){
                    if ($value == 'match'){
                      $where = " ( institutions.unitid in (select distinct admissions_info.unitid from admissions_info where (  ((SATVR25 + SATMT25) < $sat) and ((SATVR75 + SATMT75) > $sat) ) ) )";
                      array_push($tempWhereArr, $where);
                    }
                    if ($value == 'reach'){
                      $where = " ( institutions.unitid in (select distinct admissions_info.unitid from admissions_info where ( (SATVR25 + SATMT25) > $sat)  ) )";
                      array_push($tempWhereArr, $where);
                    }
                    if ($value == 'safety'){
                      $where = " ( institutions.unitid in (select distinct admissions_info.unitid from admissions_info where (  (SATVR75 + SATMT75) < $sat) and ((SATVR75 + SATMT75) > 0)  ) )";
                      array_push($tempWhereArr, $where);
                    }
                    if ($value == 'notest'){
                      $where = " ( institutions.unitid in (select distinct admissions_info.unitid from admissions_info where (  (SATVR75 + SATMT75) = 0)  ) )";
                      array_push($tempWhereArr, $where);
                    }
                  }
                  $finalWhere = "and (" . implode(" or ", $tempWhereArr) . ")";
                  #echo "finalWhere:$finalWhere\n";
                  array_push($whereArr, $finalWhere);
                  break;
             case 'schoolSetting':
                $value = $restOfArray{'options'};
                $where = " and institutions.locale in ($value)";
                array_push($whereArr, $where);
                break;
             case 'acptRate':
                 $min = $restOfArray{'min'};
                 $max = $restOfArray{'max'};
                 $where = " and (ADMSSN <> 0) and  (ADMSSN/APPLCN*100) between $min and $max";
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
                $sports = $restOfArray{'options'};
                $divisions = $restOfArray{'divisions'};

                $sportsArr = explode(",", $sports);
                $divisionsArr = explode(",", $divisions);
                $valueArr = array();
                #var_dump($sportsArr);
                #var_dump($divisionsArr);
                foreach($sportsArr as $sport){
                  foreach ($divisionsArr as $division){
                    ###echo "wow:$sport wowee:$division\n";
                    $item = $sport . "-" . $division;
                    array_push($valueArr,$item);
                  }
                }

                # Iterate through the array and convert items to strings and wrap with quotes
                array_walk($valueArr, create_function('&$str', '$str = "\"$str\"";'));
                #### Changed this since strings are already quoted... Dont need more quotes
                ####$value = implode('", "', $valueArr);
                $value = implode(',', $valueArr);
                ###echo "this is value for sports $value\n";
                $where = " and institutions.unitid in (select distinct sports.unitid from sports where CONCAT(sport_cd,'-',division) in ($value)) ";
                ###echo "this is where: $where\n";
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
  #$data = getCollegeFunc($dbh, $customer_id, 1);  #1 = return count
  $componentsArr = createWhereClauseUsingCriteria($dbh, $customer_id);
  $where = $componentsArr[0];
  $distCols = $componentsArr[1];
  $distHaving = $componentsArr[2];
  $where .= $distHaving;

  $query = "SELECT count(*) as count FROM (
  select institutions.unitid $distCols from institutions, admissions_info
  where institutions.unitid = admissions_info.unitid $where
  ) as count";
  #####echo "query:$query\n";
  $data = execSqlSingleRowPREPARED($dbh, $query);

  return $data;
}
function  getColleges($dbh, $customer_id){
  ####$data = getCollegeFunc($dbh, $customer_id);  #1 = return count
  $componentsArr = createWhereClauseUsingCriteria($dbh, $customer_id);
  $where = $componentsArr[0];
  $distCols = $componentsArr[1];
  $distHaving = $componentsArr[2];

  $where .= $distHaving;

  $query = "select instnm as name,
            institutions.unitid as schoolID,
            locale_decode as locale,
            latitude as lat,
            longitude as lng,
            CONCAT(city,',', stabbr) as location,
            webaddr as url,
            CASE
               WHEN ADMSSN = 0 THEN 'N/A'
               ELSE concat(round(ADMSSN/APPLCN*100),'%')
            END AS acpt_rate,
            instsize_decode as school_size $distCols
  from institutions, decode_instsize, decode_locale, admissions_info
  where
    institutions.instsize = decode_instsize.instsize and
    admissions_info.unitid = institutions.unitid and
    institutions.locale = decode_locale.locale
    $where  order by name
  ";
  #echo "this is the query:$query\n";
  $data = execSqlMultiRowPREPARED($dbh, $query);
  return $data;
}


####################### POSTS ####################################################
## This reads the data from a file instead of hitting google
function  getDirectionsFROMFILE($dbh, $request_data){
    $myfile = fopen("c:/tmp/googleMapResults.txt", "r") or die("Unable to open file!");
    $data = fread($myfile,filesize("c:/tmp/googleMapResults.txt"));
    fclose($myfile);
    ##echo $data;
    return($data);
}
//function  getDirectionsPLW($dbh, $request_data){
//      $dataRouteJSON  = getDirections($dbh, $request_data);
//      $dataResponse = json_decode($dataRouteJSON);
//      return $dataResponse;
//}


function  getDirectionsBest($dbh, $orig, $dest, $waypoints){
    ### This is the preferred version because it is cleaner
    ### I kept the other version in case I want to call it directly from Javascript
    ### orig and dest are locations ie: Duxbury, MA.  wayPtLocations is an array of locations
    $wayPts = "";
    if (isset($waypoints)){
      $wayPts = "optimize:true|";
      $wayPtLocations = array();
      foreach ($waypoints as $waypoint){
        array_push($wayPtLocations, $waypoint{"latLng"});
      }
      $wayPts .= implode("|", $wayPtLocations);
    }
//    echo "$wayPts";
//    die;
    $data = getDirectionsGMF($orig, $dest, $wayPts);
    $dataResponse = json_decode($data);
    return $dataResponse;
}

function  getDirections($dbh, $request_data){
    $orig = $request_data->routePoints->start;
    $dest = $request_data->routePoints->end;
    $wayPts = "";

    if (isset($request_data->waypoints)){
      $wayPts = "optimize:true|";
      $wayPtLocations = array();
      foreach ($request_data->waypoints as $waypoint){
        array_push($wayPtLocations, $waypoint->location);
      }
      $wayPts .= implode("|", $wayPtLocations);
    }

    $data = getDirectionsGMF($orig, $dest, $wayPts);

    ## Temp code to write the results of the directions call to a file (to use for testing)
    ## $myfile = fopen("c:/tmp/googleMapResults.txt", "w") or die("Unable to open file!");
    ## fwrite($myfile, $data);

    $dataResponse = json_decode($data);
    return $dataResponse;
}

function getCollegesOnRoute($dbh, $request_data, $customer_id){
  $data = getCollegesOnRouteNEW($dbh, $request_data, $customer_id);
  ####$data = getCollegesOnRouteOLD($dbh, $request_data, $customer_id);
  return($data);
}

function getCollegesWithDistance($dbh, $customer_id, $directionsResponse){
    ### This gets all the colleges and the calculates the distance off the route
    ###      it returns an array of colleges

    #### Extract Waypoints that Google Calculated
    $latLngArr = getWaypointsGMF($directionsResponse);

    #### GENERATE MISSING WAYPOINTS
    $latLngArr = genWaypointsGMF($latLngArr);

    $dataColleges = getColleges($dbh, $customer_id);

    $unit = "M";  ### Miles
    $itemsInArray = count($dataColleges);
    $itm = 0;
    while ($itm < $itemsInArray){
        $lat = $dataColleges[$itm]{'lat'};
        $lng = $dataColleges[$itm]{'lng'};
        $distanceOffRoute = getClosestDistance($lat, $lng, $latLngArr, $unit);
        $dataColleges[$itm]{'distance'} = round($distanceOffRoute,0);
        $itm++;
    }

    return $dataColleges;
}

function getCollegesOnRouteNEW($dbh, $request_data, $customer_id){
//    $dataRouteJSON  = getDirections($dbh, $request_data);
//    $dataResponse = json_decode($dataRouteJSON);
    $dataResponse  = getDirections($dbh, $request_data);

    #### Extract Waypoints that Google Calculated
    $latLngArr = getWaypointsGMF($dataResponse);

    #### GENERATE MISSING WAYPOINTS
    $latLngArr = genWaypointsGMF($latLngArr);

    $dataColleges = getColleges($dbh, $customer_id);

    $unit = "M";  ### Miles
    $itemsInArray = count($dataColleges);
    $itm = 0;
    while ($itm < $itemsInArray){
        $lat = $dataColleges[$itm]{'lat'};
        $lng = $dataColleges[$itm]{'lng'};
        $distanceOffRoute = getClosestDistance($lat, $lng, $latLngArr, $unit);
        $dataColleges[$itm]{'distance'} = round($distanceOffRoute,0);
        $itm++;
    }
//    echo "dumping...lat:$lat lng:$lng";
//    var_dump($dataColleges);
//    die("oh no");

    $finalResults = array(
                    'googleDirections' => $dataResponse,
                    'collegesOnRoute' => $dataColleges
                    );

    return $finalResults;
}


function getCollegesOnRouteOLD($dbh, $request_data, $customer_id){
    $dataRouteJSON  = getDirections($dbh, $request_data);

    $dataColleges = getColleges($dbh, $customer_id);
    $unitIDs = extractUnitIDs($dataColleges);
    $dataResponse = json_decode($dataRouteJSON);
    $g = $dataResponse->routes;

    $latLngArr = array();

    ### COLLECT WAYPOINTS
    ### ToDo: May want to decode the polylines and get all the actual lat/lng along the route
    ### there are hundreds (or thousands) along a typical route so it would be necessary to
    ### filter them.. eg: compare distance and drop the ones that are too close
    foreach ($g as $item){
      foreach ($item->legs as $myLeg){
        foreach ($myLeg->steps as $myStep){

            $lat = $myStep->start_location->lat;
            $lng = $myStep->start_location->lng;   ### no "O" in lng
            array_push($latLngArr, array($lat, $lng));
            $lat = $myStep->end_location->lat;
            $lng = $myStep->end_location->lng;   ### no "O" in lng
            array_push($latLngArr, array($lat, $lng));
            //echo "dir: " . $myStep->html_instructions . "Distance:" . $myStep->distance->text . "Lat/Lng" . $lat . $lng . "\n";
        }
      }
    }

    #### GENERATE MISSING WAYPOINTS
    $latLngArr = genWaypointsGMF($latLngArr);


    #### FIND COLLEGES
    $finalUnitIDsArr = array();
    $distance = 2500;
//    $distance = 5;   ## used for debugging
    foreach ($latLngArr as $point){
        $lat = $point[0];
        $lng = $point[1];
        $dataCollegesNearRoute = getCollegesNearby($dbh, $lat, $lng, $distance, $unitIDs);
        $finalUnitIDsArr = updateFinalArrayWithResults($dataCollegesNearRoute,$finalUnitIDsArr,$lat, $lng);
    }

    //echo "this is the finalUnitIDsArr after generating WayPoints and Finding Colleges:" . var_dump($finalUnitIDsArr) . "\n";
    $finalArr = array();
    foreach ($finalUnitIDsArr as $unitID => $rest){
      $dataArr = getCollegeUsingUnitID($dbh, $unitID);
      $distanceOffRoute = $rest{'distance'};
      ##echo "distanceOffRoute:$distanceOffRoute\n";
      $dataArr{'distance'} = $distanceOffRoute;
      array_push($finalArr, $dataArr);
    }
    $finalResults = array(
                    'googleDirections' => $dataResponse,
                    'collegesOnRoute' => $finalArr
                    );
    //echo "this is the finalArr after Adding in College Info:" . var_dump($finalArr) . "\n";
    return $finalResults;
}




function updateFinalArrayWithResults($collegesFound, $finalArr, $lat, $lng){
    foreach ($collegesFound as $itemCollege){
      $id = $itemCollege{'schoolID'};
      $dist = $itemCollege{'distance'};
//      if ('164924' == $id){
//        echo "id:$id is $dist miles away from this lat,lng: $lat, $lng\n";
//      }

      if (array_key_exists($id, $finalArr)){
        if ($dist < $finalArr{$id}{'distance'}){
//          echo "item exists in the array..updating it: $id|$dist|$lat|$lng\n";
          $finalArr{$id}{'distance'} = $dist;
          $finalArr{$id}{'lat'} = $lat;
          $finalArr{$id}{'lng'} = $lng;
        }
      } else {
//        echo "item did NOT exists in the array..adding it: $id|$dist|$lat|$lng\n";
        $finalArr{$id}{'distance'} = $dist;
        $finalArr{$id}{'lat'} = $lat;
        $finalArr{$id}{'lng'} = $lng;
      }
//      echo "id: $id distance:$dist\n";
    }
    return $finalArr;
}

function getCollegeUsingUnitID($dbh, $unitID){
    $query = "select instnm as name,
                     unitid as schoolID,
                     locale_decode as locale,
                     CONCAT(city,',', stabbr) as location,
                     webaddr as url,
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
//    array_push($unitIDsArr, $College{'id'});
    array_push($unitIDsArr, $College{'schoolID'});
  }
  return $unitIDsArr;
}

function getCollegesNearby($dbh, $lat, $lng, $distance, $unitIDs){
      $whereUnits = implode(",", $unitIDs);
      $query = "select unitid as schoolID,
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
     case 'yrsOfSchool':
     case 'runBy':
     case 'schoolSetting':
     case 'states':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "options");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'sports':
           #echo "this is request_data:\n";
           #var_dump($request_data);
           ######sportsPreProcess($request_data);
           saveCriteriaFunc($dbh, $customer_id, $request_data, "options");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "divisions");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'schoolCost':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "min");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "max");
           saveCriteriaFunc($dbh, $customer_id, $request_data, "enabled");
           break;
     case 'acptRate':
           saveCriteriaFunc($dbh, $customer_id, $request_data, "min");  # last param is the field
           saveCriteriaFunc($dbh, $customer_id, $request_data, "max");
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


  $status = array( "status" => 1);
  return $status;
}

##############################################################################
function  sportsPreProcess($request_data){
  ## todo: make this more elegant
  if (is_array($request_data->sports->options)){
  } else {
    echo "error - select a SPORT\n";
  }
  if (is_array($request_data->divisions->options)){
  } else {
    echo "error - select a DIVISION\n";
  }

  $values = array();
  foreach ($request_data->sports->options as $sport){
    foreach ($request_data->divisions->options as $div){
        #Concatenate Sport Code and the Division Identifier
        $newItem = $sport->id . "-" . $div->id;
        # Create object using the new pair..
        $obj = (object) array('id' => $newItem);
        array_push($values,$obj);
    }
  }
  $request_data->options = $values;

}

function  saveCriteriaFunc($dbh, $customer_id, $request_data, $field_cd){

  $criteria_cd = $request_data->func;
  $value="";
  if (is_array($request_data->$field_cd)){
      ### HACK:  assumes all the OPTIONS fields are multiple select fields so value will be an object
      ### NOTE: the options MUST use 'id' as the key!
      #####echo "wooo hooo\n";
      ######var_dump($request_data->$field_cd);
      #### Divisions is a new multi-select
      ####if ('options' == $field_cd){
      if ('options' == $field_cd or 'divisions' == $field_cd ){
        $values = array();
        foreach ($request_data->$field_cd as $item){
          ### hard-coded ID
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

function  addTrip($dbh, $request_data, $customer_id){
    $name = $request_data->name;

    $query = "INSERT INTO trips (customer_id, trip_name) VALUES
             (?, ?)";

    $types = 'is';  ## pass
    $params = array($customer_id, $name);

    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
    $tripID = mysqli_insert_id($dbh);

    $pointTypeCd = "START";
    $addrUnitIDCd="A";
    $addr = $request_data->startingPoint;
    $unitID = "";
    addTripPoint($dbh, $tripID, $pointTypeCd, $addrUnitIDCd, $addr, $unitID);

    $pointTypeCd = "END";
    $addr = $request_data->endingPoint;
    addTripPoint($dbh, $tripID, $pointTypeCd, $addrUnitIDCd, $addr, $unitID);

    $pointTypeCd = "WAYPT";
    $addrUnitIDCd="U";
    $addr = "";
    #### HACK.. should be a cleaner way to do this FIXME   TODO
    foreach ($request_data->pickedCollege as $item){
      $schoolID = $item->schoolID;
      ###echo "$unitID\n";
    }
    addTripPoint($dbh, $tripID, $pointTypeCd, $addrUnitIDCd, $addr, $schoolID);

    $data = array(
      array('recordsAdded' => $rowsAffected),
    );
    return $data;
}

function  addTripPoint($dbh, $tripID, $pointTypeCd, $addrUnitIDCd, $addr, $schoolID){
    $query = "INSERT INTO trip_points   (trip_id, address, UNITID, point_type_cd, addr_unitid_cd)  VALUES
             (?, ?, ?, ?, ?)";

    $types = 'isiss';  ## pass
    $params = array($tripID,$addr, $schoolID,$pointTypeCd,$addrUnitIDCd);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
    $tripPointID = mysqli_insert_id($dbh);
    return $tripPointID;
}



function  deleteTrip($dbh, $request_data, $customer_id){
    $tripID = $request_data->tripID;


    $query = "DELETE from trip_points where trip_id = ?";
    $types = 'i';  ## pass
    $params = array($tripID);
    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);

    $query = "DELETE from trips where customer_id = ? and trip_id = ?";
    $types = 'ii';  ## pass
    $params = array($customer_id, $tripID);

    $rowsAffected = execSqlActionPREPARED($dbh, $query, $types, $params);
    $data = array(
      array('recordsDeleted' => $rowsAffected),
    );
    return $data;
}

function  saveLocation(){
    return 1;
}




?>