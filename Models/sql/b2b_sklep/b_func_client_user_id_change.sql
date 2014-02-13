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

-- Zrzut struktury b_func_client_user_id_change
DELIMITER //
CREATE  ` FUNCTION `b_func_client_user_id_change`(`id_client_in` INT, `id_client_user_in` INT, `id_client_new_in` INT, `id_client_user_new_in` INT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_client TINYTEXT DEFAULT NULL;
	DECLARE status_client_user TINYTEXT DEFAULT NULL;
	
	CALL `b_proc_client_user_check`(`id_client_in`, `id_client_user_in`);
	IF @status_proc = 'ERROR' THEN
		SET `status` = @status_long_proc;
	ELSE
		SELECT id_client INTO status_client FROM global_client WHERE `id_client` = id_client_new_in;
		IF status_client IS NULL THEN
			SET status = 'ERROR,CLIENT_DONT_EXIST';
		ELSE
			SELECT id_client INTO status_client_user FROM global_client_user
			WHERE `id_client_user` = id_client_user_new_in;
			IF status_client_user IS NULL THEN
				UPDATE global_client_user SET `id_client`=id_client_new_in, `id_client_user`=id_client_user_new_in, `modified` = now()
				WHERE `id_client`=id_client_in AND `id_client_user`=id_client_user_in;
		--      AND `state`= "NEW";	
				SET status = 'SUCCESS,CLIENT_USER_NEW_ID';
			ELSE
				SET status = 'ERROR,CLIENT_USER_EXIST';
			END IF;
		END IF;
	END IF;

	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
