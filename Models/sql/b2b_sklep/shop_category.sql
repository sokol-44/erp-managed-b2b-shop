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

# Dumping structure for table b2b_sklep.shop_category
DROP TABLE IF EXISTS `shop_category`;
CREATE TABLE IF NOT EXISTS `shop_category` (
  `id_category` int(11) NOT NULL,
  `id_category_parent` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `root_number` int(10) DEFAULT NULL,
  `name` tinytext,
  `description` text,
  `date_added` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.shop_category: ~44 rows (approximately)
/*!40000 ALTER TABLE `shop_category` DISABLE KEYS */;
INSERT INTO `shop_category` (`id_category`, `id_category_parent`, `sort_order`, `root_number`, `name`, `description`, `date_added`, `date_modified`) VALUES
	(2, 0, 0, 1, 'Podręczniki', NULL, NULL, NULL),
	(4, 2, 1, 1, 'klasy I-III', NULL, NULL, NULL),
	(8, 2, 10, 1, 'dla studenta', NULL, NULL, NULL),
	(9, 2, 11, 1, 'dla szkół językowych', NULL, NULL, NULL),
	(10, 2, 11, 1, 'specjalizacje', NULL, NULL, NULL),
	(13, 9, 0, 1, 'poziom B2', NULL, NULL, NULL),
	(17, 9, 0, 1, 'dorośli', NULL, NULL, NULL),
	(19, 9, 0, 1, 'dzieci', NULL, NULL, NULL),
	(21, 10, 0, 1, 'medycyna', NULL, NULL, NULL),
	(22, 10, 0, 1, 'biznes', NULL, NULL, NULL),
	(24, 0, 0, 1, 'e-booki', NULL, NULL, NULL),
	(25, 0, 0, 1, 'Tablice interaktywne', NULL, NULL, NULL),
	(27, 0, 0, 1, 'Literatura dopasowana do poziomu', NULL, NULL, NULL),
	(28, 0, 0, 1, 'Literatura w wersji oryginalnej', NULL, NULL, NULL),
	(29, 0, 0, 1, 'Gry i zabawy językowe', NULL, NULL, NULL),
	(31, 0, 0, 2, 'Słowniki', NULL, NULL, NULL),
	(32, 0, 0, 1, 'Materiały dla nauczycieli', NULL, NULL, NULL),
	(33, 0, 0, 1, 'Nauka samodzielna', NULL, NULL, NULL),
	(34, 0, 0, 1, 'Fonetyka', NULL, NULL, NULL),
	(35, 0, 0, 1, 'Gramatyka', NULL, NULL, NULL),
	(36, 0, 0, 2, 'Egzaminy - materiały', NULL, NULL, NULL),
	(47, 36, 0, 2, 'PET', NULL, NULL, NULL),
	(48, 36, 0, 2, 'FCE', NULL, NULL, NULL),
	(49, 36, 0, 2, 'CAE', NULL, NULL, NULL),
	(50, 36, 0, 2, 'CPE', NULL, NULL, NULL),
	(66, 0, 0, 1, 'Kursy/Warsztaty dla uczniów', NULL, NULL, NULL),
	(69, 0, 0, 1, 'Słownictwo', NULL, NULL, NULL),
	(75, 27, 5, 1, 'Poziom Intermediate', NULL, NULL, NULL),
	(77, 0, 0, 1, 'Kursy/Warsztaty dla nauczycieli', NULL, NULL, NULL),
	(78, 0, 0, 1, 'Kursy/Warsztaty dla metodyków', NULL, NULL, NULL),
	(79, 0, 0, 1, 'Czasopisma', NULL, NULL, NULL),
	(80, 0, 0, 1, 'Czasopisma dla nauczycieli', NULL, NULL, NULL),
	(87, 0, 0, 1, 'Shopping - wyjazdy zagraniczne', NULL, NULL, NULL),
	(91, 36, 0, 2, 'Matura, Matura rozszerzona', NULL, NULL, NULL),
	(92, 2, 5, 1, 'przygotowanie do matury', NULL, NULL, NULL),
	(94, 0, 0, 1, 'Fiszki', NULL, NULL, NULL),
	(95, 0, 0, 1, 'Kursy językowe zagranicą', NULL, NULL, NULL),
	(98, 0, 0, 1, 'Antykwariat', NULL, NULL, NULL),
	(99, 0, 0, 1, 'Kursy/Warsztaty dla właścicieli szkół', NULL, NULL, NULL),
	(103, 95, 0, 1, 'Bułgaria', NULL, NULL, NULL),
	(105, 0, 0, 1, 'Obozy/kolonie językowe', NULL, NULL, NULL),
	(202, 36, 0, 2, 'Egzamin gimnazjalny', NULL, NULL, NULL),
	(203, 0, 0, 1, 'Język specjalistyczny', NULL, NULL, NULL),
	(206, 0, 0, 1, 'Kursy/Szkolenia dla pracowników szkół', NULL, NULL, NULL);
/*!40000 ALTER TABLE `shop_category` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
