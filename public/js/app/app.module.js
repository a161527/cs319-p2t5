(function() {
	'use strict'

	angular.module('login', [])
	angular.module('forms', [])
	angular.module('createAcct', [])

	angular.module('app', [
		'login',
		'forms',
		'createAcct',
		'ui.router'
	])
})()