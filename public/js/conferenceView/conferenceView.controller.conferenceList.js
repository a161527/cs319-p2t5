(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceListCtrl', function($scope, $state, conferenceData) {
			
			$scope.conferences = conferenceData[0].data || []
			$scope.noConferences = ($scope.conferences.length === 0)
			var permissions = conferenceData[1]

			if (permissions.indexOf('create-conference') === -1) {
				//Jquery hide is better than adding a watcher with ng-show/hide
				$(createConferenceBtn).hide()
			}

			$scope.goToCreateConference = function() {
				$state.go('dashboard.conferences.create.1')
			}
		})

})()