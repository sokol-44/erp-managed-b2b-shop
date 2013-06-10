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

-- Zrzut struktury widok b2b_sklep.product_with_client_price
DROP VIEW IF EXISTS `product_with_client_price`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `product_with_client_price`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` VIEW `b2b_sklep`.`product_with_client_price` AS select `p`.`id_product` AS `id_product`,`p`.`name` AS `name`,`p`.`description` AS `description`
 ,`p`.`picture_small_url` AS `picture_small_url`,`p`.`picture_big_url` AS `picture_big_url`
 ,`p`.`picture_id` AS `picture_id`
 ,if(`pcp`.`price`,`pcp`.`price`,`p`.`price`) AS `price`
 ,`p`.`vat`
 ,`p`.`quantity` AS `quantity`,`p`.`status` AS `status`,`c`.`id_client` AS `id_client` 
FROM (`shop_product` `p` JOIN `global_client` c
LEFT JOIN `shop_product_client_price` `pcp` ON
((`p`.`id_product` = `pcp`.`id_product` and `c`.`id_client` = `pcp`.`id_client`))) ;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
