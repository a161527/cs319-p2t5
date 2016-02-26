(function() {
	'use strict'

	angular.module('app')
		.config(function($stateProvider, $urlRouterProvider, $authProvider) {

			$authProvider.loginUrl = '/api/login';

			$stateProvider
				.state('login', {
					url: '',
					templateUrl: 'js/login/login.view.main.html',
					controller: 'loginCtrl'
				})

				.state('creatingAccount', {
					url: '/create',
					templateUrl: 'js/createAcct/createAcct.view.html',
					controller: 'createAcctCtrl'
				})

				.state('conferenceList', {
					url: '/conferences',
					templateUrl: 'js/conferenceView/conferenceView.view.conferenceList.html',
					controller: 'conferenceListCtrl'
				})

				.state('viewConference', {
					url: '/conference/:cid',
					templateUrl: 'js/conferenceView/conferenceView.view.conferenceDetails.html',
					controller: 'conferenceDetailsCtrl',
					resolve: {
						conferenceInfo: function($stateParams, $http) {
							return $http.get('api/conferences/' + $stateParams.cid)
						}
					}
				})

		})

})()