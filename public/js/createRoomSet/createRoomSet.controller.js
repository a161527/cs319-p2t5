(function() {
	'use strict'

	angular.module('createRoomSet')
		.controller('createRoomSetCtrl', function($scope, $state, $stateParams, roomSetFields, ajax, errorCodes, conferenceInfo, roomSetData) {

			$scope.showError = false
			$scope.cid = $stateParams.cid
			$scope.rid = $stateParams.rid
			$scope.conferenceName = conferenceInfo.data.name

			$scope.roomTypes = roomSetData[2]

			$scope.hasRoomTypes = ($scope.roomTypes.length > 0)

			if (roomSetData[0]) {
				$scope.editMode = true
				var roomSet = roomSetData[0]

				$scope.roomTypes.selectedOption = roomSetFields.getRoomType() || roomSet.type
				roomSetFields.setRoomType($scope.roomTypes.selectedOption)
				var rsInfo = roomSetFields.getRoomSetInfo()
				var rsName = roomSet.name
				if (rsInfo) {
					rsName = rsInfo['1'].name
				}
				roomSetFields.setRoomSetInfo({'1': {'name': rsName}})


			} else {
				$scope.editMode = false
			}

			$scope.roomType = {}
			$scope.roomType = roomSetFields.getRoomType() || {}
			$scope.roomTypeInfo = roomSetFields.getRoomTypeInfo() || {}
			$scope.roomSetInfo = roomSetFields.getRoomSetInfo() || {'1':{}}
			$scope.residenceName = roomSetData[1]

			if (!$scope.roomType.id) {
				$scope.roomType.id = 'newRoomType'
			}

			$scope.addNewType = false
			if ($scope.roomType.id !== 'newRoomType') {
				for (var i = $scope.roomTypes.length - 1; i >= 0; i--) {
					if ($scope.roomTypes[i].id.toString() === $scope.roomType.id) {
						$scope.roomType.name = $scope.roomTypes[i].name
						$scope.roomType.capacity = $scope.roomTypes[i].capacity
						$scope.roomType.accessible = $scope.roomTypes[i].accessible
					}
				};
			} else {
				$scope.roomType.name = $scope.roomTypeInfo.name
				$scope.roomType.capacity = $scope.roomTypeInfo.capacity
				$scope.roomType.accessible = $scope.roomTypeInfo.accessible
				$scope.addNewType = true
			}

			$scope.createRoomSet = function() {
				$scope.showError = false
				var roomSetInfo = []

				angular.forEach($scope.roomSetInfo, function(roomSet) {
					var rs = {}
					rs.name = roomSet.name

					// Add new room type if necessary, else set room type id
					if ($scope.addNewType) {
						rs['type.name'] = $scope.roomType.name
						rs['type.capacity'] = $scope.roomType.capacity
						rs['type.accessible'] = $scope.roomType.accessible || false
					} else {
						rs.typeID = $scope.roomType.id
					}

					roomSetInfo.push(rs)
				})

				if ($scope.editMode) {
					ajax.serviceCall('Updating room set...', 'patch', 'api/conferences/' + $stateParams.cid + '/residences/roomSets/' + $stateParams.rsid, roomSetInfo[0]).then(function(resData) {

						roomSetFields.resetAll()
						$state.go('dashboard.conferences.manage.viewResidence.viewRoomSet', {'cid': $stateParams.cid, 'rid': $stateParams.rid}, {reload: true})

					}, function(resData) {

						$scope.showError = true
						$scope.errorMessage = errorCodes[resData.data.message]

					})
				} else {
					ajax.serviceCall('Creating room sets...', 'post', 'api/conferences/' + $stateParams.cid + '/residences/' + $stateParams.rid + '/roomSets', roomSetInfo).then(function(resData) {

						roomSetFields.resetAll()
						$state.go('dashboard.conferences.manage.viewResidence.viewRoomSet', {'cid': $stateParams.cid, 'rid': $stateParams.rid}, {reload: true})

					}, function(resData) {

						$scope.showError = true
						$scope.errorMessage = errorCodes[resData.data.message]

					})
				}
				
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

			$scope.cancel = function() {
				roomSetFields.resetAll()
				$state.go('dashboard.conferences.manage.viewResidence.viewRoomSet', {'cid': $stateParams.cid, 'rid': $stateParams.rid}, {reload: true})
			}

			$scope.back = function(toState, set, model) {
				if (toState == "2" && !$scope.addNewType) {
					toState = 1
				}

				onNavigate(toState, set, model)
			}

			$scope.nextStep = function(form, toState, set, model) {
				$scope.roomType = $scope.roomTypes.selectedOption
				
				if (toState === "2" && !$scope.hasRoomTypes) {
					$scope.addNewType = true
					onNavigate(toState, set, model)
				}

				else if (form.$valid) {
					if (toState === "2") {
						if ($scope.roomType.id === "newRoomType") {
							$scope.addNewType = true
							
						} else {
							toState = "3"
							$scope.addNewType = false
						}
					}

					onNavigate(toState, set, model)
				}

				else {
					setFormDirty(form)
				}
			}

			$scope.addRoomSet = function() {
				var roomSetIndex = 1
				while($scope.roomSetInfo.hasOwnProperty(roomSetIndex)) {
					roomSetIndex += 1
				}
				$scope.roomSetInfo[roomSetIndex] = {}
			}

			$scope.deleteRoomSet = function(index) {
				if (index != 1) {
					if ($scope.roomSetInfo.hasOwnProperty(index)) {
						delete $scope.roomSetInfo[index]
					}
				}
			}

			$scope.showRemoveButton = function(index) {
				return index > 1
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.goToResidenceList = function () {
				$state.go('dashboard.conferences.manage.viewResidence', {cid: $stateParams.cid})
			}

			$scope.goToRoomSetList = function () {
				$state.go('dashboard.conferences.manage.viewResidence.viewRoomSet', {cid: $stateParams.cid, rid: $stateParams.rid})
			}

			var onNavigate = function(toState, set, model) {
				roomSetFields[set]($scope[model])

				var state = null

				if ($scope.editMode) {
					state = 'dashboard.conferences.manage.editRoomSet.' + toState
				} else {
					state = 'dashboard.conferences.manage.createRoomSet.' + toState
				}
				
				$state.go(state)
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

		})

})()