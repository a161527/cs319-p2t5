(function() {
	'use strict'

	angular.module('createResidence')
		.service('residenceData', function($window, $q, ajax) {

			var _residenceData = null

			this.getResidenceInfo = function(cid, rid) {

				return $q(function(resolve, reject) {

					if (_residenceData) {
						resolve(_residenceData)
					} else {

						refresh(cid, rid).then(function(resData) {
							resolve(resData)
						}, function(resData) {
							reject(resData)
						})

					}
				})

			}

			this.refresh = function(cid, rid) {
				return refresh(cid, rid)
			} 

			var refresh = function(cid, rid) {
				return $q(function(resolve, reject) {
					if (cid && rid) {
						var _route = 'api/conferences/' + cid + '/residences/' + rid

						ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

							_residenceData = resData.data
							resolve(_residenceData)

						}, function(resData) {

							resolve(_residenceData)

						})
					} else {
						_residenceData = null
						resolve(_residenceData)
					}

				})
			}

		})

})()