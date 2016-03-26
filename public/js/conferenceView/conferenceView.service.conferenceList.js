(function() {
	'use strict'

	angular.module('conferenceView')
		.service('conferenceList', function($window, $q, ajax) {

			var _confList = null
			var _route = 'api/conferences?includePermissions=1&includeRegistration=1'

			this.getConferenceList = function() {

				return $q(function(resolve, reject) {

					if (_confList) {
						resolve(_confList)
					} else {

						refresh().then(function(resData) {
							resolve(resData)
						}, function(resData) {
							reject(resData)
						})

					}
				})

			}

			this.getPermissions = function(cid) {

				return $q(function(resolve, reject) {
					var route = 'api/conferences/' + cid + '?includePermissions=1&includeRegistration=1'

					ajax.serviceCall('Loading ...', 'get', route).then(function(resData) {
						var permissions = resData.data.permissions
						resolve(permissions)
					}, function(resData) {
						reject(null)
					})
				})

			}

			this.refresh = function() {
				return refresh()
			} 

			this.clearPermissions = function() {
				_confList = null
			}

			var refresh = function() {
				return $q(function(resolve, reject) {

					ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

						_confList = resData.data
						resolve(_confList)

					}, function(resData) {

						reject(resData)

					})

				})
			}

			var checkPermission = function(list, cid) {
				var permissions = null
				for (var i = 0; i < list.length; i++) {
					if (parseInt(cid) === list[i].id) {
						permissions = list[i].permissions
						break
					}
				}

				return permissions
			}

		})

})()