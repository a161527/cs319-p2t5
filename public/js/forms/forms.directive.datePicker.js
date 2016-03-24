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
				template: 
					"<div class='form-group' ng-class='{" + '"has-error"' + ": !validation.{{identifier}}.$valid && validation.{{identifier}}.$dirty}'>" +
						"<label>{{label}}</label>" +

						//Init date picker
						"<div class='input-group'>" +

							"<input type='text' class='form-control' " +
								"uib-datepicker-popup='MM/dd/yyyy' " +
								"ng-model='model' " +
								"is-open='calendar.opened' " +
								"close-text='Close' " +
								"name='{{identifier}}' " +
								"id='{{identifier}}' required>" +

							//Open date picker button
							"<span class='input-group-btn'>" +
								"<button type='button' class='btn btn-default' ng-class='{" + '"btn-danger"' + ": !validation.{{identifier}}.$valid && validation.{{identifier}}.$dirty}' " +
								"ng-click='openCalendar()'>" +
									"<i class='glyphicon glyphicon-calendar'></i>" +
								"</button>" +
							"</span>" +

						"</div>" +

						"<error-message ng-show='validation.{{identifier}}.$error.required && validation.{{identifier}}.$dirty'>" +
							"Date is required" +
						"</error-message>" +
						"<error-message ng-show='!validation.{{identifier}}.$error.required && validation.{{identifier}}.$dirty && validation.{{identifier}}.$invalid'>" +
							"Date must be in the format 'mm/dd/yyyy'" +
						"</error-message>" +
					"</div>",
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