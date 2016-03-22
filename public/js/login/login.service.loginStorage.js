(function() {
	'use strict'

	angular.module('login')
		.service('loginStorage', function($window, $q, ajax, conferenceList) {

			this.emailKey = 'gobind_sarvar_email'
			this.tokenKey = 'satellizer_token'
			var _permissions = null

			this.storeEmail = function(email) {
				$window.localStorage.setItem(this.emailKey, email)
			}

			this.logout = function() {
				$window.localStorage.removeItem(this.tokenKey)
				$window.localStorage.removeItem(this.emailKey)
				conferenceList.clearPermissions()
				_permissions = null
			}

			this.getAuthToken = function() {
				return $window.localStorage.getItem(this.tokenKey)
			}

			this.getEmail = function() {
				return $window.localStorage.getItem(this.emailKey)
			}

			this.getPermissions = function() {
				return $q(function(resolve, reject) {
					if (_permissions) {

						resolve(_permissions)

					} else {

						ajax.serviceCall('Loading...', 'get', 'api/permissions').then(function(resData) {

							_permissions = resData.data
							resolve(_permissions)

						}, function(resData) {

							reject(resData)

						})

					}
				})
			}


		})

})()