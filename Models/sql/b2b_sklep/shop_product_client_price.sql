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

# Dumping structure for table b2b_sklep.shop_product_client_price
DROP TABLE IF EXISTS `shop_product_client_price`;
CREATE TABLE IF NOT EXISTS `shop_product_client_price` (
  `id_product` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `price` decimal(20,4) unsigned DEFAULT NULL,
  `vat` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_product`,`id_client`),
  KEY `fk.product_client_price.id_product` (`id_product`),
  KEY `fk.product_client_price.id_client` (`id_client`),
  CONSTRAINT `fk.product_client_price.id_client` FOREIGN KEY (`id_client`) REFERENCES `global_client` (`id_client`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.product_client_price.id_product` FOREIGN KEY (`id_product`) REFERENCES `shop_product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_product_client_price: ~0 rows (approximately)
/*!40000 ALTER TABLE `shop_product_client_price` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_product_client_price` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
