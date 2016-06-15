-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Giu 15, 2016 alle 03:23
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
-- Struttura della tabella `product_prices_revisions`
--

CREATE TABLE IF NOT EXISTS `product_prices_revisions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_prices_id` int(10) unsigned NOT NULL,
  `price_lists_revisions_id` int(10) unsigned NOT NULL,
  `price_list` decimal(15,2) DEFAULT '0.00',
  `price_retail` decimal(15,2) DEFAULT '0.00',
  `price_ecommerce` decimal(15,2) DEFAULT '0.00',
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_price_revision` (`product_prices_id`,`price_lists_revisions_id`),
  KEY `fk_product_prices_revisions_product_prices1_idx` (`product_prices_id`),
  KEY `fk_product_prices_revisions_price_lists_revisions1_idx` (`price_lists_revisions_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `product_prices_revisions`
--
ALTER TABLE `product_prices_revisions`
  ADD CONSTRAINT `product_prices_revisions_ibfk_1` FOREIGN KEY (`price_lists_revisions_id`) REFERENCES `price_lists_revisions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_product_prices_revisions_product_prices1` FOREIGN KEY (`product_prices_id`) REFERENCES `product_prices` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
