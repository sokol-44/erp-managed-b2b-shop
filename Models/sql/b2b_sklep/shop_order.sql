# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:44:59
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping data for table b2b_sklep.shop_order: ~6 rows (approximately)
DELETE FROM `shop_order`;
/*!40000 ALTER TABLE `shop_order` DISABLE KEYS */;
INSERT INTO `shop_order` (`id_order`, `id_client`, `date_create`, `date_modified`, `id_order_status`, `description`, `description_basket`) VALUES
	(1, 1, '2010-12-03 13:08:25', NULL, 1, '#order description#\r\n\r\n#order description#', '#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#\r\n\r\n\r\n#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#\r\n\r\n\r\n#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#\r\n\r\n#basket description#\r\n#basket description##basket description#\r\n\r\n#basket description#'),
	(2, 1, '2010-12-03 13:08:49', NULL, 1, 'sadsadsadasdsa', 'bbbbbbbbbbbbbbbbbbbbbdsadsadsas\r\nd\r\nsa\r\nd\r\nsa\r\nd\r\nsa\r\nd\r\nsa'),
	(3, 1, '2010-12-03 13:18:18', NULL, 1, 'kjhlhjlkhjlhjk', ''),
	(4, 1, '2010-12-03 14:09:47', NULL, 1, 'dsadsa\r\nds\r\nad\r\nsa\r\ndsa', 'dsadsadsadsa'),
	(5, 1, '2010-12-03 14:25:31', NULL, 1, 'dsadasdasd', ''),
	(6, 1, '2010-12-03 14:27:09', NULL, 1, 'NULL', '');
/*!40000 ALTER TABLE `shop_order` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
