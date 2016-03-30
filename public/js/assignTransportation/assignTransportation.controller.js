(function() {
	'use strict'

	angular.module('assignTransportation')
		.controller('transportationUsersCtrl', function($scope, $stateParams, $state, $http, users, conferenceData) {

			$scope.flights = []
			$scope.conferenceName = conferenceData.data.name

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

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

		.controller('assignTransportationCtrl', function($scope, $stateParams, $http, $state, transport, modal, conferenceData) {

			$scope.transportation = transport.data.transportation
			$scope.user = $stateParams.user
			$scope.conferenceName = conferenceData.data.name

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

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

		})

})()