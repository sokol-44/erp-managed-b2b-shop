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

-- Zrzut struktury widok company_1.shop_order_new
DROP VIEW IF EXISTS `shop_order_new`;
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `shop_order_new`;
CREATE ALGORITHM=UNDEFINED DEFINER=`company_1`@`%` SQL SECURITY DEFINER VIEW `shop_order_new` AS select `shop_order`.`id_order` AS `id_order`,`shop_order`.`id_client` AS `id_client`,`shop_order`.`date_create` AS `date_create`,`shop_order`.`date_modified` AS `date_modified`,`shop_order`.`id_order_status` AS `id_order_status`,`shop_order`.`hidden_status` AS `hidden_status`,`shop_order`.`description` AS `description`,`shop_order`.`description_basket` AS `description_basket`,`shop_order`.`id_shopping_basket` AS `id_shopping_basket` from `shop_order` where isnull(`shop_order`.`hidden_status`);
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
