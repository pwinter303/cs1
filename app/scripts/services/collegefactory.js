'use strict';

/**
 * @ngdoc service
 * @name collegeApp.collegeFactory
 * @description
 * # collegeFactory
 * Factory in the collegeApp.
 */
angular.module('collegeApp')
  .factory('collegeFactory', ['$http', '$q', function($http, $q) {

        var dataFactory = {};

        ////=============================================================================///
        dataFactory.getColleges = function() {
            var url = 'googlemap.php';
            var passedData = {action: 'getColleges'};
            // Start Standard Code... GET
            var promise = $http.get(url , {params: passedData });
            return promise.then(function(result) {
                if (typeof result.data === 'object') {
                    return result.data;
                } else {
                    // call was successful but response was invalid (result was not an object)
                    return $q.reject(result.data);
                }
            }, function(result) {
                // something went wrong.... error on the call..
                return $q.reject(result.data);
            });
        };
        ////=============================================================================///
        dataFactory.getDirections = function(passedData) {
            var url = 'googlemap.php';
            passedData.action = 'getDirections';
            // Start Standard Code... GET
            var promise = $http.post(url , passedData);
            return promise.then(function(result) {
                if (typeof result.data === 'object') {
                    return result.data;
                } else {
                    // call was successful but response was invalid (result was not an object)
                    return $q.reject(result.data);
                }
            }, function(result) {
                // something went wrong.... error on the call..
                return $q.reject(result.data);
            });
        };
        ////=============================================================================///
        dataFactory.getDirectionsOLD = function(passedData) {
            var url = 'googlemap.php';
            passedData.action = 'getDirections';
            // Start Standard Code... GET
            var promise = $http.get(url , {params: passedData });
            return promise.then(function(result) {
                if (typeof result.data === 'object') {
                    return result.data;
                } else {
                    // call was successful but response was invalid (result was not an object)
                    return $q.reject(result.data);
                }
            }, function(result) {
                // something went wrong.... error on the call..
                return $q.reject(result.data);
            });
        };
       ////=============================================================================///
        // prevent toastr is not defined error in grunt/jshint
        /*global toastr */
        dataFactory.msgSuccess = function(text) {
            toastr.options = {'timeOut': '2000'};
            toastr.success(text);
        };

        dataFactory.msgError = function(text) {
            toastr.options = {'timeOut': '5000'};
            toastr.error(text);
        };

        return dataFactory;

    }]);