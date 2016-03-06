'use strict'

var common = require('./common.js')

describe('account creation', function() {

	it('should navigate to the correct route', function() {
		browser.get(common.url)
		browser.driver.sleep(500)
		element(by.xpath('//a[contains(text(), "Create Account")]')).click()
		expect(browser.getCurrentUrl()).toEqual(common.url + 'create')
	})

	it('should display error messages when no fields are filled in and next is clicked', function() {
		browser.driver.sleep(500)
		element(by.xpath('//button[contains(text(), "Next")]')).click()
		expect(element(by.xpath('//h4[contains(text(), "Account Information")]')).isDisplayed()).toBe(true)
		common.checkErrorMessage('Email', 'is required', true)
		common.checkErrorMessage('Password', 'is required', true)
		common.checkErrorMessage('Email confirmation', 'is required', true)
		common.checkErrorMessage('Password confirmation', 'is required', true)
	})

	it('should show error if email confirmation does not match', function() {
		element(by.id('email')).sendKeys('test@foo')
		element(by.id('confirmEmail')).sendKeys('test@fo')
		element(by.id('email')).sendKeys(protractor.Key.TAB)
		expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "Email does not match")]')).isDisplayed()).toBe(true)
	})

	it('should show error if email confirmation initially matches but is changed', function() {
		element(by.id('email')).clear()
		element(by.id('confirmEmail')).clear()
		element(by.id('email')).sendKeys('test@foo')
		element(by.id('confirmEmail')).sendKeys('test@foo')
		element(by.id('email')).sendKeys(protractor.Key.TAB)
		element(by.id('email')).sendKeys('a')
		element(by.id('email')).sendKeys(protractor.Key.TAB)
		expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "Email does not match")]')).isDisplayed()).toBe(true)
	})

	it('should show error if password confirmation does not match', function() {
		element(by.id('password')).sendKeys('11111111')
		element(by.id('confirmPassword')).sendKeys('1')
		element(by.id('password')).sendKeys(protractor.Key.TAB)
		expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "Password does not match")]')).isDisplayed()).toBe(true)
	})

	it('should show error if password confirmation initially matches but is changed', function() {
		element(by.id('password')).clear()
		element(by.id('confirmPassword')).clear()
		element(by.id('password')).sendKeys('test@foo')
		element(by.id('confirmPassword')).sendKeys('test@foo')
		element(by.id('password')).sendKeys(protractor.Key.TAB)
		element(by.id('password')).sendKeys('a')
		element(by.id('password')).sendKeys(protractor.Key.TAB)
		expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "Password does not match")]')).isDisplayed()).toBe(true)
	})

	it('should navigate when all fields are correct', function() {
		element(by.id('email')).clear()
		element(by.id('confirmEmail')).clear()
		element(by.id('password')).clear()
		element(by.id('confirmPassword')).clear()
		element(by.id('password')).sendKeys('11111111')
		element(by.id('confirmPassword')).sendKeys('11111111')
		element(by.id('email')).sendKeys('test@foo')
		element(by.id('confirmEmail')).sendKeys('test@foo')
		element(by.id('password')).sendKeys(protractor.Key.TAB)
		browser.driver.sleep(500)
		element(by.xpath('//button[contains(text(), "Next")]')).click()
		expect(element(by.xpath('//h4[contains(text(), "Dependent Users")]')).isDisplayed()).toBe(true)
	})

	it('should not allow user to go to next step when account creation is cancelled, even though previous credentials were correct', function() {
		browser.driver.sleep(500)
		element(by.xpath('//button[contains(text(), "Back")]')).click()
		browser.driver.sleep(500)
		element(by.xpath('//button[contains(text(), "Cancel")]')).click()
		browser.driver.sleep(500)
		element(by.xpath('//a[contains(text(), "Create Account")]')).click()
		browser.driver.sleep(500)
		
		element(by.xpath('//button[contains(text(), "Next")]')).click()
		expect(element(by.xpath('//h4[contains(text(), "Account Information")]')).isDisplayed()).toBe(true)
		common.checkErrorMessage('Email', 'is required', true)
		common.checkErrorMessage('Password', 'is required', true)
		common.checkErrorMessage('Email confirmation', 'is required', true)
		common.checkErrorMessage('Password confirmation', 'is required', true)
	})

})