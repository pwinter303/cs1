'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:CollegelistCtrl
 * @description
 * # CollegelistCtrl
 * Controller of the collegeApp
 */
angular.module('collegeApp')
      .controller('CollegelistCtrl', ['$scope','collegeFactory', function ($scope, collegeFactory) {

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

  }]);
