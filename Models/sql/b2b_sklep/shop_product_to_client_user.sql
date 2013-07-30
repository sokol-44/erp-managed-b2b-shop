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

-- Zrzut struktury tabela company_1.shop_product_to_client_user
DROP TABLE IF EXISTS `shop_product_to_client_user`;
CREATE TABLE IF NOT EXISTS `shop_product_to_client_user` (
  `id_product` int(11) NOT NULL DEFAULT '0',
  `id_client_user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_product`,`id_client_user`),
  KEY `fk.product_to_client_user.id_product` (`id_product`),
  KEY `fk.product_to_client_user.id_client_user` (`id_client_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
