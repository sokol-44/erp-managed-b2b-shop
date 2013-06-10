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

-- Zrzut struktury procedura b2b_sklep.b_proc_client_user_add
DROP PROCEDURE IF EXISTS `b_proc_client_user_add`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `b_proc_client_user_add`(IN `id_client_user_in` INT, IN `id_client_in` INT, IN `login_in` tinytext, IN `password_in` tinytEXT, IN `password_salt_in` tinytext, IN `name_in` TINYTEXT, IN `description_in` TEXT, IN `email_in` TINYTEXT, IN `state_in` TINYTEXT)
BEGIN
	DECLARE password_text TINYTEXT;
	DECLARE hash_password_text TINYTEXT;
	DECLARE hash_salt_text TINYTEXT;
	SET hash_salt_text = b_func_hash_md5_password(password_salt_in, b_func_random_data(8));
	SET hash_password_text = b_func_hash_md5_password(password_in, hash_salt_text);
	SET password_text = CONCAT('MD53:', hash_password_text, ':', hash_salt_text);

	INSERT INTO global_client_user
	(`id_client_user`, `id_client`, `name`, `description`, `login`, `password`, `email`, `phone`, `phone_cell_in`, `created`, `state`)
	VALUES
	(id_client_user_in, id_client_in, name_in, description_in, login_in, password_text, email_in, phone_in, phone_cell_in, now(), state_in)
	ON DUPLICATE KEY UPDATE `id_client` = id_client, `name` = name_in, `description` = description_in, `login` = login,
	`password` = password_text, `email` = email_in, `phone` = phone_in, `phone_cell` = phone_cell_in, `state` = state_in;
	SET @status_proc = 'SUCCESS';
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
