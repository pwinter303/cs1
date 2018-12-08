'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:SearchcollegeCtrl
 * @description
 * # SearchcollegeCtrl
 * Controller of the collegeApp
 */
angular.module('collegeApp')
  .controller('SearchcollegeCtrl', ['$scope','collegeFactory', function ($scope, collegeFactory) {

    $scope.searchIt = function(searchString){
      var passedData = {};
      passedData.searchString = searchString;
      collegeFactory.searchColleges(passedData).then(function (data) {
        if (data){
          $scope.colleges = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError('Error Saving:' + error);
      });
    };

    $scope.evaluateCollege = function(schoolID){
      var passedData = {};
      passedData.schoolID = schoolID;
      collegeFactory.evaluateCollege(passedData).then(function (data) {
        if (data){
          $scope.evaluation = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError('Error Saving:' + error);
      });
    };

  }]);
