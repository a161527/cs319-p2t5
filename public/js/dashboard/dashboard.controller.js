(function() {
	'use strict'

	angular.module('dashboard')
		.controller('dashboardCtrl', function($scope, $state, loginStorage) {
			$scope.userName = loginStorage.getEmail();

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

			$scope.logout = function() {
				loginStorage.removeTokens()
				$state.go('login')
			}
		})

})()