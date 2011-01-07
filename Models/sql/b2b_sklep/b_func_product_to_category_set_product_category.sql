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

# Dumping structure for function b2b_sklep.b_func_product_to_category_set_product_category
DROP FUNCTION IF EXISTS `b_func_product_to_category_set_product_category`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_product_to_category_set_product_category`(`id_category_in` INT, `id_product_in` INT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_product_category TINYTEXT DEFAULT NULL;

	SET status_product_category = `b_func_product_category_check_exist`(id_category_in, id_product_in);
	IF status_product_category IS NULL THEN
		return 'PRODUCT_CATEGORY_DONT_EXIST';
	ELSE
		DELETE FROM shop_product_to_category WHERE `id_product` = id_product_in;
		INSERT INTO shop_product_to_category (`id_category`, `id_product`)
		VALUES (id_category_in, id_product_in);
		SET status = 'SUCCESS';
	END IF;

	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
