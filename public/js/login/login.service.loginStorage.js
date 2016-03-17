(function() {
	'use strict'

	angular.module('login')
		.service('loginStorage', function($window) {

			this.emailKey = 'gobind_sarvar_email'
			this.tokenKey = 'satellizer_token'

			this.storeEmail = function(email) {
				$window.localStorage.setItem(this.emailKey, email)
			}

			this.removeTokens = function() {
				$window.localStorage.removeItem(this.tokenKey)
				$window.localStorage.removeItem(this.emailKey)
			}

			this.getAuthToken = function() {
				return $window.localStorage.getItem(this.tokenKey)
			}

			this.getEmail = function() {
				return $window.localStorage.getItem(this.emailKey)
			}

		})

})()