'use strict';
/**
 * Created by paul-winter on 4/21/16.
 */
angular.module('collegeApp').controller('NavCtrl', function($scope, $location) {
  $scope.isActive = function(route) {
    return route === $location.path();
  };
});
