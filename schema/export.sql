-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2016 alle 05:17
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
('Guest', 'frontend:index', '*', 1),
('User', 'backend:index', 'index', 1);

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
('backend:index', NULL),
('frontend:index', NULL);

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
('Admin', NULL),
('Guest', NULL),
('User', 'Auth user');

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
  PRIMARY KEY (`roles_name`,`roles_inherits`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `acl_roles_inherits`
--

INSERT INTO `acl_roles_inherits` (`roles_name`, `roles_inherits`) VALUES
('Admin', 'User'),
('User', 'Guest');

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
  `msgid` text,
  `msgstr` text,
  `languages_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `msgid` (`msgid`(255),`languages_id`),
  KEY `fk_translations_languages1_idx` (`languages_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `created_at`, `status_id`, `role`) VALUES
(1, 'admin@thunderhawk.com', '$2a$08$FthHkgr9bcgUtbCLTnh7QOgHULeRmI89f53R6yvnXku.kc696MKW6', '2016-02-20 13:52:04', 1, 'Admin');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=98 ;

--
-- Dump dei dati per la tabella `users_failed_attempts`
--

INSERT INTO `users_failed_attempts` (`id`, `users_id`, `ip_address`, `attempted`) VALUES
(85, 1, 2130706433, '2016-02-22 14:31:12'),
(86, 1, 2130706433, '2016-02-22 14:31:42'),
(87, 1, 2130706433, '2016-02-22 14:32:45'),
(88, 0, 0, '2016-02-22 15:17:42'),
(89, 0, 2130706433, '2016-02-22 15:21:09'),
(90, 0, 2130706433, '2016-02-22 15:50:32'),
(91, 0, 2130706433, '2016-02-22 20:00:48'),
(92, 1, 2130706433, '2016-02-22 20:01:01'),
(93, 1, 2130706433, '2016-02-22 20:02:10'),
(94, 2, 2130706433, '2016-02-23 04:40:01'),
(95, 2, 2130706433, '2016-02-23 15:56:49'),
(96, 0, 2130706433, '2016-02-23 15:59:33'),
(97, 0, 2130706433, '2016-02-23 15:59:42');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dump dei dati per la tabella `users_remember_tokens`
--

INSERT INTO `users_remember_tokens` (`id`, `users_id`, `selector`, `token`, `expires`) VALUES
(10, 1, 'Øü¦CÝ3Êh3¢0oLhÊ|¹êdD÷\n˜d³4†', '6M\0DªiVÓÅ‰á£m¹§al¥Ð²£†U«cŠ?¾©[', '2016-03-01 16:57:57'),
(11, 1, 'ò/ì¹æZçÇßÖ¿`ÂÍ…=žéŽ ÿÎ{&xè:', 'Yî¡>ïª„s¯øx™žÁ_†á1ëÀµTý…', '2016-02-29 17:34:19'),
(12, 2, '³(°°†ûÿ²Z²m\nÚ‚Kb_¬§’_þ´Úäëe×²³¬’', '[>ú43s»¢QŸßGú›‰u<5Òg\Z+,,', '2016-03-01 16:56:54'),
(13, 4, 'ÖcØ|ñpíÒ<.tD3˜ú^¼ó"G?t&ØÎI~oJÚcü', '=V‰œ€ŒñLà…ü]ÑùçTã„õT»É)yÅxÑ.8', '2016-03-01 16:59:15'),
(14, 3, '±QÜ±®ZVF:N„XT:³BŽbÞ(‡Hè–ç', 'À?"ÁghNy‚\Zn!\nó0ou£ºœß¼®‰	jÃMz', '2016-03-01 17:00:06'),
(15, 3, 'ð]íMžÏÍAL×ˆ¡6’ù Qº€X+\0¼žÍûÑ¤^#', 'Ä§@äûî/S-ü;‡)ÑçÉøÆtÑlR}¼ªòUúH', '2016-03-01 17:10:12');

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
-- Limiti per la tabella `users_forgot_password`
--
ALTER TABLE `users_forgot_password`
  ADD CONSTRAINT `fk_users_forgot_password_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users_remember_tokens`
--
ALTER TABLE `users_remember_tokens`
  ADD CONSTRAINT `users_remember_tokens_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
