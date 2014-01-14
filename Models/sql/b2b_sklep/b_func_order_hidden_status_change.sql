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

# Dumping structure for function b2b_sklep.b_func_order_hidden_status_change
DROP FUNCTION IF EXISTS `b_func_order_hidden_status_change`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_order_hidden_status_change`(`id_order_in` INT, `new_status` TINYTEXT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_order TINYTEXT DEFAULT NULL;

	SELECT id_order INTO status_order FROM shop_order
	WHERE `id_order` = id_order_in;
	IF status_order IS NULL THEN
		SET status = 'ORDER_DONT_EXIST';
	ELSE
		UPDATE shop_product_to_category SET `hidden_status` = new_status
		WHERE `id_order` = id_order_in;
		SET status = 'SUCCESS';
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
