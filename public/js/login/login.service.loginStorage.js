(function() {
	'use strict'

	angular.module('login')
		.service('loginStorage', function($window, $q, ajax, conferenceList) {

			this.credKey = 'gobind_sarvar'
			this.tokenKey = 'satellizer_token'
			var _permissions = null
			var _confPerm = null

			this.storeCreds = function(email, id) {
				$window.localStorage.setItem(this.credKey, JSON.stringify({email: email, id: id}))
			}

			this.logout = function() {
				$window.localStorage.removeItem(this.tokenKey)
				$window.localStorage.removeItem(this.credKey)
				conferenceList.clearPermissions()
				_permissions = null
				_confPerm = null
			}

			this.getCreds = function() {
				return JSON.parse($window.localStorage.getItem(this.credKey))
			}

			this.getAuthToken = function() {
				return $window.localStorage.getItem(this.tokenKey)
			}

			this.getEmail = function() {
				return JSON.parse($window.localStorage.getItem(this.credKey)).email
			}

			this.getId = function() {
				return JSON.parse($window.localStorage.getItem(this.credKey)).id
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

			this.getConferencePermissions = function(cid) {

				return $q(function(resolve, reject) {

					if (_confPerm && _confPerm.cid === cid && _confPerm.permissions) {

						resolve(_confPerm.permissions)

					} else {
						var route = 'api/conferences/' + cid + '?includePermissions=1'

						ajax.serviceCall('Loading ...', 'get', route).then(function(resData) {
							var permissions = resData.data.permissions

							_confPerm = {cid: cid, permissions: permissions}

							resolve(permissions)

						}, function(resData) {
							reject(resData)
						})

					}

				})

			}


		})

})()