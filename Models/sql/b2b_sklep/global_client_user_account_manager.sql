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

-- Zrzut struktury tabela company_20.global_client_user_account_manager
CREATE TABLE IF NOT EXISTS `global_client_user_account_manager` (
  `id_account_manager` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) DEFAULT NULL,
  `id_client_user` int(11) DEFAULT NULL,
  `account_manager_name` varchar(50) DEFAULT NULL,
  `fullname` tinytext,
  `phone1` text,
  `phone2` tinytext,
  `email` tinytext,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `rights_edit` varchar(10) NOT NULL DEFAULT 'USER' COMMENT 'USER, CLIENT',
  `rights_use` varchar(10) NOT NULL DEFAULT 'USER' COMMENT 'USER, CLIENT',
  PRIMARY KEY (`id_account_manager`),
  UNIQUE KEY `wfmag_name` (`account_manager_name`(3)),
  KEY `fk.global_client_user_account_manager.id_client_user` (`id_client_user`),
  KEY `fk.global_client_user_account_manager.id_client` (`id_client`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
