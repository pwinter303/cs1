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

        $scope.tripsList = [{id:3,name:"WooHoo"},{id:4,name:"WooHoo2"}];


  }]);
