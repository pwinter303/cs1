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
                loc2: {zipCode: "06085", minDistanceAway: 5, maxDistanceAway:50},
                sports: {sportsRequired: "row1women", enabled: false}
            }
        });


    $scope.saveSports = function(){
      $scope.myForm.sports.enabled = true;
    }
    $scope.resetSports = function(){
      $scope.myForm.sports.enabled = false;
    }
    $scope.resetIt = function(formData, func){
      if (func == 'home'){$scope.myForm.home.enabled = false;}
      if (func == 'loc2'){$scope.myForm.loc2.enabled = false;}
      if (func == 'sports'){$scope.myForm.sports.enabled = false;}
      if (func == 'testScore'){$scope.myForm.testScore.enabled = false;}
      if (func == 'schoolSetting'){$scope.myForm.schoolSetting.enabled = false;}
      if (func == 'schoolCost'){$scope.myForm.schoolCost.enabled = false;}
      if (func == 'schoolSize'){$scope.myForm.schoolSize.enabled = false;}
      //TODO: Call Factory Service And Save via PHP
    }
    $scope.saveIt = function(formData, func){
        if (func == 'home'){$scope.myForm.home.enabled = true;}
        if (func == 'loc2'){$scope.myForm.loc2.enabled = true;}
        if (func == 'sports'){$scope.myForm.sports.enabled = true;}
        if (func == 'testScore'){$scope.myForm.testScore.enabled = true;}
        if (func == 'schoolSetting'){$scope.myForm.schoolSetting.enabled = true;}
        if (func == 'schoolCost'){$scope.myForm.schoolCost.enabled = true;}
        if (func == 'schoolSize'){$scope.myForm.schoolSize.enabled = true;}
        //TODO: Call Factory Service And Save via PHP
    }

  }]);
