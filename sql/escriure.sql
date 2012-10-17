SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(32) CHARACTER SET utf8 NOT NULL,
  `mail` varchar(64) CHARACTER SET utf8 NOT NULL,
  `url` varchar(128) CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  `post_id` int(11) NOT NULL,
  `parent` int(11) NOT NULL DEFAULT '0',
  `db` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT 'Shouldn''t be needed (the db could be guessed from the post_id).',
  `status` enum('hidden','shown') CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

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
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `comment_status` enum('open','closed','hidden') NOT NULL DEFAULT 'closed',
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
  `site_key` varchar(32) NOT NULL,
  `meta_json` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

