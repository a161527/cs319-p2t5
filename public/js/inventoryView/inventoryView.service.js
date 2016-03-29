(function() {
	'use strict'

	angular.module('inventoryView')
		.service('inventoryList', function($window, $q, ajax) {

			var _inventoryList = null

			this.getInventoryList = function(cid) {

				return $q(function(resolve, reject) {

					if (_inventoryList) {
						resolve(_inventoryList)
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

					var _route = 'api/conferences/' + cid + '/inventory'

					ajax.serviceCall('Loading...', 'get', _route).then(function(resData) {

						_inventoryList = resData.data
						resolve(_inventoryList)

					}, function(resData) {

						resolve(_inventoryList)

					})

				})
			}

		})

})()