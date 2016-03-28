(function() {
	'use strict'

	angular.module('conferenceRegistration')
		.controller('conferenceRegistrationDetailsCtrl', function($scope, $state, $stateParams, dataFormat, regDetails, numOfDependents, confDetails, inventory) {
			
			$scope.registered = []

			angular.forEach(regDetails, function(registered) {
				$scope.registered.push(registered.data)
			})

			$scope.allDependentsRegistered = numOfDependents === $scope.registered.length

			$scope.usersApprovedAndInvExists = function(registered) {
				var numApproved = 0
				angular.forEach(registered, function(dep) {
					if (dep.approved) {
						numApproved += 1
					}
				})

				return inventory.data.inventory? inventory.data.inventory.length > 0 && numApproved > 0 : false 
			}

			$scope.showAddInv = $scope.usersApprovedAndInvExists($scope.registered)

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