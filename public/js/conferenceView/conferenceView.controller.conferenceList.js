(function() {
	'use strict'

	angular.module('conferenceView')
		.controller('conferenceListCtrl', function($scope, $state, $http, conferenceData, approvedDependents, $q, confirmDeleteModal) {

			$scope.conferences = conferenceData[0] || []
			$scope.noConferences = ($scope.conferences.length === 0)
			$scope.canCreateConference = (conferenceData[1].indexOf('create-conference') !== -1)
			$scope.hasApprovedDependents = approvedDependents > 0

			$scope.registered = function(registeredList) {
				return registeredList.length > 0
			}

			$scope.goToCreateConference = function() {
				$state.go('dashboard.conferences.create', {reload: true})
			}

			$scope.goToManage = function(conference) {
				$state.go('dashboard.conferences.manage', {cid: conference.id})
			}

			$scope.goToDetails = function(conference) {
				$state.go('dashboard.conferences.registrationDetails', {cid: conference.id, conference: conference})
			}

			$scope.deleteConference = function(conference) {
				confirmDeleteModal.open('Conference', conference.name, 'api/conferences/' + conference.id)
			}
		})

})()