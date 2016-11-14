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

  $scope.tripListShow = true;
  $scope.tripPlanningShow = false;
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
    $scope.activeTripID = formData.tripID;
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
          $scope.startEndAddresses = data[0];
          $scope.tripWaypoints = data[1];
          // Spin through colleges and hide them if they're already part of the trip
          for(var iData=0; iData< $scope.colleges.length; iData++){
            $scope.colleges[iData].showMe = true;
            for(var i=0; i < $scope.tripWaypoints.length;i++){
                if($scope.tripWaypoints[i].schoolID === $scope.colleges[iData].schoolID){
                  $scope.colleges[iData].showMe = false;
                }
              }
          }
          $scope.tripListShow = false;
          $scope.tripPlanningShow = true;
        }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Getting Trip Details:' + error);
    });
  };
  $scope.deleteCollegeFromTrip = function(formData){
    var url = "college.php";
    formData.action = "deleteCollegeFromTrip";
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        collegeFactory.msgSuccess('Removed');
        // Remove college from waypoint array
        // Add college to college list array
        // Spin through colleges and hide them if they're already part of the trip
        for(var i =0; i < $scope.tripWaypoints.length; i++){
          if($scope.tripWaypoints[i].tripPtID === formData.tripPtID){
            $scope.tripWaypoints.splice(i, 1);
          }
        }
        for(var iData=0; iData< $scope.colleges.length; iData++){
            if($scope.colleges[iData].schoolID === formData.schoolID){
              $scope.colleges[iData].showMe = true;
            }
        }
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Updating Trip Details:' + error);
    });
  };

  $scope.addCollegeToTrip = function(formData){
    var url = "college.php";
    formData.action = "addCollegeToTrip";
    formData.tripID = $scope.activeTripID;
    collegeFactory.saveData(url, formData).then(function (data) {
      if (data){
        //FIXME: clean up formData which contains the college being added
        for(var iData=0; iData< $scope.colleges.length; iData++){
          if($scope.colleges[iData].schoolID === formData.schoolID){
            $scope.colleges[iData].showMe = false;
          }
        }
        // also should the push be using data from the college array?
        formData.tripPtID = data.tripPtID
        $scope.tripWaypoints.push(formData);
      }
    }, function(error) {
      // promise rejected, could be because server returned 404, 500 error...
      collegeFactory.msgError('Error Updating Trip Details:' + error);
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
