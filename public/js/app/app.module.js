(function() {
	'use strict'

	angular.module('login', [])
	angular.module('forms', [])
	angular.module('createAcct', [])
	angular.module('conferenceView', [])
	angular.module('dashboard', [])
	angular.module('conferenceWidget', [])
	angular.module('createConference', [])

	angular.module('app', [
		'login',
		'forms',
		'createAcct',
		'conferenceView',
		'dashboard',
		'conferenceWidget',
		'createConference',
		'satellizer',
		'ui.router',
		'blockUI'
	])
})()