(function() {
	'use strict'

	angular.module('approveRegistration')
		.service('getUnapprovedService', function($q, $http) {

			this.get = function(cid) {
				return $q(function(resolve, reject) {

					$http.get('api/conferences/' + cid + '/register').then(function(resData) {

						var unapproved = []

						angular.forEach(resData.data, function(current) {

							//Check if account is approved
							if (current.user.approved && !current.approved) {
								unapproved.push(current)
							}

						})

						resolve(unapproved)

					}, function(resData) {
						reject(resData)
					})

				})
			}

		})

})()