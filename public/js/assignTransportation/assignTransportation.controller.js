(function() {
	'use strict'

	angular.module('assignTransportation')
		.controller('transportationUsersCtrl', function($scope, $state, $http, users) {

			$scope.state1 = true 
			$scope.flights = []

			angular.forEach(users.data.flights, function(flight) {
				angular.forEach(flight.accounts, function(account) {

					angular.forEach(account.users, function(user) {

						if (!user.hasTransport) {
							user.fullName = user.firstName + ' ' + user.lastName
							user.uid = user.id
							user.airline = flight.airline
							user.airport = flight.airport
							user.arrivalDate = flight.arrivalDate
							user.arrivalTime = flight.arrivalTime
							user.flightNumber = flight.flightNumber

							$scope.flights.push(user)
						}

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

			$scope.viewApproved = function() {
				$state.go('dashboard.conferences.manage.assign-transportation.3')
			}

		})

		.controller('assignTransportationCtrl', function($scope, $stateParams, $http, $state, transport, modal) {

			$scope.transportation = transport.data.transportation
			$scope.user = $stateParams.user

			$scope.assign = function(id) {
				console.log($scope.user.id)
				var route = 'api/conferences/' + $stateParams.cid + '/transportation/' + id + '/assign'
				$http.post(route, {userConferenceIDs: [$scope.user.id]}).then(function(resData) {

					$state.go('dashboard.conferences.manage.assign-transportation.1')

				}, function(resData) {
					modal.open('Error: ' + resData.data.message, function() {
						$state.go('dashboard.conferences.manage.assign-transportation.1')
					})
				})
			}

		})

		.controller('viewAssignedTransportCtrl', function($scope, users) {

			$scope.state1 = false
			console.log(users)
		})

})()