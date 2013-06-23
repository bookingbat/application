Online Appointment Booking
===========
Booking Bat allows you to take appointments from your website. Allow your customers to schedule appointments with Booking Bat!


Install
===========

**Create Project**

Use composer to install the project
````
composer create-project --stability="dev" bookingbat/application
````

**Install Database**

Copy `database-config.dist.ini` to `database-config.ini` and edit in your appropriate database credentials. Then run `install.sql`

**Email queue**

The application sends emails for appointment confirmations & reminders. These are added to a queue which is then batched. To batch the queue hit the URL `/email/send`. You may wish to add a cron job to batch out the email queue at predefined intervals.

**Run Tests**
To run the unit tests simply run
````
vendor/bin/phpunit
````