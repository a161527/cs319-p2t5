(function() {
	'use strict'

	angular.module('dashboard')
		.controller('dashboardCtrl', function($scope, $state) {
			$scope.userName = "fakeUser";

			$scope.widgets = {
				'conferences': 'Conferences',
				'events': 'Events',
				'flights': 'Flights',
				'transportation': 'Transportation'
			}

			$scope.showWidget = function(toState) {
				var state = 'dashboard.' + toState
				$state.go(state);
			}
		})

})()