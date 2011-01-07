# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:50
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for procedure b2b_sklep.test_trzy_1000
DROP PROCEDURE IF EXISTS `test_trzy_1000`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `test_trzy_1000`()
BEGIN
DECLARE idx INT DEFAULT 0;
DECLARE stime INT DEFAULT 0;
DECLARE etime INT DEFAULT 0;

	SET stime =  UNIX_TIMESTAMP();
	REPEAT
		CALL test_trzy();
		SET idx = idx + 1;
	UNTIL idx > 5000 END REPEAT;
	
	SET etime = UNIX_TIMESTAMP() - stime;
	select etime;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
