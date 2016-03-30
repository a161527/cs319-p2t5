(function() {
	'use strict'

	angular.module('createRoomSet')
		.factory('roomSetFields', function() {

			var _roomType = null,
				_roomTypeInfo = null,
				_roomSetInfo = null;

			return {
				setRoomType: function(t) {
					_roomType = t
				},
				getRoomType: function() {
					return _roomType
				},
				setRoomTypeInfo: function(t) {
					t.accessible = t.accessible || false
					_roomTypeInfo = t
				},
				getRoomTypeInfo: function() {
					return _roomTypeInfo
				},
				setRoomSetInfo: function(t) {
					_roomSetInfo = t
				},
				getRoomSetInfo: function() {
					return _roomSetInfo
				},
				resetAll: function() {
					_roomType = null
					_roomTypeInfo = null
					_roomSetInfo = null
				}
			}

		})

})()