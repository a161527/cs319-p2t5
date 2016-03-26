(function() {
	'use strict'

	angular.module('forms')
		.directive('inputAddSubtract', function() {
			return {
				restrict: 'E',
				transclude: true,
				replace: true,
				scope: {
					model: '=',
					items: '='
				},
				template:
					"<div class='input-group'>" +
						"<span class='input-group-btn'>" +
							"<button type='button' class='btn btn-danger' id='foo' ng-click='subtract()'>" +
								"<i class='glyphicon glyphicon-minus'></i>" +
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
					var maxItems = scope.items

					//Prevent pasting
					$(element).find('input').bind('paste', function(e) {
						e.preventDefault();
					})

					scope.add = function() {
						if (scope.model + 1 <= maxItems) {
							scope.model += 1
						}
					}

					scope.subtract = function() {
						if (scope.model - 1 >= 0) {
							scope.model -= 1
						}
					}

					var prevModel = scope.model
					scope.checkInput = function() {
						var lastChar = scope.model.slice(-1)

						//Empty string case, set to default
						if (!lastChar) {

							scope.model = 0

						//Do not append input if not a number
						} else if (!/^\d+$/.test(scope.model)) {

							scope.model = parseInt(scope.model.slice(0, scope.model.length - 1))

						//Do not append input if result will be greater than max items
						} else if (parseInt(scope.model) > maxItems) {

							scope.model = parseInt(scope.model.slice(0, scope.model.length - 1))

						//Input is valid
						} else {
							
							prevModel = prevModel? prevModel : 0
							var newModel = parseInt(scope.model)
							scope.model = newModel
							prevModel = scope.model

						}
					}

				}
			}
		})

})()