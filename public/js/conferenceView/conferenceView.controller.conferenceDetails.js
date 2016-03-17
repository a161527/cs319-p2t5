(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceDetailsCtrl', function($scope, conferenceInfo, ajax) {

			$scope.conference = conferenceInfo[0].data || {}
			$scope.events = conferenceInfo[1].data || []

			$scope.oneAtATime = true

			$scope.status = {
				isFirstOpen: true,
				isFirstDisabled: false
			}
		})

})()