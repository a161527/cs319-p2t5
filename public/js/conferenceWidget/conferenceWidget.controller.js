(function() {
	'use strict'

	angular.module('conferenceWidget')
		.controller('conferenceWidgetCtrl', function($scope, $state, $stateParams, permissions, ajax) {

			//Hide all buttons.
			// $('div[class="col-md-3"]').hide()

			$scope.conferenceName = null
			ajax.serviceCall('Loading...', 'get', 'api/conferences/' + $stateParams.cid).then(function(resData) {
				$scope.conferenceName = resData.data.name
			}, function(resData) {
				$scope.conferenceName = null
			})

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