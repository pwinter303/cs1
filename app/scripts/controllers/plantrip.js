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

    collegeFactory.getDataUsingPost(url, formData).then(function (data) {
      if (data){
        $scope.startAddress = data.tripPoints.start;
        $scope.endAddress = data.tripPoints.end;
        $scope.tripWaypoints = data.tripPoints.wayPts;

        $scope.googleDirections = data.googleDirections;
        $scope.renderDirectionsPLW($scope.googleDirections);

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
//
//
//  $scope.getErrDone = function (){
//    var theRequest = {};
//    theRequest.routePoints = $scope.routePoints;
//    theRequest.waypoints = [{location: 'Harrisburg,PA'}];
//    collegeFactory.getCollegesOnRoute(theRequest).then(function (data) {
//      if (data){
//        $scope.collegesOnRoute = data.collegesOnRoute;
//        $scope.googleDirections = data.googleDirections;
//        $scope.renderDirectionsPLW($scope.googleDirections);
//      }
//    }, function(error) {
//      // promise rejected, could be because server returned 404, 500 error...
//      collegeFactory.msgError(error);
//    })
//  };

  $scope.getDirections = function (requestData) {
    var url = "college.php";
    var theRequest = {};
    theRequest.routePoints = $scope.routePoints;
    theRequest.waypoints = [{location: 'Harrisburg,PA'}];
//    theRequest.waypoints = [{location: '41.43206,-81.38992'}];
    theRequest.waypoints = [{location: '42.8188000,-75.5350000'}];
    theRequest.action = "getDirections";
    collegeFactory.getDataUsingPost(url,theRequest).then(function (data) {
      if (data){
        $scope.googleDirections = data;
        $scope.renderDirectionsPLW($scope.googleDirections);
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError(error);
    })
  };


//FIXME: could  should this be moved into a service?  BUT $scope is maninpulated
//    and I didnt think that was acceptable to do in a service

//  This section creates the Direction Renderer  that is used to render the map using info obtained in the PHP
uiGmapGoogleMapApi.then(function(maps) {
  var directionsDisplay = new maps.DirectionsRenderer();

  $scope.renderDirectionsPLW = function (googleDirections) {
    directionsDisplay.setMap($scope.map.control.getGMap());
    $scope.map.control.refresh();
    var displayedMap = $scope.map.control.getGMap();
    //Original request below.  Origin and Destination are in the googleDirections object so
    // simplified request to only include the travelMode
    //var request = {origin: 'Boston, MA', destination: 'Hanover,NH', travelMode: google.maps.TravelMode.DRIVING};
    var request = {travelMode: google.maps.TravelMode.DRIVING};

    renderDirections(displayedMap, googleDirections, request, directionsDisplay);
    extractAndDisplayDirections(googleDirections);
  }

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
        draggable : false,
//        draggable : true, commented 2016-11-18
        map : map
      });
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
          var $myCtr=0;
          leg.steps.forEach(function(step){
            step.start_location = asLatLng(step.start_location);
            step.end_location   = asLatLng(step.end_location);
            step.path = asPath(step.polyline);
            $myCtr++;
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
