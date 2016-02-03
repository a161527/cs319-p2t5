(function() {
	'use strict'

	angular.module('login', [])

	angular.module('app', [
		'login',
		'ui.router'
	])
})()