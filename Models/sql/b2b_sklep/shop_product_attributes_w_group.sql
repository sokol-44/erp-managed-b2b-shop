-- --------------------------------------------------------
-- Host:                         sql.company.nazwa.pl
-- Wersja serwera:               5.5.25a-log - NetArt MySQL Server
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury .shop_product_attributes_w_group
CREATE TABLE IF NOT EXISTS `shop_product_attributes_w_group` (
  `id_product` int(11) NOT NULL,
  `id_attribute` int(11) NOT NULL,
  `attribute_name` varchar(50) NOT NULL,
  `attribute_value` tinytext,
  `attribute_order` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `group_order` tinytext,
  PRIMARY KEY (`id_product`,`id_attribute`,`id_group`),
  KEY `fk.shop_product_attributes_w_group.id_product` (`id_product`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
