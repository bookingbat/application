-- phpMyAdmin SQL Dump
-- version 3.5.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 14, 2013 at 11:12 PM
-- Server version: 5.5.29-0ubuntu0.12.04.2
-- PHP Version: 5.3.10-1ubuntu3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `famefit`
--

-- --------------------------------------------------------

--
-- Table structure for table `class_enrollment`
--

CREATE TABLE IF NOT EXISTS `class_enrollment` (
  `user_id` int(50) NOT NULL,
  `class_id` int(50) NOT NULL,
  `date` date NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`class_id`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `class_enrollment`
--

INSERT INTO `class_enrollment` (`user_id`, `class_id`, `date`) VALUES
(9, 1, '2013-04-02'),
(9, 1, '2013-04-09'),
(9, 1, '2013-04-16'),
(9, 1, '2013-04-23'),
(9, 1, '2013-04-30'),
(9, 1, '2013-05-07'),
(9, 1, '2013-06-04');

-- --------------------------------------------------------

--
-- Table structure for table `class_schedule`
--

CREATE TABLE IF NOT EXISTS `class_schedule` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `instructor_userid` int(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `day_of_week` int(1) NOT NULL,
  `time` time NOT NULL,
  `condo_id` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `class_schedule`
--

INSERT INTO `class_schedule` (`id`, `instructor_userid`, `name`, `day_of_week`, `time`, `condo_id`) VALUES
(1, 8, 'Zumba', 2, '13:00:00', 0),
(2, 8, 'Super Zumba', 1, '11:00:00', 0),
(3, 8, 'class-only-at-condotower2', 1, '00:30:00', 0),
(4, 8, 'only-tower1', 1, '00:30:00', 1),
(5, 8, 'only-tower2', 1, '00:30:00', 3);

-- --------------------------------------------------------

--
-- Table structure for table `condo`
--

CREATE TABLE IF NOT EXISTS `condo` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `master_trainer_userid` int(7) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `condo`
--

INSERT INTO `condo` (`id`, `name`, `master_trainer_userid`, `active`) VALUES
(1, 'Tower 1-edited', 10, 1),
(3, 'Tower2', 13, 1),
(4, 'foobar', 13, 0);

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `sent` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `email_queue`
--

INSERT INTO `email_queue` (`id`, `data`, `sent`) VALUES
(1, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:5:"utf-8";s:11:"\0*\0_headers";a:3:{s:2:"To";a:3:{i:0;s:17:"jshpro2@gmail.com";s:6:"append";b:1;i:1;s:17:"jshpro2@gmail.com";}s:7:"Subject";a:1:{i:0;s:27:"Food Delivery Form Received";}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:2:{i:0;s:17:"jshpro2@gmail.com";i:1;s:17:"jshpro2@gmail.com";}s:14:"\0*\0_recipients";a:1:{s:17:"jshpro2@gmail.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";s:27:"Food Delivery Form Received";s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";b:0;s:12:"\0*\0_bodyHtml";O:14:"Zend_Mime_Part":12:{s:4:"type";s:9:"text/html";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:5:"utf-8";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:1880:"Food request submitted!\n<table>\n            <tr>\n            <th>cardNumber</th>\n            <td>4111111111111111</td>\n        </tr>\n                <tr>\n            <th>expirationDate</th>\n            <td>02-2017</td>\n        </tr>\n                <tr>\n            <th>firstName</th>\n            <td>65465</td>\n        </tr>\n                <tr>\n            <th>lastName</th>\n            <td>6456</td>\n        </tr>\n                <tr>\n            <th>address</th>\n            <td>654</td>\n        </tr>\n                <tr>\n            <th>city</th>\n            <td>654</td>\n        </tr>\n                <tr>\n            <th>state</th>\n            <td>654</td>\n        </tr>\n                <tr>\n            <th>zip</th>\n            <td>92105</td>\n        </tr>\n                <tr>\n            <th>email</th>\n            <td>jshpro2@gmail.com</td>\n        </tr>\n                <tr>\n            <th>delivery_type</th>\n            <td>0</td>\n        </tr>\n                <tr>\n            <th>company_name</th>\n            <td>k</td>\n        </tr>\n                <tr>\n            <th>community_building</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>apt_suite</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>phone</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>meals_per_day</th>\n            <td>2</td>\n        </tr>\n                <tr>\n            <th>days_per_week</th>\n            <td>4</td>\n        </tr>\n                <tr>\n            <th>allergies</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>food_dislikes</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>terms</th>\n            <td>1</td>\n        </tr>\n                <tr>\n            <th>amount</th>\n            <td>25</td>\n        </tr>\n        </table>";s:12:"\0*\0_isStream";b:0;}s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '2013-04-14 18:52:13'),
(2, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:5:"utf-8";s:11:"\0*\0_headers";a:3:{s:2:"To";a:3:{i:0;s:17:"jshpro2@gmail.com";s:6:"append";b:1;i:1;s:17:"jshpro2@gmail.com";}s:7:"Subject";a:1:{i:0;s:27:"Food Delivery Form Received";}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:2:{i:0;s:17:"jshpro2@gmail.com";i:1;s:17:"jshpro2@gmail.com";}s:14:"\0*\0_recipients";a:1:{s:17:"jshpro2@gmail.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";s:27:"Food Delivery Form Received";s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";b:0;s:12:"\0*\0_bodyHtml";O:14:"Zend_Mime_Part":12:{s:4:"type";s:9:"text/html";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:5:"utf-8";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:1881:"Food request submitted!\n<table>\n            <tr>\n            <th>cardNumber</th>\n            <td>4111111111111111</td>\n        </tr>\n                <tr>\n            <th>expirationDate</th>\n            <td>02-2017</td>\n        </tr>\n                <tr>\n            <th>firstName</th>\n            <td>sdfsd8</td>\n        </tr>\n                <tr>\n            <th>lastName</th>\n            <td>6456</td>\n        </tr>\n                <tr>\n            <th>address</th>\n            <td>654</td>\n        </tr>\n                <tr>\n            <th>city</th>\n            <td>654</td>\n        </tr>\n                <tr>\n            <th>state</th>\n            <td>654</td>\n        </tr>\n                <tr>\n            <th>zip</th>\n            <td>92105</td>\n        </tr>\n                <tr>\n            <th>email</th>\n            <td>jshpro2@gmail.com</td>\n        </tr>\n                <tr>\n            <th>delivery_type</th>\n            <td>0</td>\n        </tr>\n                <tr>\n            <th>company_name</th>\n            <td>k</td>\n        </tr>\n                <tr>\n            <th>community_building</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>apt_suite</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>phone</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>meals_per_day</th>\n            <td>2</td>\n        </tr>\n                <tr>\n            <th>days_per_week</th>\n            <td>4</td>\n        </tr>\n                <tr>\n            <th>allergies</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>food_dislikes</th>\n            <td>l</td>\n        </tr>\n                <tr>\n            <th>terms</th>\n            <td>1</td>\n        </tr>\n                <tr>\n            <th>amount</th>\n            <td>25</td>\n        </tr>\n        </table>";s:12:"\0*\0_isStream";b:0;}s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '2013-04-14 18:53:19'),
(3, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:10:"iso-8859-1";s:11:"\0*\0_headers";a:2:{s:2:"To";a:3:{i:0;s:18:"client@example.com";s:6:"append";b:1;i:1;s:20:"trainer2@example.com";}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:2:{i:0;s:18:"client@example.com";i:1;s:20:"trainer2@example.com";}s:14:"\0*\0_recipients";a:2:{s:18:"client@example.com";i:1;s:20:"trainer2@example.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";N;s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";O:14:"Zend_Mime_Part":12:{s:4:"type";s:10:"text/plain";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:10:"iso-8859-1";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:83:"This confirms your 0.5hr appointment with Trainer2 Lastname on Mon Apr 1st 03:30 am";s:12:"\0*\0_isStream";b:0;}s:12:"\0*\0_bodyHtml";b:0;s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '0000-00-00 00:00:00'),
(4, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:10:"iso-8859-1";s:11:"\0*\0_headers";a:2:{s:2:"To";a:3:{i:0;s:18:"client@example.com";s:6:"append";b:1;i:1;s:20:"trainer2@example.com";}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:2:{i:0;s:18:"client@example.com";i:1;s:20:"trainer2@example.com";}s:14:"\0*\0_recipients";a:2:{s:18:"client@example.com";i:1;s:20:"trainer2@example.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";N;s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";O:14:"Zend_Mime_Part":12:{s:4:"type";s:10:"text/plain";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:10:"iso-8859-1";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:83:"This confirms your 0.5hr appointment with Trainer2 Lastname on Mon Apr 1st 04:00 am";s:12:"\0*\0_isStream";b:0;}s:12:"\0*\0_bodyHtml";b:0;s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '0000-00-00 00:00:00'),
(5, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:10:"iso-8859-1";s:11:"\0*\0_headers";a:2:{s:2:"To";a:3:{i:0;s:18:"client@example.com";s:6:"append";b:1;i:1;s:0:"";}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:2:{i:0;s:18:"client@example.com";i:1;N;}s:14:"\0*\0_recipients";a:2:{s:18:"client@example.com";i:1;s:0:"";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";N;s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";O:14:"Zend_Mime_Part":12:{s:4:"type";s:10:"text/plain";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:10:"iso-8859-1";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:65:"This confirms your 1hr appointment with   on Sun Apr 7th 12:30 am";s:12:"\0*\0_isStream";b:0;}s:12:"\0*\0_bodyHtml";b:0;s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '0000-00-00 00:00:00'),
(6, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:10:"iso-8859-1";s:11:"\0*\0_headers";a:2:{s:2:"To";a:3:{i:0;s:18:"client@example.com";s:6:"append";b:1;i:1;s:22:"therapist2@example.com";}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:2:{i:0;s:18:"client@example.com";i:1;s:22:"therapist2@example.com";}s:14:"\0*\0_recipients";a:2:{s:18:"client@example.com";i:1;s:22:"therapist2@example.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";N;s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";O:14:"Zend_Mime_Part":12:{s:4:"type";s:10:"text/plain";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:10:"iso-8859-1";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:76:"This confirms your 1hr appointment with test test123 on Wed Apr 3rd 07:30 am";s:12:"\0*\0_isStream";b:0;}s:12:"\0*\0_bodyHtml";b:0;s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `therapist_appointments`
--

CREATE TABLE IF NOT EXISTS `therapist_appointments` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `therapist_userid` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(3) NOT NULL,
  `canceled` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `therapist_appointments`
--

INSERT INTO `therapist_appointments` (`id`, `therapist_userid`, `user_id`, `date`, `time`, `duration`, `canceled`) VALUES
(1, 15, 9, '2013-04-29', '19:00:00', 60, 1),
(2, 15, 9, '2013-04-29', '21:30:00', 60, 0),
(3, 14, 9, '2013-04-07', '00:30:00', 60, 0),
(4, 15, 9, '2013-04-07', '09:00:00', 60, 0),
(5, 15, 9, '2013-04-07', '00:30:00', 60, 0),
(6, 15, 9, '2013-04-03', '07:30:00', 60, 0);

-- --------------------------------------------------------

--
-- Table structure for table `therapist_availability`
--

CREATE TABLE IF NOT EXISTS `therapist_availability` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `therapist_userid` int(50) NOT NULL,
  `day_of_week` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `therapist_availability`
--

INSERT INTO `therapist_availability` (`id`, `therapist_userid`, `day_of_week`, `start`, `end`) VALUES
(31, 15, 3, '07:30:00', '09:30:00'),
(37, 14, 7, '00:30:00', '01:30:00'),
(39, 15, 7, '00:30:00', '22:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `therapist_condos`
--

CREATE TABLE IF NOT EXISTS `therapist_condos` (
  `therapist_userid` int(5) NOT NULL,
  `condo_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `therapist_condos`
--

INSERT INTO `therapist_condos` (`therapist_userid`, `condo_id`) VALUES
(14, 1),
(14, 3),
(15, 1),
(15, 3);

-- --------------------------------------------------------

--
-- Table structure for table `trainer_appointments`
--

CREATE TABLE IF NOT EXISTS `trainer_appointments` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `trainer_userid` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(3) NOT NULL,
  `canceled` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `trainer_appointments`
--

INSERT INTO `trainer_appointments` (`id`, `trainer_userid`, `user_id`, `date`, `time`, `duration`, `canceled`) VALUES
(1, 13, 9, '2013-04-29', '03:30:00', 60, 1),
(2, 13, 9, '2013-04-08', '03:30:00', 60, 0),
(3, 13, 9, '2013-04-29', '03:30:00', 60, 1),
(4, 13, 9, '2013-04-01', '03:30:00', 30, 0),
(5, 13, 9, '2013-04-01', '04:00:00', 30, 0);

-- --------------------------------------------------------

--
-- Table structure for table `trainer_availability`
--

CREATE TABLE IF NOT EXISTS `trainer_availability` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `trainer_userid` int(50) NOT NULL,
  `day_of_week` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `trainer_availability`
--

INSERT INTO `trainer_availability` (`id`, `trainer_userid`, `day_of_week`, `start`, `end`) VALUES
(24, 0, 1, '00:30:00', '01:30:00'),
(26, 13, 1, '03:30:00', '04:30:00'),
(29, 10, 1, '00:30:00', '05:30:00'),
(30, 10, 2, '02:30:00', '05:30:00'),
(31, 10, 4, '01:00:00', '02:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` varchar(35) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `assigned_trainer_userid` int(50) NOT NULL,
  `condo_id` int(50) NOT NULL,
  `member` tinyint(1) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `type`, `phone`, `assigned_trainer_userid`, `condo_id`, `member`, `first_name`, `last_name`) VALUES
(6, 'admin', 'admin@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', '', 0, 0, 0, '', ''),
(8, 'instructor', 'instructor@example.com', '5db3005d1c92d3def956044087157bb23f29c6b0', 'class-instructor', '', 0, 0, 0, '', ''),
(9, 'client', 'client@example.com', 'd2a04d71301a8915217dd5faf81d12cffd6cd958', 'client', '772-924-4299', 0, 3, 0, 'Josh', 'Ribakoff'),
(10, 'trainer', 'trainer@example.com', '297e1479cf75d300a89a5b6ec208fd979209878b', 'trainer', '', 0, 0, 0, 'Trainer', 'McTrainerson'),
(12, 'client2', 'client2@example.com', 'd2a04d71301a8915217dd5faf81d12cffd6cd958', 'client', '7729244299', 0, 0, 1, '', ''),
(13, 'trainer2', 'trainer2@example.com', '55382988130ceaea9854390218e62bd330c78a40', 'trainer', '7729244299', 0, 0, 0, 'Trainer2', 'Lastname'),
(14, 'therapist', 'therapist@example.com', 'cbcbe46084f65e236884cc6e9855d5767ce177d9', 'massage-therapist', '1234567890', 0, 0, 0, 'Josh', 'Ribakoff'),
(15, 'therapist2', 'therapist2@example.com', '73b9f9d475352202fc785865209fb5a1942f6927', 'massage-therapist', '1234567890', 0, 0, 0, 'test', 'test123'),
(16, 'testuser', 'client@example.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'client', '1234567890', 0, 0, 0, 'test', '123'),
(17, 'test123', 'test123@example.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'trainer', '231321321312', 0, 0, 0, 'test12', '123'),
(18, 'test888', 'jshpro2@gmail.com', 'eaa67f3a93d0acb08d8a5e8ff9866f51983b3c3b', 'client', '888888888888', 0, 0, 0, '888', '888');

-- --------------------------------------------------------

--
-- Table structure for table `user_payments`
--

CREATE TABLE IF NOT EXISTS `user_payments` (
  `payment_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `service` varchar(15) NOT NULL,
  `service_quantity` int(3) NOT NULL,
  `datetime` datetime NOT NULL,
  `amount_paid` decimal(4,2) NOT NULL,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `user_payments`
--

INSERT INTO `user_payments` (`payment_id`, `user_id`, `service`, `service_quantity`, `datetime`, `amount_paid`) VALUES
(27, 9, 'training', 60, '2013-04-14 04:06:14', 60.00),
(28, 9, 'massage', 60, '2013-04-14 04:09:32', 80.00),
(29, 9, 'training', 60, '2013-04-14 04:12:10', 60.00),
(30, 9, 'massage', 90, '2013-04-14 16:08:09', 99.99),
(31, 9, 'massage', 60, '2013-04-14 16:10:14', 80.00),
(32, 9, 'massage', 60, '2013-04-14 19:33:39', 80.00),
(33, 9, 'massage', 60, '2013-04-14 23:11:20', 80.00);

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE IF NOT EXISTS `user_subscriptions` (
  `user_id` int(10) NOT NULL,
  `plan` int(1) NOT NULL,
  `start` date NOT NULL,
  `end` date DEFAULT NULL,
  `authorizenet_transaction_id` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`user_id`, `plan`, `start`, `end`, `authorizenet_transaction_id`) VALUES
(9, 1, '2013-04-07', '2013-04-07', ''),
(9, 2, '2013-04-07', '2013-04-07', ''),
(9, 2, '2013-04-07', NULL, '1670591');
