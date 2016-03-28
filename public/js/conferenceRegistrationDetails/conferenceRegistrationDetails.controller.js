(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationDetailsCtrl', function($scope, $state, $stateParams, dataFormat, regDetails, numOfDependents, confDetails, inventory) {
			
			$scope.registered = []

			angular.forEach(regDetails, function(registered) {
				$scope.registered.push(registered.data)
			})

			$scope.allDependentsRegistered = numOfDependents === $scope.registered.length

			$scope.showAddInv = inventory.data.inventory? inventory.data.inventory.length > 0 : false

			//Format into object indexed by dependent id for cross referencing
			//This object currently contains info about rooms	
			$scope.conf = dataFormat.dependentsFormat(confDetails.data.registered, 'user')

			$scope.requestInventory = function() {
				$state.go('dashboard.conferences.inventoryRequest', {cid: $stateParams.cid})
			}

			$scope.register = function() {
				$state.go('dashboard.conferences.registration.1', {cid: $stateParams.cid})
			}

		})

})()