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

# Dumping structure for table b2b_sklep.global_client_user
DROP TABLE IF EXISTS `global_client_user`;
CREATE TABLE IF NOT EXISTS `global_client_user` (
  `id_client_user` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) DEFAULT NULL,
  `name` tinytext,
  `description` tinytext,
  `login` tinytext,
  `password` tinytext,
  `email` tinytext,
  `created` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `state` enum('ACTIVE','BLOCKED','SUSPENDED','ERASED') DEFAULT NULL,
  PRIMARY KEY (`id_client_user`),
  KEY `fk.global_client_user.id_client` (`id_client`),
  CONSTRAINT `fk.global_client_user.id_client` FOREIGN KEY (`id_client`) REFERENCES `global_client` (`id_client`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.global_client_user: ~2 rows (approximately)
/*!40000 ALTER TABLE `global_client_user` DISABLE KEYS */;
INSERT INTO `global_client_user` (`id_client_user`, `id_client`, `name`, `description`, `login`, `password`, `email`, `created`, `last_login`, `state`) VALUES
	(2, 1, 'dsadsa', 'dsadsa', '123', 'e750a90d2ada04f1756655ad8c582e41f16a71f9dcd07e59dab94292279a70040590347e63794304:761d02b55eaa37e44305f4f7349e35d0a834fe61', '123', '2010-10-22 15:18:37', NULL, 'ACTIVE'),
	(3, 1, 'dsadsa', 'dsadsa', '123a', 'e750a90d2ada04f1756655ad8c582e41f16a71f9dcd07e59dab94292279a70040590347e63794304:761d02b55eaa37e44305f4f7349e35d0a834fe61', '123', '2010-10-22 15:18:37', NULL, 'ACTIVE');
/*!40000 ALTER TABLE `global_client_user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
