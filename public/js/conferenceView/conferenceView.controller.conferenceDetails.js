(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceDetailsCtrl', function($scope, conferenceInfo) {

			$scope.conference = conferenceInfo.data || {}

		})

})()