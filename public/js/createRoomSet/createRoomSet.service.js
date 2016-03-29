(function() {
	'use strict'

	angular.module('createRoomSet')
		.service('roomSetData', function($window, $q, ajax) {

			var _roomSetData = null

			this.getRoomSetInfo = function(cid, rid) {

				return $q(function(resolve, reject) {

					if (_roomSetData) {
						resolve(_roomSetData)
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
						var _route = 'api/conferences/' + cid + '/roomSets/' + rid

						ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

							_roomSetData = resData.data.roomSet
							resolve(_roomSetData)

						}, function(resData) {

							resolve(_roomSetData)

						})
					} else {
						_roomSetData = null
						resolve(_roomSetData)
					}

				})
			}

		})

})()