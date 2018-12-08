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
      var url = 'college.php';
      passedData.action = 'searchForColleges';
      passedData.searchString = searchString;
      if (searchString.length < 3) {collegeFactory.msgError('Please enter at least 3 characters'); return;}

      collegeFactory.getDataUsingPost(url, passedData).then(function (data) {
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
      var url = 'college.php';
      passedData.action = 'evaluateSchoolVersusCriteria';
      passedData.schoolID = schoolID;
      collegeFactory.getDataUsingPost(url, passedData).then(function (data) {
        if (data){
          $scope.evaluation = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError('Error Saving:' + error);
      });
    };

  }]);
