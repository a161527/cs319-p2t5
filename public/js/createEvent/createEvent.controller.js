(function() {
	'use strict'

	angular.module('createEvent')
		.controller('createEventCtrl', function($scope, $state, $stateParams, ajax, errorCodes, conferenceInfo, eventData) {

			$scope.showError = false
			$scope.conferenceName = conferenceInfo.data.name

			$scope.eventInfo = eventData[0]

			if ($scope.eventInfo) {

				$scope.eventInfo.date = moment($scope.eventInfo.date, "YYYY-MM-DD").toDate()
				$scope.eventInfo.startTime = moment($scope.eventInfo.startTime, "HH:mm:ss").toDate()
				$scope.eventInfo.endTime = moment($scope.eventInfo.endTime, "HH:mm:ss").toDate()

				$scope.editMode = true

			} else {

				$scope.editMode = false

			}

			$scope.createEvent = function(form) {
				$scope.showError = false

				if (form.$valid) {
					var eventInfo = {}

					eventInfo.eventName = $scope.eventInfo.eventName
					eventInfo.description = $scope.eventInfo.description
					eventInfo.date = moment($scope.eventInfo.date).format('YYYY-MM-DD')
					eventInfo.startTime = moment($scope.eventInfo.start).format('HH:mm') + ':00'
					eventInfo.endTime = moment($scope.eventInfo.end).format('HH:mm') + ':00'
					eventInfo.location = $scope.eventInfo.location
					eventInfo.capacity = $scope.eventInfo.capacity
					eventInfo.conferenceID = $stateParams.cid

					if ($scope.editMode) {
						ajax.serviceCall('Updating event...', 'post', 'api/event/' + $stateParams.eid + '/update', eventInfo).then(function(resData) {

							$state.go('dashboard.events', {cid: $stateParams.cid}, {reload: true})

						}, function(resData) {

							$scope.showError = true
							$scope.errorMessage = errorCodes[resData.data.message]

						})
					} else {
						ajax.serviceCall('Creating event...', 'post', 'api/event/' + $stateParams.cid, eventInfo).then(function(resData) {

							$state.go('dashboard.events', {cid: $stateParams.cid}, {reload: true})

						}, function(resData) {

							$scope.showError = true
							$scope.errorMessage = errorCodes[resData.data.message]

						})
					}

				} else {
					setFormDirty(form)
				}
				
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

			$scope.cancel = function() {
				$state.go('dashboard.events', {'cid': $stateParams.cid}, {reload: true})	
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