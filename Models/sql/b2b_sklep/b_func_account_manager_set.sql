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

-- Zrzut struktury b_func_account_manager_set
DELIMITER //
CREATE  ` FUNCTION `b_func_account_manager_set`(`id_account_manager_in` INT, `id_client_in` INT, `id_client_user_in` INT, `account_manager_name_in` VARCHAR(10), `fullname_in` TINYTEXT, `phone1_in` TINYTEXT, `phone2_in` TINYTEXT, `email_in` TINYTEXT, `state_in` TINYTEXT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_client TINYTEXT DEFAULT NULL;
	DECLARE status_user_client TINYTEXT DEFAULT NULL;
	DECLARE status_account_manager TINYTEXT DEFAULT NULL;
	DECLARE status_account_manage_name TINYTEXT DEFAULT NULL;

	CALL `b_proc_client_user_check`(`id_client_in`, `id_client_user_in`);
	IF @status_proc = 'ERROR' THEN
		SET `status` = @status_long_proc;
	ELSE
		IF `id_account_manager_in` = 0 THEN
			SELECT id_account_manager INTO status_account_manage_name FROM global_client_user_account_manager
			WHERE `account_manager_name` = account_manager_name_in;
			IF status_account_manage_name IS NULL THEN
				INSERT INTO global_client_user_account_manager (`account_manager_name`) VALUES (`account_manager_name_in`);
				SELECT LAST_INSERT_ID() INTO status_account_manage_name;
				SET `status` = ',ACCOUNT_MANAGER_NEW,NAME';
			ELSE
				SET `status` = ',ACCOUNT_MANAGER_EXIST,NAME';	
			END IF;
			CALL b_proc_account_manager_set(`status_account_manage_name`, `id_client_in`, `id_client_user_in`, `account_manager_name_in`, `fullname_in`, `phone1_in`, `phone2_in`,  `email_in`,  `state_in`);
			SET `status` = CONCAT(@status_proc,`status`);
		ELSE 
			SELECT id_account_manager INTO status_account_manager FROM global_client_user_account_manager
			WHERE `id_account_manager` = id_account_manager_in;
			IF status_account_manager IS NULL THEN
				CALL b_proc_account_manager_set(`id_account_manager_in`, `id_client_in`, `id_client_user_in`, `account_manager_name_in`, `fullname_in`, `phone1_in`, `phone2_in`,  `email_in`, `state_in`);
				SET `status` = CONCAT(@status_proc,',ACCOUNT_MANAGER_NEW,ID');
			ELSE
				CALL b_proc_account_manager_set(`id_account_manager_in`, `id_client_in`, `id_client_user_in`, `account_manager_name_in`, `fullname_in`, `phone1_in`, `phone2_in`,  `email_in`,  `state_in`);
				SET `status` = CONCAT(@status_proc,',ACCOUNT_MANAGER_EXIST,ID');
			END IF;
		END IF;
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
