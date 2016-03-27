(function() {
	'use strict'

	angular.module('auditAccts')
		.controller('assignPermissionsCtrl', function($scope, $state, $q, ajax, roles) {

			$scope.emailExists = false
			$scope.availableRoles = roles.data
 			$scope.assignedRoles = {value: []}

 			$scope.add = []
 			$scope.remove = []

 			var origRoles = []

			$scope.checkEmailExists = function(email) {
				ajax.serviceCall('Checking email...', 'post', 'api/checkemail', {'email': email}).then(function(resData) {
					$scope.emailExists = resData.data.taken? true : false

					if ($scope.emailExists) {
						var route = '/api/roles/account/' + email
						return ajax.serviceCall('Retrieving permissions...', 'get', route)
					} else {
						return $q.when(null)
					}
					

				}).then(function(resData) {

					reloadRoles(resData)

				}).catch(function(resData) {
					console.log(resData)
				})
			}

			$scope.submit = function() {
				formatData()

				var email = $scope.email
				var params = {add: $scope.add, remove: $scope.remove}
				var route = '/api/roles/account/' + $scope.email

				ajax.serviceCall('Updating permissions...', 'patch', route, params).then(function(resData) {

					$state.reload()

				})

			}

			var formatData = function() {

				angular.forEach($scope.assignedRoles.value, function(role) {

					if (origRoles.indexOf(role.name) === -1) {
						$scope.add.push(role.name)
					}

				})
				
				var assignedRoleNames = []
				pushRoleNames($scope.assignedRoles.value, assignedRoleNames)

				angular.forEach(origRoles, function(origRole) {

					if (assignedRoleNames.indexOf(origRole) === -1) {
						$scope.remove.push(origRole)
					}

				})

			}

			var pushRoleNames = function(roles, origRoles) {
				angular.forEach(roles, function(role) {
					origRoles.push(role.name)
				})
			}

			var reloadRoles = function(resData) {
				if (resData) {

					$scope.assignedRoles.value = resData.data
					pushRoleNames(resData.data, origRoles)

				} else {

					$scope.assignedRoles.value = []
					origRoles = []

				}
			}

		})

})()