'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:AddfilterCtrl
 * @description
 * # AddfilterCtrl
 * Controller of the collegeApp
 */

angular.module('collegeApp')
    .controller('AddfilterCtrl', ['$scope','collegeFactory', function ($scope, collegeFactory) {

        //TODO: Replace this with a call to Factory Service to GET from PHP
        angular.extend($scope, {
            myForm: {
                schoolSize: {min: 0, max: 50000},
                schoolCost: {min: 0, max: 50000},
                schoolSetting: {rural: 0, city: 50000},
                testScore: {SAT: 0, ACT: 500, matching:"worst"},
                home: {zipCode: "02332", minDistanceAway: 50, maxDistanceAway:500},
                sports: {sportsRequired: "row1women", enabled: false}
            }
        });


    $scope.saveSports = function(){
      $scope.myForm.sports.enabled = true;
    }
    $scope.resetSports = function(){
      $scope.myForm.sports.enabled = false;
    }
    $scope.saveIt = function(formData, func){
        var x = formData;
        //TODO: Call Factory Service And Save via PHP
    }

  }]);
