'use strict';

/**
 * @ngdoc overview
 * @name collegeAppApp
 * @description
 * # collegeAppApp
 *
 * Main module of the application.
 */
angular
  .module('collegeAppApp', [
    'ngRoute',
        'uiGmapgoogle-maps',
        'ui.bootstrap'
  ])
  .config(function ($routeProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl',
        controllerAs: 'main'
      })
      .when('/about', {
        templateUrl: 'views/about.html',
        controller: 'AboutCtrl',
        controllerAs: 'about'
      })
      .when('/googlemap', {
        templateUrl: 'views/googlemap.html',
        controller: 'GooglemapCtrl',
        controllerAs: 'googleMap'
      })
      .otherwise({
        redirectTo: '/'
      });
  })
  .config(function(uiGmapGoogleMapApiProvider) {
        uiGmapGoogleMapApiProvider.configure({
            //    key: 'your api key',
            v: '3.20', //defaults to latest 3.X anyhow
            libraries: 'weather,geometry,visualization'
        });
  });

//  .config(['uiGmapgoogle-maps.providers', function (GoogleMapApi) {
//    GoogleMapApi.configure({
//        key: 'your Google Map api key',
//        v: '3.17',
//        libraries: ''
//    });
//}]);
