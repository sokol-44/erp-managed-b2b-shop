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

# Dumping structure for table b2b_sklep.global_rights
DROP TABLE IF EXISTS `global_rights`;
CREATE TABLE IF NOT EXISTS `global_rights` (
  `id_rights` int(11) NOT NULL AUTO_INCREMENT,
  `scope` enum('ADMIN','CLIENT') DEFAULT NULL,
  `name` tinytext,
  `description` tinytext,
  PRIMARY KEY (`id_rights`),
  KEY `scope` (`scope`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.global_rights: ~6 rows (approximately)
DELETE FROM `global_rights`;
/*!40000 ALTER TABLE `global_rights` DISABLE KEYS */;
INSERT INTO `global_rights` (`id_rights`, `scope`, `name`, `description`) VALUES
	(1, 'ADMIN', '2PANEL', 'Can access Panel'),
	(2, 'ADMIN', 'SUPER_ADMIN', 'Is super admin.'),
	(3, 'ADMIN', 'ADMIN', 'Common Admin'),
	(4, 'CLIENT', 'ADMIN', 'Client Admin'),
	(5, 'CLIENT', 'USER', 'User'),
	(6, 'CLIENT', 'OPERATOR', 'Client Operator');
/*!40000 ALTER TABLE `global_rights` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
