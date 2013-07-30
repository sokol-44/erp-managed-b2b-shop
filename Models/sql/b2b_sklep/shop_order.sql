-- --------------------------------------------------------
-- Host:                         sql.company.nazwa.pl
-- Wersja serwera:               5.5.25a-log - NetArt MySQL Server
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury tabela company_1.shop_order
DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE IF NOT EXISTS `shop_order` (
  `id_order` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) DEFAULT NULL,
  `date_create` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `id_order_status` int(11) NOT NULL,
  `hidden_status` tinytext,
  `description` text,
  `description_basket` text,
  `id_shopping_basket` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_order`,`id_order_status`),
  KEY `fk.order.id_client` (`id_client`),
  KEY `fk.order.id_order_status` (`id_order_status`),
  KEY `fk.order.id_shopping_basket` (`id_order_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
