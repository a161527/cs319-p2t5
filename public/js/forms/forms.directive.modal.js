(function() {
	'use strict'

	angular.module('forms')
		.directive('modal', function() {
			return {
				restrict: 'E',
				transclude: true,
				replace: true,
				template:
					'<div class="modal fade" role="dialog">' +
						'<div class="modal-dialog">' +
							'<div class="modal-content">' +
								'<div class="modal-body">' +
									'<div ng-transclude></div>' +
								'</div>' +
								'<div class="modal-footer">' +
									'<button type="button" class="btn btn-default" data-dismiss="modal">{{buttonName}}</button>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>',
				scope: {
					show: '=',
					buttonName: '@',
					modalOnConfirm: '&'
				},
				link: function(scope, element, attrs) {

					scope.$watch(function() { 
						return scope.show 
					}, function(value) {

						if (value === true) {
							element.modal('show')
						}

						scope.show = false
					})

					element.find('button').bind('click', function() {
						scope.modalOnConfirm()	
					})

				}
			}
		})

})()