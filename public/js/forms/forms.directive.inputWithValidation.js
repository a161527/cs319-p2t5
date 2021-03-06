/*
@Params:
	validation: angular validation object generated by setting the name of a form attribute
	model: ng-model, should be defined in the controller
	identifer: unique identifier for the inputs
	inpType: input type eg. password, text
	changeFn: function, optional, if exists, bind to input and execute whenever change occurs
*/
(function() {
	'use strict'

	angular.module('forms')
		.directive('inputWithValidation', function() {
			return {
				restrict: 'E',
				replace: true,
				transclude: true,
				scope: {
					validation: '=',
					model: '=',
					modelOptions: '=',
					identifier: '@',
					inpType: '@',
					placeholder: '@',
					changeFn: '&'
				},
				template: 
					"<div class='form-group' ng-class='{" + '"has-error"' + ": !validation.{{identifier}}.$valid &&" + 
						"validation.{{identifier}}.$dirty}'>" +
						"<label for='{{identifier}}'><div ng-transclude></div></label>" +

						"<input type='{{inpType}}' ng-model-options='" + 'modelOptions || {debounce: {default : 500}}\'' + 
							"ng-model='model'" + 
							"class='form-control'" + 
							"id='{{identifier}}'" + 
							"name='{{identifier}}' placeholder='{{placeholder}}' required>" +
	
						"<error-message ng-show='validation[identifier].$error[inpType] && validation.{{identifier}}.$dirty'>" +
							"<span ng-transclude></span> is invalid" +
						"</error-message>" +
						"<error-message ng-show='validation[identifier].$error.required && validation[identifier].$dirty'>" +
							"<span ng-transclude></span> is required" +
						"</error-message>" +
					"</div>",
				link: function(scope, elements, attrs) {

					//Bind the change function
					if (scope.changeFn) {
						elements.find('input').bind('change', function() {
							scope.changeFn()
						});
					}

				}
			}
		})

})()
