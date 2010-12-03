# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:45:00
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table b2b_sklep.shop_order_status
DROP TABLE IF EXISTS `shop_order_status`;
CREATE TABLE IF NOT EXISTS `shop_order_status` (
  `id_order_status` int(11) NOT NULL,
  `name` tinytext,
  PRIMARY KEY (`id_order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_order_status: ~7 rows (approximately)
DELETE FROM `shop_order_status`;
/*!40000 ALTER TABLE `shop_order_status` DISABLE KEYS */;
INSERT INTO `shop_order_status` (`id_order_status`, `name`) VALUES
	(1, 'START'),
	(2, 'WAITING_FOR_ACCEPTANCE'),
	(3, 'WAITING_FOR_PAIMENT'),
	(4, 'WAITING_FOR_DELIVER'),
	(5, 'IN_DELIVERY'),
	(6, 'DELIVERED'),
	(7, 'END');
/*!40000 ALTER TABLE `shop_order_status` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
