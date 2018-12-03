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
    'ngDialog',
    'satellizer'
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
      .when('/searchCollege', {
        templateUrl: 'views/searchcollege.html',
        controller: 'SearchcollegeCtrl',
        controllerAs: 'searchCollege'
      })
      .otherwise({
        redirectTo: '/'
      });
  })
  .config(function(uiGmapGoogleMapApiProvider) {
        uiGmapGoogleMapApiProvider.configure({
            //    key: 'your api key',
            key: 'AIzaSyCab-1RSi3hLrQX5mO2aE7CIcmbWTOLFfU',
            v: '3.30', //defaults to latest 3.X anyhow.. Was 3.2
            libraries: 'weather,geometry,visualization'
        });

  })
.config(function($authProvider) {

    $authProvider.facebook({
        clientId: '207720739783018'
    });

    // Optional: For client-side use (Implicit Grant), set responseType to 'token' (default: 'code')
    $authProvider.facebook({
        clientId: '207720739783018',
        responseType: 'token'
    });

    $authProvider.google({
        clientId: '337878234548-ib04qiar6hhv0mjov54qljm7f0179j49.apps.googleusercontent.com'
    });

    $authProvider.github({
        clientId: 'GitHub Client ID'
    });

    $authProvider.linkedin({
        clientId: 'LinkedIn Client ID'
    });

    $authProvider.instagram({
        clientId: 'Instagram Client ID'
    });

    $authProvider.yahoo({
        clientId: 'Yahoo Client ID / Consumer Key'
    });

    $authProvider.live({
        clientId: 'Microsoft Client ID'
    });

    $authProvider.twitch({
        clientId: 'Twitch Client ID'
    });

    $authProvider.bitbucket({
        clientId: 'Bitbucket Client ID'
    });

    $authProvider.spotify({
        clientId: 'Spotify Client ID'
    });

    // No additional setup required for Twitter

    $authProvider.oauth2({
        name: 'foursquare',
        url: '/auth/foursquare',
        clientId: 'Foursquare Client ID',
        redirectUri: window.location.origin,
        authorizationEndpoint: 'https://foursquare.com/oauth2/authenticate'
    });
//

    $authProvider.google({
        url: 'cs1/app/auth/google.php',
        authorizationEndpoint: 'https://accounts.google.com/o/oauth2/auth',
//        redirectUri: window.location.origin + '/cs1/app/auth/spinner.html',
        redirectUri: window.location.origin,
        requiredUrlParams: ['scope'],
        optionalUrlParams: ['display'],
        scope: ['profile', 'email'],
        scopePrefix: 'openid',
        scopeDelimiter: ' ',
        display: 'popup',
        oauthType: '2.0',
        popupOptions: { width: 452, height: 633 }
    });



});