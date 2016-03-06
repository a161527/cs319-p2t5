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
					'<span class="glyphicon glyphicon-flag"></span> <span ng-transclude></span>' + 
				'</div>' 
			}
		})

})()