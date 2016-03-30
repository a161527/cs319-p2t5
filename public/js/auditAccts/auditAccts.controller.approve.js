(function() {
	'use strict'

	angular.module('auditAccts')
		.controller('approveAcctsCtrl', function($scope, $state, ajax, unapprovedDependents, modal) {

			$scope.dependents = unapprovedDependents.data
			$scope.state1 = true

			angular.forEach($scope.dependents, function(dep) {
				dep.fullName = dep.firstName + ' ' + dep.lastName
			})

			$scope.approve = function(id) {
				var route = 'api/register/' + id + '/approve'
				ajax.serviceCall('Approving...', 'post', route).then(function(resData) {

					$state.reload()

				}, function(resData) {
					
					modal.open('Error: ' + resData.data.message)

				})
			}

			$scope.viewApproved = function() {
				$state.go('dashboard.approveAccts.2')
			}
		})

		.controller('viewApprovedAcctsCtrl', function($scope, $state, approvedDependents) {

			$scope.state1 = false

			$scope.dependents = approvedDependents.data.dependents

			angular.forEach($scope.dependents, function(dep) {
				dep.fullName = dep.firstName + ' ' + dep.lastName
			})

			$scope.back = function() {
				$state.go('dashboard.approveAccts.1')
			}
		})

})()