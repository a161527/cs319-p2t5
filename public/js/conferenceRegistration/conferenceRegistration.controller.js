(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationCtrl', function($scope, $state) {

			$scope.dependents = {'1': {firstname: 'Uncle', lastname: 'Jimmy Joe'}, 
			'2':{firstname: 'Billy', lastname: 'from the Jungles of Vancouver'},
			'3':{firstname: 'Kevin', lastname: ''}
			}

			$scope.selectDependents = {}

			//Need extra object to bypass validation for flights
			$scope.transportDependents = {}

			//Dependents all share same flight info, replace object
			$scope.flightInfo = {}

			$scope.noSelection = false

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

			$scope.removeMessage = function() {
				$scope.noSelection = false
			}

			$scope.cancel = function() {
				$state.go('dashboard.conferences')
			}

			$scope.getFlightsSet = function(sameFlightInfo) {
				return sameFlightInfo? $scope.emptyFlightInfo : $scope.dependents
			}

			$scope.getFlightIdentifier = function(sameFlightInfo) {
				return sameFlightInfo? 'airport{{idx}}' : 'airport'
			}

			$scope.back = function(currentState) {
				switch(currentState) {
					case 2:
						$state.go('dashboard.conferences.registration.1')
						break
					case 3:
						$state.go('dashboard.conferences.registration.2')
				}
			}

			$scope.nextStep = function(currentState, flightsForm, flightsFormAlt, sameFlightInfo) {

				switch(currentState) {
					case 1:
						if ($scope.checkOneSelected($scope.dependents)) {
							$scope.selectedDependents = addSelectedDependents($scope.dependents, 'register')
							$state.go('dashboard.conferences.registration.2')
						}
						break

					case 2:
						if ($scope.checkNeedsTransportation($scope.selectedDependents) < 1) {
							alert('finished')
						} else {
							$scope.transportDependents = addSelectedDependents($scope.selectedDependents, 'transportation') 
							$state.go('dashboard.conferences.registration.3')
						}
						break

					case 3:
						var currentForm = sameFlightInfo? flightsFormAlt : flightsForm

						if (currentForm.$valid) {
							alert(true)
						} else {
							setFormDirty(currentForm)
						}

				}

			}

			$scope.selectAll = function(field, value, list) {
				angular.forEach(list, function(dependent) { 
					dependent[field] = value
				})
			}

			$scope.selectAllTransportation = function(value, list) {
				angular.forEach(list, function(dependent) {
					if (dependent.accomodations) {
						dependent.transportation = value
					}
				})				
			}	

			//return number of users needing transportation
			$scope.checkNeedsTransportation = function(list) {
				var i = 0
				angular.forEach(list, function(dependent) {
					if (dependent.hasOwnProperty('transportation') && dependent['transportation'] === true) {
						i++
					}
				})
				return i	
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

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

		})

})()