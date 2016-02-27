(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceListCtrl', function($scope, conferenceList) {
			
			$scope.conferences = conferenceList.data || []
			$scope.noConferences = ($scope.conferences.length === 0)

		})

})()