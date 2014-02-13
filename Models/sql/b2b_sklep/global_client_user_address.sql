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

-- Zrzut struktury .global_client_user_address
CREATE TABLE IF NOT EXISTS `global_client_user_address` (
  `id_address` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) NOT NULL,
  `id_client_user` int(11) DEFAULT NULL,
  `description` text,
  `name` tinytext,
  `street` tinytext,
  `city` tinytext,
  `zip_code` tinytext,
  `country` tinytext,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `rights_edit` varchar(10) NOT NULL DEFAULT 'USER' COMMENT 'USER, CLIENT',
  `rights_use` varchar(10) NOT NULL DEFAULT 'USER' COMMENT 'USER, CLIENT',
  `state` varchar(10) DEFAULT 'ACTIVE',
  PRIMARY KEY (`id_address`),
  KEY `fk.global_client_user_address.id_client_user` (`id_client_user`),
  KEY `fk.global_client_user_address.id_client` (`id_client`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
