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

-- Zrzut struktury funkcja company_20.b_func_order_invoice_set
DELIMITER //
CREATE DEFINER=`company_20`@`%` FUNCTION `b_func_order_invoice_set`(`id_invoice_in` INT, `id_order_in` INT, `id_client_in` INT, `invoice_number_in` VARCHAR(50), `state_in` VARCHAR(50), `net_value_in` DECIMAL(10,2), `gross_value_in` DECIMAL(10,2), `date_issue_in` DATE, `date_pay_in` DATE, `description_in` TEXT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_order TINYTEXT DEFAULT NULL;
	DECLARE status_order_invoice TINYTEXT DEFAULT NULL;

	SELECT id_order INTO status_order FROM shop_order
	WHERE `id_order` = id_order_in;
	IF status_order IS NULL THEN
		SET status = 'ERROR,ORDER_DONT_EXIST';
	ELSE
		SELECT id_invoice INTO status_order_invoice FROM shop_order_invoice
		WHERE `id_invoice` = id_invoice_in;
		IF status_order_invoice IS NULL THEN
			CALL b_proc_order_invoice_set(`id_invoice_in`, `id_order_in`, `id_client_in`, `invoice_number_in`, `state_in`, `net_value_in`, `gross_value_in`, `date_issue_in`, `date_pay_in`, `description_in`);
			SET `status` = CONCAT(@status_proc,',ORDER_INVOICE_NEW');
		ELSE
			CALL b_proc_order_invoice_set(`id_invoice_in`, `id_order_in`, `id_client_in`, `invoice_number_in`, `state_in`, `net_value_in`, `gross_value_in`, `date_issue_in`, `date_pay_in`, `description_in`);
			SET `status` = CONCAT(@status_proc,',ORDER_INVOICE_EXIST');
		END IF;
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
