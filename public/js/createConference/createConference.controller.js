(function() {
	'use strict'

	angular.module('createConference')
		.controller('createConferenceCtrl', function($scope, $state, $stateParams, ajax, errorCodes, conferenceData) {
			
			$scope.conferenceInfo = conferenceData[0]
			$scope.showError = false

			if (conferenceData[0]) {
				$scope.editMode = true
				if ($scope.conferenceInfo.hasTransportation === 1) {
					$scope.conferenceInfo.hasTransportation = true
				}

				if ($scope.conferenceInfo.hasAccommodations === 1) {
					$scope.conferenceInfo.hasAccommodations = true
				}

				$scope.conferenceInfo.start = moment($scope.conferenceInfo.start, "YYYY-MM-DD").toDate()
				$scope.conferenceInfo.end = moment($scope.conferenceInfo.end, "YYYY-MM-DD").toDate()
			} else {
				$scope.editMode = false
			}

			$scope.createConference = function(form) {
				$scope.showError = false

				if (form.$valid) {
					// formatting request
					var conferenceInfo = $scope.conferenceInfo;
					conferenceInfo.start = moment(conferenceInfo.start).format('YYYY-MM-DD')
					conferenceInfo.end = moment(conferenceInfo.end).format('YYYY-MM-DD')
					conferenceInfo.hasTransportation = $scope.conferenceInfo.hasTransportation || false
					conferenceInfo.hasAccommodations = $scope.conferenceInfo.hasAccommodations || false

					if ($scope.editMode) {
			
						ajax.serviceCall('Updating conference...', 'put', 'api/conferences/' + $stateParams.cid, conferenceInfo).then(function(resData) {
							$state.go('dashboard.conferences.manage', {cid: conferenceInfo.id}, {reload: true})


						}, function(resData) {

							$scope.showError = true
							$scope.errorMessage = errorCodes[resData.data.message]

						})
						
					} else {

						ajax.serviceCall('Creating conference...', 'post', 'api/conferences', conferenceInfo).then(function(resData) {

							$state.go('dashboard.conferences.manage', {cid: resData['data']['id']}, {reload: true});


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
				if ($scope.editMode) {
					$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
				} else {
					$state.go('dashboard.conferences.list')
				}
				
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

		})

})()