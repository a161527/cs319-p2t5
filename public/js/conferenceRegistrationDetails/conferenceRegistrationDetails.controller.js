(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationDetailsCtrl', function($scope, $state, $stateParams, regDetails) {
			
			$scope.registered = []

			angular.forEach(regDetails, function(registered) {
				$scope.registered.push(registered.data)
			})

			$scope.requestInventory = function() {
				$state.go('dashboard.conferences.inventoryRequest')
			}

		})

})()