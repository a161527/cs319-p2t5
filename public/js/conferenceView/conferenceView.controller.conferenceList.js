(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceListCtrl', function($scope, ajax) {
			
			$scope.conferences = []

			ajax.serviceCall('Loading conferences...', 'get', 'api/conferences').then(function(resData) {
				$scope.conferences = resData.data
			}, function(resData) {

			})

		})

})()