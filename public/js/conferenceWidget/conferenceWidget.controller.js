(function() {
	'use strict'

	angular.module('conferenceWidget')
		.controller('conferenceWidgetCtrl', function($scope, $state, $stateParams, permissions) {

			console.log(permissions)

			$scope.showWidget = function(toState) {
				var state = 'dashboard.' + toState;
				$state.go(state);
			}
			
			$scope.goBack = function(currState) {
				window.alert($state);
			}
		})

})()