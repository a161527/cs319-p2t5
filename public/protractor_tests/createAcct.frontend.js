'use strict'

var common = require('./common.js')

describe('account creation', function() {

	var correctCredentials = { email: 'test@foo', password: '11111111'}

	var fillDependentsInfo = function(date, dependentIndex) {
		var diStr = dependentIndex.toString()
		var t = 'a'

		element(by.id('firstname' + diStr)).sendKeys(t)
		element(by.id('lastname' + diStr)).sendKeys(t)
		element(by.id('gender' + diStr)).sendKeys('Male')
		element(by.id('birthdate' + diStr)).clear()
		element(by.id('birthdate' + diStr)).sendKeys(date)
		element(by.id('city' + diStr)).sendKeys(t)
		element(by.id('country' + diStr)).sendKeys(t)
		element(by.id('accomodations' + diStr)).sendKeys(t)

		//trigger change
		element(by.id('accomodations' + diStr)).sendKeys(protractor.Key.TAB)
	}

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

		element(by.id('password')).sendKeys(correctCredentials.password)
		element(by.id('confirmPassword')).sendKeys(correctCredentials.password)
		element(by.id('email')).sendKeys(correctCredentials.email)
		element(by.id('confirmEmail')).sendKeys(correctCredentials.email)
		element(by.id('password')).sendKeys(protractor.Key.TAB)

		browser.driver.sleep(500)
		element(by.xpath('//button[contains(text(), "Next")]')).click()
		expect(element(by.xpath('//h4[contains(text(), "Dependent Users")]')).isDisplayed()).toBe(true)
	})

	it('should save credentials when returning to first step', function() {
		//add confirm credentials?
		browser.driver.sleep(500)
		element(by.xpath('//button[contains(text(), "Back")]')).click()
		expect(element(by.id('email')).getAttribute('value')).toBe(correctCredentials.email)
		expect(element(by.id('password')).getAttribute('value')).toBe(correctCredentials.password)

	})

	it('should not go to the next step from dependents when no credentials are filled in', function() {

		//go to dependents
		element(by.id('confirmPassword')).sendKeys(correctCredentials.password)
		element(by.id('confirmEmail')).sendKeys(correctCredentials.email)
		element(by.xpath('//button[contains(text(), "Next")]')).click()

		expect(element(by.xpath('//h4[contains(text(), "Dependent Users")]')).isDisplayed()).toBe(true)

		element(by.xpath('//button[contains(text(), "Next")]')).click()

		expect(element(by.xpath('//h4[contains(text(), "Dependent Users")]')).isDisplayed()).toBe(true)

	})

	it('should not continue when dependent age is under 16', function() {
		fillDependentsInfo('03/03/2016', 1)

		element(by.xpath('//button[contains(text(), "Next")]')).click()

		expect(element(by.xpath('//h4[contains(text(), "Dependent Users")]')).isDisplayed()).toBe(true)
	})

	it('should add additional dependents', function() {
		element(by.xpath('//button[contains(text(), "Add Dependents")]')).click()
		element(by.xpath('//button[contains(text(), "Add Dependents")]')).click()

		//one element from the additional dependent users
		expect(element(by.id('firstname2')).isDisplayed()).toBe(true)
		expect(element(by.id('firstname3')).isDisplayed()).toBe(true)

	})

	it('should remove the proper dependent', function() {
		element(by.name('removeButton2')).click()

		//second dependent should be removed
		expect($$('#firstname2').count()).toBe(0)
	})

	it('should allow to go to next step when a dependent is of age', function() {
		fillDependentsInfo('03/03/1990', 3)
		element(by.xpath('//button[contains(text(), "Next")]')).click()
		browser.driver.sleep(500)

		expect(element(by.xpath('//h4[contains(text(), "Emergency Contact")]')).isDisplayed()).toBe(true)
	})

	it('should not go to next step when dependent of age is deleted', function() {
		element(by.xpath('//button[contains(text(), "Back")]')).click()

		element(by.name('removeButton3')).click()
		element(by.xpath('//button[contains(text(), "Next")]')).click()

		expect(element(by.xpath('//h4[contains(text(), "Dependent User")]')).isDisplayed()).toBe(true)
	})

	it('should not allow user to continue if emergency contact is not filled in', function() {
		fillDependentsInfo('03/03/1990', 1)
		element(by.xpath('//button[contains(text(), "Next")]')).click()
		element(by.xpath('//button[contains(text(), "Next")]')).click()

		expect(element(by.xpath('//h4[contains(text(), "Emergency Contact")]')).isDisplayed()).toBe(true)
	})

	it('should not allow user to continue if emergency contact is not filled in', function() {
		element(by.id('emergencyContactName')).sendKeys('a')
		element(by.id('phone')).clear()
		element(by.id('phone')).sendKeys('1')

		element(by.id('phone')).sendKeys(protractor.Key.TAB)

		element(by.xpath('//button[contains(text(), "Next")]')).click()
		browser.driver.sleep(500)

		expect(element(by.xpath('//h4[contains(text(), "Review Account Details")]')).isDisplayed()).toBe(true)
	})

	it('should not allow user to go to next step when account creation is cancelled, even though previous credentials were correct', function() {
		
		browser.driver.sleep(500)

		element(by.xpath('//*[contains(text(), "Back")]')).click()
		browser.driver.sleep(500)

		element(by.xpath('//button[contains(text(), "Back")]')).click()
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