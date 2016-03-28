(function() {
	'use strict'

	angular.module('residenceView')
		.controller('residenceListCtrl', function($scope, $state, $stateParams, residenceData, ajax, conferenceInfo) {
			
			var cid = $stateParams.cid
			$scope.residences = residenceData[0] || []
			$scope.noResidences = ($scope.residences.length === 0)
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