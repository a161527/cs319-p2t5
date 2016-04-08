(function () {
	'use strict'

	angular.module('editAcct')
		.controller('editAcctCtrl', function ($scope, $state, ajax) {
			$scope.showError = false
			$scope.showChangeMessage = false
			$scope.errorMessage = ""

			$scope.saveAccountData = function(account) {

				$scope.showError = false

				var obj = {}
				if (account.password && account.password.length >= 8) {
					obj['password'] = account.password
				} else if (account.password) {
					$scope.errorMessage = "Password must be at least 8 characters long"
					$scope.showError = true
					return
				}

				obj['receiveUpdates'] = account.receiveUpdates


				ajax.serviceCall('Setting data...', 'post', '/api/editAccount', obj).then(function(resData) {
					console.log("showing msg")
					$scope.showChangeMessage = true
				},
				function (resData) {
					$scope.errorMessage = "Failed to change account data"
					$scope.showError = true
				})
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

			$scope.removeChangeMessage = function() {
				$scope.showChangeMessage = false
			}
		})
})()
