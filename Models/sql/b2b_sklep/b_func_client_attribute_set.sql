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

-- Zrzut struktury b_func_client_attribute_set
DELIMITER //
CREATE  ` FUNCTION `b_func_client_attribute_set`(`id_client_in` INT, `type_in` TEXT, `val_in` TEXT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE `status` TINYTEXT DEFAULT NULL;
	DECLARE status_client_attribute TINYTEXT DEFAULT NULL;

	SELECT id_client INTO status_client_attribute FROM global_client_attributes WHERE `id_client` = id_client_in and `type` = type_in;
	IF status_client_attribute IS NULL THEN
		CALL b_proc_client_attribute_set(`id_client_in`, `type_in`, `val_in`);
		SET `status` = CONCAT(@status_proc,',CLIENT_ATTRIBUTE_NEW');
	ELSE
		CALL b_proc_client_attribute_set(`id_client_in`, `type_in`, `val_in`);
		SET `status` = CONCAT(@status_proc,',CLIENT_ATTRIBUTE_EXIST');
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
