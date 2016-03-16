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

	it('should redirect to dashboard when already logged in', function() {
		element(by.id('email')).clear()
		element(by.id('password')).clear()
		element(by.id('email')).sendKeys('ryanchenkie@gmail.com');
		element(by.id('password')).sendKeys('secret');
		element(by.id('email')).sendKeys(protractor.Key.TAB)

		browser.driver.sleep(500)
		element(by.id('loginButton')).click()

		browser.driver.sleep(1000)
		expect(browser.getCurrentUrl()).toEqual(common.url + 'dashboard')

		browser.get(common.url)
		browser.driver.sleep(500)
		expect(browser.getCurrentUrl()).toEqual(common.url + 'dashboard')
	})

	it('should redirect to login if logout is clicked', function() {
		element(by.xpath('//*[@id="cmsNavbar"]/ul/li/a')).click()
		element(by.xpath('//*[@ng-click="logout()"]')).click()
		browser.driver.sleep(500)
		expect(browser.getCurrentUrl()).toEqual(common.url)
	})

	it('should not redirect to dashboard after logout', function() {
		browser.get(common.url)
		browser.driver.sleep(500)
		expect(browser.getCurrentUrl()).toEqual(common.url)
	})

})