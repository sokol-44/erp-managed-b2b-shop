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

# Dumping structure for function b2b_sklep.b_func_product_add
DROP FUNCTION IF EXISTS `b_func_product_add`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_product_add`(`id_product_in` INT, `name_in` TINYTEXT, `description_in` TEXT, `picture_small_url_in` TEXT, `picture_big_url_in` TEXT, `price_in` DECIMAL, `vat_in` DECIMAL, `quantity_in` INT, `status_in` TEXT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_product TINYTEXT DEFAULT NULL;

	SELECT id_product INTO status_product FROM shop_product WHERE `id_product` = id_product_in;
	IF status_product IS NULL THEN
		CALL b_proc_product_add(id_product_in, name_in, description_in, picture_small_url_in, picture_big_url_in,
		price_in, vat_in, quantity_in, status_in);
		SET status = @status_proc;
	ELSE
		SET status = 'PRODUCT_EXIST';
	END IF;



	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
