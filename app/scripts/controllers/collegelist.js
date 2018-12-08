'use strict';

/**
 * @ngdoc function
 * @name collegeApp.controller:CollegelistCtrl
 * @description
 * # CollegelistCtrl
 * Controller of the collegeApp
 */
angular.module('collegeApp')
    .controller('CollegelistCtrl', ['$scope','collegeFactory', 'ngDialog', function ($scope, collegeFactory, ngDialog) {

    $scope.getColleges = function (){
      var url = 'college.php';
      var action = 'getColleges';
      collegeFactory.getDataUsingPost(url, action).then(function (data) {
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


    /**
     * Set proper size and fade in after load.
     */
    function imageLoaded()
    {
        var image = $(this);

        var config =
        {
            maxWidth: 100,
            maxHeight: 75
        };

        // Remove tiny images
        if(image.width() < 2 || image.height() < 2)
        {
            image.remove();
            return;
        }

        // Find scale
        var scale = Math.min(config.maxWidth/image.width(), config.maxHeight/image.height());

        // Set new width and height
        image.attr({
            width: Math.ceil(scale*image.width()),
            height: Math.ceil(scale*image.height())
        });

        // Fade in
        image
            .css({display: 'inline-block'})
            .animate({opacity: 1});
    }

    function getImagesFromUrlDone(data)
    {
        $('#output')
            .empty();

        if(data && data.images){
            for(var n in data.images)
            {
                /*jshint unused:false */
                var image = $('<img>')
                    .prop(data.images[n])
                    .css({opacity: 0, display: 'none'})
                    .appendTo('#output')
                    .load(imageLoaded);
            }
        }
    }


    $scope.getPics = function(college){
//      var passedData = {url:college.url};
//      var passedData = {url:'http://www.trincoll.edu/'};
        var passedData = '';
      if (college.url){
          passedData = {url: 'http://' + college.url.toLowerCase() + '/'};
      }
      collegeFactory.postGetPics(passedData).then(function (data) {
        if (data){
            $scope.pics = data;
            ngDialog.open({
              template: 'views/popupTmpl.html',
              className: 'ngdialog-theme-default',
              scope: $scope,
              controller: ['$scope', function($scope) {
                // controller logic
//                $scope.processPics = function(){
//                  getImagesFromUrlDone($scope.pics)
//                }
//                $scope.processPics();
                $scope.appendText = function(){
//                  if($('#output').length == 0) {
                  if($('#output').length === 0) {
                    //it doesn't exist
                  }
                };
                $scope.appendText();
              }]
            });
            //getImagesFromUrlDone(data)
//          $('<span>Hello World!</span>').appendTo('#ngdialog2-aria-describedby');
          setTimeout(function(){
//            $('<span>Hello World!</span>').appendTo('#output');
            getImagesFromUrlDone($scope.pics);
          }, 2000);
        }
      }, function(error) {
        // promise rejected, could be because server returned 404, 500 error...
        collegeFactory.msgError('Error Getting Pictures:' + error);
      });
    };

    $scope.addToFavorites = function (college) {
      var index = $scope.colleges.indexOf(college);
      $scope.colleges[index].favorite = true;
    };
    $scope.trashIt = function (college) {
      var index = $scope.colleges.indexOf(college);
      $scope.colleges.splice(index,1);
    };
//    $scope.clickToOpen = function () {
//      ngDialog.open({ template: 'views/popupTmpl.html', className: 'ngdialog-theme-default' });
//    };





  }]);
