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

-- Zrzut struktury funkcja company_20.hash_arc4_password
DELIMITER //
CREATE DEFINER=`company_20`@`%` FUNCTION `hash_arc4_password`(`password` BLOB, `salt` BLOB) RETURNS tinytext CHARSET utf8
    DETERMINISTIC
BEGIN
	DECLARE hash_res TINYTEXT;
	set hash_res = 
			HEX( 
				ARC4_ENCRYPT(
					unhex( md5(password) ),
					unhex( CONCAT(md5( CONCAT(password, salt) ), md5( CONCAT(salt, password) ))))
			);
	RETURN hash_res;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
