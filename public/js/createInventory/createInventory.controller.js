(function() {
	'use strict'

	angular.module('createInventory')
		.controller('createInventoryCtrl', function($scope, $state, $stateParams, ajax, errorCodes, conferenceInfo, inventoryData) {

			$scope.inventory = {'1':{}} 
			$scope.showError = false
			$scope.conferenceName = conferenceInfo.data.name

			if (inventoryData[0]) {
				$scope.inv = inventoryData[0]
				$scope.inventory['1'] = inventoryData[0]
				$scope.editMode = true

			} else {

				$scope.editMode = false

			}
			console.log($scope.inventory)

			$scope.createInventory = function(form) {
				$scope.showError = false

				if (form.$valid) {
					var inventoryInfo = []

					angular.forEach($scope.inventory, function(inventory) {
						var inv = {}
						inv.itemName = inventory.itemName
						inv.totalQuantity = inventory.quantity
						inv.disposable = inventory.disposable || false
						inv.conferenceID = $stateParams.cid

						inventoryInfo.push(inv)
					})

					if ($scope.editMode) {
						ajax.serviceCall('Updating inventory...', 'patch', 'api/conferences/' + $stateParams.cid + '/inventory/' + $stateParams.iid, inventoryInfo[0]).then(function(resData) {

							$state.go('dashboard.conferences.manage.viewInventory', {'cid': $stateParams.cid}, {reload: true})

						}, function(resData) {

							$scope.showError = true
							$scope.errorMessage = errorCodes[resData.data.message]

						})
					} else {
						ajax.serviceCall('Creating inventory...', 'post', 'api/conferences/' + $stateParams.cid + '/inventory', inventoryInfo).then(function(resData) {

							$state.go('dashboard.conferences.manage.viewInventory', {'cid': $stateParams.cid}, {reload: true})

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
				$state.go('dashboard.conferences.manage.viewInventory', {'cid': $stateParams.cid}, {reload: true})
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