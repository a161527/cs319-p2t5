(function() {
	'use strict'

	angular.module('eventView')
		.controller('eventListCtrl', function($scope, $state, $stateParams, eventData, ajax, conferenceName, confirmDeleteModal) {
			
			var cid = $stateParams.cid
			$scope.events = eventData[0] || []
			$scope.noEvents = ($scope.events.length === 0)
			$scope.conferenceName = conferenceName
			$scope.canCreateEvent = (eventData[1].indexOf('conference-event-create') !== -1)
			$scope.isConfRegistered = false
			$scope.canEditEvent = false

			var regConference = []

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

			$scope.goToEventState = function(toState, params) {
				var state = 'dashboard.events.' + toState;
				$state.go(state, params, {reload: true})
			}

			$scope.deleteEvent = function(event) {
				confirmDeleteModal.open('Event', event.eventName, 'api/event/' + event.id)
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.goToConferenceList = function () {
				$state.go('dashboard.conferences.list')
			}

			var formatEvents = function() {
				
				for (var i = $scope.events.length - 1; i >= 0; i--) {
					// removing seconds from time
					$scope.events[i].startTime = $scope.events[i].startTime.substring(0,5)
					$scope.events[i].endTime = $scope.events[i].endTime.substring(0,5)

					// getting registration abilities
					$scope.events[i].isEventRegistered = true

					angular.forEach(eventData[3], function(regEvent) {
						
						if (regEvent.id == $scope.events[i].id) {
							if (regEvent.registrations.length < regConference.length) {
								$scope.events[i].isEventRegistered = false
							}
						}

						if ($scope.events[i].permissions.indexOf("event-info-edit") !== -1) {
							$scope.canEditEvent = true
						}
					})
				};
			}

			var getApprovedUsers = function() {
				var registered = eventData[2].length > 0

				if (registered) {
					for (var i = eventData[2].length - 1; i >= 0; i--) {
						if (eventData[2][i]['status'] == 'approved') {
							regConference.push(eventData[2][i].id)
							$scope.isConfRegistered = true
						}
					}
				}
				else {
					$scope.isEventRegistered = false
				}
			}

			getApprovedUsers()
			formatEvents()
		})

})()