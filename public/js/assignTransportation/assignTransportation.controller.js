(function() {
	'use strict'

	angular.module('assignTransportation')
		.controller('transportationUsersCtrl', function($scope, $state, $http, users) {

			$scope.flights = []

			angular.forEach(users.data.flights, function(flight) {
				console.log(flight)
				angular.forEach(flight.accounts, function(account) {

					angular.forEach(account.users, function(user) {

						user.fullName = user.firstName + ' ' + user.lastName
						user.uid = user.id
						user.airline = flight.airline
						user.airport = flight.airport
						user.arrivalDate = flight.arrivalDate
						user.arrivalTime = flight.arrivalTime
						user.flightNumber = flight.flightNumber

						$scope.flights.push(user)

					})

				})

			})

			$scope.assign = function(idx) {
				var param = {
					name: $scope.flights[idx].fullName, 
					id: $scope.flights[idx].userconferenceID
				}

				$state.go('dashboard.conferences.manage.assign-transportation.2', {user: param})
			}

		})

		.controller('assignTransportationCtrl', function($scope, $stateParams, $http, $state, transport, modal) {

			$scope.transportation = transport.data.transportation
			$scope.user = $stateParams.user

			$scope.assign = function() {
				console.log($scope.user.id)
				var route = 'api/conferences/' + $stateParams.cid + '/transportation/' + $scope.user.id + '/assign'
				$http.post(route).then(function(resData) {

					$state.go('dashboard.conferences.manage.assign-transportation.1')

				}, function(resData) {
					modal.open('Error: ' + resData.data.message, function() {
						$state.go('dashboard.conferences.manage.assign-transportation.1')
					})
				})
			}

		})

})()