(function() {
	'use strict'

	angular.module('rooms')
		.controller('AllocateRoomsCtrl', function($scope, $state, roomDependents, dataFormat) {

			$scope.noUsers = (roomDependents.data.length === 0)
			$scope.dependents = {}
			$scope.noSelection = false

			$scope.selectedDependents = []

			if (roomDependents.data.length !== 0) {
				$scope.dependents = dataFormat.dependentsFormat(roomDependents.data)
			}

			//Select all checkboxes
			$scope.selectAll = function(field, value, list) {
				angular.forEach(list, function(dependent) { 
					dependent[field] = value
				})
			}

			$scope.next = function() {
				angular.forEach($scope.dependents, function(dep) {
					if (dep.hasOwnProperty('selected') && dep['selected'] !== false) {
						$scope.selectedDependents.push({id: dep.id})
					}
				})

				if ($scope.selectedDependents.length > 0) {
					$state.go('dashboard.conferences.room-allocate.2', {selectedDependents: $scope.selectedDependents})
				} else {
					$scope.noSelection = true
				}
			}

			$scope.removeMessage = function() {
				$scope.noSelection = false
			}

		})

		.controller('ResidenceSelectCtrl', function($scope, $http, $stateParams, $state, $uibModal, residences) {

			$scope.residences = residences.data
			$scope.rooms = []
			$scope.errorMessage = null
			$scope.showError = false

			$scope.getRooms = function(residenceId) {
				var route = 'api/conferences/' + $stateParams.cid + '/residences/' + residenceId + '/rooms'
				$http.get(route).then(function(resData) {
					$scope.rooms = resData.data
					console.log($scope.rooms)
				})
			}

			$scope.assign = function(room) {
				var dependents = $stateParams.selectedDependents
				$scope.showError = false

				angular.forEach(dependents, function(dep) {
					dep['roomName'] = room.name
				})

				var route = 'api/conferences/' + $stateParams.cid + '/residences/assign'

				$http.post(route, {registrations: dependents, roomSet: room.id}).then(function(data) {
					openModal()

				}, function(resData) {
					
					console.log(resData)
					$scope.errorMessage = resData.data.message
					$scope.showError = true

				})

			}

			var openModal = function() {

				var modal = $uibModal.open({
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.modalConfirm.html',
					controller: function($scope, $uibModalInstance) {

						$scope.ok = function() {
							$uibModalInstance.close()
						}

					}
				})

				modal.result.then(function () {
					$state.go('dashboard.conferences.room-allocate', null, { reload: true })
				}, function () {
					
				})

			}

			$scope.removeMessage = function() {
				$scope.noSelection = false
			}

		})

})()