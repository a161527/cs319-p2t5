(function() {
	'use strict'

	angular.module('inventory')
		.controller('approveInventoryCtrl', function($scope, $stateParams, $state, unapprovedInventory, ajax, conferenceData) {

			$scope.inventory = unapprovedInventory.data.inventory
			$scope.state1 = true

			$scope.conferenceName = conferenceData.data.name
			
			angular.forEach($scope.inventory, function(inv) {
				inv.user['fullName'] = inv.user.firstName + ' ' + inv.user.lastName
			})

			$scope.approve = function(id) {
				var route = 'api/userinventory/' + id + '/approve'
				ajax.serviceCall('Approving...', 'get', route).then(function(resData) {

					$state.reload()

				}, function(resData) {
					console.log(resData)
				})
			}

			$scope.viewApproved = function() {
				$state.go('dashboard.conferences.manage.approve-inventory.2')
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

		.controller('viewApprovedInventoryCtrl', function($scope, $stateParams, $state, approvedInventory, conferenceData) {

			$scope.state1 = false

			$scope.inventory = approvedInventory.data.inventory

			$scope.conferenceName = conferenceData.data.name

			angular.forEach($scope.inventory, function(inv) {
				inv.user['fullName'] = inv.user.firstName + ' ' + inv.user.lastName
			})

			$scope.back = function() {
				$state.go('dashboard.conferences.manage.approve-inventory.1')
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

})()