-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `task_time_reports`;
CREATE TABLE `task_time_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `date_stamp` date NOT NULL,
  `time_spent` float(10,2) unsigned NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `active` (`active`),
  CONSTRAINT `task_time_reports_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;