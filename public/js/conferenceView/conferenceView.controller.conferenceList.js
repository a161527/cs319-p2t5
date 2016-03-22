(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceListCtrl', function($scope, $state, $http, conferenceData, $q) {

			$scope.conferences = conferenceData[0] || []
			$scope.noConferences = ($scope.conferences.length === 0)
			$scope.canCreateConference = (conferenceData[1].indexOf('create-conference') !== -1)
			console.log($scope.conferences)
			$scope.registered = function(registeredList) {
				return registeredList.length > 0
			}

			$scope.goToCreateConference = function() {
				$state.go('dashboard.conferences.create.1')
			}

			$scope.goToManage = function(conference) {
				$state.go('dashboard.conferences.manage', {cid: conference.id})
			}

			$scope.goToDetails = function(conference) {
				$state.go('dashboard.conferences.registrationDetails', {cid: conference.id, conference: conference})
			}
		})

})()