(function() {
	'use strict'

	angular.module('eventView')
		.controller('eventListCtrl', function($scope, $state, $stateParams, eventData, ajax, conferenceName) {
			
			var cid = $stateParams.cid
			$scope.events = eventData[0] || []
			$scope.noEvents = ($scope.events.length === 0)
			$scope.conferenceName = conferenceName
			$scope.canCreateEvent = (eventData[1].indexOf('conference-event-create') !== -1)

			$scope.goToCreateEvent = function() {
				$state.go('dashboard.events.create')
			}

			var formatEvents = function() {
				// removing seconds from time
				for (var i = $scope.events.length - 1; i >= 0; i--) {
					$scope.events[i].startTime = $scope.events[i].startTime.substring(0,5)
					$scope.events[i].endTime = $scope.events[i].endTime.substring(0,5)
				};
			}

			formatEvents()

		})

})()