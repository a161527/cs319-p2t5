(function() {
	'use strict'

	angular.module('createConference')
		.controller('createConferenceCtrl', function($scope, $state, conferenceFields, ajax, errorCodes) {

			$scope.conferenceInfo = conferenceFields.getConferenceInfo() || {} 
			$scope.inventory = conferenceFields.getInventory() || {'1':{}} 
			$scope.rooms = conferenceFields.getRooms() || {'1':{}}
			$scope.showError = false

			$scope.createConference = function() {
				$scope.showError = false

				// formatting request
				var conferenceInfo = $scope.conferenceInfo;
				conferenceInfo.start = conferenceInfo.startFormatted;
				delete conferenceInfo.startFormatted;
				conferenceInfo.end = conferenceInfo.endFormatted;
				delete conferenceInfo.endFormatted;

				ajax.serviceCall('Creating conference...', 'post', 'api/conferences', $scope.conferenceInfo).then(function(resData) {
					$state.go('viewConference', {cid: resData['data']['id']});


				}, function(resData) {

					$scope.showError = true
					$scope.errorMessage = errorCodes[resData.data.message]

				})
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

			$scope.addInventory = function() {
				var inventoryIndex = 1
				while($scope.inventory.hasOwnProperty(inventoryIndex)) {
					inventoryIndex += 1
				}
				$scope.inventory[inventoryIndex] = {}
			}

			$scope.deleteInventory = function(index) {
				if (index != 1) {
					if ($scope.inventory.hasOwnProperty(index)) {
						delete $scope.inventory[index]
					}
				}
			}

			$scope.addRooms = function() {
				var roomIndex = 1
				while($scope.rooms.hasOwnProperty(roomIndex)) {
					roomIndex += 1
				}
				$scope.rooms[roomIndex] = {}
			}

			$scope.deleteRooms = function(index) {
				if (index != 1) {
					if ($scope.rooms.hasOwnProperty(index)) {
						delete $scope.rooms[index]
					}
				}
			}

			$scope.showRemoveButton = function(index) {
				return index > 1
			}

			$scope.cancel = function() {
				conferenceFields.resetAll()
				$state.go('dashboard.conferences.list')
			}

			$scope.back = function(toState, set, model) {
				onNavigate(toState, set, model)
			}

			$scope.nextStep = function(form, toState, set, model) {
				if ($scope.conferenceInfo.hasAccommodations != true && model === 'inventory') {
					var skipStep = (parseInt(toState)+1).toString();
					onNavigate(skipStep, set, model)
				}
				else if (form.$valid) {
					onNavigate(toState, set, model)
				} else {
					setFormDirty(form)
				}
			}

			var setFormDirty = function(form) {
				angular.forEach(form.$error.required, function(field) {
					field.$setDirty()
				})
			}

			var onNavigate = function(toState, set, model) {
				conferenceFields[set]($scope[model])
				var state = 'dashboard.conferences.create.' + toState
				$state.go(state)
			}

			var initPopover = function() {
				$('[data-toggle="popover"]').popover({html:true});
			}

			initPopover();

		})

})()