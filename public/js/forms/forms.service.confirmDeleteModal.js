(function() {
	'use strict'

	angular.module('forms')
		.service('confirmDeleteModal', function($uibModal, ajax, $state, $stateParams) {

			this.open = function(type, name, route) {

				var modal = $uibModal.open({
					templateUrl: 'js/forms/forms.view.confirmDelete.html',
					backdrop: 'static',
					animation: false,
					controller: function($scope, $uibModalInstance) {

						$scope.type = type
						$scope.name = name
						$scope.lcType = type.toLowerCase()

						$scope.delete = function() {
							ajax.serviceCall('Deleting ' + type + '...', 'delete', route).then(function(resData) {
								$uibModalInstance.close()
								$state.go($state.current, $stateParams, {reload: true})
							}, function(resData) {
								$uibModalInstance.close()
								window.alert('delete failed')
								$state.go($state.current, $stateParams, {reload: true})
							})
						}

						$scope.cancel = function() {
							$uibModalInstance.close()
						}

					}
				})

			}

		})

})()