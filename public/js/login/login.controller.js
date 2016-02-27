(function() {
	'use strict'

	angular.module('login')
		.controller('loginCtrl', function($scope, ajax, errorCodes) {

			$scope.credentials = {}
			$scope.errorMessage = ''
			$scope.showError = false

			$scope.login = function(fieldsFilled) {
				$scope.showError = false

				if (fieldsFilled) {

					ajax.login($scope.credentials).then(function(resData) {

						alert('You did it!')

					}, function(resData) {

						$scope.showError = true
						$scope.errorMessage = errorCodes[resData.data.error]

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