(function() {
	'use strict'

	angular.module('login')
		.service('loginService', function($q) {

			this.login = function(username, password) {

				return $q(function(resolve, reject) {
					resolve(true)
				})

			}

		})

})()