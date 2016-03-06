exports.url = 'http://localhost:8000'

exports.checkErrorMessage = function(label, msg, verification) {
	expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "'+label+'")]')).isDisplayed()).toBe(verification)
	expect(element(by.xpath('//div[@class="alert alert-danger"]//*[contains(text(), "'+msg+'")]')).isDisplayed()).toBe(verification)
}