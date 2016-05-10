-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2016 alle 16:19
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

-- --------------------------------------------------------

--
-- Struttura della tabella `agent_manager`
--

CREATE TABLE IF NOT EXISTS `agent_manager` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` int(10) unsigned NOT NULL,
  `manager_id` int(10) unsigned NOT NULL,
  `customers_destinations_address_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_agent_manager_users1_idx` (`agent_id`),
  KEY `fk_agent_manager_users2_idx` (`manager_id`),
  KEY `fk_agent_manager_customers_destinations_address1_idx` (`customers_destinations_address_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

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
-- Struttura della tabella `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codice` varchar(32) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `customers_groups_id` int(10) unsigned NOT NULL,
  `customers_state_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codice` (`codice`),
  KEY `fk_customers_customers_groups1_idx` (`customers_groups_id`),
  KEY `fk_customers_customers_state1_idx` (`customers_state_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_address`
--

CREATE TABLE IF NOT EXISTS `customers_address` (
  `codice_indirizzo` varchar(32) DEFAULT NULL,
  `indirizzo` text,
  `cap` varchar(32) DEFAULT NULL,
  `citta` varchar(45) DEFAULT NULL,
  `provincia` varchar(45) DEFAULT NULL,
  `regione` varchar(45) DEFAULT NULL,
  `nazione` varchar(45) DEFAULT NULL,
  `customers_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`customers_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_book`
--

CREATE TABLE IF NOT EXISTS `customers_book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bookkeeper_id` int(10) unsigned NOT NULL,
  `customers_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_customers_book_users1_idx` (`bookkeeper_id`),
  KEY `fk_customers_book_customers1_idx` (`customers_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_destinations`
--

CREATE TABLE IF NOT EXISTS `customers_destinations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codice_destinazione` varchar(32) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `codice_indirizzo` varchar(255) DEFAULT NULL,
  `codice_insegna` varchar(255) DEFAULT NULL,
  `insegna` varchar(255) DEFAULT NULL,
  `customers_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_customers_destinations_customers1_idx` (`customers_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_destinations_address`
--

CREATE TABLE IF NOT EXISTS `customers_destinations_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `indirizzo` text,
  `cap` varchar(45) DEFAULT NULL,
  `citta` varchar(45) DEFAULT NULL,
  `provincia` varchar(45) DEFAULT NULL,
  `regione` varchar(45) DEFAULT NULL,
  `nazione` varchar(45) DEFAULT NULL,
  `telefono` varchar(45) DEFAULT NULL,
  `note` text,
  `customers_destinations_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_customers_destinations_address_customers_destinations1_idx` (`customers_destinations_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_details`
--

CREATE TABLE IF NOT EXISTS `customers_details` (
  `telefono` varchar(45) DEFAULT NULL,
  `fax` varchar(45) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `piva` varchar(255) DEFAULT NULL,
  `cf` varchar(255) DEFAULT NULL,
  `epal` tinyint(1) DEFAULT NULL,
  `monoref` tinyint(1) DEFAULT NULL,
  `note` text,
  `customers_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`customers_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_groups`
--

CREATE TABLE IF NOT EXISTS `customers_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `codice` varchar(32) NOT NULL,
  `nome` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_price_lists`
--

CREATE TABLE IF NOT EXISTS `customers_price_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price_lists_id` int(10) unsigned NOT NULL,
  `customers_destinations_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_customers_price_lists` (`price_lists_id`,`customers_destinations_id`),
  KEY `fk_customers_price_lists_price_lists1_idx` (`price_lists_id`),
  KEY `fk_customers_price_lists_customers_destinations1_idx` (`customers_destinations_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `customers_state`
--

CREATE TABLE IF NOT EXISTS `customers_state` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stato` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
-- Struttura della tabella `price_lists`
--

CREATE TABLE IF NOT EXISTS `price_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `created_at` datetime NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `product`
--

CREATE TABLE IF NOT EXISTS `product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Struttura della tabella `product_prices`
--

CREATE TABLE IF NOT EXISTS `product_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price_list` decimal(15,2) NOT NULL,
  `price_retail` decimal(15,2) NOT NULL,
  `price_lists_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_products_prices_price_lists1_idx` (`price_lists_id`),
  KEY `fk_product_prices_product1_idx` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=97 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `agent_manager`
--
ALTER TABLE `agent_manager`
  ADD CONSTRAINT `fk_agent_manager_customers_destinations_address1` FOREIGN KEY (`customers_destinations_address_id`) REFERENCES `customers_destinations_address` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_agent_manager_users1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_agent_manager_users2` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_customers_groups1` FOREIGN KEY (`customers_groups_id`) REFERENCES `customers_groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customers_customers_state1` FOREIGN KEY (`customers_state_id`) REFERENCES `customers_state` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `customers_address`
--
ALTER TABLE `customers_address`
  ADD CONSTRAINT `fk_customers_address_customers1` FOREIGN KEY (`customers_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `customers_book`
--
ALTER TABLE `customers_book`
  ADD CONSTRAINT `fk_customers_book_customers1` FOREIGN KEY (`customers_id`) REFERENCES `customers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customers_book_users1` FOREIGN KEY (`bookkeeper_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `customers_destinations`
--
ALTER TABLE `customers_destinations`
  ADD CONSTRAINT `fk_customers_destinations_customers1` FOREIGN KEY (`customers_id`) REFERENCES `customers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `customers_destinations_address`
--
ALTER TABLE `customers_destinations_address`
  ADD CONSTRAINT `fk_customers_destinations_address_customers_destinations1` FOREIGN KEY (`customers_destinations_id`) REFERENCES `customers_destinations` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `customers_details`
--
ALTER TABLE `customers_details`
  ADD CONSTRAINT `fk_customers_details_customers1` FOREIGN KEY (`customers_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `customers_price_lists`
--
ALTER TABLE `customers_price_lists`
  ADD CONSTRAINT `fk_customers_price_lists_customers_destinations1` FOREIGN KEY (`customers_destinations_id`) REFERENCES `customers_destinations` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_customers_price_lists_price_lists1` FOREIGN KEY (`price_lists_id`) REFERENCES `price_lists` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `product_prices`
--
ALTER TABLE `product_prices`
  ADD CONSTRAINT `fk_products_prices_price_lists1` FOREIGN KEY (`price_lists_id`) REFERENCES `price_lists` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_prices_product1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
  ADD CONSTRAINT `fk_users_details_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users_forgot_password`
--
ALTER TABLE `users_forgot_password`
  ADD CONSTRAINT `fk_users_forgot_password_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users_login`
--
ALTER TABLE `users_login`
  ADD CONSTRAINT `fk_users_login_users1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Limiti per la tabella `users_remember_tokens`
--
ALTER TABLE `users_remember_tokens`
  ADD CONSTRAINT `users_remember_tokens_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
