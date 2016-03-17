(function() {
	'use strict'

	/*
	@params:
		ng-model: required
		default-date: optional, on init, will set date to this if valid, else set to today
	*/
	angular.module('forms')
		.directive('datePickerInput', function() {
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
				templateUrl: 'js/forms/forms.view.datePicker.html',
				link: function(scope, elem, attrs) {

					scope.calendar = {
						opened: false
					}

					scope.openCalendar = function() {
						scope.calendar.opened= true
					}

				}
			}
		})

})()