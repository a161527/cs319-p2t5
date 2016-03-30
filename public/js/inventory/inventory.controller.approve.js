(function() {
	'use strict'

	angular.module('inventory')
		.controller('approveInventoryCtrl', function($scope, $stateParams, $state, unapprovedInventory, $http, ajax) {

			$scope.inventory = unapprovedInventory.data.inventory
			$scope.state1 = true
			$scope.rejectMsg = 'Reject'
			
			angular.forEach($scope.inventory, function(inv) {
				inv.user['fullName'] = inv.user.firstName + ' ' + inv.user.lastName
			})

			$scope.approve = function(id) {
				var route = 'api/conferences/' + $stateParams.cid +'/userinventory/' + 1000 + '/approve'
				ajax.serviceCall('Approving...', 'post', route).then(function(resData) {

					$state.reload()

				}, function(resData) {
					console.log(resData)
				})
			}

			$scope.viewApproved = function() {
				$state.go('dashboard.conferences.manage.approve-inventory.2')
			}

			$scope.reject = function(id) {
				$http.delete('api/conferences/' + $stateParams.cid + '/userinventory/' + id).then(function(resData) {
					$state.reload()
				}, function(resData) {
					console.log(resData)
				})
			}

		})

		.controller('viewApprovedInventoryCtrl', function($scope, $state, $http, $stateParams, approvedInventory, modal) {

			$scope.state1 = false
			$scope.rejectMsg = 'Remove'
			$scope.inventory = approvedInventory.data.inventory

			angular.forEach($scope.inventory, function(inv) {
				inv.user['fullName'] = inv.user.firstName + ' ' + inv.user.lastName
			})

			$scope.back = function() {
				$state.go('dashboard.conferences.manage.approve-inventory.1')
			}

			$scope.reject = function(id) {
				$http.delete('api/conferences/' + $stateParams.cid + '/userinventory/' + id).then(function(resData) {
					$state.reload()
				}, function(resData) {
					console.log(resData)
				})
			}

		})

})()