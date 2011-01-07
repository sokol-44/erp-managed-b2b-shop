# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:50
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for view b2b_sklep.shop_order_new
DROP VIEW IF EXISTS `shop_order_new`;
# Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `shop_order_new`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `shop_order_new` AS select `shop_order`.`id_order` AS `id_order`,`shop_order`.`id_client` AS `id_client`,`shop_order`.`date_create` AS `date_create`,`shop_order`.`date_modified` AS `date_modified`,`shop_order`.`id_order_status` AS `id_order_status`,`shop_order`.`hidden_status` AS `hidden_status`,`shop_order`.`description` AS `description`,`shop_order`.`description_basket` AS `description_basket` from `shop_order` where isnull(`shop_order`.`hidden_status`);
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
