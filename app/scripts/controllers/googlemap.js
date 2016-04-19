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

    $scope.getColleges = function (){
        collegeFactory.getColleges().then(function (data) {
            if (data){
                $scope.colleges = data;
            }
        }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            collegeFactory.msgError(error);
        });
    };
    $scope.getColleges();


    angular.extend($scope, {
        map: {
            zoom: 7,
            center: {latitude: 40.75, longitude: -74.65},
//            center: {
//                latitude: 35.681382,
//                longitude: 139.766084
//            },
            options: {
                maxZoom: 20,
                minZoom: 3
            },
            control: {},
            routes: {
                start: [
                    {name:'Duxbury,MA', latlng:'42.0400000,-70.6700000'},
                  {name:'Arlington, VA', latlng:'38.8700000,-77.1000000'},
                  {name:'Montclair,NJ', latlng:'40.82590,-74.2090'}
                ],
                end: [
                    {name:'Arlington,VA', latlng:'38.8700000,-77.1000000'},
                  {name:'Duxbury, MA', latlng:'42.0400000,-70.6700000'}
                ]
            }
        },
        routePoints: {
            start: {},
            end: {}
        }
        });
        $scope.routePoints.start = $scope.map.routes.start[0];
        $scope.routePoints.end = $scope.map.routes.end[0];

        uiGmapGoogleMapApi.then(function(maps) {
            var directionsDisplay = new maps.DirectionsRenderer();

            $scope.calcRoute = function (routePoints) {
                directionsDisplay.setMap($scope.map.control.getGMap());
                var directionsService = new maps.DirectionsService();
                var start = routePoints.start.latlng;
                var end = routePoints.end.latlng;
                var request = {
                    origin: start,
                    destination: end,
                    travelMode: maps.TravelMode.WALKING
                };
                //ToDo: Replace this with a call to a service that will call GoogleMaps WebService
                //ToDo: Get result of webservice and correct it so it matches JS result
                directionsService.route(request, function(response, status) {
                    if (status == maps.DirectionsStatus.OK) {                       //jshint ignore:line
                        directionsDisplay.setDirections(response);
                    }
                });
                return;
            };

            $scope.calcRouteNEW = function (routePoints, waypoints) {
                directionsDisplay.setMap($scope.map.control.getGMap());
                var theRequest = {};
                theRequest.routePoints = routePoints;
                theRequest.waypoints = waypoints;

                collegeFactory.getDirections(theRequest).then(function (data) {
                    if (data){
                        // Process the route returned from Google Web Service
                        //ToDo: Replace this with a call to a service that will call GoogleMaps WebService
                        //ToDo: Get result of webservice and correct it so it matches JS result
                        if (data.status == maps.DirectionsStatus.OK) {                       //jshint ignore:line
                            directionsDisplay.setDirections(data);
                        }
                    }
                }, function(error) {
                    // promise rejected, could be because server returned 404, 500 error...
                    collegeFactory.msgError(error);
                });

                return;
            };
          $scope.getCollegesOnRoute = function (routePoints, waypoints) {
            directionsDisplay.setMap($scope.map.control.getGMap());
            var theRequest = {};
            theRequest.routePoints = routePoints;
            theRequest.waypoints = waypoints;

            collegeFactory.getCollegesOnRoute(theRequest).then(function (data) {
              if (data){
                // Process the route returned from Google Web Service
                //ToDo: Replace this with a call to a service that will call GoogleMaps WebService
                //ToDo: Get result of webservice and correct it so it matches JS result
                if (data.status == maps.DirectionsStatus.OK) {                       //jshint ignore:line
                  directionsDisplay.setDirections(data);
                }
              }
            }, function(error) {
              // promise rejected, could be because server returned 404, 500 error...
              collegeFactory.msgError(error);
            });

            return;
          };

        });
    }]);
