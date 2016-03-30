(function() {
	'use strict'

	angular.module('createEvent')
		.service('eventData', function($window, $q, ajax) {

			var _eventData = null

			this.getEventInfo = function(eid) {

				return $q(function(resolve, reject) {

					if (_eventData) {
						resolve(_eventData)
					} else {

						refresh(eid).then(function(resData) {
							resolve(resData)
						}, function(resData) {
							reject(resData)
						})

					}
				})

			}

			this.refresh = function(eid) {
				return refresh(eid)
			} 

			var refresh = function(eid) {
				return $q(function(resolve, reject) {
					if (eid) {
						var _route = 'api/event/' + eid

						ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

							_eventData = resData.data
							resolve(_eventData)

						}, function(resData) {

							resolve(_eventData)

						})
					} else {
						_eventData = null
						resolve(_eventData)
					}

				})
			}

		})

})()