(function() {
	'use strict'

	angular.module('createConference')
		.controller('createConferenceCtrl', function($scope, $state, conferenceCredentials) {

			$scope.conferenceInfo = conferenceCredentials.getConferenceInfo() || {} 
			$scope.inventory = conferenceCredentials.getInventory() || {'1':{}} 
			$scope.rooms = conferenceCredentials.getRooms() || {'1':{}}
			$scope.hasRooms = conferenceCredentials.getHasRooms() || false
			$scope.triggerModal = false

			$scope.createConference = function() {
				alert('You did it!')
				conferenceCredentials.resetAll()
				var confID = 1; //conferenceCredentials['id'];
				$state.go('viewConference', {cid: confID})
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
				conferenceCredentials.resetAll()
				$state.go('dashboard.conferences')
			}

			$scope.back = function(toState, set, model) {
				onNavigate(toState, set, model)
			}

			$scope.nextStep = function(form, toState, set, model) {
				conferenceCredentials.setHasRooms($scope.hasRooms)
				if ($scope.hasRooms != true && model === 'inventory') {
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
				conferenceCredentials[set]($scope[model])
				var state = 'dashboard.conferences.create.' + toState
				$state.go(state)
			}

		})

})()