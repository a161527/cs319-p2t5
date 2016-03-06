(function() {
	'use strict'

	angular.module('app')
		.run(function($rootScope, $state, $window) {

			$rootScope.$on('$stateChangeStart', function(e, toState, toStateParams, fromState, fromStateParams) {
				
				var parentState = toState.name.split('.')[0]

				//dashboard is the only state that requires login
				if (parentState === 'dashboard') {

					if (!$window.localStorage['satellizer_token']) {

						e.preventDefault()
						$state.go('login')

					}
					
				} 

			})

		})

})()