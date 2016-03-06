(function() {
	'use strict'

	/*
	@params:
		ng-model: required
		default-date: optional, on init, will set date to this if valid, else set to today
	*/
	angular.module('forms')
		.directive('datePickerInput', function($parse) {
			return {
				restrict: 'E',
				require: 'ngModel',
				transclude: true,
				replace: true,
				template:
					'<div class="form-group">' +
						'<label><ng-transclude></ng-transclude></label>' +
						'<div class="input-group date" id="datetimepicker">' +
							'<input type="text" class="form-control">' +
							'<span class="input-group-addon">' +
							'<span class="glyphicon glyphicon-calendar"></span>' +
							'</span>' +
						'</div>' +
					'</div>',
				link: function(scope, element, attrs) {

					//default value
					var displayFormat = 'MM/DD/YYYY'
					var today = moment().format(displayFormat)
					var outputFormat = 'YYYY-MM-DD'
					var ngModel = $parse(attrs['ngModel'])

					var getDateValue = function() {
						var date = $(element).find('input').val()
						var formattedDate = moment(date).format(outputFormat)
						return formattedDate
					}

					var getDefaultDate = function() {
						var defaultDate

						//If ng-model already has a value, set it to that
						if ($parse(attrs['ngModel'])(scope)) {

							defaultDate = ngModel(scope)

						} else if (attrs['defaultDate'] && moment(attrs['defaultDate']).isValid()) {

							defaultDate = attrs['defaultDate']

						} else {
							defaultDate = today
						}

						return defaultDate
					}

					var datePickerParams = {
						defaultDate: getDefaultDate(),
						format: displayFormat
					}

					//need to do this because ng-model cannot be assigned to input
					//get ng-model from parent scope
					var modelSetter = ngModel.assign

					//init datepicker and set date for ng-model
					$(element).find('#datetimepicker').datetimepicker(datePickerParams)

					modelSetter(scope, getDateValue())


					$(element).find('#datetimepicker').bind('dp.change', function(value) {
						
						modelSetter(scope, getDateValue())
						scope.$apply()
			
					})

				}
			}
		})

})()