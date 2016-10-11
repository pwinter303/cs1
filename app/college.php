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
        array('id' => 'worst', 'name' => 'The lowest in the school'),
        array('id' => 'average', 'name' => 'Average for those attending'),
        array('id' => 'best', 'name' => 'The best in the school')
        );
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

    ##echo "$where,$distCols,$distHaving\n";

    $where .= $distHaving;
    $selectCols = "instnm as name,
                             unitid as id,
                             locale_decode as locale,
                             CONCAT(city,',', stabbr) as location,
                             webaddr as url,
                             instsize_decode as school_size $distCols";

    $countSelect = "";
    $countEnd = "";
    if ($count){
      $countSelect = "SELECT count(*) as count FROM (";
      $countEnd = ") as count";
    }

    $query = "$countSelect  select  $selectCols
    from institutions, decode_instsize, decode_locale
    where
      institutions.instsize = decode_instsize.instsize and
      institutions.locale = decode_locale.locale
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


####################### POSTS ####################################################
## This reads the data from a file instead of hitting google
function  getDirectionsFROMFILE($dbh, $request_data){
    $myfile = fopen("c:/tmp/googleMapResults.txt", "r") or die("Unable to open file!");
    $data = fread($myfile,filesize("c:/tmp/googleMapResults.txt"));
    fclose($myfile);
    ##echo $data;
    return($data);
}

function  getDirectionsAsPOST($dbh, $request_data){
    $orig = $request_data->routePoints->start;
    $dest = $request_data->routePoints->end;
    $wayPts = "";

    #var_dump($request_data->waypoints);
    if (isset($request_data->waypoints)){
      #$wayPts = "&waypoints=optimize:true|";
      $wayPts = "optimize:true|";
      $wayPtLocations = array();
      foreach ($request_data->waypoints as $waypoint){
        array_push($wayPtLocations, $waypoint->location);
      }
      $wayPts .= implode("|", $wayPtLocations);
    }

    $myKey = "&key=AIzaSyBJW90ZQrxG82XCEqDn9uxBlef8x7Oebkc";
    $parameters = "origin=" . urlencode($orig) . "&destination=" . urlencode($dest) . "&waypoints=" . urlencode($wayPts) . $myKey;

    $encodedParams = $parameters;

    $url = "https://maps.googleapis.com/maps/api/directions/json?" . $encodedParams;
    #$urlUnFmttd = "https://maps.googleapis.com/maps/api/directions/json?" . "origin=" . $orig . "&destination=" . $dest . "&waypoints="  . $wayPts . $myKey;
    #echo "url is:$url\n";
    #echo "urlUnFmttd is:$urlUnFmttd\n";
    error_reporting(0);
    header('Content-Type: application/json');
    #echo file_get_contents($_GET["url"]);
    if (!$data = file_get_contents($url)) {
          $error = error_get_last();
          echo "HTTP request failed. Error was: " . $error['message'];
    }
    ## Temp code to write the results of the directions call to a file (to use for testing)
    ## $myfile = fopen("c:/tmp/googleMapResults.txt", "w") or die("Unable to open file!");
    ## fwrite($myfile, $data);
    return($data);
}

