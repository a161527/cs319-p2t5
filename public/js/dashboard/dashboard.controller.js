(function() {
	'use strict'

	angular.module('dashboard')
		.controller('dashboardCtrl', function($scope, $state, loginStorage) {
			$scope.userName = loginStorage.getEmail();

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