(function() {
	'use strict'

	angular.module('login')
		.controller('loginCtrl', function($scope, $state, ajax, errorCodes, loginStorage) {

			$scope.credentials = {}
			$scope.errorMessage = ''
			$scope.showError = false
			$scope.doResetInstead = false

			$scope.login = function() {
				$scope.showError = false

				console.log("running login")

				console.log($scope.loginForm.email.$valid)
				console.log($scope.loginForm.password.$valid)

				if ($scope.loginForm.$valid) {

					ajax.login($scope.credentials).then(function(resData) {

						loginStorage.storeCreds($scope.credentials.email, resData.data.accountID)
						$state.go('dashboard.home')

					}, function(resData) {

						$scope.showError = true
						$scope.errorMessage = errorCodes[resData.data.message]

					})

				} else {
					angular.forEach($scope.loginForm.$error.required, function(field) {
						field.$setDirty()
					})
				}
			}

			$scope.resetPassword = function() {
				$scope.showError = false
				if($scope.loginForm.email.$valid) {
					var route = 'api/resetPassword'
					var obj = {}
					obj['email'] = $scope.credentials.email
					ajax.serviceCall('Requesting Reset...', 'post', route, obj).then(function(resData) {
						$scope.showResetMessage = true
					}, function(resData) {
						$scope.showError = true
						$scope.errorMessage = "Password reset request failed."
					})
				} else {
					$scope.showError = true
					$scope.errorMessage = "A valid email is required for password reset."
				}
			}

			$scope.removeMessage = function() {
				$scope.showError = false
			}

		})

})()
