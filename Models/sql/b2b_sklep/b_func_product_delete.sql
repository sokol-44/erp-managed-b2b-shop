-- --------------------------------------------------------
-- Host:                         sql.company.nazwa.pl
-- Wersja serwera:               5.5.25a-log - NetArt MySQL Server
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury funkcja company_1.b_func_product_delete
DROP FUNCTION IF EXISTS `b_func_product_delete`;
DELIMITER //
CREATE DEFINER=`company_1`@`%` FUNCTION `b_func_product_delete`(`id_product_in` INT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_product TINYTEXT DEFAULT NULL;
	DECLARE status_category_product INT DEFAULT 0;

	SELECT id_product INTO status_product FROM shop_product WHERE `id_product` = id_product_in;
	IF status_product IS NULL THEN
		SET status = 'PRODUCT_DONT_EXIST';
	ELSE
		SELECT count(p2c.id_product) INTO status_category_product
		FROM shop_product_to_category p2c
		WHERE `id_product` = id_product_in;
		IF status_category_product = 0 and status_category_category = 0 THEN
			UPDATE shop_product set status = 'NA' WHERE id_product = id_product_in;
			SET status = 'SUCCESS';
		ELSE
			SET status = 'PRODUCT_IN_CATEGORY';
		END IF;
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
