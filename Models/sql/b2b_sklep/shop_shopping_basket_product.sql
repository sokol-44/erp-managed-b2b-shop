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

# Dumping structure for table b2b_sklep.shop_shopping_basket_product
DROP TABLE IF EXISTS `shop_shopping_basket_product`;
CREATE TABLE IF NOT EXISTS `shop_shopping_basket_product` (
  `id_client` int(11) NOT NULL,
  `id_nr_shopping_basket` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY (`id_client`,`id_nr_shopping_basket`,`id_product`),
  KEY `fk.shopping_basket_product.id_product` (`id_product`),
  KEY `fk.shopping_basket_product.id_nr_shopping_basket` (`id_nr_shopping_basket`),
  KEY `fk.shopping_basket_product.id_client` (`id_client`),
  CONSTRAINT `fk.shopping_cart_product.id_client` FOREIGN KEY (`id_client`) REFERENCES `global_client_user` (`id_client`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.shopping_cart_product.id_nr_shopping_basket` FOREIGN KEY (`id_nr_shopping_basket`) REFERENCES `shop_shopping_basket` (`id_nr_shopping_basket`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.shopping_cart_product.id_product` FOREIGN KEY (`id_product`) REFERENCES `shop_product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_shopping_basket_product: ~0 rows (approximately)
/*!40000 ALTER TABLE `shop_shopping_basket_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_shopping_basket_product` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
