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

# Dumping structure for function b2b_sklep.b_func_random_data
DROP FUNCTION IF EXISTS `b_func_random_data`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_random_data`(`lenght` INT) RETURNS blob
    NO SQL
BEGIN
	DECLARE stream BLOB DEFAULT '';
	DECLARE idx INT DEFAULT 0;
	WHILE idx < lenght DO
		SET stream = CONCAT( stream, CHAR(FLOOR(RAND() * 256)) );
		SET idx = idx + 1;
	END WHILE;
	return stream;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
