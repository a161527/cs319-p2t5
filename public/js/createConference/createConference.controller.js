(function() {
	'use strict'

	angular.module('createConference')
		.controller('createConferenceCtrl', function($scope, $state, conferenceFields, ajax, errorCodes) {

			$scope.conferenceInfo = conferenceFields.getConferenceInfo() || {}
			$scope.showError = false

			$scope.createConference = function() {
				$scope.showError = false

				// formatting request
				var conferenceInfo = $scope.conferenceInfo;
				conferenceInfo.start = conferenceInfo.startFormatted;
				delete conferenceInfo.startFormatted;
				conferenceInfo.end = conferenceInfo.endFormatted;
				delete conferenceInfo.endFormatted;

				ajax.serviceCall('Creating conference...', 'post', 'api/conferences', $scope.conferenceInfo).then(function(resData) {
					$state.go('dashboard.conferences.manage', {cid: resData['data']['id']});


				}, function(resData) {

					$scope.showError = true
					$scope.errorMessage = errorCodes[resData.data.message]

				})
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

			$scope.cancel = function() {
				conferenceFields.resetAll()
				$state.go('dashboard.conferences.list')
			}

			$scope.back = function(toState, set, model) {
				onNavigate(toState, set, model)
			}

			$scope.nextStep = function(form, toState, set, model) {
				if (form.$valid) {
					onNavigate(toState, set, model)
				} else {
					setFormDirty(form)
				}
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

			var onNavigate = function(toState, set, model) {
				conferenceFields[set]($scope[model])
				var state = 'dashboard.conferences.create.' + toState
				$state.go(state)
			}

			var initPopover = function() {
				$('[data-toggle="popover"]').popover({html:true});
			}

			initPopover();

		})

})()