(function () {
	'use strict'

	angular.module('editAcct')
		.controller('editAcctCtrl', function ($scope, $state, ajax) {
			if($location.search()['token']) {
				console.log("Got token")
				ajax.loginWithToken($location.search()['token'])
				ajax.serviceCall("Getting login info", 'get', '/api/login').then(function(resData) {
					loginStorage.storeCreds(resData.data.email, resData.data.accountID)
					$state.go('dashboard.home')
				}, function(resData) {
					$scope.showError = true
					$scope.errorMessage = "Your token doesn't seem to work."
				})
				return
			}
			$scope.showError = false
			$scope.showChangeMessage = false
			$scope.errorMessage = ""
			$scope.account = {}

			ajax.serviceCall('Loading...', 'get', '/api/receiveUpdates').then(
				function(resData) {
					//Need to convert to boolean so do this
					$scope.account.receiveUpdates = resData.data.receiveUpdates ? true : false
				},
				function(resData){})

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
