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
	angular.module('rooms', [])
	angular.module('eventView', [])
	angular.module('createEvent', [])
	angular.module('dependents', [])

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
		'rooms',
		'dependents',
		'eventView',
		'createEvent',
		'ui.select',
		'satellizer',
		'ui.router',
		'blockUI',
		'ui.bootstrap'
	])
})()