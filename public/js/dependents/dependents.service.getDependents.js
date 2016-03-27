(function() {
	'use strict'

	angular.module('dependents')
		.service('getDependentsService', function($q, $http, ajax, loginStorage) {


			this.getApproved = function() {

				return $q(function(resolve, reject) {

					$http.get('api/accounts/' + loginStorage.getId() + '/dependents').then(function(resData) {

						var dependents = {}

						angular.forEach(resData.data.dependents, function(dep) {
							if (dep.approved) {
								dependents[dep.id] = dep
							}
						})

						resolve(dependents)

					}, function(resData) {
						reject(resData)
					})

				})
				
			}

			this.getNumberOfApproved = function() {
				return $q(function(resolve, reject) {

					$http.get('api/accounts/' + loginStorage.getId() + '/dependents').then(function(resData) {

						resolve(resData.data.dependents.length)

					}, function(resData) {
						reject(resData)
					})

				})
			}

		})

})()