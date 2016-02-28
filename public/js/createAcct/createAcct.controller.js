(function() {
	'use strict'

	angular.module('createAcct')
		.controller('createAcctCtrl', function($scope, $state, accountCredentials) {

			$scope.dependents = accountCredentials.getDependents() || {'1':{}} 
			$scope.createAcct = accountCredentials.getAccountInfo() || {}
			$scope.contact = accountCredentials.getContact() || {}
			$scope.transfer = accountCredentials.getTransfer() || false

			$scope.createAccount = function() {
				alert('You did it!')
				accountCredentials.resetAll()
				$state.go('login')
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
				accountCredentials['setAccountInfo']($scope.createAcct)
				$state.go('login')
			}

			$scope.back = function(toState, set, model) {
				if (model === 'dependents') {
					accountCredentials.setTransfer($scope.transfer)
				}

				onNavigate(toState, set, model)
			}

			$scope.nextStep = function(form, toState, set, model) {
				accountCredentials.setTransfer($scope.transfer)
				if ($scope.transfer === true && model === 'dependents') {
					onNavigate(toState, set, model)
				}
				else if (form.$valid) {
					onNavigate(toState, set, model)
				} else {
					setFormDirty(form)
				}
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

			var onNavigate = function(toState, set, model) {
				accountCredentials[set]($scope[model])
				var state = 'createAccount.' + toState
				$state.go(state)
			}

		})

})()