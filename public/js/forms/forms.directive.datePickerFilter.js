(function() {
	'use strict'

	/*
	@params:
		ng-model: required
		default-date: optional, on init, will set date to this if valid, else set to today
	*/
	angular.module('forms')
		.directive('datePickerFilter', function(dataFormat) {
			return {
				restrict: 'E',
				replace: true,
				transclude: true,
				scope: {
					model: '='
				},
				template: 
					"<div class='input-group'>" +

						"<input type='text' class='form-control' " +
							"uib-datepicker-popup='yyyy-MM-dd' " +
							"ng-model='filterVal' " +
							"is-open='calendar.opened' " +
							"close-text='Close'>" +

						//Open date picker button
						"<span class='input-group-btn'>" +
							"<button type='button' class='btn btn-default' " +
							"ng-click='openCalendar()'>" +
								"<i class='glyphicon glyphicon-calendar'></i>" +
							"</button>" +
						"</span>" +

					"</div>",
				link: function(scope, elem, attrs) {

					scope.$watch('filterVal', function(value) {
						scope.model = dataFormat.dateFormat(value)
					}, true);

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