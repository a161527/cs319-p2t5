(function() {
	'use strict'

	angular.module('login')
		.controller('createAcctCtrl', function($scope) {

			var dependentIndex = 1
			$scope.dependentIndices = [dependentIndex]
			$scope.firstname = {}
			$scope.lastname = {}

			$scope.createAccount = function(createAcctForm) {
				if (createAcctForm.$valid) {

				} else {
					angular.forEach(createAcctForm.$error.required, function(field) {
						field.$setDirty()
					})
				}
			}

			$scope.resetAuthError = function(element) {
				if (element.$error.auth) {
					element.$setValidity('auth', true)
				}
			}

			$scope.addPerson = function() {
				dependentIndex += 1
				$scope.dependentIndices.push(dependentIndex)
			}

			$scope.deleteLastPerson = function() {
				if (dependentIndex != 1) {
					dependentIndex -= 1
					$scope.dependentIndices.pop()
				}
			}
		})

})()