(function() {
	'use strict'

	angular.module('approveRegistration')
		.controller('approveRegistrationCtrl', function($scope, $stateParams, $state, $http, unapproved, conferenceData) {

			$scope.state1 = true
			$scope.state2 = !$scope.state1
			$scope.rejectButtonTxt = 'Reject'
			$scope.rejectButtonIcon = 'remove'

			$scope.unapprovedList = unapproved
			$scope.conferenceName = conferenceData.data.name

			angular.forEach($scope.unapprovedList, function(dep){
				dep['fullName'] = dep.user.firstName + ' ' + dep.user.lastName
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

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.reject = function(id) {
				$http.delete('api/conferences/' + $stateParams.cid + '/register/' + id).then(function(resData) {
					
					$state.reload()

				}, function(resData) {
					console.log(resData)
				})
			}

		})

		.controller('conferenceApprovedUsersCtrl', function($scope, $stateParams, $state, $http, approved, conferenceData) {

			$scope.unapprovedList = approved.data
			$scope.rejectButtonTxt = 'Remove'
			$scope.rejectButtonIcon = 'trash'

			$scope.conferenceName = conferenceData.data.name

			angular.forEach($scope.unapprovedList, function(dep){
				dep['fullName'] = dep.user.firstName + ' ' + dep.user.lastName
			}) 
			
			$scope.state1 = false
			$scope.state2 = !$scope.state1

			$scope.back = function() {
				$state.go('dashboard.conferences.manage.approve-registration.1')
			}

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.reject = function(id) {
				$http.delete('api/conferences/' + $stateParams.cid + '/register/' + id).then(function(resData) {
					
					$state.reload()

				}, function(resData) {
					console.log(resData)
				})

			}

		})

})()