(function() {
	'use strict'

	angular.module('rooms')
		.service('getRoomUsersService', function($q, $http) {

			/*
			Return all users that are approved for the conference and have had their accounts approved
			*/
			this.get = function(cid) {
				return $q(function(resolve, reject) {

					$http.get('api/conferences/' + cid + '/residences/assign/missing').then(function(resData) {
						console.log(resData)

						var resList = []

						angular.forEach(resData.data, function(dep) {
							if (dep.approved && dep.user.approved) {
								resList.push(dep)
							}
						})

						resolve(resList)

					}, function(resData) {
						reject(resData)
					})

				})
			}


		})

})()