'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:GooglemapCtrl
 * @description
 * # GooglemapCtrl
 * Controller of the collegeApp
 */

angular.module('collegeApp')
    .controller('GooglemapCtrl', ['$scope', 'uiGmapGoogleMapApi', 'collegeFactory', function ($scope, uiGmapGoogleMapApi, collegeFactory) {

    angular.extend($scope, {
        map: {
            zoom: 7,
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
    $scope.routePoints.start = "Duxbury,MA";
    $scope.routePoints.end = "Duxbury,MA";
    $scope.collegesOnRoute = "";
//    $scope.routePoints.end = "State College,PA";
//    $scope.collegesOnRoute = "";

//  This section creates the Direction Renderer  that is used to render the map using info obtained in the PHP
    uiGmapGoogleMapApi.then(function(maps) {
      var directionsDisplay = new maps.DirectionsRenderer();

//      FIXME: This code can be removed, since the route is being done in PHP
//      ALTHOUGH.....MAY WANT TO KEEP IT SINCE IT ALLOWS FOR DEBUGGING (ie: RUN CODE via PHP then run it via this JS)

//      $scope.calcRoute = function (routePoints) {
//          directionsDisplay.setMap($scope.map.control.getGMap());
//          var directionsService = new maps.DirectionsService();
//
//          var start = routePoints.start.latlng;
//          var end = routePoints.end.latlng;
//          var request = {
//              origin: start,
//              destination: end,
//              travelMode: maps.TravelMode.WALKING
//          };
//          //ToDo: Replace this with a call to a service that will call GoogleMaps WebService
//          //ToDo: Get result of webservice and correct it so it matches JS result
//          directionsService.route(request, function(response, status) {
//              if (status == maps.DirectionsStatus.OK) {                       //jshint ignore:line
//                  directionsDisplay.setDirections(response);
//              }
//          });
//      };

      $scope.getCollegesOnRoute = function () {
        directionsDisplay.setMap($scope.map.control.getGMap());
        var theRequest = {};
        theRequest.routePoints = $scope.routePoints;
        theRequest.waypoints = $scope.waypoints;

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

      $scope.addStop = function (college) {
        $scope.waypoints.push(college);
        $scope.getCollegesOnRoute();
      };
      $scope.removeStop = function (college) {
        var index = $scope.waypoints.indexOf(college);
        $scope.waypoints.splice(index,1);
        $scope.getCollegesOnRoute();
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
