(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationCtrl', function($scope, $state, $stateParams, ajax, dataFormat, $uibModal) {

			$scope.dependents = {'2': {firstname: 'Uncle', lastname: 'Jimmy Joe', id: '2'}, 
			'3':{firstname: 'Billy', lastname: 'from the Jungles of Vancouver', id: '3'},
			'4':{firstname: 'Kevin', lastname: '', id: '4'}
			}

			//a new dependents object created so modifications can be made without affecting original object
			$scope.selectDependents = {}

			//Additional dependents object to bypass validation for flights
			$scope.hasFlightDependents = {}

			//Final object to be passed in service call
			$scope.formattedData = null

			$scope.back = function(currentState) {
				switch(currentState) {

					case 2:
						$state.go('dashboard.conferences.registration.1')
						break

					case 3:
						$state.go('dashboard.conferences.registration.2')
						break

					case 4:

						//If no dependents have any flights
						if ($scope.checkHasFlights($scope.selectedDependents) < 1) {
							$state.go('dashboard.conferences.registration.2')
						} else {
							$state.go('dashboard.conferences.registration.3')
						}
						break
						
				}
			}

			//Determine which step to go to based on current step
			//Current state is the only required parameter
			//The rest of the parameters are required for step 3 to handle validation
			$scope.nextStep = function(currentState, flightsForm, flightsFormAlt, sameFlightInfo) {

				switch(currentState) {

					case 1:

						//Valid only if at least one dependent is selected
						if ($scope.checkOneSelected($scope.dependents)) {

							$scope.selectedDependents = addSelectedDependents($scope.dependents, 'register')
							$state.go('dashboard.conferences.registration.2')

						}
						break

					case 2:

						//if at least one dependent has flight, go to review
						if ($scope.checkHasFlights($scope.selectedDependents) < 1) {

							$scope.formattedData = $scope.formatData()
							$state.go('dashboard.conferences.registration.4')

						//otherwise go to fill out flights forms
						} else {

							$scope.hasFlightsDependents = addSelectedDependents($scope.selectedDependents, 'hasFlight')

							$scope.sameFlightInfo.value = false
							$scope.showSameFlightInfoCheckbox.value = $scope.checkHasFlights($scope.selectedDependents) > 1

							$state.go('dashboard.conferences.registration.3')

						}
						break

					case 3:

						//Determine which form to use for validation
						var currentForm = sameFlightInfo? flightsFormAlt : flightsForm

						if (currentForm.$valid) {

							$scope.formattedData = $scope.formatData()
							$state.go('dashboard.conferences.registration.4')

						} else {
							setFormDirty(currentForm)
						}
						break

				}

			}

			//return an object with data formatted for service call
			$scope.formatData = function() {

				var list = []

				angular.forEach($scope.selectedDependents, function(dependent) {

					var obj = {}

					obj['attendees'] = [parseInt(dependent.id)]

					//If dependent has any of these fields, set to the value of the field, otherwise set to false
					obj['hasFlight'] = dependent.hasOwnProperty('hasFlight')? dataFormat.trueFalseFormat(dependent['hasFlight']) : dataFormat.trueFalseFormat(false)
					obj['needsTransportation'] = dependent.hasOwnProperty('needsTransportation')? dataFormat.trueFalseFormat(dependent['needsTransportation']) : dataFormat.trueFalseFormat(false)
					obj['needsAccommodation'] = dependent.hasOwnProperty('needsAccommodation')? dataFormat.trueFalseFormat(dependent['needsAccommodation']) : dataFormat.trueFalseFormat(false)


					//add object if has flights
					if (dependent['hasFlight'] === true && $scope.hasFlightsDependents.hasOwnProperty("" + dependent.id)) {

						//initialize object to be able to use angular copy
						obj['flight'] = {}

						if ($scope.sameFlightInfo.value) {

							angular.copy($scope.flightInfo, obj['flight'])

						} else {

							angular.copy($scope.hasFlightsDependents["" + dependent.id].flights, obj['flight'])

						}

						obj['flight'].arrivalDate = dataFormat.dateFormat(obj['flight'].arrivalDate)
						obj['flight'].arrivalTime = dataFormat.timeFormat(obj['flight'].arrivalTime)
						obj['flight'].number = parseInt(obj['flight'].number)
					}
 					
 					list.push(obj)
				})

				return list
			}





			/*
			STEP 1 methods
			*/

			$scope.noSelection = false

			//If at least one dependent has 'register', then can move to the next step
			$scope.checkOneSelected = function(dependents) {
				for (var key in dependents) {
					if (dependents[key].hasOwnProperty('register')) {
						if (dependents[key]['register'] === true) {
							$scope.noSelection = false
							return true
						}
					}
				}
				$scope.noSelection = true
				return false
			}

			//Remove 'must have one dependents selected' message
			$scope.removeMessage = function() {
				$scope.noSelection = false
			}

			$scope.cancel = function() {
				$state.go('dashboard.conferences.list')
			}





			/*
			STEP 2 methods
			*/

			//Select all checkboxes
			$scope.selectAll = function(field, value, list) {
				angular.forEach(list, function(dependent) { 
					dependent[field] = value
				})
			}

			//Select all checkboxes specifically for flights
			//only select if the dependent has 'needsTransportation' field checked
			$scope.selectAllFlights = function(value, list) {
				angular.forEach(list, function(dependent) {
					if (dependent.needsTransportation) {
						dependent.hasFlight = value
					}
				})				
			}

			//return number of users having flights
			//For checking if step 3 filling out flights form is needed
			$scope.checkHasFlights = function(list) {
				var i = 0
				angular.forEach(list, function(dependent) {
					if (dependent.hasOwnProperty('hasFlight') && dependent['hasFlight'] === true) {
						i++
					}
				})
				return i	
			}




			/*
			STEP 3 methods
			*/

			//Dependents all share same flight info, replace object with this
			$scope.flightInfo = {}

			$scope.sameFlightInfo = {value: false}
			$scope.showSameFlightInfoCheckbox = {value: false}

			//Return appropriate flights object based on whether or not the dependents share the same flight info
			$scope.getFlightsSet = function(sameFlightInfo) {
				return sameFlightInfo.value? $scope.emptyFlightInfo : $scope.dependents
			}

			//Return appropriate flights identifier for validation purposes
			$scope.getFlightIdentifier = function(sameFlightInfo) {
				return sameFlightInfo.value? 'airport{{idx}}' : 'airport'
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}




			/*
			STEP 4 methods
			*/

			$scope.showSubmitError = false

			$scope.submit = function() {
				$scope.showSubmitError = false
				ajax.serviceCall('Submitting...', 'post', 'api/conferences/' + $stateParams.cid + '/register', $scope.formattedData).then(function(resData) {

					openModal()

				}, function(resData) {
					
					$scope.showSubmitError = true

				})
			}

			$scope.removeSubmitError = function() {
				$scope.showSubmitError = false
			}

			//Return a new object with the dependents based on a flag in the object
			var addSelectedDependents = function(dependents, field) {
				var selected = {}
				for (var i in dependents) {
					if (dependents[i].hasOwnProperty(field)) {
						if (dependents[i][field]) {
							selected[i] = dependents[i]
						}
					}
				}

				return selected
			}

			var openModal = function() {

				var modal = $uibModal.open({
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.modalConfirm.html',
					controller: function($scope, $uibModalInstance) {

						$scope.ok = function() {
							$uibModalInstance.close()
						}

					}
				})

				modal.result.then(function () {
					$state.go('dashboard.conferences')
				}, function () {
					$state.go('dashboard.conferences')
				})

			}

		})

})()