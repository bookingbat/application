Online Appointment Booking
===========
Booking Bat allows you to take appointments from your website. Allow your customers to schedule appointments with Booking Bat!


Install
===========

## Create Project ##
Use composer to install the project
````
composer create-project --stability="dev" bookingbat/application
````

## Install Database ##
Copy `database-config.dist.ini` to `database-config.ini` and edit in your appropriate database credentials. Then run `install.sql`

## Run Tests ##
To run the unit tests simply run
````
vendor/bin/phpunit
````