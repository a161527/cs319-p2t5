(function() {
	'use strict'

	angular.module('createAcct')
		.controller('createAcctCtrl', function($scope) {

			var dependentIndex = 1
			$scope.dependentIndices = [dependentIndex]
			$scope.dependents = {}

			$scope.createAccount = function(createAcctForm, dependentsForm) {
				var dependentsHandled = dependentsForm.$valid || $scope.transfer
				if (createAcctForm.$valid && dependentsHandled) {

				} else {
					angular.forEach(createAcctForm.$error.required, function(field) {
						field.$setDirty()
					})

					angular.forEach(dependentsForm.$error.required, function(field) {
						field.$setDirty()
					})
				}
			}

			$scope.checkPasswordMatch = function(pwValidation) {
				if ($scope.confirmPassword != $scope.password) {
					pwValidation.$setValidity('pwMatch', false)
				} else {
					pwValidation.$setValidity('pwMatch', true)
				}
			}

			$scope.addPerson = function() {
				dependentIndex += 1
				$scope.dependentIndices.push(dependentIndex)
			}

			$scope.deleteLastPerson = function() {
				if (dependentIndex != 1) {
					delete $scope.dependents[dependentIndex]
					dependentIndex -= 1
					$scope.dependentIndices.pop()
				}
			}
		})

})()