# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:48
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table b2b_sklep.core_sessions
DROP TABLE IF EXISTS `core_sessions`;
CREATE TABLE IF NOT EXISTS `core_sessions` (
  `sesskey` varchar(64) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `init` int(11) unsigned NOT NULL,
  `expiry` int(11) unsigned NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.core_sessions: ~1 rows (approximately)
/*!40000 ALTER TABLE `core_sessions` DISABLE KEYS */;
INSERT INTO `core_sessions` (`sesskey`, `active`, `init`, `expiry`, `value`) VALUES
	('t83v68jbmhpbulgbo11ppl8j16', 1, 1294408653, 1295014443, 'P|O:6:"Person":7:{s:3:"all";N;s:5:"login";b:0;s:9:"logged_in";b:0;s:2:"id";i:0;s:5:"roles";a:0:{}s:4:"data";a:0:{}s:10:"session_id";s:26:"t83v68jbmhpbulgbo11ppl8j16";}Info|O:4:"Info":1:{s:8:"messages";a:4:{s:5:"error";a:0:{}s:7:"warning";a:0:{}s:7:"success";a:0:{}s:5:"other";a:0:{}}}Shopping_Basket_Chain|O:21:"Shopping_Basket_Chain":5:{s:9:"id_client";i:0;s:14:"id_client_user";i:0;s:11:"Basket_List";a:1:{i:1;O:15:"Shopping_Basket":4:{s:8:"contents";a:0:{}s:21:"id_nr_shopping_basket";i:1;s:6:"params";a:10:{s:9:"id_client";i:0;s:21:"id_nr_shopping_basket";i:1;s:11:"description";s:0:"";s:11:"date_create";s:19:"2011-01-07 15:03:00";s:13:"date_modified";s:0:"";s:9:"ts_create";i:1294408980;s:11:"ts_modified";s:0:"";s:20:"using_id_client_user";i:0;s:16:"using_session_id";i:0;s:10:"using_date";i:0;}s:5:"total";a:4:{s:13:"product_total";i:0;s:13:"product_types";i:0;s:9:"sum_gross";i:0;s:9:"sum_netto";i:0;}}}s:13:"id_basket_set";b:0;s:17:"id_basket_current";i:1;}BackTrail|O:9:"BackTrail":1:{s:17:"\0BackTrail\0trails";a:4:{i:0;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:1;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:2;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:3;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}}}');
/*!40000 ALTER TABLE `core_sessions` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
