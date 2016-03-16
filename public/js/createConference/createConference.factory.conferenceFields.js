(function() {
	'use strict'

	angular.module('createConference')
		.factory('conferenceFields', function() {

			var _conferenceInfo = null
			var _inventory = null
			var _rooms = null

			return {
				setConferenceInfo: function(t) {
					//format dates
					t.startFormatted = moment(t.start).format('YYYY-MM-DD')
					t.endFormatted = moment(t.end).format('YYYY-MM-DD')

					//format booleans
					t.hasTransportation = t.hasTransportation || false;
					t.hasAccommodations = t.hasAccommodations || false;
					
					_conferenceInfo = t
				},
				getConferenceInfo: function() {
					return _conferenceInfo
				},
				setInventory: function(t) {
					//format boolean
					for (var key in t) {
						t[key].disposable = t[key].disposable || false;
					}

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
				resetAll: function() {
					_conferenceInfo = null
					_inventory = null
					_rooms = null
				}
			}

		})

})()