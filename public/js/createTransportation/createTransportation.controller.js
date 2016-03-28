(function() {
	'use strict'

	angular.module('createTransportation')
		.controller('createTransportationCtrl', function($scope, $state, $stateParams, ajax, errorCodes) {

			$scope.transportation = {'1':{}} 
			$scope.showError = false

			$scope.createTransportation = function(form) {
				$scope.showError = false

				if (form.$valid) {
					var transportationInfo = []

					angular.forEach($scope.transportation, function(transportation) {
						var trans = {}
						trans.name = transportation.name
						trans.company = transportation.company
						trans.phone = transportation.phone
						trans.capacity = transportation.capacity
						trans.conferenceID = $stateParams.cid

						transportationInfo.push(trans)
					})

					ajax.serviceCall('Creating transportation...', 'post', 'api/conferences/' + $stateParams.cid + '/transportation', transportationInfo).then(function(resData) {

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

			$scope.addTransportation = function() {
				var transportationIndex = 1
				while($scope.transportation.hasOwnProperty(transportationIndex)) {
					transportationIndex += 1
				}
				$scope.transportation[transportationIndex] = {}
			}

			$scope.deleteTransportation = function(index) {
				if (index != 1) {
					if ($scope.transportation.hasOwnProperty(index)) {
						delete $scope.transportation[index]
					}
				}
			}

			$scope.showRemoveButton = function(index) {
				return index > 1
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

		})

})()