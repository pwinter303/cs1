'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:PlantripCtrl
 * @description
 * # PlantripCtrl
 * Controller of the collegeApp
 */
angular.module('collegeApp')
  .controller('PlantripCtrl', ['$scope', 'uiGmapGoogleMapApi', 'collegeFactory', function ($scope, uiGmapGoogleMapApi, collegeFactory) {


  angular.extend($scope, {
    map: {
      zoom: 5,
      //FIXME: This should be based on the persons home address
      center: {latitude: 40.75, longitude: -74.65},
      options: {
        maxZoom: 20,
        minZoom: 3,
        scrollwheel: false
      },
      control: {}
    },
    routePoints: {
      start: {},
      end: {}
    },
    waypoints: []
  });

  // FIXME: done just to get map working
  $scope.routePoints.start = "Duxbury,MA";
  $scope.routePoints.end = "Lewisburg,PA";
  $scope.collegesOnRoute = "";


  $scope.tripListShow = true;
  $scope.tripPlanningShow = false;
  $scope.addTripShow = false;
  $scope.addCollegeShow = false;
  $scope.roundTrip = false;
  $scope.trip = {startingPoint:'',endingPoint:''};

  $scope.$watch('trip.startingPoint', function () {
    if ($scope.roundTrip) {
      $scope.trip.endingPoint = $scope.trip.startingPoint;
    }
  });

  $scope.$watch('roundTrip', function (checkBoxValue) {
    if (checkBoxValue) {
      $scope.trip.endingPoint = $scope.trip.startingPoint;
    } else {
      $scope.trip.endingPoint = '';
    }
  });

  $scope.getTrips = function (){
    var url = "college.php";
    var action = "getTrips";
    collegeFactory.getData(url, action).then(function (data) {
      if (data){
        $scope.trips = data;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError(error);
    });
  };
  $scope.getTrips();

  $scope.addTrip = function(formData){
    var url = "college.php";
    formData.action = "addTrip";
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        collegeFactory.msgSuccess('Added');
        $scope.getTrips();
        $scope.addTripShow = false;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Saving:' + error);
    });
  };

  $scope.deleteTrip = function(formData){
    var url = "college.php";
    formData.action = "deleteTrip";
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        collegeFactory.msgSuccess('Deleted');
        $scope.getTrips();
        $scope.addTripShow = false;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Saving:' + error);
    });
  };

  $scope.getTripDetails = function(formData){
    $scope.tripPlanningShow = true;
    var url = "college.php";
    formData.action = "getTripDetails";
    $scope.activeTripID = formData.tripID;
    $scope.getCollegesOnRoute();
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        $scope.startEndAddresses = data[0];
        $scope.tripWaypoints = data[1];

        // pass start and end off to get colleges on route
        // get the list of colleges returned
        // then do the show - hide logic on the 2nd set of colleges
        // OR... should get colleges on route just return the schoolID
        // and distance and then the script can update the college array that is
        // already local..
        // also.. keep in mind adding college to trip or deleting it
        // will need to perform the same logic....




          // Spin through colleges and hide them if they're already part of the trip
          for(var iData=0; iData< $scope.colleges.length; iData++){
            $scope.colleges[iData].showMe = true;
            for(var i=0; i < $scope.tripWaypoints.length;i++){
                if($scope.tripWaypoints[i].schoolID === $scope.colleges[iData].schoolID){
                  $scope.colleges[iData].showMe = false;
                }
              }
          }
          $scope.tripListShow = false;


        }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Getting Trip Details:' + error);
    });
  };

  $scope.deleteCollegeFromTrip = function(formData){
    var url = "college.php";
    formData.action = "deleteCollegeFromTrip";
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        collegeFactory.msgSuccess('Removed');
        // Remove college from waypoint array
        // Add college to college list array
        // Spin through colleges and hide them if they're already part of the trip
        for(var i =0; i < $scope.tripWaypoints.length; i++){
          if($scope.tripWaypoints[i].tripPtID === formData.tripPtID){
            $scope.tripWaypoints.splice(i, 1);
          }
        }
        for(var iData=0; iData< $scope.colleges.length; iData++){
            if($scope.colleges[iData].schoolID === formData.schoolID){
              $scope.colleges[iData].showMe = true;
            }
        }
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Updating Trip Details:' + error);
    });
  };

  $scope.addCollegeToTrip = function(formData){
    var url = "college.php";
    formData.action = "addCollegeToTrip";
    formData.tripID = $scope.activeTripID;
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        //FIXME: clean up formData which contains the college being added
        for(var iData=0; iData< $scope.colleges.length; iData++){
          if($scope.colleges[iData].schoolID === formData.schoolID){
            $scope.colleges[iData].showMe = false;
          }
        }
        // also should the push be using data from the college array?
        formData.tripPtID = data.tripPtID;
        $scope.tripWaypoints.push(formData);
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Updating Trip Details:' + error);
    });
  };

  $scope.getColleges = function (){
    var url = "college.php";
    var action = "getColleges";
    collegeFactory.getData(url, action).then(function (data) {
      if (data){
        $scope.colleges = data;
//            $scope.rowCollection = data;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError(error);
    });
  };
  $scope.getColleges();


//  This section creates the Direction Renderer  that is used to render the map using info obtained in the PHP
uiGmapGoogleMapApi.then(function(maps) {
  var directionsDisplay = new maps.DirectionsRenderer();

  $scope.getCollegesOnRoute = function () {
    directionsDisplay.setMap($scope.map.control.getGMap());
    var theRequest = {};
    theRequest.routePoints = $scope.routePoints;
    //theRequest.waypoints = $scope.waypoints;
    theRequest.waypoints = [{location: 'Harrisburg,PA'}];

    //temp hack.. ToDo. FixMe:  I think this can safely be removed..
      var request = {origin: 'Boston, MA', destination: 'Hanover,NH', travelMode: google.maps.TravelMode.DRIVING};

    collegeFactory.getCollegesOnRoute(theRequest).then(function (data) {
      if (data){
        // Process the route returned from PHP code
        if (data.status == maps.DirectionsStatus.OK) {                       //jshint ignore:line
          directionsDisplay.setDirections(data);
        }
        $scope.collegesOnRoute = data.collegesOnRoute;
        $scope.googleDirections = data.googleDirections;
        for(var w = 0; w < $scope.waypoints.length; w++) {
          $scope.waypoints[w].distance = 0;
        }

        // This is needed because the map is originally initialized when
        //    the div is hidden so it doesnt initialize properly.  When the
        //    div is shown for the first time it must be refreshed.
        //    There were other options for getting around the issue but refresh worked
        //    and is the cleanest
        //    other option:  https://github.com/angular-ui/angular-google-maps/issues/76
        $scope.map.control.refresh();

        //renderDirections(this.myMap, result, this.myRequest, this.myDirectionsDisplay);
        var displayedMap = $scope.map.control.getGMap();
        renderDirections(displayedMap, $scope.googleDirections, request, directionsDisplay);
        extractAndDisplayDirections($scope.googleDirections);

      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError(error);
    });

//        return;
    };

});  // end of uiGoogleMapApi

    <!-- credit goes to: -->
    <!--gis.stackexchange.com/questions/15197/google-maps-v3-in-javascript-api-render-route-obtained-with-web-api/187869#187869-->
    function renderDirections(map, response, request, renderer){
//      var copyOfResponse = response;
      typecastRoutes(response.routes);
//        console.log(response);
//      2016-11-16 commented it out
//      var justRoutes = response.routes;


      renderer.setOptions({
        directions : {
          routes : response.routes,
          // PLW: In VERSION 3 REPLACED UB with 'request' !!!!!!!!
          // "ub" is important and not returned by web service it's an
          // object containing "origin", "destination" and "travelMode"
          //  ub : request,
          request : request
        },
        draggable : true,
        map : map
      });
    }

    function extractAndDisplayDirections(response){
      var route = response.routes[0];
      var summaryPanel = document.getElementById('directions-panel');
      summaryPanel.innerHTML = '';
      // For each route, display summary information.
      for (var i = 0; i < route.legs.length; i++) {
        var routeSegment = i + 1;
        summaryPanel.innerHTML += '<b>Route Segment: ' + routeSegment +
          '</b><br>';
        summaryPanel.innerHTML += route.legs[i].start_address + ' to ';
        summaryPanel.innerHTML += route.legs[i].end_address + '<br>';
        summaryPanel.innerHTML += route.legs[i].distance.text + '<br><br>';
      }
    }


    <!-- -->
    function typecastRoutes(routes){
      routes.forEach(function(route){
        route.bounds = asBounds(route.bounds);
        // I don't think `overview_path` is used but it exists on the
        // response of DirectionsService.route()
        route.overview_path = asPath(route.overview_polyline);

        route.legs.forEach(function(leg){
          leg.start_location = asLatLng(leg.start_location);
          leg.end_location   = asLatLng(leg.end_location);

          leg.steps.forEach(function(step){
            step.start_location = asLatLng(step.start_location);
            step.end_location   = asLatLng(step.end_location);
            step.path = asPath(step.polyline);
          });

        });
      });
    }

    function asBounds(boundsObject){
      return new google.maps.LatLngBounds(asLatLng(boundsObject.southwest),
        asLatLng(boundsObject.northeast));
    }

    function asLatLng(latLngObject){
      return new google.maps.LatLng(latLngObject.lat, latLngObject.lng);
    }

    function asPath(encodedPolyObject){
      return google.maps.geometry.encoding.decodePath( encodedPolyObject.points );
    }


    function geocodeAddress(geocoder, resultsMap, address) {
      geocoder.geocode({'address': address}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
          resultsMap.setCenter(results[0].geometry.location);
//                var marker = new google.maps.Marker({
          var marker = new MarkerWithLabel({
            map: resultsMap,
            title: 'Hello World!',
            labelContent: "UHTFD",
            labelAnchor: new google.maps.Point(22, 0),
            labelClass: "labels", // the CSS class for the label
            labelStyle: {opacity: 0.75},
            position: results[0].geometry.location
          });
        } else {
          alert('Geocode was not successful for the following reason: ' + status);
        }
      });
    }

  }]);
