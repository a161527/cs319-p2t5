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

			this.createConference = function(conferenceInfo) {

				return $q(function(resolve, reject) {
					blockUI.start('Creating conference...')

					conferenceInfo.start = parseDate(conferenceInfo.start);
					conferenceInfo.end = parseDate(conferenceInfo.end);

					console.log(conferenceInfo);

					$http.post('api/conferences', conferenceInfo).then(function(resData) {

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
					var promise = parameters ? $http[requestMethod](route, parameters) : $http[requestMethod](route)

					promise.then(function(resData) {

						blockUI.stop()
						resolve(resData)

					}, function(resData) {

						blockUI.stop()
						reject(resData)

					})
				})

			}

			var parseDate = function(date) {
					var year = date.getFullYear(),
						month = date.getMonth() + 1,
						day = date.getDate();
					if (month < 10) {
						month = '0' + month;
					}
					if (day < 10) {
						day = '0' + day;
					}

					var formatted = year + '-' + month + '-' + day;

					return formatted;
			}

		})

})()