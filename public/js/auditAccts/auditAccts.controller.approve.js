(function() {
	'use strict'

	angular.module('auditAccts')
		.controller('approveAcctsCtrl', function($scope, $state, ajax, unapprovedDependents) {

			$scope.dependents = unapprovedDependents.data

			angular.forEach($scope.dependents, function(dep) {
				dep.fullName = dep.firstName + ' ' + dep.lastName
			})

			$scope.approve = function(id) {
				var route = 'api/register/' + id + '/approve'
				ajax.serviceCall('Approving...', 'post', route).then(function(resData) {

					$state.reload()

				}, function(resData) {
					console.log(resData)
				})
			}
		})

})()