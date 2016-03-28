(function() {
	'use strict'

	angular.module('createEvent')
		.controller('createEventCtrl', function($scope, $state, $stateParams, ajax, errorCodes) {

			$scope.event = {} 
			$scope.showError = false

			$scope.createEvent = function(form) {
				$scope.showError = false
				console.log('blah')

				if (form.$valid) {
					var eventInfo = {}

					eventInfo.eventName = $scope.event.name
					eventInfo.description = $scope.event.description
					eventInfo.date = moment($scope.event.date).format('YYYY-MM-DD')
					eventInfo.startTime = moment($scope.event.start).format('HH:mm') + ':00'
					eventInfo.endTime = moment($scope.event.end).format('HH:mm') + ':00'
					eventInfo.location = $scope.event.location
					eventInfo.capacity = $scope.event.capacity
					eventInfo.conferenceID = $stateParams.cid

					console.log(eventInfo)

					ajax.serviceCall('Creating event...', 'post', 'api/event/' + $stateParams.cid, eventInfo).then(function(resData) {

						$state.go('dashboard.conferences.manage', {'cid': $stateParams.cid}, {reload: true})

					}, function(resData) {

						$scope.showError = true
						$scope.errorMessage = errorCodes[resData.data.message]

					})
				} else {
					setFormDirty(form)
				}
				
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

			$scope.cancel = function() {
				$state.go('dashboard.conferences.manage', {'cid': $stateParams.cid}, {reload: true})
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

			$('#eventForm').on('keyup keypress', function(e) {
				keyCode = e.keyCode || e.which;
				if (keyCode === 13) { 
					e.preventDefault();
					return false;
				}
			});

		})

})()