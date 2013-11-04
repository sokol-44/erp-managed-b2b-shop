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

-- Zrzut struktury funkcja company_20.b_func_order_attribute_set
DELIMITER //
CREATE DEFINER=`company_20`@`%` FUNCTION `b_func_order_attribute_set`(`id_order_in` INT, `type_in` TEXT, `val_in` TEXT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE `status` TINYTEXT DEFAULT NULL;
	DECLARE status_order_attribute TINYTEXT DEFAULT NULL;

	SELECT id_order INTO status_order_attribute FROM shop_order_attributes WHERE `id_order` = id_order_in and `type` = type_in;
	IF status_order_attribute IS NULL THEN
		CALL b_proc_order_attribute_set(`id_order_in`, `type_in`, `val_in`);
		SET `status` = CONCAT(@status_proc,',ORDER_ATTRIBUTE_NEW');
	ELSE
		CALL b_proc_order_attribute_set(`id_order_in`, `type_in`, `val_in`);
		SET `status` = CONCAT(@status_proc,',ORDER_ATTRIBUTE_EXIST');
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
