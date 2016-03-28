(function() {
	'use strict'

	angular.module('createRoomSet')
		.controller('createRoomSetCtrl', function($scope, $state, $stateParams, roomSetFields, ajax, errorCodes, roomTypes) {

			$scope.showError = false
			$scope.cid = $stateParams.cid
			$scope.rid = $stateParams.rid
			$scope.roomTypes = roomTypes.data
			$scope.hasRoomTypes = (roomTypes.data.length > 0)
			$scope.roomType = {}
			$scope.roomType = roomSetFields.getRoomType() || {}
			$scope.roomTypeInfo = roomSetFields.getRoomTypeInfo() || {}
			$scope.roomSetInfo = roomSetFields.getRoomSetInfo() || {'1':{}}

			if (!$scope.roomType.id) {
				$scope.roomType.id = 'newRoomType'
			}

			$scope.addNewType = false
			if ($scope.roomType.id !== 'newRoomType') {
				for (var i = roomTypes.data.length - 1; i >= 0; i--) {
					if (roomTypes.data[i].id.toString() === $scope.roomType.id) {
						$scope.roomType.name = roomTypes.data[i].name
						$scope.roomType.capacity = roomTypes.data[i].capacity
						$scope.roomType.accessible = roomTypes.data[i].accessible
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

				ajax.serviceCall('Creating room sets...', 'post', 'api/conferences/' + $stateParams.cid + '/residences/' + $stateParams.rid + '/roomSets', roomSetInfo).then(function(resData) {

					roomSetFields.resetAll()
					$state.go('dashboard.conferences.manage.viewResidence.viewRoomSet', {'cid': $stateParams.cid, 'rid': $stateParams.rid}, {reload: true})

				}, function(resData) {

					$scope.showError = true
					$scope.errorMessage = errorCodes[resData.data.message]

				})
				
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

			var onNavigate = function(toState, set, model) {
				roomSetFields[set]($scope[model])
				var state = 'dashboard.conferences.manage.createRoomSet.' + toState
				$state.go(state)
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

		})

})()