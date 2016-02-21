(function() {
	'use strict'

	angular.module('forms')
		.directive('requiredInput', function() {
			return {
				restrict: 'E',
				replace: true,
				transclude: true,
				scope: {
					validation: '=',
					model: '=',
					identifier: '@',
					inpType: '@'
				},
				templateUrl: 'js/forms/forms.view.requiredInput.html'
			}
		})

})()