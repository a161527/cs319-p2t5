(function() {
	'use strict'

	angular.module('app')
		.run(function($rootScope, $state, loginStorage) {

			$rootScope.$on('$stateChangeStart', function(e, toState, toStateParams, fromState, fromStateParams) {
				
				var delimiter = '.'
				var parentState = toState.name.split(delimiter)[0]	

				//Return true if in a conference manage state
				var checkIfManageState = function(toState) {
					var states = toState.name.split(delimiter)
					return states.indexOf('manage') !== -1
				}

				//returns the name of the management widget
				//if the last result of the split is an number, then it is a multistep widget
				//in this case, the state name is the result prior to that
				var getManageStateName = function(toState) {
					var states = toState.name.split(delimiter)
					var last = states.length - 1

					if (parseInt(states[last])) {
						return states[last-1]
					} else {
						return states[last]
					}
					
				}

				var checkConferencePermissions = function(toState, permissions) {
					switch(getManageStateName(toState)) {
						case 'room-allocate':
							checkHasPermission('conference-room-edit', permissions)
							break
						case 'approve-registration':
							checkHasPermission('conference-registration-approval', permissions)
					}
				}

				var checkHasPermission = function(permName, permissions) {
					if (permissions.indexOf(permName) === -1) {
						//permission does not exist, redirect to current state
						console.log('You do not have permission')
						$state.go('dashboard.home')
					}
				}

				/*
				Start of route handling
				*/

				//dashboard is the only state that requires login
				if (parentState === 'dashboard') {

					if (!loginStorage.getAuthToken() || !loginStorage.getCreds()) {

						e.preventDefault()
						loginStorage.logout()
						$state.go('login')

					} 

					/*
					Successfully logged in, check conference permissions
					comment out for development purposes if necessary
					*/

					else {

						if (checkIfManageState(toState)) {
							
							//check permissions

							if (toStateParams.cid) {
								loginStorage.getConferencePermissions(toStateParams.cid).then(function(resData) {
									console.log(resData)

									if (resData.length === 0) {
										$state.go('dashboard.home')
									} else {

										checkConferencePermissions(toState, resData)

									}

								}, function(resData) {
									
									console.log('Something went wrong')

								})
							}

						}

					}
					//end of check conference permissions
					
				//if credentials are valid, go to dashboard
				//No need to check permissions here, since it is only going to home
				} else if (parentState === 'login') {

					if (loginStorage.getAuthToken() && loginStorage.getCreds()) {

						e.preventDefault()
						$state.go('dashboard.home')

					} 

				}

			})

		})

})()