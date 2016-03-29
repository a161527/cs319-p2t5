(function() {
	'use strict'

	angular.module('createConference')
		.service('conferenceData', function($window, $q, ajax) {

			var _conferenceData = null

			this.getConferenceInfo = function(cid) {

				return $q(function(resolve, reject) {

					if (_conferenceData) {
						resolve(_conferenceData)
					} else {

						refresh(cid).then(function(resData) {
							resolve(resData)
						}, function(resData) {
							reject(resData)
						})

					}
				})

			}

			this.refresh = function(cid) {
				return refresh(cid)
			} 

			var refresh = function(cid) {
				return $q(function(resolve, reject) {
					if (cid) {
						var _route = 'api/conferences/' + cid

						ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

							_conferenceData = resData.data
							resolve(_conferenceData)

						}, function(resData) {

							resolve(_conferenceData)

						})
					} else {
						_conferenceData = null
						resolve(_conferenceData)
					}

				})
			}

		})

})()