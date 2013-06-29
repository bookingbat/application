SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `staff_userid` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(3) NOT NULL,
  `canceled` int(1) NOT NULL,
  `guest_name` varchar(50) NOT NULL,
  `guest_phone` int(10) NOT NULL,
  `guest_email` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

CREATE TABLE IF NOT EXISTS `availability` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `staff_userid` int(50) NOT NULL,
  `day_of_week` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `sent` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;


CREATE TABLE IF NOT EXISTS `user` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` varchar(35) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `staff_services` (
  `staff_user_id` int(10) NOT NULL,
  `service_id` int(10) NOT NULL,
  PRIMARY KEY (`staff_user_id`,`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE  `services` ADD  `padding` INT( 3 ) NOT NULL;
ALTER TABLE  `services` ADD  `durations` VARCHAR( 50 ) NOT NULL;