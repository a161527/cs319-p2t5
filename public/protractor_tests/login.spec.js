'use strict'

describe('login page', function() {

	var url = 'http://localhost:8000'
	browser.get(url)

	it('should not display errors on init', function() {
		expect(element(by.css('.alert-danger')).isDisplayed()).toBe(false)
	})

	it('should not display error when username input is alphanumeric, "-" or "_"', function() {
		element(by.model('username')).sendKeys('a0-_');

		expect(element(by.css('.alert-danger')).isDisplayed()).toBe(false)
	})

	it('should display requires username message typing and deleting input', function() {
		element(by.model('username')).clear().then(function() {
			expect(element(by.xpath('//*[contains(text(), "Username is required")]')).isDisplayed()).toBe(true)
		});		
	})

	it('should display error message for invalid characters', function() {
		element(by.model('username')).sendKeys('+');

		expect(element(by.xpath('//*[contains(text(), "Username can only contain alphanumeric characters, dashes, and underscores")]'))
			.isDisplayed())
		.toBe(true)
	})

	it('should not display error for any password input, "-" or "_"', function() {
		browser.get(url)
		element(by.model('password')).sendKeys('a0-_+');

		expect(element(by.css('.alert-danger')).isDisplayed()).toBe(false)
	})

	it('should display requires password message on delete', function() {
		element(by.model('password')).clear().then(function() {

			expect(element(by.xpath('//*[contains(text(), "Password is required")]')).isDisplayed())
			.toBe(true)

		});	
	})

	it('should display require messages when login is clicked with no fields', function() {
		browser.get(url)
		element(by.css('.btn-default')).click()

		expect(element(by.xpath('//*[contains(text(), "Username is required")]')).isDisplayed()).toBe(true)
		expect(element(by.xpath('//*[contains(text(), "Password is required")]')).isDisplayed()).toBe(true)
	})

})