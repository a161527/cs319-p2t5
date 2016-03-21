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
					url: '/dashboard/conferences',
					templateUrl: 'js/conferenceView/conferenceView.view.conferenceList.html',
					controller: 'conferenceListCtrl',
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
					controller: 'conferenceRegistrationCtrl'
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
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.transportation.html'
				})

				.state('dashboard.conferences.registration.4', {
					url: '',
					templateUrl: 'js/conferenceRegistration/conferenceRegistration.view.flights.html'
				})


				/*
				INVENTORY
				*/
				.state('dashboard.conferences.inventoryRequest', {
					url: '/requestInventory',
					templateUrl: 'js/inventory/inventory.view.request.html',
					controller: 'requestInventoryCtrl'
				})
		})

})()