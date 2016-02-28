'use strict';

describe('login', function() {
	var scope

	beforeEach(angular.mock.module('app'))
	beforeEach(angular.mock.module('login'))

	beforeEach(angular.mock.inject(function($rootScope, $controller, $injector, $http) {
		scope = $rootScope.$new();

		$controller('loginCtrl', {$scope: scope});
	}))

	describe('loginInit', function() {

		it('should have correct init values', function() {
			expect(scope.errorMessage).toBe('')
			expect(scope.showError).toBe(false)
	    });

	})

	describe('error message', function() {

		it('should remove error message display', function() {
			
			scope.showError = true
			scope.removeMessage()
			expect(scope.showError).toBe(false)

		}) 

	})

})