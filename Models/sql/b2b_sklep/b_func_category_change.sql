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

-- Zrzut struktury funkcja b2b_sklep.b_func_category_change
DROP FUNCTION IF EXISTS `b_func_category_change`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` FUNCTION `b_func_category_change`(`id_category_in` INT, `id_category_parent_in` INT, `sort_order_in` INT, `root_number_in` TINYINT, `name_in` TEXT, `description_in` TEXT) RETURNS tinytext CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE status TINYTEXT DEFAULT NULL;
	DECLARE status_category TINYTEXT DEFAULT NULL;

	SELECT id_category INTO status_category FROM shop_category WHERE `id_category` = id_category_in;
	IF status_category IS NULL THEN
		SET status = 'ERROR,CATEGORY_DONT_EXIST';
	ELSE
		CALL b_proc_category_add(id_category_in, id_category_parent_in, sort_order_in, root_number_in, name_in, description_in);
		SET status = concat(@status_proc,',CATEGORY_EXIST');
	END IF;
	return status;
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
