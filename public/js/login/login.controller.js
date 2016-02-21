(function() {
	'use strict'

	angular.module('login')
		.controller('loginCtrl', function($scope, $http, $auth) {

			$scope.credentials = {}

			$scope.login = function(fieldsFilled) {

				if (fieldsFilled) {
					$auth.login($scope.credentials).then(function(resData) {
						console.log(resData)
					}, function(resData) {
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