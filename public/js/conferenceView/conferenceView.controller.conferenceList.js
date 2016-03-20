(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceListCtrl', function($scope, $state, conferenceData) {
			
			$scope.conferences = conferenceData[0] || []
			$scope.noConferences = ($scope.conferences.length === 0)
			$scope.canCreateConference = (conferenceData[1].indexOf('create-conference') !== -1)

			$scope.registered = function(registeredList) {
				return registeredList > 0
			}
			console.log($scope.conferences)
			$scope.goToCreateConference = function() {
				$state.go('dashboard.conferences.create.1')
			}
		})

})()