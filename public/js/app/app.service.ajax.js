(function() {
	'use strict'

	angular.module('app')
		.service('ajax', function($q, $http, $auth, blockUI) {

			this.login = function(credentials) {

				return $q(function(resolve, reject) {
					blockUI.start('Logging in...')

					$auth.login(credentials).then(function(resData) {

						blockUI.stop()
						resolve(resData)

					}, function(resData) {

						blockUI.stop()
						reject(resData)

					})
				})

			}

			/*
			@Params:
				loadMessage: string - message to display when loading
				requestMethod: string - eg. post, get
				route: string - backend route
				parameter: obj - optional parameters to pass to route
			Returns a promise object with resulting data
			*/
			this.serviceCall = function(loadMessage, requestMethod, route, parameters) {

				return $q(function(resolve, reject) {
					blockUI.start(loadMessage)
					var promise = parameters ? $http[requestMethod](route) : $http[requestMethod](route, parameters)

					promise.then(function(resData) {

						blockUI.stop()
						resolve(resData)

					}, function(resData) {

						blockUI.stop()
						reject(resData)

					})
				})

			}

		})

})()