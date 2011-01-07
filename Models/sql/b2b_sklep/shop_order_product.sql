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

# Dumping structure for table b2b_sklep.shop_order_product
DROP TABLE IF EXISTS `shop_order_product`;
CREATE TABLE IF NOT EXISTS `shop_order_product` (
  `id_order` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `name` text,
  `price` decimal(20,4) unsigned DEFAULT NULL,
  `vat` tinyint(3) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_order`,`id_product`),
  KEY `fk.order_product.id_order` (`id_order`),
  KEY `fk.order_product.id_product` (`id_product`),
  CONSTRAINT `fk.order_product.id_order` FOREIGN KEY (`id_order`) REFERENCES `shop_order` (`id_order`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.order_product.id_product` FOREIGN KEY (`id_product`) REFERENCES `shop_product` (`id_product`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_order_product: ~9 rows (approximately)
/*!40000 ALTER TABLE `shop_order_product` DISABLE KEYS */;
INSERT INTO `shop_order_product` (`id_order`, `id_product`, `name`, `price`, `vat`, `quantity`) VALUES
	(1, 9989, 'Marketing Management 11e', 65.0000, 5, 1),
	(1, 10049, 'Psychology and Life 17th e. Zimbardo, Gerrig', 60.0000, 5, 1),
	(2, 9905, 'English Advanced Vocabulary and Structure Practice', 26.0000, 5, 2),
	(2, 9909, 'Gramatyka angielska dla zaawansowanych', 28.0000, 5, 1),
	(2, 10049, 'Psychology and Life 17th e. Zimbardo, Gerrig', 60.0000, 5, 3),
	(3, 9781, 'Français Présent. Dwumiesięcznik. Nr 1 na CD', 8.9000, 5, 1),
	(4, 9654, 'English Teaching Professional - roczna prenumerata', 115.0000, 5, 1),
	(5, 10784, 'IO PARLO-karty pracy+CD-kurs j.włoskiego dla dzieci 6-8 lat', 69.0000, 5, 1),
	(6, 10784, 'IO PARLO-karty pracy+CD-kurs j.włoskiego dla dzieci 6-8 lat', 69.0000, 5, 1);
/*!40000 ALTER TABLE `shop_order_product` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
