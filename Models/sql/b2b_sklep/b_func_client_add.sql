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

-- Zrzut struktury funkcja company_1.b_func_client_add
DROP FUNCTION IF EXISTS `b_func_client_add`;
DELIMITER //
CREATE DEFINER=`company_1`@`%` FUNCTION `b_func_client_add`(`id_client_in` INT, `name_in` TINYTEXT, `description_in` TEXT, `email_in` TINYTEXT, `phone_in` TINYTEXT, `state_in` TINYTEXT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	
	SELECT id_client INTO status FROM global_client WHERE `id_client` = id_client_in;
	IF status IS NULL THEN
		INSERT INTO global_client (`id_client`, `name`, `description`, `email`, `phone`, `created`, `state`)
		VALUES (id_client_in, name_in, description_in, email_in, phone_in, now(), state_in);
		SET status = 'SUCCESS,CLIENT_NEW';
	ELSE
		SET status = 'ERROR,CLIENT_EXIST';
	END IF;

	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
