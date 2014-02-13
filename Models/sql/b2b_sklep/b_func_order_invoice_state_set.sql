-- --------------------------------------------------------
-- Host:                         sql.company.nazwa.pl
-- Wersja serwera:               5.5.25a-log - NetArt MySQL Server
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury b_func_order_invoice_state_set
DELIMITER //
CREATE  ` FUNCTION `b_func_order_invoice_state_set`(`id_invoice_in` INT, `id_order_in` INT, `state_in` VARCHAR(50)) RETURNS tinytext CHARSET latin2
    MODIFIES SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_order TINYTEXT DEFAULT NULL;
	DECLARE status_order_invoice TINYTEXT DEFAULT NULL;

	SELECT id_order INTO status_order FROM shop_order
	WHERE `id_order` = `id_order_in`;
	IF status_order IS NULL THEN
		SET status = 'ERROR,ORDER_DONT_EXIST';
	ELSE
		SELECT id_invoice INTO status_order_invoice FROM shop_order_invoice
		WHERE `id_invoice` = `id_invoice_in`;
		IF status_order_invoice IS NULL THEN
			SET `status` = 'ERROR,ORDER_INVOICE_DONT_EXIST';
		ELSE
      UPDATE shop_order_invoice set `state` = `state_in` WHERE `id_invoice` = `id_invoice_in`;
			SET `status` = 'SUCCESS,ORDER_INVOICE_EXIST';
		END IF;
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
