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
              schoolSize: {options: 1},
              schoolCost: {min: 0, max: 50000},
              schoolSetting: {rural: 0, city: 50000},
              testScore: {SAT: 0, ACT: 500, matching:"worst"},
              home: {zipCode: "02332", minDistanceAway: 50, maxDistanceAway:500},
              loc2: {zipCode: "06085", minDistanceAway: 5, maxDistanceAway:50},
              sports: {sportsRequired: "row1women", enabled: false}
          }
      });

      $scope.resetIt = function(formData, func){
        switch(func){
          case 'home': {$scope.myForm.home.enabled = false; break;}
          case 'loc2': {$scope.myForm.loc2.enabled = false; break;}
          case 'sports': {$scope.myForm.sports.enabled = false; break;}
          case 'testScore': {$scope.myForm.testScore.enabled = false; break;}
          case 'schoolSetting': {$scope.myForm.schoolSetting.enabled = false; break;}
          case 'schoolCost': {$scope.myForm.schoolCost.enabled = false; break;}
          case 'schoolSize': {$scope.myForm.schoolSize.enabled = false; break;}
        }
        collegeFactory.saveCriteria(formData).then(function (data) {
          if (data){
            collegeFactory.msgSuccess('Updated');
            // todo: update # colleges
          }
        }, function(error) {
          // promise rejected, could be because server returned 404, 500 error...
          collegeFactory.msgError('Error Saving:' + error);
        });
      };

      $scope.saveIt = function(formData, func){
        formData.func = func;
        switch(func){
          case 'home': {$scope.myForm.home.enabled = true; break;}
          case 'loc2': {$scope.myForm.loc2.enabled = true; break;}
          case 'sports': {$scope.myForm.sports.enabled = true; break;}
          case 'testScore': {$scope.myForm.testScore.enabled = true; break;}
          case 'schoolSetting': {$scope.myForm.schoolSetting.enabled = true; break;}
          case 'schoolCost': {$scope.myForm.schoolCost.enabled = true; break;}
          case 'schoolSize': {$scope.myForm.schoolSize.enabled = true; break;}
        }

        collegeFactory.saveCriteria(formData).then(function (data) {
            if (data){
              collegeFactory.msgSuccess('Updated');
              // todo: update # colleges
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
          collegeFactory.msgError('Error Saving:' + error);
          });
      };

  }]);
