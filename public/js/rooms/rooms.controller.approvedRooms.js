(function() {
	'use strict'

	angular.module('rooms')
		.controller('ApprovedRoomsCtrl', function($scope, $state, assignedRooms) {

			$scope.roomsAssigned = assignedRooms.data
			
			$scope.back = function() {
				$state.go('dashboard.conferences.manage.room-allocate.1')
			}

		})

})()