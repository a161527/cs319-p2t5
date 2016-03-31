(function() {
	'use strict'

	angular.module('reportView')
		.controller('reportViewCtrl', function($scope, $state, $stateParams, ajax, eventData, conferenceData, viewReport) {
			
			$scope.events = eventData[0] || []
			console.log($scope.events)
			$scope.noEvents = ($scope.events.length === 0)
			$scope.conferenceName = conferenceData.name
			$scope.viewConference = true
			$scope.reports = []

			if (viewReport === 'event') {

				$scope.viewConference = false
				var authorized = false
				
				angular.forEach($scope.events, function(evt) {
					evt.reports = []

					evt.reports.push({'label': 'Event Registration', 'route': '/reports/' + conferenceData.id + '_' + evt.id + '_EventRegistration.csv'})
					evt.reports.push({'label': 'Event Demographics', 'route': '/reports/' + conferenceData.id + '_' + evt.id + '_EventDemographics.csv'})
				})
	
			} else {

				$scope.viewConference = true
				$scope.reports.push({'label': 'Conference Registration', 'route': '/reports/' + conferenceData.id + '_ConferenceRegistration.csv'})
				$scope.reports.push({'label': 'Conference Demographics', 'route': '/reports/' + conferenceData.id + '_ConferenceDemographics.csv'})
				$scope.reports.push({'label': 'Inventory Assignment', 'route': '/reports/' + conferenceData.id + '_InventoryAssignment.csv'})
				$scope.reports.push({'label': 'Transportation Schedule', 'route': '/reports/' + conferenceData.id + '_ConferenceRegistration.csv'})

			}
			

			$scope.goToConference = function () {
				$state.go('dashboard.conferences.manage', {cid: $stateParams.cid})
			}

			$scope.getReport = function(route) {
				ajax.serviceCall('Downloading report...', 'get', route, {dataType: "text/csv"})
			}

		})

})()