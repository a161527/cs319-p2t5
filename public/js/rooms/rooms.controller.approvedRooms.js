(function() {
	'use strict'

	angular.module('rooms')
		.controller('ApprovedRoomsCtrl', function($scope, $state, $stateParams, $http, assignedRooms, conferenceData, modal) {

			$scope.roomsAssigned = assignedRooms.data
			$scope.conferenceName = conferenceData.data.name
			
			$scope.back = function() {
				$state.go('dashboard.conferences.manage.room-allocate.1')
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.remove = function(id) {
				$http.delete('api/conferences/' + $stateParams.cid + '/residences/' + id).then(function(resData) {
					$state.reload()
				}, function(resData) {
					modal.open('Error: ' + resData.data.message)
				})
			}

		})

})()