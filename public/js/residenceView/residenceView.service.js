(function() {
	'use strict'

	angular.module('residenceView')
		.service('residenceList', function($window, $q, ajax) {

			var _residenceList = null

			this.getResidenceList = function(cid) {

				return $q(function(resolve, reject) {

					if (_residenceList) {
						resolve(_residenceList)
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

					var _route = 'api/conferences/' + cid + '/residences'

					ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

						_residenceList = resData.data
						resolve(_residenceList)

					}, function(resData) {

						resolve(_residenceList)

					})

				})
			}

		})

})()