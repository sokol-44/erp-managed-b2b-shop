# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:47
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for function b2b_sklep.b_func_client_user_change
DROP FUNCTION IF EXISTS `b_func_client_user_change`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_client_user_change`(`id_client_user_in` INT, `id_client_in` INT, `login_in` TINYTEXT, `password_in` TINYTEXT, `password_salt_in` BLOB, `name_in` TINYTEXT, `description_in` TEXT, `email_in` TINYTEXT, `state_in` TINYTEXT) RETURNS tinytext CHARSET utf8
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
			SET status = 'CLIENT_USER_DONT_EXIST';
		ELSE
			CALL b_proc_client_user_add(id_client_user_in, id_client_in, login_in, password_in, password_salt_in, name_in,
			description_in, email_in, state_in);
			SET status = @status_proc;
		END IF;
	END IF;


	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
