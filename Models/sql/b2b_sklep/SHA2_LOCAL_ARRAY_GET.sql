-- --------------------------------------------------------
-- Host:                         sql.company.nazwa.pl
-- Wersja serwera:               5.5.25a-log - NetArt MySQL Server
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              8.0.0.4396
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury funkcja company_1.SHA2_LOCAL_ARRAY_GET
DROP FUNCTION IF EXISTS `SHA2_LOCAL_ARRAY_GET`;
DELIMITER //
CREATE DEFINER=`company_1`@`%` FUNCTION `SHA2_LOCAL_ARRAY_GET`(array BLOB, idx TINYINT UNSIGNED) RETURNS int(10) unsigned
    NO SQL
    DETERMINISTIC
BEGIN
    SET idx = (idx * 4) + 1;
    RETURN (ASCII(SUBSTRING(array FROM idx+0 FOR 1)) << 24) | (ASCII(SUBSTRING(array FROM idx+1 FOR 1)) << 16) |
           (ASCII(SUBSTRING(array FROM idx+2 FOR 1)) <<  8) | (ASCII(SUBSTRING(array FROM idx+3 FOR 1)) <<  0);
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
