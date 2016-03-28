(function() {
	'use strict'

	angular.module('roomSetView')
		.controller('roomSetListCtrl', function($scope, $state, $stateParams, roomSetData, residenceData, ajax, conferenceInfo) {
			var resData = {}

			for (var i = residenceData[0].length - 1; i >= 0; i--) {
				if (residenceData[0][i].id == $stateParams.rid) {
					resData = residenceData[0][i]
					continue
				}
			};
			
			var cid = $stateParams.cid
			$scope.roomSets = roomSetData[0] || []
			$scope.noRoomSets = ($scope.roomSets.length === 0)
			$scope.conferenceName = conferenceInfo.data.name
			$scope.residenceName = resData.name

			$scope.showWidget = function(toState) {
				var state = 'dashboard.conferences.manage.' + toState;
				$state.go(state, {cid: $stateParams.cid});
			}
		})

})()