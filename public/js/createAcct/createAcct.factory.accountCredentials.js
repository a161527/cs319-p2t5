(function() {
	'use strict'

	angular.module('createAcct')
		.factory('accountCredentials', function() {

			var _dependents = null
			var _accountInfo = null
			var _contact = null
			var _transfer = false
			var _emailAvailable = false

			return {
				setDependents: function(t) {
					_dependents = t
				},
				getDependents: function() {
					return _dependents
				},
				setAccountInfo: function(t) {
					_accountInfo = t
				},
				getAccountInfo: function() {
					return _accountInfo
				},
				setContact: function(t) {
					_contact = t
				},
				getContact: function() {
					return _contact
				},
				setTransfer: function(t) {
					_transfer = t
				},
				getTransfer:function() {
					return _transfer
				},
				setEmailAvailable: function(t) {
					_emailAvailable = t
				},
				getEmailAvailable:function() {
					return _emailAvailable
				},
				resetAll: function() {
					_dependents = null
					_accountInfo = null
					_contact = null
					_transfer = false
				}
			}

		})

})()