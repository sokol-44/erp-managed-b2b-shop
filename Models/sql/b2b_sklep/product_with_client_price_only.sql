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

-- Zrzut struktury tabela company_20.product_with_client_price_only
CREATE TABLE IF NOT EXISTS `product_with_client_price_only` (
  `id_product` int(11) DEFAULT NULL,
  `name` text,
  `description` text,
  `producer` tinytext,
  `catalog_index` tinytext,
  `picture_small_url` tinytext,
  `picture_big_url` tinytext,
  `picture_id` int(10) unsigned DEFAULT NULL,
  `price` decimal(20,4) unsigned DEFAULT NULL,
  `vat` tinyint(3) unsigned DEFAULT NULL,
  `quantity` int(10) unsigned DEFAULT NULL,
  `status` enum('ACTIVE','NA') DEFAULT NULL,
  `id_client` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin2;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
