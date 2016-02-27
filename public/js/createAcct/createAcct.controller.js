(function() {
	'use strict'

	angular.module('createAcct')
		.controller('createAcctCtrl', function($scope, $state, accountCredentials) {

			$scope.dependents = accountCredentials.getDependents() || {'1':{}} 
			$scope.createAcct = accountCredentials.getAccountInfo() || {}
			$scope.contact = accountCredentials.getContact() || {}
			$scope.transfer = accountCredentials.getTransfer() || false

			$scope.createAccount = function(form) {
				if (form.$valid) {
					alert('You did it!')
					accountCredentials.resetAll()
					$state.go('login')
				} else {
					angular.forEach(form.$error.required, function(field) {
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
				var dependentIndex = 1
				while($scope.dependents.hasOwnProperty(dependentIndex)) {
					dependentIndex += 1
				}
				$scope.dependents[dependentIndex] = {}
			}

			$scope.deleteDependent = function(index) {
				if (index != 1) {
					if ($scope.dependents.hasOwnProperty(index)) {
						delete $scope.dependents[index]
					}
				}
			}

			$scope.showRemoveButton = function(index) {
				return index > 1
			}

			$scope.cancel = function() {
				accountCredentials.resetAll()
				$state.go('login')
			}

			$scope.back = function(state, set, model) {
				accountCredentials[set]($scope[model])
				accountCredentials.setTransfer($scope.transfer)
				var state = 'createAccount.' + state
				$state.go(state)
			}

			$scope.nextStep = function(form, state, set, model) {
				if (form.$valid || $scope.transfer === true) {

					accountCredentials.setTransfer($scope.transfer)

					accountCredentials[set]($scope[model])
					var state = 'createAccount.' + state
					$state.go(state)
				} else {
					angular.forEach(form.$error.required, function(field) {
						field.$setDirty()
					})
				}
			}

		})

})()