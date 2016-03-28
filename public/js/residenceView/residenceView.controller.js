(function() {
	'use strict'

	angular.module('residenceView')
		.controller('residenceListCtrl', function($scope, $state, $stateParams, residenceData, ajax, conferenceInfo) {
			console.log(conferenceInfo)
			
			var cid = $stateParams.cid
			$scope.residences = residenceData[0] || []
			$scope.noResidences = ($scope.residences.length === 0)
			$scope.conferenceName = conferenceInfo.data.name

			$scope.showWidget = function(toState) {
				var state = 'dashboard.conferences.manage.' + toState;
				$state.go(state, {cid: $stateParams.cid});
			}
		})

})()