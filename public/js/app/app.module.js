(function() {
	'use strict'

	angular.module('login', [])
	angular.module('forms', [])
	angular.module('createAcct', [])
	angular.module('conferenceView', [])

	angular.module('app', [
		'login',
		'forms',
		'createAcct',
		'conferenceView',
		'satellizer',
		'ui.router',
		'blockUI'
	])
})()