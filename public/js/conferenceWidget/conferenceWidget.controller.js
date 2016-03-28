(function() {
	'use strict'

	angular.module('conferenceWidget')
		.controller('conferenceWidgetCtrl', function($scope, $state, $stateParams, permissions, ajax, conferenceInfo) {

			$scope.permissions = {
				"conference-event-create": false,
				"conference-registration-approval": false,
				"conference-permission-management": false,
				"conference-info-edit": false,
				"conference-inventory-edit": false,
				"conference-room-edit": false,
				"conference-transportation-edit": false,
				"conference-announce": false,
				"conference-view-statistics": false
			}

			$scope.conferenceName = conferenceInfo.data.name

			var showWidgets = function(list) {
				angular.forEach(list, function(permission) {
					$scope.permissions[permission] = true
				}) 
			}

			showWidgets(permissions)

			$scope.showWidget = function(toState) {
				var state = 'dashboard.conferences.manage.' + toState;
				$state.go(state);
			}

			$scope.showEventList = function() {
				$state.go('dashboard.events', {cid: $stateParams.cid})
			}
			
			$scope.goBack = function(currState) {
				window.alert($state);
			}

		})

})()