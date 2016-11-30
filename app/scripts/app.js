'use strict';

/**
 * @ngdoc overview
 * @name collegeApp
 * @description
 * # collegeApp
 *
 * Main module of the application.
 */
angular
  .module('collegeApp', [
    'ngRoute',
    'uiGmapgoogle-maps',
    'ui.bootstrap',
    'smart-table',
    'ngOdometer',
    'ngDialog'
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
      .when('/criteria', {
        templateUrl: 'views/criteria.html',
        controller: 'CriteriaCtrl',
        controllerAs: 'criteria'
      })
      .when('/collegelist', {
        templateUrl: 'views/collegelist.html',
        controller: 'CollegelistCtrl',
        controllerAs: 'collegelist'
      })
      .when('/plantrip', {
        templateUrl: 'views/plantrip.html',
        controller: 'PlantripCtrl',
        controllerAs: 'plantrip'
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