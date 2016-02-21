(function() {
	'use strict'

	angular.module('app')
		.config(function($stateProvider, $urlRouterProvider, $authProvider) {

			$authProvider.loginUrl = '/api/login';

			$stateProvider
				.state('preLogin', {
					url: '',
					templateUrl: 'js/login/login.view.main.html',
					controller: 'loginCtrl'
				})

				.state('loggedIn', {
					url: '/create',
					templateUrl: 'js/createAcct/createAcct.view.html',
					controller: 'createAcctCtrl'
				})

		})

})()