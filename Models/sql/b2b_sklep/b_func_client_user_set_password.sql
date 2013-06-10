-- --------------------------------------------------------
-- Host:                         localhost
-- Wersja serwera:               5.1.40-community-log - MySQL Community Server (GPL)
-- Serwer OS:                    Win32
-- HeidiSQL Wersja:              8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury funkcja b2b_sklep.b_func_client_user_set_password
DROP FUNCTION IF EXISTS `b_func_client_user_set_password`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_client_user_set_password`(`id_client_user_in` INT, `id_client_in` INT, `password_in` TINYTEXT, `password_salt_in` BLOB) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_client_user TINYTEXT DEFAULT NULL;
	DECLARE hash_password_text TINYTEXT DEFAULT NULL;
	DECLARE hash_salt_text TINYTEXT DEFAULT NULL;
	DECLARE password_text TINYTEXT DEFAULT NULL;
	SET hash_salt_text = b_func_hash_md5_password(password_salt_in, b_func_random_data(8));
	SET hash_password_text = b_func_hash_md5_password(password_in, hash_salt_text);
	SET password_text = CONCAT('MD53:', hash_password_text, ':', hash_salt_text);

	SELECT id_client_user INTO status_client_user FROM global_client_user
	WHERE `id_client_user` = id_client_user_in and `id_client` = id_client_in;
	IF status_client_user IS NULL THEN
		SET status = 'ERROR,CLIENT_USER_DONT_EXIST';
	ELSE
		UPDATE global_client_user SET `password` = password_text WHERE `id_client_user` = id_client_user_in;
		SET status = 'SUCCESS,CLIENT_USER_EXIST';
	END IF;

	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
