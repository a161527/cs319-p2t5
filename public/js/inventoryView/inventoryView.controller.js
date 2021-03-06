(function() {
	'use strict'

	angular.module('inventoryView')
		.controller('inventoryListCtrl', function($scope, $state, $stateParams, inventoryData, ajax, conferenceInfo, permissions, confirmDeleteModal) {
			
			var cid = $stateParams.cid
			$scope.inventory = inventoryData[0].inventory || []
			$scope.noInventory = ($scope.inventory.length === 0)
			$scope.conferenceName = conferenceInfo.data.name
			$scope.canEdit = (permissions.indexOf('conference-inventory-edit') !== -1)

			$scope.showWidget = function(toState, params) {
				var state = 'dashboard.conferences.manage.' + toState;
				var allParams = {cid: $stateParams.cid}
				if (params) {
					angular.forEach(params, function(val, key) {
						allParams[key] = val
					})
				}
				$state.go(state, allParams);
			}

			$scope.deleteInventory = function(inventory) {
				confirmDeleteModal.open('Inventory', inventory.itemName, 'api/conferences/' + $stateParams.cid + '/inventory/' + inventory.id)
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

})()