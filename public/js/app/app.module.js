(function() {
	'use strict'

	angular.module('login', [])
	angular.module('forms', [])
	angular.module('createAcct', [])
	angular.module('conferenceView', [])
	angular.module('dashboard', [])
	angular.module('conferenceWidget', [])
	angular.module('createConference', [])
	angular.module('format', [])
	angular.module('conferenceRegistration', [])
	angular.module('inventory', [])

	angular.module('app', [
		'format',
		'login',
		'forms',
		'createAcct',
		'conferenceView',
		'conferenceRegistration',
		'dashboard',
		'conferenceWidget',
		'createConference',
		'inventory',
		'ui.select',
		'satellizer',
		'ui.router',
		'blockUI',
		'ui.bootstrap'
	])
})()