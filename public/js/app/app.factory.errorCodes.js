(function() {
	'use strict'

	angular.module('app')
		.factory('errorCodes', function() {

			return {
				invalid_credentials: 'Invalid credentials',
				could_not_create_token: 'Unable to create token. Please try again'
			}

		})

})()