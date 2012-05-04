SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permaid` varchar(64) NOT NULL,
  `nick` varchar(16) NOT NULL,
  `date` datetime NOT NULL,
  `title` varchar(64) NOT NULL,
  `text` text NOT NULL,
  `tags` varchar(128) NOT NULL,
  `db` varchar(10) NOT NULL,
  `status` enum('draft','published') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(32) NOT NULL,
  `lang` varchar(5) NOT NULL,
  `locale` varchar(16) NOT NULL,
  `collate` varchar(24) NOT NULL,
  `analytics_enabled` tinyint(1) NOT NULL,
  `analytics_code` varchar(16) NOT NULL,
  `url` varchar(64) NOT NULL,
  `statics_url` varchar(64) NOT NULL,
  `theme` varchar(16) NOT NULL,
  `db` varchar(10) NOT NULL,
  `title` varchar(32) NOT NULL,
  `page_size` int(11) NOT NULL,
  `robots` enum('allow','disallow') NOT NULL,
  `mail` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;
