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

-- Zrzut struktury procedura company_22.b_proc_account_manager_set
DELIMITER //
CREATE  ` PROCEDURE `b_proc_account_manager_set`(IN `id_account_manager_in` INT, IN `id_client_in` INT, IN `id_client_user_in` INT, IN `account_manager_name_in` VARCHAR(10), IN `fullname_in` TINYTEXT, IN `phone1_in` TINYTEXT, IN `phone2_in` TINYTEXT, IN `email_in` TINYTEXT, IN `state_in` TINYTEXT)
BEGIN
	INSERT INTO global_client_user_account_manager (`id_account_manager`, `id_client`, `id_client_user`, `account_manager_name`, `fullname`, `phone1`, `phone2`, `email`, `state`, `date_created`)
	VALUES (`id_account_manager_in`, `id_client_in`, `id_client_user_in`, `account_manager_name_in`, `fullname_in`, `phone1_in`, `phone2_in`, `email_in`, `state_in`, now())
	ON DUPLICATE KEY UPDATE
	`id_client` = `id_client_in`, `id_client_user` = `id_client_user_in`, `phone1` = `phone1_in`, `phone2` = `phone2_in`, `email` = `email_in`, `state` = `state_in`, `date_modified` = now();
	SET @status_proc = 'SUCCESS';
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
