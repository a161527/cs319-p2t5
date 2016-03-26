(function() {
	'use strict'

	angular.module('forms')
		.service('modal', function($uibModal) {

			this.open = function(message, handler) {

				var modal = $uibModal.open({
					templateUrl: 'js/forms/forms.view.modalConfirm.html',
					backdrop: 'static',
					controller: function($scope, $uibModalInstance) {

						$scope.message = message

						$scope.ok = function() {
							$uibModalInstance.close()
						}

					}
				})

				modal.result.then(function () {
					handler()
				}, function () {
					
				})

			}

		})

})()