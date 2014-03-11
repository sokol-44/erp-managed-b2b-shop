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

-- Zrzut struktury .tool_battery_ups
CREATE TABLE IF NOT EXISTS `tool_battery_ups` (
  `id_ups` int(11) NOT NULL AUTO_INCREMENT,
  `maker` varchar(20) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `output_power` int(11) DEFAULT NULL,
  `output_power_w` int(11) DEFAULT NULL,
  `cabinet` varchar(50) DEFAULT NULL,
  `internal_count` int(11) DEFAULT NULL,
  `internal_capacity` int(11) DEFAULT NULL,
  `external_count` int(11) DEFAULT NULL,
  `external_capacity` int(11) DEFAULT NULL,
  `box` enum('RACK 19"','TOWER') DEFAULT NULL,
  `typology` enum('LINE-INTERACTIVE','ON-LINE') DEFAULT NULL,
  `phase` tinyint(4) DEFAULT NULL,
  `quality` tinyint(4) DEFAULT NULL,
  `max_external` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id_ups`),
  KEY `internal_capacity` (`internal_capacity`),
  KEY `external_capacity` (`external_capacity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
