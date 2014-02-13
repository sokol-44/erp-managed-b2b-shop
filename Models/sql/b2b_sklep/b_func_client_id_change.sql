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

-- Zrzut struktury b_func_client_id_change
DELIMITER //
CREATE  ` FUNCTION `b_func_client_id_change`(`id_client_in` INT, `id_client_new` INT) RETURNS tinytext CHARSET latin2
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE affected_rows INT DEFAULT NULL;
	
	SELECT id_client INTO status FROM global_client WHERE `id_client` = id_client_in;
	IF status IS NULL THEN
		SET status = 'ERROR,CLIENT_NEW';
	ELSE		
		UPDATE global_client SET `id_client`=id_client_new, `state`= "ACTIVE", `modified` = now()
		WHERE `id_client`=id_client_in;
--     AND `state`= "NEW";
    SELECT ROW_COUNT() into affected_rows;
    IF affected_rows = 0 THEN
		  SET status = 'ERROR,CLIENT_NOT_NEW';
	  ELSE		
		  SET status = 'SUCCESS,CLIENT_EXIST';
      UPDATE global_client_user SET `id_client`=id_client_new, `state`= "ACTIVE", `modified` = now()
		  WHERE `id_client`=id_client_in;
--       AND `state`= "NEW";
    END IF;
	END IF;

	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
