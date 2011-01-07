# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:47
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for function b2b_sklep.b_c_dbe
DROP FUNCTION IF EXISTS `b_c_dbe`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_c_dbe`() RETURNS int(11)
    READS SQL DATA
BEGIN
	DECLARE count INT DEFAULT NULL;
	SELECT sesskey INTO count FROM core_sessions WHERE sesskey = 'sasa';
	IF count IS NULL THEN
		SET count = 1000;
	ELSE
		SET count = 10;
	END IF;
	return count;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
