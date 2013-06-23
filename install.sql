SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bookingbat`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `staff_userid` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(3) NOT NULL,
  `canceled` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `staff_userid`, `user_id`, `date`, `time`, `duration`, `canceled`) VALUES
(1, 15, 9, '2013-04-29', '19:00:00', 60, 1),
(2, 15, 9, '2013-04-29', '21:30:00', 60, 0),
(3, 14, 9, '2013-04-07', '00:30:00', 60, 0),
(4, 15, 9, '2013-04-07', '09:00:00', 60, 0),
(5, 15, 9, '2013-04-07', '00:30:00', 60, 0),
(6, 15, 9, '2013-04-03', '07:30:00', 60, 0),
(7, 19, 0, '2013-06-27', '00:30:00', 90, 1);

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE IF NOT EXISTS `availability` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `staff_userid` int(50) NOT NULL,
  `day_of_week` int(11) NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`id`, `staff_userid`, `day_of_week`, `start`, `end`) VALUES
(40, 19, 2, '00:30:00', '19:00:00'),
(41, 19, 4, '00:30:00', '22:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `sent` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;


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
  `first_name` varchar(25) NOT NULL,
  `last_name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `type`, `phone`, `first_name`, `last_name`) VALUES
(6, 'admin', 'admin@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', '', '', ''),
(19, 'staff', 'staff@example.com', '6ccb4b7c39a6e77f76ecfa935a855c6c46ad5611', 'staff', '7729244299', 'staff', 'staff');

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