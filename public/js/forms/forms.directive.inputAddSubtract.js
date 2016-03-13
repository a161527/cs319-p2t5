(function() {
	'use strict'

	angular.module('forms')
		.directive('inputAddSubtract', function() {
			return {
				restrict: 'E',
				transclude: true,
				replace: true,
				scope: {
					maxItems: '@',
					model: '='
				},
				template:
					"<div class='input-group'>" +
						"<span class='input-group-btn'>" +
							"<button type='button' class='btn btn-danger' id='foo'>" +
								"<i class='glyphicon glyphicon-minus' ng-click='subtract()'></i>" +
							"</button>" +
						"</span>" +
						"<input type='text' ng-model='model' ng-change='checkInput()' class='form-control' ng-pattern='restrictNum'>" +
						"<span class='input-group-btn'>" +
							"<button type='button' class='btn btn-success' name='subtract' ng-click='add()'>" +
								"<i class='glyphicon glyphicon-plus'></i>" +
							"</button>" +
						"</span>" +
					"</div>",
				link: function(scope, element, attrs) {

					scope.model = 0

					//Prevent pasting
					$(element).find('input').bind('paste', function(e) {
						e.preventDefault();
					})

					scope.add = function() {
						if (scope.model + 1 <= scope.maxItems) {
							scope.model += 1
						}
					}

					scope.subtract = function() {
						if (scope.model > 0) {
							scope.model -= 1
						}
					}

					scope.checkInput = function() {
						var lastChar = scope.model.slice(-1)

						//Empty string case, set to default
						if (!lastChar) {

							scope.model = 0

						//Do not append input if not a number
						} else if (!/^\d+$/.test(scope.model)) {

							scope.model = parseInt(scope.model.slice(0, scope.model.length - 1))

						//Do not append input if result will be greater than max items
						} else if (parseInt(scope.model) > scope.maxItems) {

							scope.model = parseInt(scope.model.slice(0, scope.model.length - 1))

						//Handles case when input is 0
						} else {
							scope.model = parseInt(scope.model)
						}
					}

				}
			}
		})

})()