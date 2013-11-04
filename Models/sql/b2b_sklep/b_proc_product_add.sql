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

-- Zrzut struktury procedura company_20.b_proc_product_add
DELIMITER //
CREATE DEFINER=`company_20`@`%` PROCEDURE `b_proc_product_add`(IN `id_product_in` INT, IN `name_in` TINYTEXT, IN `description_in` TEXT, IN `producer_in` TINYTEXT, IN `catalog_index_in` TINYTEXT, IN `picture_small_url_in` TEXT, IN `picture_big_url_in` TEXT, IN `picture_id_in` INT, IN `price_in` DECIMAL, IN `vat_in` DECIMAL, IN `quantity_in` INT, IN `status_in` TEXT)
BEGIN
	INSERT INTO shop_product (`id_product`, `name`, `description`, `producer`, `catalog_index`, `picture_small_url`, `picture_big_url`, `picture_id`, `price`, `vat`, `quantity`, `status`)
	VALUES (id_product_in, name_in, description_in, producer_in, catalog_index_in, picture_small_url_in, picture_big_url_in, picture_id_in, price_in, vat_in, quantity_in, status_in)
	ON DUPLICATE KEY UPDATE
	`name` = name_in, `description` = description_in, `producer` = producer_in, `catalog_index` = catalog_index_in, `picture_small_url` = picture_small_url_in, `picture_big_url` = picture_big_url_in, `picture_id` = picture_id_in, `price` = price_in, `vat` = vat_in, `quantity` = quantity_in, `status` = status_in;
	SET @status_proc = 'SUCCESS';
END//
DELIMITER ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
