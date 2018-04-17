Please add security to all pages probably at the top. 
Make sure that you carry the right session between pages, make sure that you create a token for the sessions inroder to make sure that it is secure. Or other security technology that you wish to use.


Please create table for users data

Users can:

1) clock-in and clock-out
2) request vacation day
3) request sick days 
4) database will be updated both timecard and users database.

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lname` varchar(100) CHARACTER SET latin1 NOT NULL,
  `gname` varchar(100) CHARACTER SET latin1 NOT NULL,
  `location` varchar(200) NOT NULL,
  `wife` int(2) NOT NULL,
  `children` int(2) NOT NULL,
  `payrate` int(50) NOT NULL,
  `username` varchar(50) CHARACTER SET latin1 NOT NULL,
  `password` varchar(200) CHARACTER SET latin1 NOT NULL,
  `attempt` int(3) NOT NULL,
  `lockout` int(11) NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `phone` char(11) CHARACTER SET latin1 NOT NULL,
  `profile_pic` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` varchar(50) CHARACTER SET latin1 NOT NULL,
  `gender` varchar(10) CHARACTER SET latin1 NOT NULL,
  `pw` varchar(200) NOT NULL,
  `status` int(4) NOT NULL,
  `in_or_out` int(4) NOT NULL DEFAULT '0',
  `punchin` datetime NOT NULL,
  `status_today` varchar(100) NOT NULL,
  `request_vacation` date NOT NULL,
  `request_vacation1` date NOT NULL,
  `request_vacation2` date NOT NULL,
  `sick_day_available` int(10) NOT NULL,
  `sick_day_used` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;


Please also create the timecard table

CREATE TABLE IF NOT EXISTS `timecard` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `employment_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `location` varchar(200) NOT NULL,
  `rank_rate` int(11) NOT NULL,
  `clockin` datetime NOT NULL,
  `clockout` datetime NOT NULL,
  `total` time NOT NULL,
  `overtime` time NOT NULL,
  `payroll` int(20) NOT NULL,
  `actual_time_in` datetime NOT NULL,
  `actual_time_out` datetime NOT NULL,
  `actual_total` time NOT NULL,
  `comment` text NOT NULL,
  `process_by` varchar(200) NOT NULL,
  `recon` varchar(100) NOT NULL,
  `dept` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;
