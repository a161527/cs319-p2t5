(function() {
	'use strict'

	angular.module('eventRegistration')
		.controller('eventRegistrationCtrl', function($scope, $state, $stateParams, ajax, dataFormat, conferenceData, dependents, eventData) {

			$scope.dependents = {}
			var fullDepList = dataFormat.dependentsFormat(dependents.data.dependents, 'id')
			var approvedReg = []
			var registered = []

			$scope.conferenceName = conferenceData.data.name
			$scope.eventName = eventData.data.eventName

			// get users w/ approved conference registration
			angular.forEach(conferenceData.data.registered, function(confReg) {
				if (confReg.status == 'approved') {

					approvedReg.push(confReg.id)
				}
			})

			// get users who are already registered for event
			angular.forEach(eventData.data.registrations, function(user) {
				registered.push(user.userId)
			})

			// only show users who have approved conference registration and aren't already registered
			angular.forEach(fullDepList, function(dep) {
				if ((approvedReg.indexOf(dep.id) !== -1) && (registered.indexOf(dep.id) == -1)) {
					$scope.dependents[dep.id] = dep
				}
			})

			//a new dependents object created so modifications can be made without affecting original object
			$scope.selectDependents = {}

			//Final object to be passed in service call
			$scope.formattedData = null


			//return an object with data formatted for service call
			$scope.formatData = function() {

				var formatted = {'ids': []}

				angular.forEach($scope.selectedDependents, function(dependent) {
 					
 					formatted.ids.push(parseInt(dependent.id))

				})

				return formatted
			}


			$scope.noSelection = false

			//If at least one dependent has 'register', then can move to the next step
			$scope.checkOneSelected = function(dependents) {
				for (var key in dependents) {
					if (dependents[key].hasOwnProperty('register')) {
						if (dependents[key]['register'] === true) {
							$scope.noSelection = false
							return true
						}
					}
				}
				$scope.noSelection = true
				return false
			}

			//Remove 'must have one dependents selected' message
			$scope.removeMessage = function() {
				$scope.noSelection = false
			}

			$scope.cancel = function() {
				$state.go('dashboard.events', {reload: true})
			}

			//Select all checkboxes
			$scope.selectAll = function(field, value, list) {
				angular.forEach(list, function(dependent) { 
					dependent[field] = value
				})
			}

			$scope.showSubmitError = false

			$scope.submit = function() {
				$scope.selectedDependents = addSelectedDependents($scope.dependents, 'register')
				$scope.formattedData = $scope.formatData()

				$scope.showSubmitError = false
				ajax.serviceCall('Submitting...', 'post', 'api/event/' + $stateParams.eid + '/register', $scope.formattedData).then(function(resData) {
					
					$state.go('dashboard.events', $stateParams, {reload: true})

				}, function(resData) {
					
					$scope.showSubmitError = true
					$state.go('dashboard.events', $stateParams, {reload: true})

				})
			}

			$scope.removeSubmitError = function() {
				$scope.showSubmitError = false
			}

			$scope.goToConferenceList = function () {
				$state.go('dashboard.conferences.list')
			}

			$scope.goToEventList = function () {
				$state.go('dashboard.events', {cid: $stateParams.cid})
			}

			//Return a new object with the dependents based on a flag in the object
			var addSelectedDependents = function(dependents, field) {
				var selected = {}
				for (var i in dependents) {
					if (dependents[i].hasOwnProperty(field)) {
						if (dependents[i][field]) {
							selected[i] = dependents[i]
						}
					}
				}

				return selected
			}

			var getConfRegistered = function () {
				for (var i = conferenceData.data.registered.length - 1; i >= 0; i--) {
				};
				return []
			}

			var getEventRegistered = function() {
				return []
			}

			getConfRegistered()
			getEventRegistered()

		})

})()