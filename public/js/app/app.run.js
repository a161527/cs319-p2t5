(function() {
	'use strict'

	angular.module('app')
		.run(function($rootScope, $state, loginStorage) {

			$rootScope.$on('$stateChangeStart', function(e, toState, toStateParams, fromState, fromStateParams) {
				
				var parentState = toState.name.split('.')[0]

				//dashboard is the only state that requires login
				if (parentState === 'dashboard') {

					if (!loginStorage.getAuthToken() || !loginStorage.getCreds()) {

						e.preventDefault()
						loginStorage.logout()
						$state.go('login')

					}
					
				//if credentials are valid, go to dashboard
				} else if (parentState === 'login') {

					if (loginStorage.getAuthToken() && loginStorage.getCreds()) {

						e.preventDefault()
						$state.go('dashboard')

					} 

				}

			})

		})

})()