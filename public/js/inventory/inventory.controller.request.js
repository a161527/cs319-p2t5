(function() {
	'use strict'

	angular.module('inventory')
		.controller('requestInventoryCtrl', function($scope) {

			$scope.dependents = {'1': {firstname: 'Uncle', lastname: 'Jimmy Joe'}, 
			'2':{firstname: 'Billy', lastname: 'from the Jungles of Vancouver'},
			'3':{firstname: 'Kevin', lastname: ''}
			}

			$scope.itemArray = [
				{id: 1, name: 'Uranium', quantity: 1000},
				{id: 2, name: 'Mystery Box', quantity: 1000},
				{id: 3, name: 'Socks', quantity: 1000},
				{id: 4, name: 'Something', quantity: 1000},
			]

			$scope.selected = { value: null }

			$scope.currentItems = []

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
		})

})()