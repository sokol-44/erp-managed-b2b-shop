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

# Dumping structure for table b2b_sklep.shop_order_status_history
DROP TABLE IF EXISTS `shop_order_status_history`;
CREATE TABLE IF NOT EXISTS `shop_order_status_history` (
  `id_order_status` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `description` text,
  PRIMARY KEY (`id_order`,`id_order_status`),
  KEY `fk.order_status_history.id_order_status` (`id_order_status`),
  KEY `fk.order_status_history.id_order` (`id_order`),
  CONSTRAINT `fk.order_status_history.id_order` FOREIGN KEY (`id_order`) REFERENCES `shop_order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.order_status_history.id_order_status` FOREIGN KEY (`id_order_status`) REFERENCES `shop_order_status` (`id_order_status`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_order_status_history: ~6 rows (approximately)
/*!40000 ALTER TABLE `shop_order_status_history` DISABLE KEYS */;
INSERT INTO `shop_order_status_history` (`id_order_status`, `id_order`, `timestamp`, `description`) VALUES
	(1, 1, '2010-12-03 13:08:25', '#order description#\r\n\r\n#order description#'),
	(1, 2, '2010-12-03 13:08:49', 'sadsadsadasdsa'),
	(1, 3, '2010-12-03 13:18:18', 'kjhlhjlkhjlhjk'),
	(1, 4, '2010-12-03 14:09:47', 'dsadsa\r\nds\r\nad\r\nsa\r\ndsa'),
	(1, 5, '2010-12-03 14:25:31', 'dsadasdasd'),
	(1, 6, '2010-12-03 14:27:09', 'NULL');
/*!40000 ALTER TABLE `shop_order_status_history` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
