(function() {
	'use strict'

	angular.module('createAcct')
		.controller('createAcctCtrl', function($scope) {

			var dependentIndex = 1

			//initialize form to have one dependent
			$scope.dependents = {'1':{}}
			$scope.createAcct = {}

			$scope.createAccount = function(createAcctForm, dependentsForm) {
				var dependentsHandled = dependentsForm.$valid || $scope.transfer
				if (createAcctForm.$valid && dependentsHandled) {
					alert('You did it!')
				} else {
					angular.forEach(createAcctForm.$error.required, function(field) {
						field.$setDirty()
					})

					angular.forEach(dependentsForm.$error.required, function(field) {
						field.$setDirty()
					})
				}
			}

			$scope.checkMatch = function(validation, field, confirmationField) {
				if (field != confirmationField) {
					validation.$setValidity('match', false)
				} else {
					validation.$setValidity('match', true)
				}
			}

			$scope.addPerson = function() {
				dependentIndex += 1
				$scope.dependents[dependentIndex] = {}
			}

			$scope.deleteDependent = function(index) {
				if (index != 1) {
					delete $scope.dependents[index]
					dependentIndex -= 1
				}
			}

			$scope.showRemoveButton = function(index) {
				return index > 1
			}

		})

})()