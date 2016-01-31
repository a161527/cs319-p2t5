(function() {
	'use strict'

	angular.module('login')
		.controller('loginCtrl', function($scope, loginService) {

			loginService.login('username', 'password').then(function(resData) {
				console.log(resData)
			})

		})

})()