(function() {
	'use strict'

	angular.module('inventory')
		.controller('requestInventoryCtrl', function($scope, $stateParams, $http, $state, inventoryList, registered, errorCodes, dependents, dataFormat, modal, ajax) {

			var getConferenceApprovedUsers = function(dependents, registered) {
				var registeredObj = dataFormat.dependentsFormat(registered.data.registered, 'user')
				var regDep = []
				angular.forEach(dependents.data.dependents, function(dep) {
					if (registeredObj[dep.id] && registeredObj[dep.id].status === 'approved') {
						regDep.push(dep)
					}
				})
				return dataFormat.dependentsFormat(regDep, 'id')
			}

			$scope.dependents = getConferenceApprovedUsers(dependents, registered)
			$scope.itemArray = []

			angular.forEach(inventoryList.data.inventory, function(inv) {
				if (inv.currentQuantity > 0) {
					$scope.itemArray.push(inv)
				}
			})

			$scope.selected = { value: null }

			$scope.currentItems = []
			$scope.formattedData = []

			$scope.showError = {value: false, message: ''}

			$scope.submit = function() {
				$scope.formattedData = formatData()

				if ($scope.formattedData.length !== 0) {

					var route = 'api/conferences/' + $stateParams.cid+ '/inventory/reserve'
						ajax.serviceCall('Requesting Inventory...', 'post', route, $scope.formattedData).then(function(resData) {

							modal.open('Inventory requested', function() {
								$state.go('dashboard.conferences.registrationDetails', {cid: $stateParams.cid}) 
							})

						}, function(resData) {

							$scope.showError.message = errorCodes[resData.data.message]
							$scope.showError.value = true

						})

				} else {
					$scope.showError.message = 'No items added'
					$scope.showError.value = true
				}

			}

			$scope.cancel = function() {
				$state.go('dashboard.conferences.registrationDetails', {cid: $stateParams.cid}) 
			}

			$scope.removeError = function() {
				$scope.showError.value = false
			}

			$scope.addItem = function(item) {
				if (item) {
					for (var i = 0; i < $scope.itemArray.length; i++) {
						if ($scope.itemArray[i].id === item.id) {
							$scope.itemArray.splice(i, 1);
						}
					}
					$scope.currentItems.push(item)
					$scope.selected = { value: null }
				}
			}

			$scope.removeItem = function(item) {
				if (item) {
					for (var i = 0; i < $scope.currentItems.length; i++) {
						if ($scope.currentItems[i].id === item.id) {
							$scope.currentItems.splice(i, 1);
						}
					}
					$scope.itemArray.push(item)

					for (i in $scope.dependents) {
						delete $scope.dependents[i][item.name]
					}
				}
			}

			var formatData = function() {
				var items = []

				angular.forEach($scope.currentItems, function(item) {

					angular.forEach($scope.dependents, function(dep) {

						if (dep[item.itemName] && dep[item.itemName] > 0) {

							var currItem = {}
							currItem.id = item.id
							currItem.quantity = dep[item.itemName]
							currItem.dependentID = dep.id

							items.push(currItem)
						}
					})

				})

				return items
			}

		})

})()