function getCollegesOnRoute($dbh, $request_data, $customer_id){
    $dataRouteJSON  = getDirectionsAsPOST($dbh, $request_data);
    ##var_dump($dataRouteJSON);
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
    $latLngArr = genWaypoints($latLngArr);


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

function genWaypoints($latLngArr){
  $itemsInArray = count($latLngArr);
  $ctr = 0;
  while ($ctr < $itemsInArray){
      list ($lat1,$lng1) = $latLngArr[$ctr];
      list ($lat2,$lng2) = $latLngArr[$ctr + 1];
      $dist = distance($lat1, $lng1, $lat2, $lng2, ""); #### last param is unit (blank is miles)
      $dist = round($dist);
      if ($dist > 11){
          list ($latNew, $lngNew) = midpoint($lat1, $lng1, $lat2, $lng2);
          //echo "lat1:$lat1,lat2:$lat2 dist is:$dist adding these:$latNew:$lngNew after $lat1:$lng1 and before $lat2:lng2\n";
          $newItem = array(array($latNew,$lngNew));
          array_splice( $latLngArr, $ctr + 1, 0, $newItem ); // add items to array
          #var_dump($latLngArr);
          #die;
      } else {
          $ctr++;
      }
  }
  return $latLngArr;
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

function convertPolylineToLatLng(){

# Do steps 1-11 given here
# https://developers.google.com/maps/documentation/utilities/polylinealgorithm
# in reverse order and inverted (i.e. left shift -> right shift, add -> subtract)

$string = "udgiEctkwIldeRe}|x@cfmXq|flA`nrvApihC";
$string = "mmf~Fjp{rL`CgEp@oAx@cBf@aAjAyCbA_D@CZiA@C?A?ARu@h@}BFW\\cBf@}BdAuERw@Ng@Rk@j@kAR[BCBENQJO@AHI\\a@d@a@t@i@JIjEaDlBsAXSBALILKJG@AZOp@]d@QRGTIRETEf@IRAVATAT?\\@`@BTBTB`ANl@HRBt@H\\@l@?JA^Aj@Gb@GZIfAQZIZIr@O|@Ux@UxBk@tA_@b@Md@KXIrBg@dD}@d@KhAUjA[t@QRGVGFCb@K\\INCPC\\GRCTA`@ARAV@P@V@F@D@F@TDTDTFRHRHRHPJRNRLPLPPNNPPNRLPNTLTNVJTXr@`@|@JXv@hBnCnGf@nA|@xBr@bB~BtFZt@vAfDtHvQJVRj@Pb@X|@r@dCRl@f@xA`@bAb@`Ad@|@l@fA`@r@d@t@j@z@t@bAV\\RVf@n@rA~At@v@v@x@dAbAb@`@BBNLNNTPRR`@Zt@n@p@f@x@p@z@l@XRXRZRt@d@JHv@h@^Tn@^hAl@|@f@xAt@~@b@r@ZHDDBB@fCdAd@PTJ`@NB@FBxAf@nA`@LDB@pBh@vEjAjEjAbDv@PDnA\\lFvA|Cv@fKnCVH~MrDzDbA~Bj@pGbBNBB@JBn@PxA^b@Lr@PvBj@r@RpD`AbCn@`GzAbAX~A`@h@R~@XrA`@~@Zd@R|@\\h@Tj@X^Ph@Vh@Z\\PVPh@\\v@f@x@j@fAz@~@z@dBdBn@p@dBjBdAjApAxAlApAFHPPRRdGtGt@x@fCpClApAdAfAdA`AxApAzAhAlAx@ZRf@XPJp@^f@VnBbAPJRJLFz@\\l@R|@XdD~@pCt@pElAfAVpDdA~Bp@`AVhBj@r@T~Aj@RHd@PRHTLx@`@z@`@z@`@|@d@x@`@h@Xz@f@lAr@vBzANHp@f@d@\\f@^dA~@|AnAfA`ArArAvAzArAzArAzA`BvBrCbEj@z@vLjQn@`AnC~Dl@z@hCzDBDhGbJjC|DnDfFdA|A~@tAz@lAlAfB\\h@`@j@j@z@hElGf@p@jAvAp@v@v@x@`AbAjA`AjA~@`BjADBlBjAd@Vn@\\n@Xf@Tr@Zt@VjDlAbExAD@@@PHD@|Bv@FBPF~Bz@r@VtAj@t@ZRHNFHBzAd@dBb@hB`@HB|AVfBVx@JhEVz@DP@^P~DGtCApB?bEB|B?|BBp@?l@D|@Dr@Fv@Jj@Fd@Hl@Jl@Hh@Lj@Lh@Jh@LNDXFh@L~AZzBf@nCn@jFjAvCn@\\Hz@Rz@RfJpBfATtBd@hB\\|B\\|BV|BVrBNzBNbAD~AD`A@tA?rDAvC?|AAlCAzAA@?f@@bC?pAAlA?nDAtF?zB@z@?jAA`BAbB?zAA~@@X@L?dBH~AJnBPr@Ht@Lv@Nn@NTFPDx@T`Cv@^NXL|@`@z@b@dAl@n@^z@j@p@f@h@b@^Zz@r@bA`At@v@`@f@Z^d@j@^f@f@t@f@x@Zf@^r@Xp@\\x@Rf@L\\Pl@L`@VfANr@Hd@DNJp@ZdCPtAHh@Ff@`@lD";
# Step 11) unpack the string as unsigned char 'C'
$byte_array = array_merge(unpack('C*', $string));
$results = array();

$index = 0; # tracks which char in $byte_array
do {
  $shift = 0;
  $result = 0;
  do {
    $char = $byte_array[$index] - 63; # Step 10
    # Steps 9-5
    # get the least significat 5 bits from the byte
    # and bitwise-or it into the result
    $result |= ($char & 0x1F) << (5 * $shift);
    $shift++; $index++;
  } while ($char >= 0x20); # Step 8 most significant bit in each six bit chunk
    # is set to 1 if there is a chunk after it and zero if it's the last one
    # so if char is less than 0x20 (0b100000), then it is the last chunk in that num

  # Step 3-5) sign will be stored in least significant bit, if it's one, then
  # the original value was negated per step 5, so negate again
  if ($result & 1)
    $result = ~$result;
  # Step 4-1) shift off the sign bit by right-shifting and multiply by 1E-5
  $result = ($result >> 1) * 0.00001;
  $results[] = $result;
} while ($index < count($byte_array));

# to save space, lat/lons are deltas from the one that preceded them, so we need to
# adjust all the lat/lon pairs after the first pair
for ($i = 2; $i < count($results); $i++) {
  $results[$i] += $results[$i - 2];
}

# chunk the array into pairs of lat/lon values

$plwArr = array_chunk($results, 2);
foreach ($plwArr as $item){
  #var_dump($item);
  list ($lat, $lng) = $item;
  echo "lat:$lat lng:$lng<br>";
}
//var_dump(array_chunk($results, 2));

# Test correctness by using Google's polylineutility here:
# https://developers.google.com/maps/documentation/utilities/polylineutility


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
                     unitid as id,
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
    array_push($unitIDsArr, $College{'id'});
  }
  return $unitIDsArr;
}

function getCollegesNearby($dbh, $lat, $lng, $distance, $unitIDs){
      $whereUnits = implode(",", $unitIDs);

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
      #####var_dump($request_data->$field_cd);
      #### Divisions is a new multi-select
      ####if ('options' == $field_cd){
      if ('options' == $field_cd or 'divisions' == $field_cd ){
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