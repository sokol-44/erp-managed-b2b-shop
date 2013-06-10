-- --------------------------------------------------------
-- Host:                         localhost
-- Wersja serwera:               5.1.40-community-log - MySQL Community Server (GPL)
-- Serwer OS:                    Win32
-- HeidiSQL Wersja:              8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury funkcja b2b_sklep.b_func_product_to_category_set_product_category
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
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
