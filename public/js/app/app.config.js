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

				/* 
				DASHBOARD TEMPLATE
				*/
				.state('dashboard', {
					url: '/dashboard',
					templateUrl: 'js/dashboard/dashboard.view.html',
					controller: 'dashboardCtrl',
					resolve: {
						globalPermissions: function(loginStorage) {
							return loginStorage.getPermissions()
						}
					}
				})

				/*
				DASHBOARD HOME
				*/
				.state('dashboard.home', {
					url: '/home',
					templateUrl: 'js/dashboard/dashboard.view.home.html',
					controller: 'dashboardCtrl'
				})

				/*
				APPROVE ACCOUNTS
				*/
				.state('dashboard.approveAccts', {
					url: '/approve-accounts',
					templateUrl: 'js/auditAccts/auditAccts.view.approve.html',
					controller: 'approveAcctsCtrl',
					resolve: {
						unapprovedDependents: function($http) {
							return $http.get('api/unapprovedUsers')
						}
					}
				})

				/*
				ASSIGN PERMISSIONS
				*/
				.state('dashboard.assignPermissions', {
					url: '/assign-permissions',
					templateUrl: 'js/auditAccts/auditAccts.view.assign.html',
					controller: 'assignPermissionsCtrl',
					resolve: {
						roles: function($http) {
							return $http.get('/api/roles/assignable')
						}
					}
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
						},
						approvedDependents: function(getDependentsService) {
							return getDependentsService.getNumberOfApproved()
						}
					}
				}) 

				/*
				CONFERENCE MANAGEMENT
				*/
				.state('dashboard.conferences.manage', {
					url: '/manage/:cid',
					templateUrl: 'js/conferenceWidget/conferenceWidget.view.html',
					controller: 'conferenceWidgetCtrl',
					resolve: {
						permissions: function(loginStorage, $stateParams) {
							return loginStorage.getConferencePermissions($stateParams.cid)
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
					templateUrl: 'js/createConference/createConference.view.reviewInfo.html',
					controller: 'createConferenceCtrl'
				})


				/*
				CONFERENCE REGISTRATION
				*/
				.state('dashboard.conferences.registration', {
					url: '/conferenceRegistration/:cid',
					abstract: true,
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.html',
					controller: 'conferenceRegistrationCtrl',
					resolve: {
						dependents: function(getDependentsService) {
							return getDependentsService.getApproved()
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
					url: '/details/:cid',
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

						},
						inventory: function($stateParams, $http) {
							return $http.get('api/conferences/' + $stateParams.cid + '/inventory')
						}

					}
				})


				/*
				INVENTORY
				*/
				.state('dashboard.conferences.inventoryRequest', {
					url: '/requestInventory/:cid',
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

				.state('dashboard.conferences.manage.room-allocate', {
					url: '/allocate-room',
					abstract: true,
					template: '<div ui-view></div>'
				})

				.state('dashboard.conferences.manage.room-allocate.1', {
					url: '',
					templateUrl: 'js/rooms/rooms.view.html',
					controller: 'AllocateRoomsCtrl',
					resolve: {
						roomDependents: function($stateParams, getRoomUsersService) {
							return getRoomUsersService.get($stateParams.cid)
						}
					}
				})

				.state('dashboard.conferences.manage.room-allocate.2', {
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
				REGISTRATION APPROVAL
				*/
				.state('dashboard.conferences.manage.approve-registration', {
					url: '/approveRegistration',
					templateUrl: 'js/approveRegistration/approveRegistration.view.html',
					controller: 'approveRegistrationCtrl',
					resolve: {
						unapproved: function($stateParams, getUnapprovedService) {
							return getUnapprovedService.get($stateParams.cid)
						}
					}
				})

				/*
				EVENT LIST
				*/
				.state('dashboard.events', {
					url: '/:cid/events',
					templateUrl: 'js/eventView/eventView.view.eventList.html',
					controller: 'eventListCtrl',
					resolve: {
						eventData: function(eventList, $q, $stateParams) {
							return $q.all([
								eventList.refresh($stateParams.cid),
								eventList.getPermissions($stateParams.cid),
								eventList.getConferenceRegistration($stateParams.cid),
								eventList.getEventInfo($stateParams.cid)
							])
						},
						conferenceName: function(eventList, $stateParams) {
							return eventList.getConferenceName($stateParams.cid)
						}
					}
				})

				/*
				EVENT REGISTRATION
				*/
				.state('dashboard.events.register', {
					url: '/register/:eid',
					templateUrl: 'js/eventRegistration/eventRegistration.view.html',
					controller: 'eventRegistrationCtrl',
					resolve: {
						conferenceData: function($stateParams, $http) {
							return $http.get('api/conferences/' + $stateParams.cid + '?includeRegistration=1')
						},
						dependents: function($http, loginStorage) {
							return $http.get('api/accounts/' + loginStorage.getId() + '/dependents')
						},
						eventData: function($http, $stateParams) {
							return $http.get('api/event/' + $stateParams.eid + '?includeRegistration=1')
						}
					}
				})

				/*
				CREATE EVENT
				*/
				.state('dashboard.conferences.manage.createEvent', {
					url: '/event/create',
					templateUrl: 'js/createEvent/createEvent.view.html',
					controller: 'createEventCtrl'
				})

				/*
				CREATE INVENTORY
				*/
				.state('dashboard.conferences.manage.createInventory', {
					url: '/inventory/create',
					templateUrl: 'js/createInventory/createInventory.view.html',
					controller: 'createInventoryCtrl'
				})

				/*
				CREATE RESIDENCE
				*/
				.state('dashboard.conferences.manage.createResidence', {
					url: '/residence/create',
					templateUrl: 'js/createResidence/createResidence.view.html',
					controller: 'createResidenceCtrl'
				})

				/*
				CREATE ROOM SET
				*/
				.state('dashboard.conferences.manage.createRoomSet', {
					url: '/residence/:rid/roomset/create',
					abstract: true,
					templateUrl: 'js/createRoomSet/createRoomSet.view.html',
					controller: 'createRoomSetCtrl',
					resolve: {
						roomTypes: function($http, $stateParams) {
							return $http.get('/api/conferences/' + $stateParams.cid + '/residences/' + $stateParams.rid + '/roomTypes')
						},
						residenceName: function($http, $stateParams) {
							return "TODO"
						}
					}
				})
				

				.state('dashboard.conferences.manage.createRoomSet.1', {
					url: '',
					templateUrl: 'js/createRoomSet/createRoomSet.view.roomType.html',
					controller: 'createRoomSetCtrl'
				})

				.state('dashboard.conferences.manage.createRoomSet.2', {
					url: '',
					templateUrl: 'js/createRoomSet/createRoomSet.view.roomTypeInfo.html',
					controller: 'createRoomSetCtrl'
				})

				.state('dashboard.conferences.manage.createRoomSet.3', {
					url: '',
					templateUrl: 'js/createRoomSet/createRoomSet.view.roomSetInfo.html',
					controller: 'createRoomSetCtrl'
				})

				.state('dashboard.conferences.manage.createRoomSet.4', {
					url: '',
					templateUrl: 'js/createRoomSet/createRoomSet.view.reviewInfo.html',
					controller: 'createRoomSetCtrl'
				})

				/*
				CREATE TRANSPORTATION
				*/
				.state('dashboard.conferences.manage.createTransportation', {
					url: '/transportation/create',
					templateUrl: 'js/createTransportation/createTransportation.view.html',
					controller: 'createTransportationCtrl'
				})

				/*
				RESIDENCE LIST
				*/
				.state('dashboard.conferences.manage.viewResidence', {
					url: '/residences',
					templateUrl: 'js/residenceView/residenceView.view.html',
					controller: 'residenceListCtrl',
					resolve: {
						residenceData: function(residenceList, $q, $stateParams) {
							return $q.all([
								residenceList.refresh($stateParams.cid)
							])
						}
					}
				})

				/*
				ROOM SET LIST
				*/
				.state('dashboard.conferences.manage.viewResidence.viewRoomSet', {
					url: '/:rid/roomsets',
					templateUrl: 'js/roomSetView/roomSetView.view.html',
					controller: 'roomSetListCtrl',
					resolve: {
						roomSetData: function(roomSetList, $q, $stateParams) {
							return $q.all([
								roomSetList.refresh($stateParams.cid, $stateParams.rid)
							])
						},
						roomTypes: function($http, $stateParams) {
							return $http.get('/api/conferences/' + $stateParams.cid + '/residences/' + $stateParams.rid + '/roomTypes')
						}
					}
				})

				/*
				INVENTORY LIST
				*/
				.state('dashboard.conferences.manage.viewInventory', {
					url: '/inventory',
					templateUrl: 'js/inventoryView/inventoryView.view.html',
					controller: 'inventoryListCtrl',
					resolve: {
						inventoryData: function(inventoryList, $q, $stateParams) {
							return $q.all([
								inventoryList.refresh($stateParams.cid)
							])
						}
					}
				})
				
				/*
				TRANSPORTATION LIST
				*/
				.state('dashboard.conferences.manage.viewTransportation', {
					url: '/transportation',
					templateUrl: 'js/transportationView/transportationView.view.html',
					controller: 'transportationListCtrl',
					resolve: {
						transportationData: function(transportationList, $q, $stateParams) {
							return $q.all([
								transportationList.refresh($stateParams.cid)
							])
						}
					}
				})
		})

})()