-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `task_status_histories`;
CREATE TABLE `task_status_histories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL,
  `task_status` enum('opened','closed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'opened',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  CONSTRAINT `task_status_histories_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;