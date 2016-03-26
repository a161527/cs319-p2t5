(function() {
	'use strict'

	angular.module('createConference')
		.factory('conferenceFields', function() {

			var _conferenceInfo = null

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
				resetAll: function() {
					_conferenceInfo = null
				}
			}

		})

})()