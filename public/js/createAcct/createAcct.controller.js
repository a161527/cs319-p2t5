(function() {
	'use strict'

	angular.module('createAcct')
		.controller('createAcctCtrl', function($scope, $state, accountCredentials, ajax) {

			$scope.dependents = accountCredentials.getDependents() || {'1':{}} 
			$scope.createAcct = accountCredentials.getAccountInfo() || {}
			$scope.contact = accountCredentials.getContact() || {}
			$scope.transfer = accountCredentials.getTransfer() || false
			$scope.invalidAge = false

			//Need this to show message in sync with ajax call for check email
			$scope.showAvailMsg = false
			$scope.emailAvailable = accountCredentials.getEmailAvailable() || false

			$scope.createAccount = function() {
				alert('You did it!')
				accountCredentials.resetAll()
				$state.go('login')
			}

			$scope.checkMatch = function(validation, field, confirmationField) {
				if (confirmationField && field !== confirmationField) {
					validation.$setValidity('match', false)
				} else {
					validation.$setValidity('match', true)
				}
			}

			$scope.checkEmailAvail = function(validation, email) {
				$scope.showAvailMsg = false
				if (validation.$valid) {

					ajax.serviceCall('Checking email...', 'post', 'api/checkemail', {'email': email}).then(function(resData) {
						$scope.showAvailMsg = true

						if (!resData.data.taken) {
							$scope.emailAvailable = true
						} else {
							$scope.emailAvailable = false
						}
						
					}, function(resData) {
						//something went wrong
					})
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
				// accountCredentials['setAccountInfo']($scope.createAcct)
				// $scope.triggerModal = true
				accountCredentials.resetAll()
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
				accountCredentials.setEmailAvailable($scope.emailAvailable)

				if (model === 'dependents') {
					
					checkDependentsForm(form, toState, set, model)

				}

				else if (form.$valid && $scope.emailAvailable) {
					onNavigate(toState, set, model)
				} 

				else {
					setFormDirty(form)
				}
			}

			$scope.removeAgeMessage = function() {
				$scope.invalidAge = false
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

			var checkDependentsForm = function(form, toState, set, model) {
				if ($scope.transfer === true) {

					onNavigate(toState, set, model)

				} else if (form.$valid) {

					var validDependentsAge = verifyDependentsAge($scope.dependents, moment())

					if (validDependentsAge) {
						onNavigate(toState, set, model)
					} else {
						$scope.invalidAge = true
					}

				} else {
					setFormDirty(form)
				}
			}

			var verifyDependentsAge = function(dependents, today) {
				var minAge = 16
				var todayM = moment(today)

				for (var key in dependents) {

					if (dependents.hasOwnProperty(key)) {

						var dateM = moment(dependents[key].birthdate)
						var age = parseInt(moment.duration(todayM.diff(dateM)).asYears())

						if (age >= minAge) {
							return true
						}

					}
				}
				return false
			}

		})

})()