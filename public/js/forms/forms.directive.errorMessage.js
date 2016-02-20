(function() {
	'use strict'

	angular.module('forms')
		.directive('errorMessage', function() {
			return {
				restrict: 'E',
				transclude: true,
				replace: true,
				template: 
				'<div class="alert alert-danger">' +
					'<div ng-transclude></div>' + 
				'</div>'
			}
		})

})()