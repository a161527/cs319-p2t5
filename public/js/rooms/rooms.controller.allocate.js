(function() {
	'use strict'

	angular.module('rooms')
		.controller('AllocateRoomsCtrl', function($scope, $state, $http, $stateParams, roomDependents, dataFormat, conferenceData) {

			$scope.noUsers = (roomDependents.length === 0)
			$scope.dependents = {}
			$scope.noSelection = false

			$scope.selectedDependents = []

			$scope.conferenceName = conferenceData.data.name

			if (roomDependents.length !== 0) {
				$scope.dependents = dataFormat.dependentsFormat(roomDependents, 'userID')
			}

			$scope.viewApproved = function() {
				$state.go('dashboard.conferences.manage.room-allocate.3')
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
					$state.go('dashboard.conferences.manage.room-allocate.2', {selectedDependents: $scope.selectedDependents})
				} else {
					$scope.noSelection = true
				}
			}

			$scope.removeMessage = function() {
				$scope.noSelection = false
			}

			$scope.cancel = function() {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

		.controller('ResidenceSelectCtrl', function($scope, $http, $stateParams, $state, modal, residences, conferenceData) {

			$scope.residences = residences.data
			$scope.conferenceName = conferenceData.data.name

			$scope.rooms = []
			$scope.roomSets = []

			$scope.errorMessage = null
			$scope.showError = false

			$scope.residence = {name: '', id: null}
			$scope.roomSet = {name:'', id: null}

			$scope.newRoomNameError = false

			$scope.back = function() {
				$state.go('dashboard.conferences.manage.room-allocate.1', {cid: $stateParams.cid})
			}

			$scope.getRoomSets = function(residenceId, name) {

				var route = 'api/conferences/' + $stateParams.cid + '/residences/' + residenceId + '/roomSets'
				$http.get(route).then(function(resData) {

					$scope.roomSet.name = ''
					$scope.roomSet.id = null

					$scope.rooms = []

					$scope.residence.name = name
					$scope.residence.id = residenceId

					$scope.roomSets = resData.data
					console.log($scope.roomSets)
				})
			}

			$scope.getRooms = function(roomSetId, name) {
				var route = 'api/conferences/' + $stateParams.cid + '/residences' + '/roomSets/' + roomSetId + '/rooms'

				$http.get(route).then(function(resData) {
					$scope.rooms = resData.data

					$scope.roomSet.name = name
					$scope.roomSet.id = roomSetId
				})

			}

			$scope.assign = function(room) {
				
				$scope.showError = false

				var route = 'api/conferences/' + $stateParams.cid + '/residences/assign'

				$http.post(route, formatData(room)).then(function(data) {


					modal.open('Successfully assigned users', function() {
						$state.go('dashboard.conferences.manage.room-allocate.1', null, { reload: true })
					})


				}, function(resData) {
					
					console.log(resData)
					$scope.errorMessage = resData.data.message
					$scope.showError = true

				})

			}


			$scope.assignNewRoom = function() {
				if (!$scope.roomNameAssign) {
					$scope.newRoomNameError = true
				} else {
					$scope.assign()
				}
			}

			var formatData = function(room) {
				var dependents = $stateParams.selectedDependents
				var obj = {}
				obj['registrationIds'] = []

				angular.forEach(dependents, function(dep) {
					obj['registrationIds'].push(dep.id)
				})

				obj['roomName'] = room? room.roomName : $scope.roomNameAssign
				obj['roomSet'] = $scope.roomSet.id

				return obj
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

})()