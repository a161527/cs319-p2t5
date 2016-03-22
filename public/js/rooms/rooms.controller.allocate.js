(function() {
	'use strict'

	angular.module('rooms')
		.controller('AllocateRoomsCtrl', function($scope, $http) {

			$http.get('api/conferences/1/residences/assign/missing').then(function(data) {
				console.log(data)
			})

		})

})()