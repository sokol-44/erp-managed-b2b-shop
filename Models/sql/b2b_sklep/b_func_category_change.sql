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

# Dumping structure for function b2b_sklep.b_func_category_change
DROP FUNCTION IF EXISTS `b_func_category_change`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_category_change`(`id_category_in` INT, `id_category_parent_in` INT, `sort_order_in` INT, `root_number_in` TINYINT, `name_in` TEXT, `description_in` TEXT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_category TINYTEXT DEFAULT NULL;

	SELECT id_category INTO status_category FROM shop_category WHERE `id_category` = id_category_in;
	IF status_category IS NULL THEN
		SET status = 'CATEGORY_DONT_EXIST';
	ELSE
		CALL b_proc_category_add(id_category_in, id_category_parent_in, sort_order_in, root_number_in, name_in, description_in);
		SET status = @status_proc;
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
