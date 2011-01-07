# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:49
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table b2b_sklep.shop_order
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
  PRIMARY KEY (`id_order`,`id_order_status`),
  KEY `fk.order.id_client` (`id_client`),
  KEY `fk.order.id_order_status` (`id_order_status`),
  CONSTRAINT `fk.order.id_client` FOREIGN KEY (`id_client`) REFERENCES `global_client` (`id_client`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_order: ~6 rows (approximately)
/*!40000 ALTER TABLE `shop_order` DISABLE KEYS */;
INSERT INTO `shop_order` (`id_order`, `id_client`, `date_create`, `date_modified`, `id_order_status`, `hidden_status`, `description`, `description_basket`) VALUES
	(1, 1, '2010-12-03 13:08:25', NULL, 1, NULL, '#order description#\r\n\r\n#order description#', '#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#\r\n\r\n\r\n#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#\r\n\r\n\r\n#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#\r\n\r\n#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#'),
	(2, 1, '2010-12-03 13:08:49', NULL, 1, NULL, 'sadsadsadasdsa', 'bbbbbbbbbbbbbbbbbbbbbdsadsadsas\r\nd\r\nsa\r\nd\r\nsa\r\nd\r\nsa\r\nd\r\nsa'),
	(3, 1, '2010-12-03 13:18:18', NULL, 1, NULL, 'kjhlhjlkhjlhjk', ''),
	(4, 1, '2010-12-03 14:09:47', NULL, 1, NULL, 'dsadsa\r\nds\r\nad\r\nsa\r\ndsa', 'dsadsadsadsa'),
	(5, 1, '2010-12-03 14:25:31', NULL, 1, NULL, 'dsadasdasd', ''),
	(6, 1, '2010-12-03 14:27:09', NULL, 1, NULL, 'NULL', '');
/*!40000 ALTER TABLE `shop_order` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
