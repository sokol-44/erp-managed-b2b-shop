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

-- Zrzut struktury procedura company_1.b_proc_client_add
DROP PROCEDURE IF EXISTS `b_proc_client_add`;
DELIMITER //
CREATE DEFINER=`company_1`@`%` PROCEDURE `b_proc_client_add`(IN `id_client_in` INT, IN `name_in` TINYTEXT, IN `description_in` TEXT, IN `email_in` TINYTEXT, IN `phone_in` TINYTEXT, IN `state_in` TINYTEXT)
BEGIN
	INSERT INTO global_client (`id_client`, `name`, `description`, `email`, `phone`, `created`, `state`)
	VALUES (id_client_in, name_in, description_in, email_in, phone_in, now(), state_in)
	ON DUPLICATE KEY UPDATE
	`name` = name_in, `description` = description_in, `email` = email_in, `phone` = phone_in, `state` = state_in;
	SET @status_proc = 'SUCCESS';
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
