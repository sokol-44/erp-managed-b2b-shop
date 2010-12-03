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

# Dumping structure for table b2b_sklep.global_rights2client
DROP TABLE IF EXISTS `global_rights2client`;
CREATE TABLE IF NOT EXISTS `global_rights2client` (
  `id_client_user` int(11) NOT NULL,
  `id_rights` int(11) NOT NULL,
  PRIMARY KEY (`id_rights`,`id_client_user`),
  KEY `fk.global_rights2client.id_client_user` (`id_client_user`),
  KEY `fk.global_rights2client.id_rights2client` (`id_rights`),
  CONSTRAINT `fk.global_rights2client.id_hotel_user` FOREIGN KEY (`id_client_user`) REFERENCES `global_client_user` (`id_client_user`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk.global_rights2client.id_rights2hotel` FOREIGN KEY (`id_rights`) REFERENCES `global_rights` (`id_rights`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.global_rights2client: ~4 rows (approximately)
DELETE FROM `global_rights2client`;
/*!40000 ALTER TABLE `global_rights2client` DISABLE KEYS */;
INSERT INTO `global_rights2client` (`id_client_user`, `id_rights`) VALUES
	(2, 4),
	(2, 5),
	(2, 6),
	(3, 5);
/*!40000 ALTER TABLE `global_rights2client` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
