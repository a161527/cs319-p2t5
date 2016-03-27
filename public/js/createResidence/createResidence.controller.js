(function() {
	'use strict'

	angular.module('createResidence')
		.controller('createResidenceCtrl', function($scope, $state, $stateParams, ajax, errorCodes) {

			$scope.residence = {'1':{}} 
			$scope.showError = false

			$scope.createResidence = function(form) {
				$scope.showError = false

				if (form.$valid) {
					var residenceInfo = []

					angular.forEach($scope.residence, function(residence) {
						var res = {}
						res.name = residence.name
						res.location = residence.address
						res.conferenceID = $stateParams.cid

						residenceInfo.push(res)
					})

					console.log(residenceInfo)

					// TODO: residence creation endpoint needs to be updated to take in multiple objects
					ajax.serviceCall('Creating residences...', 'post', 'api/conferences/' + $stateParams.cid + '/residences', residenceInfo[0]).then(function(resData) {

						console.log(resData)
						$state.go('dashboard.conferences.manage', {'cid': $stateParams.cid}, {reload: true})

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
				$state.go('dashboard.conferences.manage', {'cid': $stateParams.cid}, {reload: true})
			}

			$scope.addResidence = function() {
				var residenceIndex = 1
				while($scope.residence.hasOwnProperty(residenceIndex)) {
					residenceIndex += 1
				}
				$scope.residence[residenceIndex] = {}
			}

			$scope.deleteResidence = function(index) {
				if (index != 1) {
					if ($scope.residence.hasOwnProperty(index)) {
						delete $scope.residence[index]
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