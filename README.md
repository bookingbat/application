[![Build Status](https://travis-ci.org/bookingbat/application.png?branch=master)](https://travis-ci.org/bookingbat/application)

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
./test
````

Ensure xdebug is enabled for your CLI webserver. With xdebug present, ZF2 redirects do not run if a PHP warning was generated earlier. Without xdebug they do work. Therefore there may be a test failure in Travis CI (which uses xdebug) that you don't see locally, if you don't do this:

````
php --server=localhost:8000 --docroot="public" &
echo '<?php phpinfo();' > public/phpinfo.php
#find the php.ini path and load xdebug like you normally do
ps aux | grep php
# (find the PID of the webserver & kill it, then restart it)
````
