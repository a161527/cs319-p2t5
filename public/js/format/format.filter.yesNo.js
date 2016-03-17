(function() {
	'use strict'

	/*
	Filter that formats a true/false value into yes or no for display
	*/
	angular.module('format')
		.filter('yesNo', function() {

			return function(input) {
				if (input || input === '1') {
					return 'Yes'
				} else {
					return 'No'
				}
			} 
				
		})

})()