(function() {
	'use strict'

	angular.module('login')
		.controller('loginCtrl', function($scope, $state, ajax, errorCodes, loginStorage) {

			$scope.credentials = {}
			$scope.errorMessage = ''
			$scope.showError = false

			$scope.login = function(fieldsFilled) {
				$scope.showError = false

				if (fieldsFilled) {

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

			$scope.removeMessage = function() {
				$scope.showError = false
			}

		})

})()