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

    $scope.getCollegeCount = function (){
      collegeFactory.getCollegeCount().then(function (data) {
        if (data){
          //$scope.collegeCountOld = $scope.collegeCount;
          $scope.collegeCount = data;

        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getCollegeCount();

    $scope.getCriteria = function (){
      collegeFactory.getCriteria().then(function (data) {
        if (data){
          $scope.myForm = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getCriteria();

    $scope.getLocale = function (){
      collegeFactory.getLocale().then(function (data) {
        if (data){
          $scope.locales = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getLocale();

    $scope.getTestScoreRelation = function (){
      collegeFactory.getTestScoreRelation().then(function (data) {
        if (data){
          $scope.TestScoreRelations = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getTestScoreRelation();

    $scope.getSchoolSizes = function (){
      collegeFactory.getSchoolSizes().then(function (data) {
        if (data){
          $scope.sizes = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getSchoolSizes();
    $scope.getSchoolSizes = function (){
      collegeFactory.getSchoolSizes().then(function (data) {
        if (data){
          $scope.sizes = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getSchoolSizes();

    $scope.getSports = function (){
      collegeFactory.getSports().then(function (data) {
        if (data){
          $scope.sports = data;
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError(error);
      });
    };
    $scope.getSports();

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
