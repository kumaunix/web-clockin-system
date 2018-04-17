
Please create table for sms data

CREATE TABLE IF NOT EXISTS `sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` varchar(255) DEFAULT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `conv_id` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `file` varchar(250) NOT NULL,
  `time` datetime DEFAULT NULL,
  `view` varchar(255) DEFAULT NULL,
  `view_time` datetime DEFAULT NULL,
  `ip_add` int(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;


