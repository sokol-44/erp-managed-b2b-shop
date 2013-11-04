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

-- Zrzut struktury funkcja company_20.b_func_client_user_set_address
DELIMITER //
CREATE DEFINER=`company_20`@`%` FUNCTION `b_func_client_user_set_address`(`id_address_in` INT, `id_client_user_in` INT, `id_client_in` INT, `description_in` TEXT, `name_in` TEXT, `street_in` TEXT, `city_in` TEXT, `zip_code_in` TEXT, `country_in` TEXT) RETURNS text CHARSET latin2
    MODIFIES SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_client_user TINYTEXT DEFAULT NULL;
	DECLARE status_client_user_address TINYTEXT DEFAULT NULL;

	SELECT id_client_user INTO status_client_user FROM global_client_user
	WHERE `id_client_user` = id_client_user_in and `id_client` = id_client_in;
	IF status_client_user IS NULL THEN
		SET status = 'ERROR,CLIENT_USER_DONT_EXIST';
	ELSE
      SELECT id_address INTO status_client_user_address FROM global_client_user_address
      WHERE `id_address` = id_address_in;
      IF status_client_user_address IS NULL THEN
         INSERT into global_client_user_address (id_address, id_client_user, id_client, 
         description, name, street, city, zip_code, country, date_created) values 
         (id_address_in, id_client_user_in, id_client_in, 
         description_in, name_in, street_in, city_in, zip_code_in, country_in, now());
         SET status = 'SUCCESS,DONT_EXIST,INSERT';
      ELSE
         UPDATE global_client_user_address set 
         description = description_in, name = name_in, street = street_in, city = city_in,
         zip_code = zip_code_in, country = country_in, date_modified = now()
         WHERE id_address = id_address_in AND id_client_user = id_client_user_in
         AND id_client = id_client_in;
		   SET status = 'SUCCESS,EXIST,UPDATE';
      END IF;
	END IF;
   
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
