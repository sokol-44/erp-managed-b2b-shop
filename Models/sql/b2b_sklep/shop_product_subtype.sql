-- --------------------------------------------------------
-- Host:                         localhost
-- Wersja serwera:               5.1.40-community-log - MySQL Community Server (GPL)
-- Serwer OS:                    Win32
-- HeidiSQL Wersja:              8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury tabela b2b_sklep.shop_product_subtype
DROP TABLE IF EXISTS `shop_product_subtype`;
CREATE TABLE IF NOT EXISTS `shop_product_subtype` (
  `id_product_subtype` int(11) NOT NULL,
  `id_product` int(11) DEFAULT NULL,
  `description` text,
  `picture_small_url` tinytext,
  `picture_big_url` tinytext,
  `picture_id` int(11) DEFAULT NULL,
  `price_diff` decimal(20,4) DEFAULT NULL,
  `status` enum('ACTIVE','NA') DEFAULT NULL,
  PRIMARY KEY (`id_product_subtype`),
  KEY `fk.product_subtype.id_product` (`id_product`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
