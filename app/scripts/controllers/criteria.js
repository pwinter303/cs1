'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:CriteriaCtrl
 * @description
 * # CriteriaCtrl
 * Controller of the collegeApp
 */

angular.module('collegeApp')
    .controller('CriteriaCtrl', ['$scope','collegeFactory', function ($scope, collegeFactory) {

    $scope.myCount = 12333;

    $scope.getCollegeCount = function (){
      var url = "college.php";
      var action = "getCollegeCount";
      collegeFactory.getData(url, action).then(function (data) {
        if (data){
          //$scope.collegeCountOld = $scope.collegeCount;
          $scope.collegeCount = data.count;

        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getCollegeCount();

    $scope.getCriteria = function (){
      var url = "college.php";
      var action = "getCriteriaForWeb";
      collegeFactory.getData(url, action).then(function (data) {
        if (data){
          $scope.myForm = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getCriteria();

    // get reference data
    $scope.getCriteriaRefData = function (){
      var url = "college.php";
      var action = "getCriteriaRefData";
      collegeFactory.getData(url, action).then(function (data) {
        if (data){
          $scope.locales = data.locales;
          $scope.sizes = data.sizes;
          $scope.yrsOfSchool = data.yrsOfSchool;
          $scope.TestScoreRelations = data.TestScoreRelations;
          $scope.sports = data.sports;
          $scope.divisions = data.divisions;
          $scope.runBy = data.runBy;
          $scope.states = data.states;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getCriteriaRefData();
//

    $scope.resetIt = function(formData, func){
      formData.func = func;
      switch(func){
        case 'home': {$scope.myForm.home.enabled = false; break;}
        case 'loc2': {$scope.myForm.loc2.enabled = false; break;}
        case 'sports': {$scope.myForm.sports.enabled = false; break;}
        case 'testScore': {$scope.myForm.testScore.enabled = false; break;}
        case 'schoolSetting': {$scope.myForm.schoolSetting.enabled = false; break;}
        case 'schoolCost': {$scope.myForm.schoolCost.enabled = false; break;}
        case 'schoolSize': {$scope.myForm.schoolSize.enabled = false; break;}
        case 'yrsOfSchool': {$scope.myForm.yrsOfSchool.enabled = false; break;}
        case 'runBy': {$scope.myForm.runBy.enabled = false; break;}
        case 'acptRate': {$scope.myForm.acptRate.enabled = false; break;}
        case 'states': {$scope.myForm.states.enabled = false; break;}
      }
      collegeFactory.saveCriteria(formData).then(function (data) {
        if (data){
          collegeFactory.msgSuccess('Updated');
          $scope.getCollegeCount();
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
        case 'yrsOfSchool': {$scope.myForm.yrsOfSchool.enabled = true; break;}
        case 'runBy': {$scope.myForm.runBy.enabled = true; break;}
        case 'acptRate': {$scope.myForm.acptRate.enabled = true; break;}
        case 'states': {$scope.myForm.states.enabled = true; break;}
      }

      collegeFactory.saveCriteria(formData).then(function (data) {
          if (data){
            collegeFactory.msgSuccess('Updated');
            $scope.getCollegeCount();
          }
        }, function(error) {
          // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError('Error Saving:' + error);
        });
    };

  }]);
