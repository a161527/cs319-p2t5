(function() {
	'use strict'

	angular.module('inventory')
		.controller('requestInventoryCtrl', function($scope, $stateParams, $http, $state, inventoryList, conferenceList, dependents, dataFormat, modal, ajax) {

			$scope.dependents = dataFormat.dependentsFormat(dependents.data.dependents, 'id')
			$scope.itemArray = inventoryList.data.inventory

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
								$state.go('dashboard.home') 
							})

						}, function(resData) {

							$scope.showError.message = 'Something went wrong'
							$scope.showError.value = true

						})

				} else {
					$scope.showError.message = 'No items selected'
					$scope.showError.value = true
				}

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