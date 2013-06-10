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

-- Zrzut struktury funkcja b2b_sklep.b_func_product_client_price_only_add
DROP FUNCTION IF EXISTS `b_func_product_client_price_only_add`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_product_client_price_only_add`(`id_client_in` INT, `id_product_in` INT, `price_in` DECIMAL(10,0), `vat_in` INT) RETURNS tinytext CHARSET utf8
    MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_product TINYTEXT DEFAULT NULL;

	SELECT id_product INTO status_product FROM shop_product_client_price WHERE `id_product` = id_product_in and `id_client` = id_client_in;
	IF status_product IS NULL THEN
		CALL b_proc_product_client_price_add(id_product_in, id_client_in, price_in, vat_in);
		SET status = @status_proc;
	ELSE
		SET status = 'PRODUCT_CLIENT_PRICE_EXIST';
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
