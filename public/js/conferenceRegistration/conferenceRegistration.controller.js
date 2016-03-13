(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationCtrl', function($scope) {

			$scope.dependents = {'1': {firstname: 'Uncle', lastname: 'Jimmy Joe'}, '2':{firstname: 'Uncle', lastname: 'Jimmy Joe'}}
			$scope.noSelection = false

			$scope.itemArray = [
				{id: 1, name: 'first'},
				{id: 2, name: 'second'},
				{id: 3, name: 'third'},
				{id: 4, name: 'fourth'},
				{id: 5, name: 'fifth'},
			]
			$scope.selected = { value: null }

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
		})

})()