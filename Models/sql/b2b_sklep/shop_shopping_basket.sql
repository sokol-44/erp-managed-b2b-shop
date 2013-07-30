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

-- Zrzut struktury tabela company_1.shop_shopping_basket
DROP TABLE IF EXISTS `shop_shopping_basket`;
CREATE TABLE IF NOT EXISTS `shop_shopping_basket` (
  `id_shopping_basket` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) DEFAULT NULL,
  `description` text,
  `date_create` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `using_id_client_user` int(11) DEFAULT NULL,
  `using_session_id` tinytext,
  `using_date` datetime DEFAULT NULL,
  `state` varchar(10) DEFAULT NULL COMMENT 'USE_0, FREE_0, LOCK_0; USE_1, FREE_1, LOCK_1; USE_2, FREE_2, LOCK_2; # ORDER, FAV ',
  PRIMARY KEY (`id_shopping_basket`),
  KEY `fk.shopping_basket.id_client_user` (`using_id_client_user`),
  KEY `fk.shopping_basket.id_client` (`id_client`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
