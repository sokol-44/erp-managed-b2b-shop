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

-- Zrzut struktury funkcja company_1.b2b_sha2
DROP FUNCTION IF EXISTS `b2b_sha2`;
DELIMITER //
CREATE DEFINER=`company_1`@`%` FUNCTION `b2b_sha2`(`message` MEDIUMBLOB, `bits` SMALLINT) RETURNS char(64) CHARSET utf8
    DETERMINISTIC
BEGIN
   DECLARE res CHAR(64) DEFAULT NULL;
   DECLARE CONTINUE HANDLER FOR 1305
   BEGIN
      SET res = SHA2_LOCAL(`message`, `bits`);
   END;
   SET res = SHA2(`message`, `bits`);
   RETURN res;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
