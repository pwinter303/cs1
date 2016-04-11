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



    $scope.getAndDisplayInfo = function(){
        $scope.heading = "Size of School Filter";
        $scope.description = "Size of the school is typically one of the critical criteria for selecting a college.";
        $scope.values = "Select the size of the school";
    }

  }]);
