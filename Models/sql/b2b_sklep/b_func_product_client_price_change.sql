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

-- Zrzut struktury funkcja company_1.b_func_product_client_price_change
DROP FUNCTION IF EXISTS `b_func_product_client_price_change`;
DELIMITER //
CREATE DEFINER=`company_1`@`%` FUNCTION `b_func_product_client_price_change`(`id_client_in` INT, `id_product_in` INT, `price_in` DECIMAL(10,0), `vat_in` INT) RETURNS tinytext CHARSET utf8
    MODIFIES SQL DATA
    DETERMINISTIC
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_product TINYTEXT DEFAULT NULL;

	SELECT id_product INTO status_product FROM shop_product_client_price WHERE `id_product` = id_product_in and `id_client` = id_client_in;
	IF status_product IS NULL THEN
		SET status = 'PRODUCT_CLIENT_PRICE_DONT_EXIST';
	ELSE
		CALL b_proc_product_client_price_add(id_product_in, id_client_in, price_in, vat_in);
		SET status = @status_proc;
	END IF;
	RETURN status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
