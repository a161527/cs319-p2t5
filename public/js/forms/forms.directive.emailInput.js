(function() {
	'use strict'

	angular.module('forms')
		.directive('emailInput', function() {
			return {
				restrict: 'E',
				replace: true,
				scope: {
					validation: '=',
					model: '=',
					identifier: '@'
				},
				templateUrl: 'js/forms/forms.view.emailInput.html'
			}
		})

})()