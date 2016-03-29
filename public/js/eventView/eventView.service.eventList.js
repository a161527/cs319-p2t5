(function() {
	'use strict'

	angular.module('eventView')
		.service('eventList', function($window, $q, ajax) {

			var _eventList = null
			var _conferenceName = null
			var _permissions = null
			var _registration = null
			var _eventInfo = null
			var _eventPermissions = null

			this.getEventList = function(cid) {

				return $q(function(resolve, reject) {

					if (_eventList) {
						resolve(_eventList)
					} else {

						refresh(cid).then(function(resData) {
							resolve(resData)
						}, function(resData) {
							resolve(null)
						})

					}
				})

			}

			this.getPermissions = function(cid) {

				return $q(function(resolve, reject) {
					if (_permissions) {
						resolve(_permissions)
					} else {
						ajax.serviceCall('Loading...', 'get', 'api/conferences/' + cid + '?includePermissions=1').then(function(resData) {
							_permissions = resData.data.permissions
							resolve(_permissions)
						}, function(resData) {
							resolve(null)
						})
					}
				})

			}

			this.getConferenceRegistration = function(cid) {
				return $q(function(resolve, reject) {
					ajax.serviceCall('Loading...', 'get', 'api/conferences/' + cid + '?includeRegistration=1').then(function(resData) {
						_registration = resData.data.registered
						resolve(_registration)
					}, function(resData) {
						resolve(null)
					})
				})
			}

			this.getEventInfo = function(cid) {
				return $q(function(resolve, reject) {
					ajax.serviceCall('Loading...', 'get', 'api/event/conference/' + cid + '?includePermissions=1&includeRegistration=1').then(function(resData) {
						_eventInfo = resData.data
						resolve(_eventInfo)
					}, function(resData) {
						resolve(null)
					})
				})
			}

			this.refresh = function(cid) {
				return refresh(cid)
			} 

			this.clearPermissions = function() {
				_permissions = null
			}

			this.getConferenceName = function(cid) {
				return $q(function(resolve, reject) {

					ajax.serviceCall('Loading...', 'get', 'api/conferences/' + cid).then(function(resData) {

						_conferenceName = resData['data']['name']
						resolve(_conferenceName)

					}, function(resData) {

						resolve(null)

					})
				})
			}

			var refresh = function(cid) {
				return $q(function(resolve, reject) {

					var _route = 'api/event/conference/' + cid + '?includePermissions=1'

					ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

						_eventList = resData.data
						resolve(_eventList)

					}, function(resData) {

						resolve(_eventList)

					})

				})
			}

		})

})()