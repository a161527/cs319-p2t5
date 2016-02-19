(function() {
	'use strict'

	angular.module('login')
		.directive('requiredInput', function() {
			return {
				restrict: 'E',
				replace: true,
				transclude: true,
				scope: {
					validation: '=',
					model: '=',
					identifier: '@'
				},
				templateUrl: 'js/login/login.view.requiredInput.html'
			}
		})

})()