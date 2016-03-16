'use strict'

var common = require('./common.js')

describe('login services', function() {

	it('should not login on incorrect username', function() {
		browser.get(common.url)
		browser.driver.sleep(500)

		element(by.id('email')).sendKeys('test@foo');
		element(by.id('password')).sendKeys('11111111');

		browser.driver.sleep(500)
		element(by.id('loginButton')).click()

		expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "Invalid credentials")]')).isDisplayed()).toBe(true)
	})

	it('should not login on incorrect password', function() {
		element(by.id('email')).clear()
		element(by.id('password')).clear()
		element(by.id('email')).sendKeys('ryanchenkie@gmail.com');
		element(by.id('password')).sendKeys('11111111');
		element(by.id('email')).sendKeys(protractor.Key.TAB)

		browser.driver.sleep(500)
		element(by.id('loginButton')).click()

		expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "Invalid credentials")]')).isDisplayed()).toBe(true)
	})

	it('should go to dashboard when credentials are correct', function() {
		element(by.id('email')).clear()
		element(by.id('password')).clear()
		element(by.id('email')).sendKeys('ryanchenkie@gmail.com');
		element(by.id('password')).sendKeys('secret');
		element(by.id('email')).sendKeys(protractor.Key.TAB)

		browser.driver.sleep(500)
		element(by.id('loginButton')).click()

		browser.driver.sleep(1000)
		expect(browser.getCurrentUrl()).toEqual(common.url + 'dashboard')
	})

	it('should redirect to dashboard when already logged in', function() {
		browser.get(common.url)
		browser.driver.sleep(500)
		expect(browser.getCurrentUrl()).toEqual(common.url + 'dashboard')
	})

})