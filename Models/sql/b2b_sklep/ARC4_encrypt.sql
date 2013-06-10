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

-- Zrzut struktury funkcja b2b_sklep.ARC4_encrypt
DROP FUNCTION IF EXISTS `ARC4_encrypt`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `ARC4_encrypt`(`ptext` BLOB, `ckey` BLOB) RETURNS blob
    NO SQL
    DETERMINISTIC
BEGIN
DECLARE stream BLOB(256) DEFAULT '';
DECLARE stream_pos INT DEFAULT 0;
DECLARE idx INT DEFAULT 0;
DECLARE idx_j INT DEFAULT 0;
DECLARE ckey_lenght INT DEFAULT 0;
DECLARE ptext_lenght INT DEFAULT 0;
DECLARE ptext_idx INT DEFAULT 0;
DECLARE temp TINYBLOB default '';
DECLARE ret_stream BLOB default '';
DECLARE stream_at_idx_j TINYBLOB default '';

	REPEAT
		SET stream = CONCAT( stream, CHAR(idx)) ;
		SET idx = idx + 1;
	UNTIL idx > 255 END REPEAT;
	
	SET ckey_lenght = LENGTH(ckey);
	SET idx = 0;
	REPEAT
		SET idx_j = MOD(( idx_j + ASCII( SUBSTRING( stream, idx+1, 1) ) +  ASCII( SUBSTRING( ckey, MOD(idx,ckey_lenght)+1, 1) ) ), 256);
		
		SET temp = SUBSTRING( stream, idx+1, 1);
		SET stream = CONCAT( SUBSTRING( stream, 1, idx), SUBSTRING( stream, idx_j+1, 1) , SUBSTRING( stream, idx+2) );
		SET stream = CONCAT( SUBSTRING( stream, 1, idx_j), temp , SUBSTRING( stream, idx_j+2) );
		SET idx = idx + 1;
	UNTIL idx > 255 END REPEAT;	
	
	SET ptext_lenght = LENGTH(ptext)-1;
	SET idx = 0;
	SET idx_j = 0;
	REPEAT
		SET idx = MOD(idx+1, 256);
		SET idx_j = MOD( idx_j + ASCII( SUBSTRING( stream, idx+1, 1) ), 256);
		SET temp = SUBSTRING( stream, idx+1, 1);
		SET stream_at_idx_j = SUBSTRING( stream, idx_j+1, 1);
		SET stream = CONCAT( SUBSTRING( stream, 1, idx), stream_at_idx_j , SUBSTRING( stream, idx+2) );
		SET stream = CONCAT( SUBSTRING( stream, 1, idx_j), temp , SUBSTRING( stream, idx_j+2) );
		SET stream_pos = MOD( (ASCII( stream_at_idx_j ) + ASCII( temp )), 256);
		SET ret_stream = CONCAT(ret_stream, CHAR( ASCII( SUBSTRING( ptext, ptext_idx+1, 1) ) ^ ASCII( SUBSTRING( stream, stream_pos+1, 1) )  ));
		SET ptext_idx = ptext_idx + 1;
	UNTIL ptext_idx > ptext_lenght END REPEAT;		
	
	RETURN ret_stream;
	
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
