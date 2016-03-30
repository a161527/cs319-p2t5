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
				var getStateName = function(toState) {
					var states = toState.name.split(delimiter)
					var last = states.length - 1

					if (parseInt(states[last])) {
						return states[last-1]
					} else {
						return states[last]
					}
					
				}

				var checkGlobalPermissions = function(toState, permissions) {
					switch(getStateName(toState)) {

						case 'assignPermissions':
							var somePerms = permissions.indexOf('manage-some-permissions') !== -1
							var allPerms = permissions.indexOf('manage-global-permissions') !== -1

							if (!(somePerms || allPerms)) {
								console.log('You do not have permission')
								$state.go('dashboard.home')
							}
							break

						case 'approveAccts':
							checkHasPermission('approve-user-registration', permissions)
							break

					}
				}

				var checkConferencePermissions = function(toState, permissions) {
					switch(getStateName(toState)) {
						case 'room-allocate':
							checkHasPermission('conference-room-edit', permissions)
							break
						case 'approve-registration':
							checkHasPermission('conference-registration-approval', permissions)
							break
						case 'assign-transportation':
							checkHasPermission('conference-transportation-edit', permissions)
							break
						case 'approve-inventory':
							checkHasPermission('conference-inventory-edit', permissions)
							break
						case 'createEvent':
							checkHasPermission('conference-event-create', permissions)
							break
						case 'createResidence':
							checkHasPermission('conference-room-edit', permissions)
							break
						case 'createRoomSet':
							checkHasPermission('conference-room-edit', permissions)
							break
						case 'createInventory':
							checkHasPermission('conference-inventory-edit', permissions)
							break
						case 'createTransportation':
							checkHasPermission('conference-transportation-edit', permissions)
							break
						case 'editEvent':
							checkHasPermission('conference-event-create', permissions)
							break
						case 'editResidence':
							checkHasPermission('conference-room-edit', permissions)
							break
						case 'editRoomSet':
							checkHasPermission('conference-room-edit', permissions)
							break
						case 'editInventory':
							checkHasPermission('conference-inventory-edit', permissions)
							break
						case 'editTransportation':
							checkHasPermission('conference-transportation-edit', permissions)
							break
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
				************Start of route handling*******
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

						//Check conference permissions
						if (checkIfManageState(toState)) {
							
							//check permissions

							if (toStateParams.cid) {
								loginStorage.getConferencePermissions(toStateParams.cid).then(function(resData) {

									if (resData.length === 0) {
										$state.go('dashboard.home')
									} else {

										checkConferencePermissions(toState, resData)

									}

								}, function(resData) {
									console.log('Something went wrong')
								})
							}

						//Check global permissions
						} 

						else {
							loginStorage.getPermissions().then(function(resData) {
								checkGlobalPermissions(toState, resData)
							}, function(resData) {
								console.log('Something went wrong')
							})
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