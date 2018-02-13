'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the collegeApp
 */
angular.module('collegeApp')
  .controller('MainCtrl', function ($scope, $auth, $location,$rootScope, $window) {
    this.awesomeThings = [
      'HTML5 Boilerplate',
      'AngularJS',
      'Karma'
    ];
        $scope.authenticate = function(provider) {
            $auth.authenticate(provider)
                .then(function(response) {
                    toastr.success('You have successfully signed in with ' + provider + '!');
//                    $window.localStorage.currentUser = JSON.stringify(response.data.user);
//                    $rootScope.currentUser = JSON.parse($window.localStorage.currentUser);
                    console.debug("success", response);
//                    $location.path('/criteria');
                })
                .catch(function(error) {
                    if (error.message) {
                        // Satellizer promise reject error.
                        toastr.error(error.message);
                    } else if (error.data) {
                        // HTTP response error from server
                        toastr.error(error.data.message, error.status);
                    } else {
                        toastr.error(error);
                    }
                });
        };
  });
