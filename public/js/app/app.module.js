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
	angular.module('assignTransportation', [])
	angular.module('eventView', [])
	angular.module('createEvent', [])
	angular.module('dependents', [])
	angular.module('auditAccts', [])
	angular.module('approveRegistration', [])
	angular.module('eventRegistration', [])


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
		'assignTransportation',
		'createEvent',
		'eventRegistration',
		'auditAccts',
		'ui.select',
		'satellizer',
		'ui.router',
		'blockUI',
		'ui.bootstrap',
		'approveRegistration'
	])
})()