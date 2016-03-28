(function() {
	'use strict'

	angular.module('roomSetView')
		.service('roomSetList', function($window, $q, ajax) {

			var _roomSetList = null

			this.getRoomSetList = function(cid, rid) {

				return $q(function(resolve, reject) {

					if (_roomSetList) {
						resolve(_roomSetList)
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

			var refresh = function(cid,rid) {
				return $q(function(resolve, reject) {

					var _route = 'api/conferences/' + cid + '/residences/' + rid + '/roomSets'

					ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

						_roomSetList = resData.data
						resolve(_roomSetList)

					}, function(resData) {

						resolve(_roomSetList)

					})

				})
			}

		})

})()