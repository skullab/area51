-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2016 alle 23:03
-- Versione del server: 5.6.17
-- PHP Version: 5.6.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `area_riservata`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `acl_access_list`
--

CREATE TABLE IF NOT EXISTS `acl_access_list` (
  `roles_name` varchar(32) NOT NULL,
  `resources_name` varchar(32) NOT NULL,
  `access_name` varchar(32) NOT NULL,
  `allowed` int(3) NOT NULL,
  PRIMARY KEY (`roles_name`,`resources_name`,`access_name`),
  KEY `resources_name` (`resources_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `acl_access_list`
--

INSERT INTO `acl_access_list` (`roles_name`, `resources_name`, `access_name`, `allowed`) VALUES
('Admin', '*', '*', 1),
('Guest', 'frontend:index', '*', 1),
('User', 'backend:index', 'index', 1),
('User', 'backend:users', 'profile', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `acl_resources`
--

CREATE TABLE IF NOT EXISTS `acl_resources` (
  `name` varchar(32) NOT NULL,
  `description` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `acl_resources`
--

INSERT INTO `acl_resources` (`name`, `description`) VALUES
('*', 'All resources of application.'),
('backend:index', 'Module: Backend - Controller: Index'),
('frontend:index', '');

--
-- Trigger `acl_resources`
--
DROP TRIGGER IF EXISTS `acl_resources_after_delete`;
DELIMITER //
CREATE TRIGGER `acl_resources_after_delete` AFTER DELETE ON `acl_resources`
 FOR EACH ROW BEGIN
DELETE FROM acl_resources_access WHERE resources_name = OLD.name ;
DELETE FROM acl_access_list WHERE resources_name = OLD.name ;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `acl_resources_access`
--

CREATE TABLE IF NOT EXISTS `acl_resources_access` (
  `resources_name` varchar(32) NOT NULL,
  `access_name` varchar(32) NOT NULL,
  PRIMARY KEY (`resources_name`,`access_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `acl_resources_access`
--

INSERT INTO `acl_resources_access` (`resources_name`, `access_name`) VALUES
('backend:index', 'index'),
('frontend:index', 'index'),
('frontend:index', 'sign');

-- --------------------------------------------------------

--
-- Struttura della tabella `acl_roles`
--

CREATE TABLE IF NOT EXISTS `acl_roles` (
  `name` varchar(32) NOT NULL,
  `description` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `acl_roles`
--

INSERT INTO `acl_roles` (`name`, `description`) VALUES
('Admin', 'Amministratore : Ha accesso a tutto'),
('Bookkeeper', 'Contabilita'),
('Guest', 'Visitatore : Ha accesso solo al frontend'),
('Promotions Manager', 'Responsabile approvazioni promozioni'),
('Sales Agent', 'Agente Commerciale'),
('Supply Chain', 'Gestione della catena di approvvigionamento'),
('User', 'Utente base');

--
-- Trigger `acl_roles`
--
DROP TRIGGER IF EXISTS `acl_roles_after_delete`;
DELIMITER //
CREATE TRIGGER `acl_roles_after_delete` AFTER DELETE ON `acl_roles`
 FOR EACH ROW BEGIN
DELETE FROM acl_roles_inherits WHERE roles_inherits = OLD.name ;
DELETE FROM acl_access_list WHERE roles_name = OLD.name;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `acl_roles_inherits`
--

CREATE TABLE IF NOT EXISTS `acl_roles_inherits` (
  `roles_name` varchar(32) NOT NULL,
  `roles_inherits` varchar(32) NOT NULL,
  PRIMARY KEY (`roles_name`,`roles_inherits`),
  KEY `roles_inherits` (`roles_inherits`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `acl_roles_inherits`
--

INSERT INTO `acl_roles_inherits` (`roles_name`, `roles_inherits`) VALUES
('User', 'Guest'),
('Admin', 'User');

-- --------------------------------------------------------

--
-- Struttura della tabella `app_settings`
--

CREATE TABLE IF NOT EXISTS `app_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `data` blob,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `locale` char(5) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `locale` (`locale`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dump dei dati per la tabella `languages`
--

INSERT INTO `languages` (`id`, `locale`, `is_default`) VALUES
(1, 'it_IT', 0),
(2, 'en_US', 0),
(3, 'es_ES', 0),
(11, 'de_DE', 0);

--
-- Trigger `languages`
--
DROP TRIGGER IF EXISTS `languages_before_insert`;
DELIMITER //
CREATE TRIGGER `languages_before_insert` BEFORE INSERT ON `languages`
 FOR EACH ROW BEGIN
	DECLARE howMany TINYINT(1);
	DECLARE hasDefault TINYINT(1);
    SELECT COUNT(*) FROM languages INTO howMany;
    SELECT SUM(is_default) FROM languages INTO hasDefault ;
    IF howMany = 0 THEN
    	SET NEW.is_default = 1 ;
    END IF;
    IF hasDefault AND NEW.is_default THEN
    	SET NEW.is_default = 0 ;
    END IF;
END
//
DELIMITER ;
DROP TRIGGER IF EXISTS `languages_before_update`;
DELIMITER //
CREATE TRIGGER `languages_before_update` BEFORE UPDATE ON `languages`
 FOR EACH ROW BEGIN
	DECLARE hasDefault TINYINT(1);
    SELECT SUM(is_default) FROM languages INTO hasDefault ;
    IF hasDefault AND NEW.is_default THEN
    	SET NEW.is_default = 0 ;
    END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `namespace` varchar(112) DEFAULT NULL,
  `controller` varchar(32) NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace_controller` (`namespace`,`controller`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dump dei dati per la tabella `settings`
--

INSERT INTO `settings` (`id`, `namespace`, `controller`, `data`) VALUES
(11, 'API\\Mvc\\Controller', 'index', 0x613a353a7b733a353a227469746c65223b733a383a226d79207469746c65223b733a333a22696e74223b693a37383b733a343a22626f6f6c223b623a313b733a343a226e756c6c223b4e3b733a353a22666c6f6174223b643a372e373939393939393939393939393939383b7d),
(12, 'Thunderhawk\\Modules\\Frontend\\Controllers', 'index', 0x613a313a7b733a343a2274657374223b733a31313a2268656c6c6f20776f726c64223b7d);

--
-- Trigger `settings`
--
DROP TRIGGER IF EXISTS `settings_before_insert`;
DELIMITER //
CREATE TRIGGER `settings_before_insert` BEFORE INSERT ON `settings`
 FOR EACH ROW BEGIN
	IF NEW.namespace IS NULL THEN
    	SET NEW.namespace = '' ;
    END IF ;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Struttura della tabella `translations`
--

CREATE TABLE IF NOT EXISTS `translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment` varchar(255) DEFAULT NULL,
  `msgid` text NOT NULL,
  `msgstr` text NOT NULL,
  `languages_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_translations_languages1_idx` (`languages_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_id` int(10) unsigned NOT NULL,
  `role` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `status_id` (`status_id`,`role`),
  KEY `role` (`role`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`, `status_id`, `role`) VALUES
(4, 'ivan.maruca@gmail.com', '$2a$08$D9SvrVILt90goHFVdON7G.HR0EhGdO5z4O9nJDz2pd7tAhcqKZMfy', '2016-02-23 15:59:04', 1, 'Admin'),
(11, 'foo@foo.it', '$2a$08$1ur8OIi2QLlJ57DSfpTUFesv5KNODralkpp6Y0LJPvJOGMWOKGE9.', '2016-03-04 02:14:16', 1, 'User');

-- --------------------------------------------------------

--
-- Struttura della tabella `users_details`
--

CREATE TABLE IF NOT EXISTS `users_details` (
  `name` varchar(45) DEFAULT NULL,
  `surname` varchar(45) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `portrait` varchar(255) DEFAULT NULL,
  `users_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`users_id`),
  KEY `fk_users_details_users1_idx` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `users_details`
--

INSERT INTO `users_details` (`name`, `surname`, `address`, `phone`, `portrait`, `users_id`) VALUES
('', '', '', '', NULL, 11);

-- --------------------------------------------------------

--
-- Struttura della tabella `users_failed_attempts`
--

CREATE TABLE IF NOT EXISTS `users_failed_attempts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `ip_address` int(10) unsigned DEFAULT NULL,
  `attempted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=121 ;

--
-- Dump dei dati per la tabella `users_failed_attempts`
--

INSERT INTO `users_failed_attempts` (`id`, `users_id`, `ip_address`, `attempted`) VALUES
(117, 4, 2130706433, '2016-02-27 17:14:33'),
(118, 2, 2130706433, '2016-02-27 18:23:13'),
(119, 0, 2130706433, '2016-03-02 02:39:15'),
(120, 9, 2130706433, '2016-03-04 01:40:19');

-- --------------------------------------------------------

--
-- Struttura della tabella `users_forgot_password`
--

CREATE TABLE IF NOT EXISTS `users_forgot_password` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(255) NOT NULL,
  `private_key` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  `users_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_forgot_password_users1_idx` (`users_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dump dei dati per la tabella `users_forgot_password`
--

INSERT INTO `users_forgot_password` (`id`, `created_at`, `token`, `private_key`, `expires`, `users_id`) VALUES
(3, '2016-03-02 23:03:59', 'ðñÝuªïÞ\\#UüèxÎÛ’Tx<‡ÿú¤–†JÀ¼MN·mÙþlV¿ÊÉø@$û]»Î†[q‹$\ZèºÄ¹	ÈŒâx\\€å?ðo3*ë-_§', '-‘‡Í<©WBÃ)7D>', '2016-03-03 01:03:59', 4),
(4, '2016-03-03 00:27:49', '—¦ñˆ&or%˜G^þ¬1	©äÒE¬eæ%oTh}Á†F\r!vnN»Ši	W-QtfGêý\\ëÉþÉmü¸eèqEsÁbþÕº-@æ¤æk', 'ãÁÿ''5JØŽÝÆ&BÑô', '2016-03-03 02:27:49', 4);

-- --------------------------------------------------------

--
-- Struttura della tabella `users_login`
--

CREATE TABLE IF NOT EXISTS `users_login` (
  `last_access` datetime NOT NULL,
  `last_operation` datetime DEFAULT NULL,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `busy` tinyint(1) NOT NULL DEFAULT '0',
  `users_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`users_id`),
  KEY `fk_users_login_users1_idx` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `users_login`
--

INSERT INTO `users_login` (`last_access`, `last_operation`, `online`, `busy`, `users_id`) VALUES
('2016-03-08 23:00:21', '2016-03-08 23:01:17', 1, 0, 4),
('2016-03-06 14:24:13', '2016-03-06 14:25:51', 1, 0, 11);

-- --------------------------------------------------------

--
-- Struttura della tabella `users_remember_tokens`
--

CREATE TABLE IF NOT EXISTS `users_remember_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL,
  `selector` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `users_status`
--

CREATE TABLE IF NOT EXISTS `users_status` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `status` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dump dei dati per la tabella `users_status`
--

INSERT INTO `users_status` (`id`, `name`, `description`) VALUES
(1, 'active', 'The user is active'),
(2, 'locked', 'The user is temporarly locked'),
(3, 'suspended', 'The user is suspended'),
(4, 'banned', 'The user is banned'),
(5, 'hacked', 'the user account has been hacked');

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `acl_roles_inherits`
--
ALTER TABLE `acl_roles_inherits`
  ADD CONSTRAINT `acl_roles_inherits_ibfk_1` FOREIGN KEY (`roles_name`) REFERENCES `acl_roles` (`name`) ON DELETE CASCADE,
  ADD CONSTRAINT `acl_roles_inherits_ibfk_2` FOREIGN KEY (`roles_inherits`) REFERENCES `acl_roles` (`name`) ON DELETE CASCADE;

--
-- Limiti per la tabella `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `fk_translations_languages1` FOREIGN KEY (`languages_id`) REFERENCES `languages` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `users_status` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role`) REFERENCES `acl_roles` (`name`) ON DELETE CASCADE;

--
-- Limiti per la tabella `users_details`
--
ALTER TABLE `users_details`
  ADD CONSTRAINT `fk_users_details_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users_forgot_password`
--
ALTER TABLE `users_forgot_password`
  ADD CONSTRAINT `fk_users_forgot_password_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users_login`
--
ALTER TABLE `users_login`
  ADD CONSTRAINT `fk_users_login_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users_remember_tokens`
--
ALTER TABLE `users_remember_tokens`
  ADD CONSTRAINT `users_remember_tokens_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
