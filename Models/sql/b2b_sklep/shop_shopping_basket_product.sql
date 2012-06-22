# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:45:01
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping data for table b2b_sklep.shop_shopping_basket_product: ~1 rows (approximately)
DELETE FROM `shop_shopping_basket_product`;
/*!40000 ALTER TABLE `shop_shopping_basket_product` DISABLE KEYS */;
INSERT INTO `shop_shopping_basket_product` (`id_client`, `id_nr_shopping_basket`, `id_product`, `quantity`, `date_added`) VALUES
	(1, 5, 10757, 1, '2010-12-03 14:27:53');
/*!40000 ALTER TABLE `shop_shopping_basket_product` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
