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

-- Zrzut struktury funkcja company_20.b_func_product_change
DELIMITER //
CREATE DEFINER=`company_20`@`%` FUNCTION `b_func_product_change`(`id_product_in` INT, `name_in` TINYTEXT, `description_in` TEXT, `producer_in` TINYTEXT, `catalog_index_in` TINYTEXT, `picture_small_url_in` TEXT, `picture_big_url_in` TEXT, `picture_id_in` INT, `price_in` DECIMAL, `vat_in` DECIMAL, `quantity_in` INT, `status_in` TEXT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_product TINYTEXT DEFAULT NULL;

	SELECT id_product INTO status_product FROM shop_product WHERE `id_product` = id_product_in;
	IF status_product IS NULL THEN
		SET status = 'ERROR,PRODUCT_DONT_EXIST';
	ELSE
		CALL b_proc_product_add(id_product_in, name_in, description_in, producer_in, catalog_index_in, picture_small_url_in, picture_big_url_in, picture_id_in, price_in, vat_in, quantity_in, status_in);
		SET status = concat(@status_proc,',PRODUCT_EXIST');
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
