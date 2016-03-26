(function() {
	'use strict'

	angular.module('eventRegistration')
		.controller('eventRegistrationCtrl', function($scope, $state, $stateParams, ajax, dataFormat, conferenceData, dependents) {

			$scope.dependents = {}
			var fullDepList = dataFormat.dependentsFormat(dependents.data.dependents, 'id')
			var approvedReg = []

			// get users w/ approved conference registration
			angular.forEach(conferenceData.data.registered, function(confReg) {
				if (confReg.status == 'approved') {
					approvedReg.push(confReg.id)
				}
			})

			// only show users who have approved conference registration
			angular.forEach(fullDepList, function(dep) {
				if (approvedReg.indexOf(dep.id) !== -1) {
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
				$state.go('dashboard.events')
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

				console.log($scope.formattedData)

				$scope.showSubmitError = false
				ajax.serviceCall('Submitting...', 'post', 'api/event/' + $stateParams.cid + '/register', $scope.formattedData).then(function(resData) {

					$state.go('dashboard.events')

				}, function(resData) {
					
					$scope.showSubmitError = true

				})
			}

			$scope.removeSubmitError = function() {
				$scope.showSubmitError = false
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

			var openModal = function() {

				var modal = $uibModal.open({
					templateUrl: 'js/eventRegistration/eventRegistration.view.modalConfirm.html',
					controller: function($scope, $uibModalInstance) {

						$scope.ok = function() {
							$uibModalInstance.close()
						}

					}
				})

				modal.result.then(function () {
					$state.go('dashboard.events.list')
				}, function () {
					
				})

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