'use strict'

var common = require('./common.js')

describe('login page', function() {

	it('should not display errors on init', function() {
		browser.get(common.url)
		expect(element(by.css('.alert-danger')).isDisplayed()).toBe(false)
	})

	it('should display require messages when login is clicked with no fields', function() {
		browser.driver.sleep(500)
		element(by.id('loginButton')).click()

		common.checkErrorMessage('Password', 'is required', true)
		common.checkErrorMessage('Email', 'is required', true)
	})

	it('should not display error when email is correct', function() {
		element(by.id('email')).sendKeys('test@foo');

		expect(element(by.css('.alert-danger')).isDisplayed()).toBe(false)
	})

	it('should display requires username message typing and deleting input', function() {
		element(by.id('email')).clear().then(function() {
			element(by.id('email')).sendKeys(protractor.Key.TAB)
			common.checkErrorMessage('Email', 'is required', true)
		});		
	})

	it('should display error message for invalid emails', function() {
		element(by.id('email')).sendKeys('abc');
		element(by.id('email')).sendKeys(protractor.Key.TAB)
		common.checkErrorMessage('Email', 'is invalid', true)
	})


	it('should display requires password message on delete', function() {
		element(by.id('password')).sendKeys('abc')
		element(by.id('password')).clear().then(function() {
			element(by.id('password')).sendKeys(protractor.Key.TAB)

			common.checkErrorMessage('Password', 'is required', true)

		});	
	})

})