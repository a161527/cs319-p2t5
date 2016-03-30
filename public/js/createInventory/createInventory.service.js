(function() {
	'use strict'

	angular.module('createInventory')
		.service('inventoryData', function($window, $q, ajax) {

			var _inventoryData = null

			this.getInventoryInfo = function(cid, iid) {

				return $q(function(resolve, reject) {

					if (_inventoryData) {
						resolve(_inventoryData)
					} else {

						refresh(cid, iid).then(function(resData) {
							resolve(resData)
						}, function(resData) {
							reject(resData)
						})

					}
				})

			}

			this.refresh = function(cid, iid) {
				return refresh(cid, iid)
			} 

			var refresh = function(cid, iid) {
				return $q(function(resolve, reject) {
					if (cid && iid) {
						var _route = 'api/conferences/' + cid + '/inventory/' + iid
						ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

							_inventoryData = resData.data
							resolve(_inventoryData)

						}, function(resData) {

							resolve(_inventoryData)

						})
					} else {
						_inventoryData = null
						resolve(_inventoryData)
					}

				})
			}

		})

})()