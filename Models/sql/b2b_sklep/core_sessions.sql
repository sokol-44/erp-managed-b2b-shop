# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:44:58
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table b2b_sklep.core_sessions
DROP TABLE IF EXISTS `core_sessions`;
CREATE TABLE IF NOT EXISTS `core_sessions` (
  `sesskey` varchar(64) CHARACTER SET ascii NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `init` int(11) unsigned NOT NULL,
  `expiry` int(11) unsigned NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.core_sessions: ~5 rows (approximately)
DELETE FROM `core_sessions`;
/*!40000 ALTER TABLE `core_sessions` DISABLE KEYS */;
INSERT INTO `core_sessions` (`sesskey`, `active`, `init`, `expiry`, `value`) VALUES
	('54oq0huh148r0n2v5dor83nek1', 1, 1291211674, 1291902049, 'P|O:6:"Person":7:{s:3:"all";N;s:5:"login";s:3:"123";s:9:"logged_in";b:1;s:2:"id";s:1:"2";s:5:"roles";a:3:{s:5:"ADMIN";s:5:"ADMIN";s:4:"USER";s:4:"USER";s:8:"OPERATOR";s:8:"OPERATOR";}s:4:"data";a:10:{s:14:"id_client_user";s:1:"2";s:9:"id_client";s:1:"1";s:4:"name";s:6:"dsadsa";s:11:"description";s:6:"dsadsa";s:5:"login";s:3:"123";s:5:"email";s:3:"123";s:7:"created";s:19:"2010-10-22 15:18:37";s:10:"last_login";N;s:5:"state";s:6:"ACTIVE";s:8:"id_table";s:1:"2";}s:10:"session_id";s:26:"54oq0huh148r0n2v5dor83nek1";}Info|O:4:"Info":1:{s:8:"messages";a:4:{s:5:"error";a:0:{}s:7:"warning";a:0:{}s:7:"success";a:0:{}s:5:"other";a:0:{}}}Shopping_Basket_Chain|O:21:"Shopping_Basket_Chain":4:{s:9:"id_client";i:1;s:14:"id_client_user";i:2;s:13:"id_basket_set";b:0;s:17:"id_basket_current";s:1:"1";}BackTrail|O:9:"BackTrail":1:{s:17:"\0BackTrail\0trails";a:2:{i:0;a:5:{s:3:"GET";a:1:{s:3:"com";s:5:"login";}s:4:"POST";a:0:{}s:3:"com";s:5:"login";s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:1;a:5:{s:3:"GET";a:1:{s:3:"com";s:5:"login";}s:4:"POST";a:2:{s:10:"lgn_CLIENT";s:3:"123";s:12:"pswrd_CLIENT";s:3:"123";}s:3:"com";s:5:"login";s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}}}'),
	('7knp1gmnjl7eufm633gnulvb12', 1, 1290516189, 1291708923, 'P|O:6:"Person":7:{s:3:"all";N;s:5:"login";s:3:"123";s:9:"logged_in";b:1;s:2:"id";s:1:"2";s:5:"roles";a:0:{}s:4:"data";a:10:{s:14:"id_client_user";s:1:"2";s:9:"id_client";s:1:"1";s:4:"name";s:6:"dsadsa";s:11:"description";s:6:"dsadsa";s:5:"login";s:3:"123";s:5:"email";s:3:"123";s:7:"created";s:19:"2010-10-22 15:18:37";s:10:"last_login";N;s:5:"state";s:6:"ACTIVE";s:8:"id_table";s:1:"2";}s:10:"session_id";s:26:"7knp1gmnjl7eufm633gnulvb12";}Info|O:4:"Info":1:{s:8:"messages";a:4:{s:5:"error";a:0:{}s:7:"warning";a:0:{}s:7:"success";a:0:{}s:5:"other";a:0:{}}}Shopping_Basket_Chain|O:21:"Shopping_Basket_Chain":4:{s:9:"id_client";i:1;s:14:"id_client_user";i:2;s:17:"id_basket_current";s:1:"1";s:13:"id_basket_set";b:0;}BackTrail|O:9:"BackTrail":1:{s:17:"\0BackTrail\0trails";a:2:{i:0;a:5:{s:3:"GET";a:1:{s:3:"com";s:5:"login";}s:4:"POST";a:0:{}s:3:"com";s:5:"login";s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:1;a:5:{s:3:"GET";a:1:{s:3:"com";s:5:"login";}s:4:"POST";a:2:{s:10:"lgn_CLIENT";s:3:"123";s:12:"pswrd_CLIENT";s:3:"123";}s:3:"com";s:5:"login";s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}}}'),
	('jh0jvvju1egl1bc5ep81cr8287', 1, 1291361965, 1291966765, ''),
	('l86n3f9bg33fo27n939sb5uh36', 1, 1290778733, 1291387922, 'P|O:6:"Person":7:{s:3:"all";N;s:5:"login";s:4:"123a";s:9:"logged_in";b:1;s:2:"id";s:1:"3";s:5:"roles";a:0:{}s:4:"data";a:10:{s:14:"id_client_user";s:1:"3";s:9:"id_client";s:1:"1";s:4:"name";s:6:"dsadsa";s:11:"description";s:6:"dsadsa";s:5:"login";s:4:"123a";s:5:"email";s:3:"123";s:7:"created";s:19:"2010-10-22 15:18:37";s:10:"last_login";N;s:5:"state";s:6:"ACTIVE";s:8:"id_table";s:1:"3";}s:10:"session_id";s:26:"l86n3f9bg33fo27n939sb5uh36";}Info|O:4:"Info":1:{s:8:"messages";a:4:{s:5:"error";a:0:{}s:7:"warning";a:0:{}s:7:"success";a:0:{}s:5:"other";a:0:{}}}Shopping_Basket_Chain|O:21:"Shopping_Basket_Chain":4:{s:9:"id_client";i:1;s:14:"id_client_user";i:3;s:13:"id_basket_set";b:0;s:17:"id_basket_current";s:1:"1";}BackTrail|O:9:"BackTrail":1:{s:17:"\0BackTrail\0trails";a:9:{i:0;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:1;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:2;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:3;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:4;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:5;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:6;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:7;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:8;a:5:{s:3:"GET";a:0:{}s:4:"POST";a:0:{}s:3:"com";N;s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}}}'),
	('peopkovtck7eqh6305ue9p9sm2', 1, 1291362386, 1291992112, 'P|O:6:"Person":7:{s:3:"all";N;s:5:"login";s:3:"123";s:9:"logged_in";b:1;s:2:"id";s:1:"2";s:5:"roles";a:3:{s:5:"ADMIN";s:5:"ADMIN";s:4:"USER";s:4:"USER";s:8:"OPERATOR";s:8:"OPERATOR";}s:4:"data";a:10:{s:14:"id_client_user";s:1:"2";s:9:"id_client";s:1:"1";s:4:"name";s:6:"dsadsa";s:11:"description";s:6:"dsadsa";s:5:"login";s:3:"123";s:5:"email";s:3:"123";s:7:"created";s:19:"2010-10-22 15:18:37";s:10:"last_login";N;s:5:"state";s:6:"ACTIVE";s:8:"id_table";s:1:"2";}s:10:"session_id";s:26:"peopkovtck7eqh6305ue9p9sm2";}Info|O:4:"Info":1:{s:8:"messages";a:4:{s:5:"error";a:0:{}s:7:"warning";a:0:{}s:7:"success";a:0:{}s:5:"other";a:0:{}}}Shopping_Basket_Chain|O:21:"Shopping_Basket_Chain":4:{s:9:"id_client";i:1;s:14:"id_client_user";i:2;s:13:"id_basket_set";b:0;s:17:"id_basket_current";s:1:"5";}BackTrail|O:9:"BackTrail":1:{s:17:"\0BackTrail\0trails";a:3:{i:0;a:5:{s:3:"GET";a:1:{s:3:"com";s:5:"login";}s:4:"POST";a:0:{}s:3:"com";s:5:"login";s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:1;a:5:{s:3:"GET";a:1:{s:3:"com";s:5:"login";}s:4:"POST";a:0:{}s:3:"com";s:5:"login";s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}i:2;a:5:{s:3:"GET";a:1:{s:3:"com";s:5:"login";}s:4:"POST";a:2:{s:10:"lgn_CLIENT";s:3:"123";s:12:"pswrd_CLIENT";s:3:"123";}s:3:"com";s:5:"login";s:16:"PERSON_LOGGED_IN";b:0;s:10:"PAGE_TITLE";s:0:"";}}}');
/*!40000 ALTER TABLE `core_sessions` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
