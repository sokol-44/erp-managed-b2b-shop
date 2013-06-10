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

-- Zrzut struktury tabela b2b_sklep.shop_shopping_basket_version
DROP TABLE IF EXISTS `shop_shopping_basket_version`;
CREATE TABLE IF NOT EXISTS `shop_shopping_basket_version` (
  `id_shopping_basket_version` int(11) NOT NULL AUTO_INCREMENT,
  `id_shopping_basket` int(11) DEFAULT NULL,
  `id_client_user` int(11) DEFAULT NULL,
  `id_client` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id_shopping_basket_version`),
  KEY `fk.shop_shopping_basket_state.id_client` (`id_client`),
  KEY `fk.shop_shopping_basket_state.id_client_user` (`id_client_user`),
  KEY `fk.shop_shopping_basket_state.id_shopping_basket` (`id_shopping_basket`),
  KEY `basket_client` (`id_shopping_basket_version`,`id_shopping_basket`,`id_client_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
