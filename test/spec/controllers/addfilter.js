'use strict';

describe('Controller: AddfilterCtrl', function () {

  // load the controller's module
  beforeEach(module('collegeApp'));

  var AddfilterCtrl,
    scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    AddfilterCtrl = $controller('AddfilterCtrl', {
      $scope: scope
      // place here mocked dependencies
    });
  }));

  it('should attach a list of awesomeThings to the scope', function () {
    expect(AddfilterCtrl.awesomeThings.length).toBe(3);
  });
});
