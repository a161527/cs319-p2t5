(function() {
	'use strict'

	angular.module('login')
		.controller('loginCtrl', function($scope, ajax) {
			$scope.credentials = {}

			$scope.login = function(fieldsFilled) {

				if (fieldsFilled) {

					ajax.login($scope.credentials).then(function(resData) {
						alert('You did it!')
					}, function(resData) {
						console.log(resData.data.error)
					})

				} else {
					angular.forEach($scope.loginForm.$error.required, function(field) {
						field.$setDirty()
					})
				}
				// $scope.loginForm.username.$setValidity('auth', false)
			}

			$scope.resetAuthError = function(element) {
				if (element.$error.auth) {
					element.$setValidity('auth', true)
				}
			}
			
		})

})()