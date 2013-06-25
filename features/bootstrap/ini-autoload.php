<?php
ini_set('display_errors', 'on');
require 'application/bootstrap.php';
require_once('vendor/autoload.php');

`mysql --user=root -e "drop database bookingbat_tests"`;
`mysql --user=root -e "create database bookingbat_tests"`;
`mysql --user=root bookingbat_tests < install.sql`;