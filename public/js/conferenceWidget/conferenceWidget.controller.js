(function() {
	'use strict'

	angular.module('conferenceWidget')
		.controller('conferenceWidgetCtrl', function($scope, $state, $stateParams, permissions, ajax, conferenceInfo) {

			//Hide all buttons.
			// $('div[class="col-md-3"]').hide()

			$scope.conferenceName = conferenceInfo.data.name

			var showWidgets = function(list) {
				list.forEach(function(permission) {
					$('#' + permission).show()
				}) 
			}

			showWidgets(permissions)

			$scope.showWidget = function(toState) {
				var state = 'dashboard.conferences.' + toState;
				$state.go(state, {cid: $stateParams.cid});
			}
			
			$scope.goBack = function(currState) {
				window.alert($state);
			}

			$scope.createEvent = function() {
				$state.go('dashboard.events.create', {'cid': $stateParams.cid})
			}

		})

})()