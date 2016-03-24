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

			this.timeFormat = function(time) {
				return moment(time).format('hh:mm:ss')
			}

			this.dependentsFormat = function(dependentsList) {
				var obj = {}
				angular.forEach(dependentsList, function(dep) {
					obj[dep.userID] = dep
				})
				return obj
			}

		})

})()