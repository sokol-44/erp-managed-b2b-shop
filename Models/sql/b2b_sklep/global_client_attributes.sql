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

# Dumping structure for table b2b_sklep.global_client_attributes
DROP TABLE IF EXISTS `global_client_attributes`;
CREATE TABLE IF NOT EXISTS `global_client_attributes` (
  `id_client` int(11) NOT NULL,
  `type` enum('PRODUCT_VIEW_NAME') NOT NULL,
  `value` tinytext,
  PRIMARY KEY (`id_client`,`type`),
  KEY `fk.global_client_attributes.id_client` (`id_client`),
  CONSTRAINT `fk.global_client_attributes.id_client` FOREIGN KEY (`id_client`) REFERENCES `global_client` (`id_client`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.global_client_attributes: ~0 rows (approximately)
DELETE FROM `global_client_attributes`;
/*!40000 ALTER TABLE `global_client_attributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_client_attributes` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
