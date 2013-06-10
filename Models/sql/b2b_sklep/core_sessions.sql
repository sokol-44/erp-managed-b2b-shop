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

-- Zrzut struktury tabela b2b_sklep.core_sessions
DROP TABLE IF EXISTS `core_sessions`;
CREATE TABLE IF NOT EXISTS `core_sessions` (
  `sesskey` varchar(64) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `init` int(11) unsigned NOT NULL,
  `expiry` int(11) unsigned NOT NULL,
  `value` mediumtext NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
