# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:48
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table b2b_sklep.global_rights2admin
DROP TABLE IF EXISTS `global_rights2admin`;
CREATE TABLE IF NOT EXISTS `global_rights2admin` (
  `id_admin` int(11) NOT NULL,
  `id_rights` int(11) NOT NULL,
  PRIMARY KEY (`id_admin`,`id_rights`),
  KEY `fk.global_rights2admin.id_admin` (`id_admin`),
  KEY `fk.global_rights2admin.id_rights2admin` (`id_rights`),
  CONSTRAINT `fk.global_rights2admin.id_admin` FOREIGN KEY (`id_admin`) REFERENCES `global_admin` (`id_admin`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.global_rights2admin.id_rights2admin` FOREIGN KEY (`id_rights`) REFERENCES `global_rights` (`id_rights`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.global_rights2admin: ~7 rows (approximately)
/*!40000 ALTER TABLE `global_rights2admin` DISABLE KEYS */;
INSERT INTO `global_rights2admin` (`id_admin`, `id_rights`) VALUES
	(1, 1),
	(1, 2),
	(2, 2),
	(3, 2),
	(5, 1),
	(7, 2),
	(9, 1);
/*!40000 ALTER TABLE `global_rights2admin` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
