(function() {
	'use strict'

	angular.module('app')
		.factory('errorCodes', function() {

			return {
				invalid_credentials: 'Invalid credentials',
				could_not_create_token: 'Unable to create token. Please try again',
				out_of_stock: 'An item you have requested is out of stock or you have requested more than the current quantity for that item'
			}

		})

})()