(function() {
	'use strict'

	angular.module('dashboard')
		.controller('dashboardCtrl', function($scope, $state, loginStorage, globalPermissions) {
			$scope.userName = loginStorage.getEmail();

			$scope.permissions = {
				'manage-some-permissions': false,
				'manage-global-permissions': false,
				'approve-user-registration': false
			}

			angular.forEach(globalPermissions, function(permission) {
				$scope.permissions[permission] = true
			})

			$scope.showWidget = function(toState) {
				var state = 'dashboard.' + toState
				$('#sidebarNav').collapse('hide');
				$state.go(state);
			}

			$scope.logout = function() {
				loginStorage.logout()
				$state.go('login')
			}

			$scope.goEditAcct = function() {
                                console.log("Go to edit")
				$state.go('editAcct')
			}
		})

})()
