(function() {
	'use strict'

	angular.module('createEvent')
		.controller('createEventCtrl', function($scope, $state, $stateParams, ajax, errorCodes) {
			$scope.showError = false

			$scope.createEvent = function(form) {
				var eventInfo = {}

				eventInfo.eventName = $scope.eventName
				eventInfo.date = moment($scope.date).format('YYYY-MM-DD')
				eventInfo.location = $scope.location
				eventInfo.startTime = moment($scope.start).format('HH:mm:ss')
				eventInfo.endTime = moment($scope.end).format('HH:mm:ss')
				eventInfo.capacity = $scope.capacity
				eventInfo.description = $scope.description

				$scope.showError = false

				if (form.$valid) {
					ajax.serviceCall('Creating event...', 'post', 'api/event/' + $stateParams.cid, eventInfo).then(function(resData) {

						console.log(resData)
						$state.go('dashboard.events', {'cid': $stateParams.cid}, {reload: true})

					}, function(resData) {
						console.log(resData)

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
				$state.go('dashboard.events', {'cid': $stateParams.cid})
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

		})

})()