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

# Dumping structure for table b2b_sklep.shop_shopping_basket
DROP TABLE IF EXISTS `shop_shopping_basket`;
CREATE TABLE IF NOT EXISTS `shop_shopping_basket` (
  `id_client` int(11) NOT NULL,
  `id_nr_shopping_basket` int(11) NOT NULL,
  `description` text,
  `date_create` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `using_id_client_user` int(11) DEFAULT NULL,
  `using_session_id` tinytext,
  `using_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_nr_shopping_basket`,`id_client`),
  KEY `fk.shopping_basket.id_client` (`id_client`),
  KEY `fk.shopping_basket.id_client_user` (`using_id_client_user`),
  CONSTRAINT `fk.shopping_basket.id_client` FOREIGN KEY (`id_client`) REFERENCES `global_client` (`id_client`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.shopping_basket.id_client_user` FOREIGN KEY (`using_id_client_user`) REFERENCES `global_client_user` (`id_client_user`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_shopping_basket: ~0 rows (approximately)
/*!40000 ALTER TABLE `shop_shopping_basket` DISABLE KEYS */;
/*!40000 ALTER TABLE `shop_shopping_basket` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
