-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Giu 15, 2016 alle 03:22
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
-- Struttura della tabella `product_prices`
--

CREATE TABLE IF NOT EXISTS `product_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price_list` decimal(15,2) DEFAULT '0.00',
  `price_retail` decimal(15,2) DEFAULT '0.00',
  `price_ecommerce` decimal(15,2) DEFAULT '0.00',
  `product_id` int(10) unsigned NOT NULL,
  `price_lists_id` int(10) unsigned NOT NULL,
  `update_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_product_price` (`product_id`,`price_lists_id`),
  KEY `fk_products_prices_price_lists1_idx` (`price_lists_id`),
  KEY `fk_product_prices_product1_idx` (`product_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1992 ;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `product_prices`
--
ALTER TABLE `product_prices`
  ADD CONSTRAINT `product_prices_ibfk_2` FOREIGN KEY (`price_lists_id`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
