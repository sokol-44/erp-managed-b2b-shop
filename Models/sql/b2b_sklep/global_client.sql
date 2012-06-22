# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:44:58
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

# Dumping data for table b2b_sklep.global_client: ~2 rows (approximately)
DELETE FROM `global_client`;
/*!40000 ALTER TABLE `global_client` DISABLE KEYS */;
INSERT INTO `global_client` (`id_client`, `name`, `description`, `email`, `phone`, `created`, `state`) VALUES
	(1, 'dsadsa', 'dsad', 'asdsa', 'dsadsa', NULL, 'ACTIVE'),
	(2, 'dsadsa', 'dsad', 'asdsa', 'dsadsa', NULL, 'ACTIVE');
/*!40000 ALTER TABLE `global_client` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
