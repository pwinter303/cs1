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

//    ////=============================================================================///
//    dataFactory.getLocale = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getLocale'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };    ////=============================================================================///
//    dataFactory.getTestScoreRelation = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getTestScoreRelation'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
//    ////=============================================================================///
//    dataFactory.getSchoolSizes = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getSchoolSizes'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
//    ////=============================================================================///
//    dataFactory.getYrsOfSchool = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getYrsOfSchool'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
//    ////=============================================================================///
//    dataFactory.getRunBy = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getRunBy'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
//    ////=============================================================================///
//    dataFactory.getCriteriaRefData = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getCriteriaRefData'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
//    ////=============================================================================///
//    dataFactory.getCriteria = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getCriteriaForWeb'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
//    ////=============================================================================///
//    dataFactory.getColleges = function() {
//        var url = 'college.php';
//        var passedData = {action: 'getColleges'};
//        // Start Standard Code... GET
//        var promise = $http.get(url , {params: passedData });
//        return promise.then(function(result) {
//            if (typeof result.data === 'object') {
//                return result.data;
//            } else {
//                // call was successful but response was invalid (result was not an object)
//                return $q.reject(result.data);
//            }
//        }, function(result) {
//            // something went wrong.... error on the call..
//            return $q.reject(result.data);
//        });
//    };
    ////=============================================================================///
    dataFactory.getData = function(url, action) {
        var url = url;
        var passedData = {action: action};
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
//    dataFactory.getCollegeCount = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getCollegeCount'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
////    ////=============================================================================///
//    dataFactory.getSports = function() {
//      var url = 'college.php';
//      var passedData = {action: 'getSports'};
//      // Start Standard Code... GET
//      var promise = $http.get(url , {params: passedData });
//      return promise.then(function(result) {
//        if (typeof result.data === 'object') {
//          return result.data;
//        } else {
//          // call was successful but response was invalid (result was not an object)
//          return $q.reject(result.data);
//        }
//      }, function(result) {
//        // something went wrong.... error on the call..
//        return $q.reject(result.data);
//      });
//    };
    ////=============================================================================///
    dataFactory.getDirections = function(passedData) {
        var url = 'college.php';
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
    dataFactory.getCollegesOnRoute = function(passedData) {
      var url = 'college.php';
      passedData.action = 'getCollegesOnRoute';
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
//    dataFactory.getDirectionsOLD = function(passedData) {
//        var url = 'college.php';
//        passedData.action = 'getDirections';
//        // Start Standard Code... GET
//        var promise = $http.get(url , {params: passedData });
//        return promise.then(function(result) {
//            if (typeof result.data === 'object') {
//                return result.data;
//            } else {
//                // call was successful but response was invalid (result was not an object)
//                return $q.reject(result.data);
//            }
//        }, function(result) {
//            // something went wrong.... error on the call..
//            return $q.reject(result.data);
//        });
//    };
    ////=============================================================================///
    dataFactory.saveCriteria = function(passedData) {
      var url = 'college.php';
      passedData.action = 'saveCriteria';
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
    dataFactory.postGetPics = function(passedData) {
      var url = 'scanForImages.php';
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