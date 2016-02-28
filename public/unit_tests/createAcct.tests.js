'use strict';

describe('create account', function() {
	var scope

	beforeEach(angular.mock.module('app'))
	beforeEach(angular.mock.module('createAcct'))

	beforeEach(angular.mock.inject(function($rootScope, $controller, $injector, $compile) {
		scope = $rootScope.$new();

		$controller('createAcctCtrl', {$scope: scope});
	}))

	var defaultDependents = {'1':{}}

	describe('create account init', function() {

		it('should have correct init values', function() {
			expect(scope.createAcct).toEqual({})
			expect(scope.dependents).toEqual(defaultDependents)
	    });

	})

	describe('handling dependents', function() {

		it('should add to dependents object when addPerson is called', function() {
			scope.addPerson()
			expect(scope.dependents.hasOwnProperty('2')).toBe(true)
			expect(scope.dependents['2']).toEqual({})
		})

		it('should remove object from dependent when delete called', function() {
			scope.deleteDependent('2')
			expect(scope.dependents.hasOwnProperty('2')).toBe(false)
			expect(scope.dependents).toEqual(defaultDependents)
		})

		it('should not remove the initial dependents object', function() {
			scope.deleteDependent('1')
			expect(scope.dependents).toEqual(defaultDependents)
		})

		it('should not remove the initial dependents object on bad parameters', function() {
			scope.deleteDependent(1)
			scope.deleteDependent()
			scope.deleteDependent(null)
			scope.deleteDependent(undefined)

			expect(scope.dependents).toEqual(defaultDependents)
		})

		it('should remove the correct object', function() {
			scope.addPerson()
			scope.addPerson()

			expect(scope.dependents.hasOwnProperty('2')).toBe(true)
			expect(scope.dependents.hasOwnProperty('3')).toBe(true)

			scope.deleteDependent('2')

			expect(scope.dependents.hasOwnProperty('2')).toBe(false)
			expect(scope.dependents.hasOwnProperty('3')).toBe(true)

			scope.deleteDependent('3')
			expect(scope.dependents).toEqual(defaultDependents)
		})

		it('should not do anything if an index does not exist', function() {
			scope.deleteDependent('3')
			expect(scope.dependents).toEqual(defaultDependents)
		})

		it('should add an object with the same index even if it has existed before', function() {
			scope.addPerson()
			scope.addPerson()

			expect(scope.dependents.hasOwnProperty('2')).toBe(true)
			expect(scope.dependents.hasOwnProperty('3')).toBe(true)

			scope.deleteDependent('2')

			expect(scope.dependents.hasOwnProperty('2')).toBe(false)
			expect(scope.dependents.hasOwnProperty('3')).toBe(true)

			scope.addPerson()

			expect(scope.dependents.hasOwnProperty('2')).toBe(true)
			expect(scope.dependents.hasOwnProperty('3')).toBe(true)

			scope.deleteDependent('2')
			scope.deleteDependent('3')
			expect(scope.dependents).toEqual(defaultDependents)
		})
	})

})