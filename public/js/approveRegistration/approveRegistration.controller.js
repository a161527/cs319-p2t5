(function() {
	'use strict'

	angular.module('approveRegistration')
		.controller('approveRegistrationCtrl', function($scope, $stateParams, $state, $http, unapproved) {

			$scope.unapprovedList = unapproved

			console.log(unapproved)

			$scope.approve = function(id) {
				var route = 'api/conferences/' + $stateParams.cid + '/register/' + id + '/approve' 
				$http.post(route).then(function(resData) {
					
					$state.reload()

				}, function(resData) {
					


				})
			}

		})

})()