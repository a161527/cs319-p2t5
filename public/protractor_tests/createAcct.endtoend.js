'use strict'

var common = require('./common.js')

describe('account creation service', function() {

	it('should display success when email is avaiable', function() {
		browser.get(common.url)
		browser.driver.sleep(500)
		element(by.xpath('//a[contains(text(), "Create Account")]')).click()
		
		element(by.id('email')).sendKeys('test@foo')
		element(by.id('email')).sendKeys(protractor.Key.TAB)
		browser.driver.sleep(1000)
		expect(element(by.xpath('//*[contains(text(), "Email is available")]')).isDisplayed()).toBe(true)
	})

	it('should display error when email is not available', function() {
		element(by.id('email')).clear()
		element(by.id('email')).sendKeys('ryanchenkie@gmail.com')
		element(by.id('email')).sendKeys(protractor.Key.TAB)
		browser.driver.sleep(1000)
		expect(element(by.xpath('//*[contains(text(), "Email is available")]')).isDisplayed()).toBe(false)
		expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "Email is already in use")]')).isDisplayed()).toBe(true)
	})

})