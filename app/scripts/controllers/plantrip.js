'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:PlantripCtrl
 * @description
 * # PlantripCtrl
 * Controller of the collegeApp
 */
angular.module('collegeApp')
  .controller('PlantripCtrl', ['$scope','collegeFactory', function ($scope, collegeFactory) {

  //$scope.trips = [{id:3,name:"WooHoo"},{id:4,name:"WooHoo2"}];

  $scope.addTripShow = false;
  $scope.roundTrip = false;
  $scope.trip = {startingPoint:'',endingPoint:''};

  $scope.$watch('trip.startingPoint', function () {
    if ($scope.roundTrip) {
      $scope.trip.endingPoint = $scope.trip.startingPoint;
    }
  })

  $scope.$watch('roundTrip', function (checkBoxValue) {
    if (checkBoxValue) {
      $scope.trip.endingPoint = $scope.trip.startingPoint;
    } else {
      $scope.trip.endingPoint = '';
    }
  })

  $scope.getTrips = function (){
    var url = "college.php";
    var action = "getTrips";
    collegeFactory.getData(url, action).then(function (data) {
      if (data){
        $scope.trips = data;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError(error);
    });
  };
  $scope.getTrips();

  $scope.addTrip = function(formData){
    var url = "college.php";
    formData.action = "addTrip";
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        collegeFactory.msgSuccess('Added');
        $scope.getTrips();
        $scope.addTripShow = false;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Saving:' + error);
    });
  };

  $scope.deleteTrip = function(formData){
    var url = "college.php";
    formData.action = "deleteTrip";
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        collegeFactory.msgSuccess('Deleted');
        $scope.getTrips();
        $scope.addTripShow = false;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Saving:' + error);
    });
  };

  $scope.getTripDetails = function(formData){
    var url = "college.php";
    formData.action = "getTripDetails";
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
//        collegeFactory.msgSuccess('Deleted');
          $scope.startEndAddresses = data[0];
          $scope.tripWaypoints = data[1];
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Getting Trip Details:' + error);
    });
  };

  $scope.getColleges = function (){
    var url = "college.php";
    var action = "getColleges";
    collegeFactory.getData(url, action).then(function (data) {
      if (data){
        $scope.colleges = data;
//            $scope.rowCollection = data;
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError(error);
    });
  };
  $scope.getColleges();


  }]);
