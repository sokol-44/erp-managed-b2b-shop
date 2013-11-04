-- --------------------------------------------------------
-- Host:                         sql.company.nazwa.pl
-- Wersja serwera:               5.5.25a-log - NetArt MySQL Server
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury funkcja company_20.b_func_product_to_category_add
DELIMITER //
CREATE DEFINER=`company_20`@`%` FUNCTION `b_func_product_to_category_add`(`id_product_in` INT, `id_category_in` INT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE `status` TINYTEXT DEFAULT NULL;
	DECLARE `status_product` TINYTEXT DEFAULT NULL;
	DECLARE `status_category` TINYTEXT DEFAULT NULL;
	DECLARE `status_product_category` TINYTEXT DEFAULT NULL;
	 
	SELECT id_product INTO status_product FROM shop_product WHERE `id_product` = id_product_in;
	SELECT id_category INTO status_category FROM shop_category WHERE `id_category` = id_category_in;
	IF status_product IS NULL OR status_category IS NULL THEN
		SET status = 'ERROR,PRODUCT_OR_CATEGORY_DONT_EXIST';
	ELSE
		SELECT id_category INTO status_product_category FROM shop_product_to_category 
		WHERE `id_category` = id_category_in and `id_product` = id_product_in;
		IF status_product_category IS NULL THEN
			INSERT INTO shop_product_to_category (`id_category`, `id_product`)
			VALUES (id_category_in, id_product_in);
			SET status = 'SUCCESS,PRODUCT_TO_CATEGORY_NEW';
		ELSE
			SET status = 'SUCCESS,PRODUCT_TO_CATEGORY_EXIST';
		END IF;
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
