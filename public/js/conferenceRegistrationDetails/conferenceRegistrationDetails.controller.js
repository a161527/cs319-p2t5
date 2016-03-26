(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationDetailsCtrl', function($scope, $state, $stateParams, regDetails, inventory) {
			
			$scope.registered = []
			$scope.showAddInv = inventory.data.inventory? inventory.data.inventory.length > 0 : false

			angular.forEach(regDetails, function(registered) {
				$scope.registered.push(registered.data)
			})

			$scope.requestInventory = function() {
				$state.go('dashboard.conferences.inventoryRequest', {cid: $stateParams.cid})
			}

		})

})()