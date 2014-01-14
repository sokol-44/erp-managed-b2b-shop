# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:45:01
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for function b2b_sklep.strip_tags
DROP FUNCTION IF EXISTS `strip_tags`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `strip_tags`( x longtext) RETURNS longtext CHARSET utf8
    READS SQL DATA
BEGIN
DECLARE sstart INT UNSIGNED;
DECLARE ends INT UNSIGNED;
SET sstart = LOCATE('<', x, 1);
REPEAT
SET ends = LOCATE('>', x, sstart);
SET x = CONCAT(SUBSTRING( x, 1 ,sstart -1) ,SUBSTRING(x, ends +1 )) ;
SET sstart = LOCATE('<', x, 1);
UNTIL sstart < 1 END REPEAT;
return x;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
