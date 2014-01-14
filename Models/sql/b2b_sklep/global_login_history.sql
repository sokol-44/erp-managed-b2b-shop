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

# Dumping structure for table b2b_sklep.global_login_history
DROP TABLE IF EXISTS `global_login_history`;
CREATE TABLE IF NOT EXISTS `global_login_history` (
  `id_global_login_history` int(11) NOT NULL AUTO_INCREMENT,
  `scope` enum('ADMIN','CLIENT','ERROR') DEFAULT NULL,
  `id_person` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `date_last` datetime DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  `ip` tinytext,
  PRIMARY KEY (`id_global_login_history`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.global_login_history: ~0 rows (approximately)
/*!40000 ALTER TABLE `global_login_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `global_login_history` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
