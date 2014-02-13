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

-- Zrzut struktury b_func_client_user_change
DELIMITER //
CREATE  ` FUNCTION `b_func_client_user_change`(`id_client_user_in` INT, `id_client_in` INT, `login_in` TINYTEXT, `name_in` TINYTEXT, `description_in` TEXT, `email_in` TINYTEXT, `phone_in` TINYTEXT, `phone_cell_in` TINYTEXT, `state_in` TINYTEXT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_client TINYTEXT DEFAULT NULL;
	DECLARE status_client_user TINYTEXT DEFAULT NULL;

	SELECT id_client INTO status_client FROM global_client WHERE `id_client` = id_client_in;
	IF status_client IS NULL THEN
		SET status = 'CLIENT_DONT_EXIST';
	ELSE
		SELECT id_client_user INTO status_client_user FROM global_client_user
		WHERE `id_client_user` = id_client_user_in and `id_client` = id_client_in;
		IF status_client_user IS NULL THEN
			SET status = 'ERROR,CLIENT_USER_DONT_EXIST';
		ELSE
	      UPDATE global_client_user SET `name` = name_in, `description` = description_in, `login` = login,
	      `email` = email_in, `phone` = phone_in, `phone_cell` = phone_cell_in, `state` = state_in 
         WHERE `id_client_user` = id_client_user_in and `id_client` = id_client_in;
			SET status = 'SUCCESS,CLIENT_USER_EXIST';
		END IF;
	END IF;

	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
