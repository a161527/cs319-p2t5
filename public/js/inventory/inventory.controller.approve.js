(function() {
	'use strict'

	angular.module('inventory')
		.controller('approveInventoryCtrl', function($scope, $stateParams, $state, unapprovedInventory, ajax) {

			$scope.inventory = unapprovedInventory.data.inventory
			
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

		})

})()