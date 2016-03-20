(function() {
	'use strict'

	angular.module('dashboard')
		.controller('dashboardCtrl', function($scope, $state, loginStorage) {
			$scope.userName = loginStorage.getEmail();

			$scope.widgets = {
				'conferences.list': 'Conferences',
				'events': 'Events',
				'flights': 'Flights',
				'transportation': 'Transportation'
			}

			$scope.showWidget = function(toState) {
				var state = 'dashboard.' + toState
				$('#sidebarNav').collapse('hide');
				$state.go(state);
			}

			$scope.logout = function() {
				loginStorage.logout()
				$state.go('login')
			}
		})

})()