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

-- Zrzut struktury procedura company_20.b_proc_client_user_check
DELIMITER //
CREATE DEFINER=`company_20`@`%` PROCEDURE `b_proc_client_user_check`(IN `id_client_in` INT, IN `id_client_user_in` INT)
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_client TINYTEXT DEFAULT NULL;
	DECLARE status_client_user TINYTEXT DEFAULT NULL;

	SET @status_proc = 'ERROR';
	
	SELECT id_client INTO status_client FROM global_client WHERE `id_client` = id_client_in;
	IF status_client IS NULL THEN
		SET status = 'ERROR,CLIENT_DONT_EXIST';
	ELSE
		SELECT id_client INTO status_client_user FROM global_client_user
		WHERE `id_client_user` = id_client_user_in;
		IF status_client_user IS NULL THEN
			SET status = 'ERROR,CLIENT_USER_DONT_EXIST';
		ELSE
			IF status_client_user != status_client THEN
				SET status = 'ERROR,CLIENT_USER_FROM_CLIENT_DONT_EXIST';
			ELSE
				SET status = 'SUCCESS,CLIENT_USER_FROM_CLIENT_EXIST';
				SET @status_proc = 'SUCCESS';
			END IF;
		END IF;
	END IF;
	SET @status_long_proc = status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
