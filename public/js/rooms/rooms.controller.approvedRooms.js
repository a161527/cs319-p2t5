(function() {
	'use strict'

	angular.module('rooms')
		.controller('ApprovedRoomsCtrl', function($scope, $stateParams, $state, assignedRooms, conferenceData) {

			$scope.roomsAssigned = assignedRooms.data
			$scope.conferenceName = conferenceData.data.name
			
			$scope.back = function() {
				$state.go('dashboard.conferences.manage.room-allocate.1')
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

})()