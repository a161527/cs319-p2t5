(function() {
	'use strict'

	angular.module('transportationView')
		.service('transportationList', function($window, $q, ajax) {

			var _transportationList = null

			this.getTransportationList = function(cid) {

				return $q(function(resolve, reject) {

					if (_transportationList) {
						resolve(_transportationList)
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

					var _route = 'api/conferences/' + cid + '/transportation'

					ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

						_transportationList = resData.data
						resolve(_transportationList)

					}, function(resData) {

						resolve(_transportationList)

					})

				})
			}

		})

})()