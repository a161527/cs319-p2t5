(function() {
	'use strict'

	angular.module('inventoryView')
		.controller('inventoryListCtrl', function($scope, $state, $stateParams, inventoryData, ajax, conferenceInfo) {
			
			var cid = $stateParams.cid
			$scope.inventory = inventoryData[0].inventory || []
			$scope.noInventory = ($scope.inventory.length === 0)
			$scope.conferenceName = conferenceInfo.data.name

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

		})

})()