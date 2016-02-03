(function() {
	'use strict'

	angular.module('login')
		.controller('loginCtrl', function($scope, loginService) {

			$scope.login = function(fieldsFilled) {

				if (fieldsFilled) {
					loginService.login('username', 'password').then(function(resData) {
						console.log(resData)
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