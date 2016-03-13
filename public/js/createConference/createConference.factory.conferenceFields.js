(function() {
	'use strict'

	angular.module('createConference')
		.factory('conferenceFields', function() {

			var _conferenceInfo = null
			var _inventory = null
			var _rooms = null
			var _hasRooms = false

			return {
				setConferenceInfo: function(t) {
					_conferenceInfo = t
				},
				getConferenceInfo: function() {
					return _conferenceInfo
				},
				setInventory: function(t) {
					_inventory = t
				},
				getInventory: function() {
					return _inventory
				},
				setRooms: function(t) {
					_rooms = t
				},
				getRooms: function() {
					return _rooms
				},
				setHasRooms: function(t) {
					_hasRooms = t
				},
				getHasRooms:function() {
					return _hasRooms
				},
				resetAll: function() {
					_conferenceInfo = null
					_inventory = null
					_rooms = null
					_hasRooms = false
				}
			}

		})

})()