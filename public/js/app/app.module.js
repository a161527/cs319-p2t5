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
	angular.module('createInventory', [])
	angular.module('createResidence', [])
	angular.module('createTransportation', [])
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
		'createEvent',
		'createInventory',
		'createResidence',
		'createTransportation',
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