# cs319-p2t5
CS319 Project 2 Team 5

Running Jasmine tests:
- install all dependencies with 'npm install'
- start Karma with 'karma start karma.config'

Running Protractor:
- install Protractor globally 'npm install -g protractor'
- run 'webdriver-manager update'
- start Selenium server 'webdriver-manager start'
- tests are in 'protractor_tests' navigate here and run 'protractor protractor.config'
- all tests added must contain '.spec' in the file name

Database (prereq):

-You need a DB server running on your local machine. this could be either phpmyadmin or mysqlserver etc.

-then make sure the .env file has the credentials to login to the database on your local machine

-use the "php artisan migrate" command to populate DB.

-use the "php artisan migrate:reset" command to delete all table from DB.
