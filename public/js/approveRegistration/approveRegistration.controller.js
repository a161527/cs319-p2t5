(function() {
	'use strict'

	angular.module('approveRegistration')
		.controller('approveRegistrationCtrl', function($scope, $stateParams, $state, $http, unapproved) {

			$scope.state1 = true
			$scope.state2 = !$scope.state1

			$scope.unapprovedList = unapproved

			$http.get('api/conferences/1/register?include=approved').then(function(resData) {
				console.log(resData)
			})

			$scope.approve = function(id) {
				var route = 'api/conferences/' + $stateParams.cid + '/register/' + id + '/approve' 
				$http.post(route).then(function(resData) {
					
					$state.reload()

				}, function(resData) {
					


				})
			}

			$scope.viewApproved = function() {
				$state.go('dashboard.conferences.manage.approve-registration.2')
			}

		})

		.controller('conferenceApprovedUsersCtrl', function($scope, $stateParams, $state, approved) {

			$scope.unapprovedList = approved.data
			
			$scope.state1 = false
			$scope.state2 = !$scope.state1

			$scope.back = function() {
				$state.go('dashboard.conferences.manage.approve-registration.1')
			}

		})

})()