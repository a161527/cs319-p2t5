(function() {
	'use strict'

	angular.module('residenceView')
		.controller('residenceListCtrl', function($scope, $state, $stateParams, residenceData, ajax, conferenceInfo, permissions, confirmDeleteModal) {
			
			var cid = $stateParams.cid
			$scope.residences = residenceData[0] || []
			$scope.noResidences = ($scope.residences.length === 0)
			$scope.conferenceName = conferenceInfo.data.name
			$scope.canEdit = (permissions.indexOf('conference-room-edit') !== -1)

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

			$scope.deleteResidence = function(residence) {
				confirmDeleteModal.open('Residence', residence.name, 'api/conferences/' + $stateParams.cid + '/residences/' + residence.id)
			}

		})

})()