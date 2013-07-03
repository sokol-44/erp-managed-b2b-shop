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

-- Zrzut struktury funkcja b2b_sklep.b_func_product_set
DROP FUNCTION IF EXISTS `b_func_product_set`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_product_set`(`id_product_in` INT, `name_in` TINYTEXT, `description_in` TEXT, `producer_in` TINYTEXT, `catalog_index_in` TINYTEXT, `picture_small_url_in` TEXT, `picture_big_url_in` TEXT, `picture_id_in` INT, `price_in` DECIMAL, `vat_in` DECIMAL, `quantity_in` INT, `status_in` TEXT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_product TINYTEXT DEFAULT NULL;

	SELECT id_product INTO status_product FROM shop_product WHERE `id_product` = id_product_in;
	IF status_product IS NULL THEN
      CALL b_proc_product_add(id_product_in, name_in, description_in, producer_in, catalog_index_in, picture_small_url_in, picture_big_url_in, picture_id_in, price_in, vat_in, quantity_in, status_in);
		SET status = concat(@status_proc,',PRODUCT_DONT_EXIST');
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
