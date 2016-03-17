(function() {
	'use strict'

	angular.module('conferenceWidget')
		.controller('conferenceWidgetCtrl', function($scope, $state, $stateParams, permissions) {

			//Hide all buttons.
			// $('div[class="col-md-3"]').hide()

			var showWidgets = function(list) {
				list.forEach(function(permission) {
					$('#' + permission).show()
				}) 
			}

			showWidgets(permissions)

			$scope.showWidget = function(toState) {
				var state = 'dashboard.' + toState;
				$state.go(state);
			}
			
			$scope.goBack = function(currState) {
				window.alert($state);
			}
		})

})()