(function() {
	'use strict'

	angular.module('format')
		.service('dataFormat', function() {

			this.dateFormat = function(date) {
				return moment(date).format('YYYY-MM-DD')
			}

			this.trueFalseFormat = function(value) {
				return value? 1 : 0
			}

		})

})()