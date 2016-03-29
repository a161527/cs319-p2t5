(function() {
	'use strict'

	angular.module('assignTransportation')
		.controller('transportationUsersCtrl', function($scope, $state, $http, users) {

			$scope.flights = []

			angular.forEach(users.data.flights, function(flight) {
				flight.fullName = flight.accounts[1].users[0].firstName + ' ' + flight.accounts[1].users[0].lastName
				$scope.flights.push(flight)
			})

			$scope.assign = function(idx) {
				var param = {
					name: $scope.flights[idx].fullName, 
					id: $scope.flights[idx].id
				}

				$state.go('dashboard.conferences.manage.assign-transportation.2', {user: param})
			}

		})

		.controller('assignTransportationCtrl', function($scope, $stateParams, $http, transport) {

			$scope.transportation = transport.data.transportation
			$scope.user = $stateParams.user

			$scope.assign = function() {
				var route = 'api/conferences/' + $stateParams.cid + '/transportation/' + $stateParams.user.id + '/assign'
				$http.post(route).then(function(resData) {

					$state.go('dashboard.conferences.manage.assign-transportation.1')

				})
			}

		})

})()