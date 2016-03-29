(function() {
	'use strict'

	angular.module('createRoomSet')
		.service('roomSetData', function($window, $q, ajax) {

			var _roomSetData = null
			var _resName = null
			var _roomTypes = null

			this.getRoomSetInfo = function(cid, rsid) {

				return $q(function(resolve, reject) {

					if (_roomSetData) {
						resolve(_roomSetData)
					} else {

						refresh(cid, rsid).then(function(resData) {
							resolve(resData)
						}, function(resData) {
							reject(resData)
						})

					}
				})

			}

			this.getResidenceName = function(cid, rid) {
				return $q(function(resolve, reject) {
					ajax.serviceCall('Loading...', 'get', 'api/conferences/' + cid + '/residences/' + rid).then(function(resData) {

						_resName = resData.data.name
						resolve(_resName)

					}, function(resData) {

						reject(_resName)

					})
				})
			}

			this.getRoomTypes = function(cid, rid) {
				return $q(function(resolve, reject) {
					ajax.serviceCall('Loading...', 'get', '/api/conferences/' + cid + '/residences/' + rid + '/roomTypes').then(function(resData) {

						_roomTypes = resData.data
						_roomTypes.push({'id': 'newRoomType', 'name': 'Add new room type', 'capacity': null, 'accessible': null})
						resolve(_roomTypes)

					}, function(resData) {

						_roomTypes = []
						resolve(_roomTypes)

					})
				})
			}

			this.refresh = function(cid, rsid) {
				return refresh(cid, rsid)
			} 

			var refresh = function(cid, rsid) {
				return $q(function(resolve, reject) {
					if (cid && rsid) {
						var _route = 'api/conferences/' + cid + '/residences/roomSets/' + rsid

						ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

							_roomSetData = resData.data
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