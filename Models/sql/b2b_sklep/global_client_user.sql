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

# Dumping data for table b2b_sklep.global_client_user: ~2 rows (approximately)
DELETE FROM `global_client_user`;
/*!40000 ALTER TABLE `global_client_user` DISABLE KEYS */;
INSERT INTO `global_client_user` (`id_client_user`, `id_client`, `name`, `description`, `login`, `password`, `email`, `created`, `last_login`, `state`) VALUES
	(2, 1, 'dsadsa', 'dsadsa', '123', 'e750a90d2ada04f1756655ad8c582e41f16a71f9dcd07e59dab94292279a70040590347e63794304:761d02b55eaa37e44305f4f7349e35d0a834fe61', '123', '2010-10-22 15:18:37', NULL, 'ACTIVE'),
	(3, 1, 'dsadsa', 'dsadsa', '123a', 'e750a90d2ada04f1756655ad8c582e41f16a71f9dcd07e59dab94292279a70040590347e63794304:761d02b55eaa37e44305f4f7349e35d0a834fe61', '123', '2010-10-22 15:18:37', NULL, 'ACTIVE');
/*!40000 ALTER TABLE `global_client_user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
