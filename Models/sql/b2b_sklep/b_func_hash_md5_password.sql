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

-- Zrzut struktury funkcja b2b_sklep.b_func_hash_md5_password
DROP FUNCTION IF EXISTS `b_func_hash_md5_password`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_hash_md5_password`(`password_in` blob, `salt_in` blob) RETURNS tinytext CHARSET utf8
    NO SQL
    DETERMINISTIC
BEGIN
	DECLARE hash_res TINYTEXT;
	set hash_res = md5(
			concat(
					unhex(
						md5(
							concat(password_in, salt_in)
							)
						),
					password_in,
					unhex(
						md5(
							concat(salt_in, password_in)
							)
						)
					)
			);

	RETURN hash_res;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
