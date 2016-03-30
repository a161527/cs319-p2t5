(function() {
	'use strict'

	angular.module('transportationView')
		.controller('transportationListCtrl', function($scope, $state, $stateParams, transportationData, ajax, conferenceInfo, permissions, confirmDeleteModal) {
			
			var cid = $stateParams.cid
			$scope.transportation = transportationData[0].transportation || []
			$scope.noTransportation = ($scope.transportation.length === 0)
			$scope.conferenceName = conferenceInfo.data.name
			$scope.canEdit = (permissions.indexOf('conference-transportation-edit') !== -1)

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

			$scope.deleteTransportation = function(transportation) {
				confirmDeleteModal.open('Transportation', transportation.name, 'api/conferences/' + $stateParams.cid + '/transportation/' + transportation.id)
			}

		})

})()