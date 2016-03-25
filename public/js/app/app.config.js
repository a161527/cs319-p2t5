(function() {
	'use strict'

	angular.module('app')
		.config(function($stateProvider, $urlRouterProvider, $authProvider, $locationProvider) {

			$authProvider.loginUrl = '/api/login';

			$stateProvider
				.state('login', {
					url: '/',
					templateUrl: 'js/login/login.view.main.html',
					controller: 'loginCtrl'
				})

				.state('createAccount', {
					url: '/create',
					abstract: true,
					templateUrl: 'js/createAcct/createAcct.view.html',
					controller: 'createAcctCtrl'
				})

				.state('createAccount.1', {
					url: '',
					templateUrl: 'js/createAcct/createAcct.view.accountInfo.html',
					controller: 'createAcctCtrl'
				})

				.state('createAccount.2', {
					url: '',
					templateUrl: 'js/createAcct/createAcct.view.dependents.html',
					controller: 'createAcctCtrl'
				})

				.state('createAccount.3', {
					url: '',
					templateUrl: 'js/createAcct/createAcct.view.emergencyContact.html',
					controller: 'createAcctCtrl'
				})

				.state('createAccount.4', {
					url: '',
					templateUrl: 'js/createAcct/createAcct.view.reviewInfo.html',
					controller: 'createAcctCtrl'
				})

				.state('conferenceList', {
					url: '/conferences',
					templateUrl: 'js/conferenceView/conferenceView.view.conferenceList.html',
					controller: 'conferenceListCtrl',
					resolve: {
						conferenceList: function($http) {
							return $http.get('api/conferences')
						}
					}
				})

				.state('viewConference', {
					url: '/conference/:cid',
					templateUrl: 'js/conferenceView/conferenceView.view.conferenceDetails.html',
					controller: 'conferenceDetailsCtrl',
					resolve: {
						conferenceInfo: function($stateParams, $http, $q) {
							return $q.all([$http.get('api/conferences/' + $stateParams.cid), $http.get('/api/event/conference/' + $stateParams.cid)])
						}
					}
				})

				/* 
				DASHBOARD TEMPLATE
				*/
				.state('dashboard', {
					templateUrl: 'js/dashboard/dashboard.view.html',
					controller: 'dashboardCtrl'
				})

				/*
				DASHBOARD HOME
				*/
				.state('dashboard.home', {
					url: '/dashboard',
					templateUrl: 'js/dashboard/dashboard.view.home.html',
					controller: 'dashboardCtrl'
				})


				/*
				CONFERENCE LIST
				*/
				.state('dashboard.conferences', {
					url: '/conferences',
					abstract: true,
					template: '<div ui-view></div>'
				})

				.state('dashboard.conferences.list', {
					templateUrl: 'js/conferenceView/conferenceView.view.conferenceList.html',
					controller: 'conferenceListCtrl',
					url: '/list',
					resolve: {
						conferenceData: function(conferenceList, $q, loginStorage) {
							return $q.all([
								conferenceList.refresh(), 
								loginStorage.getPermissions()
							])
						}
					}
				}) 

				/*
				CONFERENCE MANAGEMENT
				*/
				.state('dashboard.conferences.manage', {
					url: '/manage/?:cid',
					templateUrl: 'js/conferenceWidget/conferenceWidget.view.html',
					controller: 'conferenceWidgetCtrl',
					resolve: {
						permissions: function(conferenceList, $stateParams) {
							return conferenceList.getPermissions($stateParams.cid)
						},
						conferenceInfo: function($http, $stateParams) {
							var conferenceInfo = $http.get('api/conferences/' + $stateParams.cid)
							return conferenceInfo
						}
					}
				})

				/*
				CONFERENCE CREATION
				*/
				.state('dashboard.conferences.create', {
					url: '/create',
					abstract: true,
					templateUrl: 'js/createConference/createConference.view.html',
					controller: 'createConferenceCtrl'
				})

				.state('dashboard.conferences.create.1', {
					url: '',
					templateUrl: 'js/createConference/createConference.view.conferenceInfo.html',
					controller: 'createConferenceCtrl'
				})

				.state('dashboard.conferences.create.2', {
					url: '',
					templateUrl: 'js/createConference/createConference.view.conferenceInventory.html',
					controller: 'createConferenceCtrl'
				})

				.state('dashboard.conferences.create.3', {
					url: '',
					templateUrl: 'js/createConference/createConference.view.conferenceRooms.html',
					controller: 'createConferenceCtrl'
				})

				.state('dashboard.conferences.create.4', {
					url: '',
					templateUrl: 'js/createConference/createConference.view.reviewInfo.html',
					controller: 'createConferenceCtrl'
				})


				/*
				CONFERENCE REGISTRATION
				*/
				.state('dashboard.conferences.registration', {
					url: '/conferenceRegistration/?:cid',
					abstract: true,
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.html',
					controller: 'conferenceRegistrationCtrl',
					resolve: {
						dependents: function($http, loginStorage) {
							return $http.get('api/accounts/' + loginStorage.getId() + '/dependents')
						}
					}
				})

				.state('dashboard.conferences.registration.1', {
					url: '',
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.selectDependents.html',
				})

				.state('dashboard.conferences.registration.2', {
					url: '',
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.accomodations.html'
				})

				.state('dashboard.conferences.registration.3', {
					url: '',
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.flights.html'
				})

				.state('dashboard.conferences.registration.4', {
					url: '',
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.review.html'
				})


				//Expects conference object
				.state('dashboard.conferences.registrationDetails', {
					url: '/details/?:cid',
					templateUrl: 'js/conferenceRegistrationDetails/conferenceRegistrationDetails.view.html',
					controller: 'conferenceRegistrationDetailsCtrl',
					params: {
						conference: null
					},
					resolve: {
						confDetails: function($stateParams, $http, $q) {
							
							//If the object is already passed, no need to make api call
							if ($stateParams.conference) {

								//match object from api call by setting data field
								return $q.resolve({data: $stateParams.conference})

							} else {
								return $http.get('api/conferences/' + $stateParams.cid + '?includePermissions=1&includeRegistration=1')
							}

						},
						regDetails: function($stateParams, $http, $q, confDetails) {

							var promises = []
							angular.forEach(confDetails.data.registered, function(reg) {
								promises.push($http.get('api/conferences/' + $stateParams.cid + '/register/' + reg.id))
							})

							return $q.all(promises)

						}

					}
				})


				/*
				INVENTORY
				*/
				.state('dashboard.conferences.inventoryRequest', {
					url: '/requestInventory/?:cid',
					templateUrl: 'js/inventory/inventory.view.request.html',
					controller: 'requestInventoryCtrl',
					resolve: {
						inventoryList: function($stateParams, $http, $q) {
							return $http.get('api/conferences/' + $stateParams.cid + '/inventory')
						},
						dependents: function($http, loginStorage) {
							return $http.get('api/accounts/' + loginStorage.getId() + '/dependents')
						}
					}
				})

				/*
				ROOMS
				*/

				.state('dashboard.conferences.room-allocate', {
					url: '/allocate-room/?:cid',
					templateUrl: 'js/rooms/rooms.view.html',
					controller: 'AllocateRoomsCtrl',
					resolve: {
						roomDependents: function($stateParams, $http) {
							return $http.get('api/conferences/' + $stateParams.cid + '/residences/assign/missing')
						}
					}
				})

				.state('dashboard.conferences.room-allocate.2', {
					url: '',
					templateUrl: 'js/rooms/rooms.view.roomList.html',
					controller: 'ResidenceSelectCtrl',
					params: {
						selectedDependents: null
					},
					resolve: {
						residences: function($stateParams, $http) {
							return $http.get('api/conferences/' + $stateParams.cid + '/residences')
						}
					}
				})

				/*
				EVENT LIST
				*/
				.state('dashboard.events', {
					url: '/dashboard/:cid/events',
					templateUrl: 'js/eventView/eventView.view.eventList.html',
					controller: 'eventListCtrl',
					resolve: {
						eventData: function(eventList, $q, loginStorage, $stateParams) {
							return $q.all([
								eventList.refresh($stateParams.cid),
								eventList.getPermissions($stateParams.cid)
							])
						},
						conferenceName: function(eventList, $stateParams) {
							return eventList.getConferenceName($stateParams.cid)
						}
					}
				})

				/*
				EVENT CREATION
				*/
				.state('dashboard.events.create', {
					url: '/create',
					templateUrl: 'js/createEvent/createEvent.view.html',
					controller: 'createEventCtrl'
				})
				
		})

})()