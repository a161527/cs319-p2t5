(function() {
	'use strict'

	angular.module('dashboard')
		.controller('dashboardCtrl', function($scope, $state, loginStorage, conferenceList) {
			$scope.userName = loginStorage.getEmail();

			$scope.widgets = {
				'conferences': 'Conferences',
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
				conferenceList.clearPermissions()
				loginStorage.clearAll()
				$state.go('login')
			}
		})

})()