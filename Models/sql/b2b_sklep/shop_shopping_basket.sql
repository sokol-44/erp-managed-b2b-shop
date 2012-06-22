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

# Dumping data for table b2b_sklep.shop_shopping_basket: ~3 rows (approximately)
DELETE FROM `shop_shopping_basket`;
/*!40000 ALTER TABLE `shop_shopping_basket` DISABLE KEYS */;
INSERT INTO `shop_shopping_basket` (`id_client`, `id_nr_shopping_basket`, `description`, `date_create`, `date_modified`, `using_id_client_user`, `using_session_id`, `using_date`) VALUES
	(1, 3, '', '2010-11-29 15:23:21', NULL, 2, '7knp1gmnjl7eufm633gnulvb12', '2010-11-29 15:23:21'),
	(1, 4, '', '2010-11-29 15:23:22', NULL, 2, '7knp1gmnjl7eufm633gnulvb12', '2010-11-29 15:23:22'),
	(1, 5, '', '2010-11-29 15:23:24', '2010-12-03 14:27:53', 2, 'peopkovtck7eqh6305ue9p9sm2', '2010-12-03 14:27:53');
/*!40000 ALTER TABLE `shop_shopping_basket` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
