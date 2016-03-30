(function() {
	'use strict'

	angular.module('createTransportation')
		.service('transportationData', function($window, $q, ajax) {

			var _transportationData = null

			this.getTransportationInfo = function(cid, tid) {

				return $q(function(resolve, reject) {

					if (_transportationData) {
						resolve(_transportationData)
					} else {

						refresh(cid, tid).then(function(resData) {
							resolve(resData)
						}, function(resData) {
							reject(resData)
						})

					}
				})

			}

			this.refresh = function(cid, tid) {
				return refresh(cid, tid)
			} 

			var refresh = function(cid, tid) {
				return $q(function(resolve, reject) {
					if (cid && tid) {
						var _route = 'api/conferences/' + cid + '/transportation/' + tid

						ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

							_transportationData = resData.data.transportation
							resolve(_transportationData)

						}, function(resData) {

							resolve(_transportationData)

						})
					} else {
						_transportationData = null
						resolve(_transportationData)
					}

				})
			}

		})

})()