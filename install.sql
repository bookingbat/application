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

--
-- Dumping data for table `email_queue`
--

INSERT INTO `email_queue` (`id`, `data`, `sent`) VALUES
(7, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:10:"iso-8859-1";s:11:"\0*\0_headers";a:2:{s:2:"To";a:2:{i:0;s:17:"staff@example.com";s:6:"append";b:1;}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:1:{i:0;s:17:"staff@example.com";}s:14:"\0*\0_recipients";a:1:{s:17:"staff@example.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";N;s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";O:14:"Zend_Mime_Part":12:{s:4:"type";s:10:"text/plain";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:10:"iso-8859-1";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:74:"This confirms your 1.5hr appointment with staff staff on 12:30am 6/27/2013";s:12:"\0*\0_isStream";b:0;}s:12:"\0*\0_bodyHtml";b:0;s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '0000-00-00 00:00:00'),
(8, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:10:"iso-8859-1";s:11:"\0*\0_headers";a:2:{s:2:"To";a:2:{i:0;s:17:"staff@example.com";s:6:"append";b:1;}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:1:{i:0;s:17:"staff@example.com";}s:14:"\0*\0_recipients";a:1:{s:17:"staff@example.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";N;s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";O:14:"Zend_Mime_Part":12:{s:4:"type";s:10:"text/plain";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:10:"iso-8859-1";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:40:"The appointment on \nhas been cancelled .";s:12:"\0*\0_isStream";b:0;}s:12:"\0*\0_bodyHtml";b:0;s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '0000-00-00 00:00:00'),
(9, 'O:9:"Zend_Mail":18:{s:11:"\0*\0_charset";s:10:"iso-8859-1";s:11:"\0*\0_headers";a:2:{s:2:"To";a:2:{i:0;s:17:"staff@example.com";s:6:"append";b:1;}s:4:"From";a:2:{i:0;s:39:"Fame Fitness <no-reply@famefitness.com>";s:6:"append";b:1;}}s:18:"\0*\0_headerEncoding";s:16:"quoted-printable";s:8:"\0*\0_from";s:24:"no-reply@famefitness.com";s:6:"\0*\0_to";a:1:{i:0;s:17:"staff@example.com";}s:14:"\0*\0_recipients";a:1:{s:17:"staff@example.com";i:1;}s:11:"\0*\0_replyTo";N;s:14:"\0*\0_returnPath";N;s:11:"\0*\0_subject";N;s:8:"\0*\0_date";N;s:13:"\0*\0_messageId";N;s:12:"\0*\0_bodyText";O:14:"Zend_Mime_Part":12:{s:4:"type";s:10:"text/plain";s:8:"encoding";s:16:"quoted-printable";s:2:"id";N;s:11:"disposition";s:6:"inline";s:8:"filename";N;s:11:"description";N;s:7:"charset";s:10:"iso-8859-1";s:8:"boundary";N;s:8:"location";N;s:8:"language";N;s:11:"\0*\0_content";s:50:"The appointment on 2013-06-27\nhas been cancelled .";s:12:"\0*\0_isStream";b:0;}s:12:"\0*\0_bodyHtml";b:0;s:16:"\0*\0_mimeBoundary";N;s:8:"\0*\0_type";N;s:14:"hasAttachments";b:0;s:9:"\0*\0_parts";a:0:{}s:8:"\0*\0_mime";N;}', '0000-00-00 00:00:00');

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
