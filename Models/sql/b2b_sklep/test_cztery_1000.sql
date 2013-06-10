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

-- Zrzut struktury procedura b2b_sklep.test_cztery_1000
DROP PROCEDURE IF EXISTS `test_cztery_1000`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `test_cztery_1000`()
BEGIN
DECLARE idx INT DEFAULT 0;
DECLARE stime INT DEFAULT 0;
DECLARE etime INT DEFAULT 0;
DECLARE res BLOB DEFAULT '';

	SET stime =  UNIX_TIMESTAMP();
	REPEAT
		SELECT hash_arc4_password( CONCAT('Wiki', CHAR( MOD(idx,255) ) ), 'pediapediapediapediapediapediapediapediapediapedia') INTO res;
		SET idx = idx + 1;
	UNTIL idx > 1000 END REPEAT;
	
	SET etime = UNIX_TIMESTAMP() - stime;
	select etime;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
