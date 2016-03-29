(function() {
	'use strict'

	/*
	@params:
		ng-model: required
		default-date: optional, on init, will set date to this if valid, else set to today
	*/
	angular.module('forms')
		.directive('timepickerWithValidation', function() {
			return {
				restrict: 'E',
				replace: true,
				transclude: true,
				scope: {
					validation: '=',
					model: '=',
					identifier: '@',
					label: '@'
				},
				template: 
					'<div>' +
						'<label>{{label}}</label>' +
						'<uib-timepicker ng-model="model" hour-step="1" minute-step="1" show-meridian="true" name="{{identifier}}"></uib-timepicker>' +
						'<error-message ng-show="validation[identifier].$invalid">Input is not valid</error-message>' +
					'</div>',	
				link: function(scope, elem, attrs) {

					//scope.model = new Date()

				}
			}
		})

})()