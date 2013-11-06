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

-- Zrzut struktury procedura company_20.b_proc_order_invoice_set
DELIMITER //
CREATE DEFINER=`company_20`@`%` PROCEDURE `b_proc_order_invoice_set`(`id_invoice_in` INT, `id_order_in` INT, `id_client_in` INT, `invoice_number_in`  VARCHAR(50), `state_in` VARCHAR(50), `net_value_in` DECIMAL(10,2), `gross_value_in` DECIMAL(10,2), `date_issue_in` DATE, `date_pay_in` DATE,  `description_in` TEXT)
BEGIN
	INSERT INTO shop_order_invoice (`id_invoice`, `id_order`, `id_client`, `invoice_number`, `state`, `net_value`, `gross_value`, `date_issue`, `date_pay`, `description`)
	VALUES (`id_invoice_in`, `id_order_in`, `id_client_in`, `invoice_number_in`, `state_in`, `net_value_in`, `gross_value_in`, `date_issue_in`, `date_pay_in`, `description_in`)
	ON DUPLICATE KEY UPDATE
	`id_order` = `id_order_in`, `id_client` = `id_client_in`, `invoice_number` = `invoice_number_in`, `state` = `state_in`, `net_value` = `net_value_in`,  `gross_value` = `gross_value_in`, `date_issue` = `date_issue_in`, `date_pay` = `date_pay_in`, `description` = `description_in`;
	SET @status_proc = 'SUCCESS';
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
