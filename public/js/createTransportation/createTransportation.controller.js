(function() {
	'use strict'

	angular.module('createTransportation')
		.controller('createTransportationCtrl', function($scope, $state, $stateParams, ajax, errorCodes, conferenceInfo, transportationData) {

			$scope.transportationInfo = {'1':{}} 
			$scope.showError = false
			$scope.conferenceName = conferenceInfo.data.name

			if (transportationData[0]) {
				$scope.transportationInfo['1'] = transportationData[0]
				$scope.editMode = true

			} else {

				$scope.editMode = false

			}

			$scope.createTransportation = function(form) {
				$scope.showError = false

				if (form.$valid) {
					var transportationInfo = []

					angular.forEach($scope.transportationInfo, function(transportation) {
						var trans = {}
						trans.name = transportation.name
						trans.company = transportation.company
						trans.phone = transportation.phone
						trans.capacity = transportation.capacity
						trans.conferenceID = $stateParams.cid

						transportationInfo.push(trans)
					})

					if ($scope.editMode) {
						ajax.serviceCall('Updating transportation...', 'patch', 'api/conferences/' + $stateParams.cid + '/transportation/' + $stateParams.tid, transportationInfo[0]).then(function(resData) {

							$state.go('dashboard.conferences.manage.viewTransportation', {'cid': $stateParams.cid}, {reload: true})

						}, function(resData) {

							$scope.showError = true
							$scope.errorMessage = errorCodes[resData.data.message]

						})
					} else {
						ajax.serviceCall('Creating transportation...', 'post', 'api/conferences/' + $stateParams.cid + '/transportation', transportationInfo).then(function(resData) {

							$state.go('dashboard.conferences.manage.viewTransportation', {'cid': $stateParams.cid}, {reload: true})

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
				$state.go('dashboard.conferences.manage.viewTransportation', {'cid': $stateParams.cid}, {reload: true})
			}

			$scope.addTransportation = function() {
				var transportationIndex = 1
				while($scope.transportationInfo.hasOwnProperty(transportationIndex)) {
					transportationIndex += 1
				}
				$scope.transportationInfo[transportationIndex] = {}
			}

			$scope.deleteTransportation = function(index) {
				if (index != 1) {
					if ($scope.transportationInfo.hasOwnProperty(index)) {
						delete $scope.transportationInfo[index]
					}
				}
			}

			$scope.showRemoveButton = function(index) {
				return index > 1
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.goToTransportationList = function () {
				$state.go('dashboard.conferences.manage.viewTransportation', {cid: $stateParams.cid})
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

		})

})()