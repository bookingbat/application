SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


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
-- Table structure for table `email_queue`
--

CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `sent` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

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
(6, 'admin', 'admin@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', '', 0, 0, 0, '', '');
