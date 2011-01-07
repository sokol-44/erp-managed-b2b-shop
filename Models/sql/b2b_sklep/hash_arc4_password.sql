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

# Dumping structure for function b2b_sklep.hash_arc4_password
DROP FUNCTION IF EXISTS `hash_arc4_password`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `hash_arc4_password`(`password` BLOB, `salt` BLOB) RETURNS tinytext CHARSET utf8
    DETERMINISTIC
BEGIN
	DECLARE hash_res TINYTEXT;
	set hash_res = 
			HEX( 
				ARC4_ENCRYPT(
					unhex( md5(password) ),
					unhex( CONCAT(md5( CONCAT(password, salt) ), md5( CONCAT(salt, password) ))))
			);
	RETURN hash_res;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
