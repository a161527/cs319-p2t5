(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationCtrl', function($scope, $state) {

			$scope.dependents = {'1': {firstname: 'Uncle', lastname: 'Jimmy Joe'}, 
			'2':{firstname: 'Billy', lastname: 'from the Jungles of Vancouver'},
			'3':{firstname: 'Kevin', lastname: ''}
			}

			//Dependents all share same flight info, replace object
			$scope.flightInfo = {}

			$scope.registration = {}
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

			$scope.nextStep = function(currentState) {

				switch(currentState) {
					case 1:
						if ($scope.checkOneSelected($scope.dependents)) {
							$state.go('dashboard.conferences.registration.2')
						}
						break

					case 2: 
						if (!$scope.registration.accomodations) {
							alert('finished')
						} else {
							$state.go('dashboard.conferences.registration.3')
						}
						
				}

			}
		})

})()