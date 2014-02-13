-- --------------------------------------------------------
-- Host:                         sql.company.nazwa.pl
-- Wersja serwera:               5.5.25a-log - NetArt MySQL Server
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Zrzut struktury widok company_22.product_with_client_price
-- Usuwanie tabeli tymczasowej i tworzenie ostatecznej struktury WIDOKU
DROP TABLE IF EXISTS `product_with_client_price`;
CREATE ALGORITHM=TEMPTABLE  ` SQL SECURITY DEFINER VIEW `product_with_client_price` AS select `p`.`id_product` AS `id_product`,`p`.`name` AS `name`,`p`.`description` AS `description`,`p`.`producer` AS `producer`,`p`.`catalog_index` AS `catalog_index`,`p`.`picture_small_url` AS `picture_small_url`,`p`.`picture_big_url` AS `picture_big_url`,`p`.`picture_id` AS `picture_id`,if(`pcp`.`price`,`pcp`.`price`,`p`.`price`) AS `price`,`p`.`vat` AS `vat`,`p`.`quantity` AS `quantity`,`p`.`status` AS `status`,`c`.`id_client` AS `id_client` from ((`shop_product` `p` join `global_client` `c`) left join `shop_product_client_price` `pcp` on(((`p`.`id_product` = `pcp`.`id_product`) and (`c`.`id_client` = `pcp`.`id_client`))));
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
