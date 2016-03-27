(function() {
	'use strict'

	angular.module('auditAccts')
		.controller('assignPermissionsCtrl', function($scope, $state, $q, ajax, roles, loginStorage, modal) {

			$scope.validEmail = {value: true, message: 'Account does not exist'}
			$scope.availableRoles = roles.data
 			$scope.assignedRoles = {value: []}

 			$scope.showPermissions = false
 			$scope.showPermChange = {value: false}

 			$scope.add = []
 			$scope.remove = []

 			var origRoles = []


 			//Check if email exists and load roles for the user if true
			$scope.checkEmailExists = function(email) {

				//disallow editing of own permissions
				if (email === loginStorage.getEmail()) {
					$scope.showPermissions = false
					$scope.validEmail.value = false
					$scope.validEmail.message = 'You cannot edit your own permissions'
				}

				//only continue if email is not an empty string
				else if (email) {

					ajax.serviceCall('Checking email...', 'post', 'api/checkemail', {'email': email}).then(function(resData) {

						if (resData.data.taken) {



							$scope.validEmail.value = true
							$scope.showPermissions = true

							var route = '/api/roles/account/' + email
							return ajax.serviceCall('Retrieving permissions...', 'get', route)

						//resolve with an empty value, reset all values
						} else {

							$scope.validEmail.message = 'Account does not exist'
							$scope.validEmail.value = false
							$scope.showPermissions = false
							
							return $q.when(null)
						}
					
					}).then(function(resData) {

						//reload the assigned roles for the user
						reloadRoles(resData)

					}).catch(function(resData) {
						console.log(resData)
					})

				}

			}

			$scope.submit = function() {
				if ($scope.showPermChange.value) {
					formatData()

					var email = $scope.email
					var params = {add: $scope.add, remove: $scope.remove}
					var route = '/api/roles/account/' + $scope.email

					ajax.serviceCall('Updating permissions...', 'patch', route, params).then(function(resData) {

						modal.open('Permissions assigned', function() {
							ajax.serviceCall('Retrieving permissions...', 'get', route).then(function(resData) {
								reloadRoles(resData)
							}, function(resData) {
								console.log(resData)
							})
						})

					}, function(resData) {
						console.log(resData)
					})
				}	
			}

			//helper function for submit
			//modifies $scope.add and $scope.remove for the api call
			var formatData = function() {

				//If role is not in the original array of roles, it is added, push it to 'add'
				angular.forEach($scope.assignedRoles.value, function(role) {

					if (origRoles.indexOf(role.name) === -1) {
						$scope.add.push(role.name)
					}

				})
				
				//push names of the permission objects for indexing
				var assignedRoleNames = []
				pushRoleNames($scope.assignedRoles.value, assignedRoleNames)

				//Check if the original roles have been removed, if so, add to 'remove'
				angular.forEach(origRoles, function(origRole) {

					if (assignedRoleNames.indexOf(origRole) === -1) {
						$scope.remove.push(origRole)
					}

				})

			}

			var pushRoleNames = function(roles, array) {
				angular.forEach(roles, function(role) {
					array.push(role.name)
				})
			}

			var reloadRoles = function(resData) {
				if (resData) {

					$scope.showPermChange.value = false
					origRoles = []

					$scope.assignedRoles.value = resData.data
					pushRoleNames(resData.data, origRoles)

				} else {

					$scope.assignedRoles.value = []
					origRoles = []

				}
			}

		})

})()