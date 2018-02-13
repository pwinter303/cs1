<?php

#### this contains the google and distance functions

//PLW Added 2016-04-06
date_default_timezone_set('America/New_York');



function  getDirectionsGMF($orig, $dest, $wayPts){
//    $myKey = "&key=AIzaSyBJW90ZQrxG82XCEqDn9uxBlef8x7Oebkc";
    $myKey = "&key=AIzaSyCab-1RSi3hLrQX5mO2aE7CIcmbWTOLFfU";
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
    return($data);
}

function getWaypointsGMF($googleResponseJSON){

    $routes = $googleResponseJSON->routes;

    ### COLLECT WAYPOINTS
    ### ToDo: May want to decode the polylines and get all the actual lat/lng along the route
    ### there are hundreds (or thousands) along a typical route so it would be necessary to
    ### filter them.. eg: compare distance and drop the ones that are too close
    $latLngArr = array();
    foreach ($routes as $item){
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
    return $latLngArr;
}



function genWaypointsGMF($latLngArr){
    $itemsInArray = count($latLngArr);
    $ctr = 0;
    while ($ctr < $itemsInArray){
        list ($lat1,$lng1) = $latLngArr[$ctr];
        list ($lat2,$lng2) = $latLngArr[$ctr + 1];
        $dist = distanceGMF($lat1, $lng1, $lat2, $lng2, ""); #### last param is unit (blank is miles)
        $dist = round($dist);
        if ($dist > 11){
            list ($latNew, $lngNew) = midpointGMF($lat1, $lng1, $lat2, $lng2);
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


function midpointGMF($lat1, $lng1, $lat2, $lng2) {
    ### credit for these two functions goes to:  http://stackoverflow.com/questions/5657194/need-help-calculating-longitude-and-latitude-midpoint-using-javascript-from-php
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

    $latNew = ($lat3*180)/$pi;
    $lngNew = ($lng3*180)/$pi;
    return array($latNew, $lngNew);
}

function convertPolylineToLatLngGMF(){

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


function getClosestDistance($lat1, $lng1, $latLngArr, $unit) {

    $finalDistance = 999999999;
    foreach ($latLngArr as $point){
        $ptLat = $point[0];
        $ptLng = $point[1];
        $distance = distanceGMF($lat1, $lng1, $ptLat, $ptLng, $unit);
        if ($distance < $finalDistance){
          $finalDistance = $distance;
        }
    }
    return $finalDistance;
}

function distanceGMF($lat1, $lng1, $lat2, $lng2, $unit) {
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


?>