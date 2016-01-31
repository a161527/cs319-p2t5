'use strict';

describe('login', function() {

	var scope, service

	beforeEach(angular.mock.module('login'))

	beforeEach(angular.mock.inject(function($rootScope, $controller, $injector) {
		scope = $rootScope.$new();

		service = $injector.get('loginService');

		$controller('loginCtrl', {$scope: scope});
	}))

	describe('loginInit', function() {

		it('should not exist', function() {
			expect(scope.test).toBe(undefined);
	    });

	})

})