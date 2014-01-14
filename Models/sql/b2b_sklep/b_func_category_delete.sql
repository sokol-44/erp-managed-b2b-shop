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

# Dumping structure for function b2b_sklep.b_func_category_delete
DROP FUNCTION IF EXISTS `b_func_category_delete`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_category_delete`(`id_category_in` INT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_category TINYTEXT DEFAULT NULL;
	DECLARE status_category_product INT DEFAULT 0;
	DECLARE status_category_category INT DEFAULT 0;

	SELECT id_category INTO status_category FROM shop_category WHERE `id_category` = id_category_in;
	IF status_category IS NULL THEN
		SET status = 'CATEGORY_DONT_EXIST';
	ELSE
		SELECT count(p2c.id_category) INTO status_category_product
		FROM shop_product_to_category p2c join shop_product p on (p2c.id_product = p.id_product)
		WHERE `id_category` = id_category_in;
		SELECT count(c1.id_category) INTO status_category_category
		FROM shop_category c1 join shop_category c2 on (c1.id_category_parent = c2.id_category)
		WHERE c2.`id_category` = id_category_in;
		IF status_category_product = 0 and status_category_category = 0 THEN
			DELETE FROM shop_category WHERE id_category = id_category_in;
			SET status = 'SUCCESS';
		ELSE
			SET status = 'CATEGORY_NOT_EMPTY';
		END IF;
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
