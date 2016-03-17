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

						loginStorage.storeEmail($scope.credentials.email)
						$state.go('dashboard')

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
			console.log(loginStorage.getAuthToken())
			$scope.removeMessage = function() {
				$scope.showError = false
			}

		})

})()