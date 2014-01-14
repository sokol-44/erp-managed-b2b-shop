# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:44:59
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping data for table b2b_sklep.shop_category: ~44 rows (approximately)
DELETE FROM `shop_category`;
/*!40000 ALTER TABLE `shop_category` DISABLE KEYS */;
INSERT INTO `shop_category` (`id_category`, `id_category_parent`, `sort_order`, `name`, `description`, `date_added`, `date_modified`) VALUES
	(2, 0, 0, 'Podręczniki', NULL, NULL, NULL),
	(4, 2, 1, 'klasy I-III', NULL, NULL, NULL),
	(8, 2, 10, 'dla studenta', NULL, NULL, NULL),
	(9, 2, 11, 'dla szkół językowych', NULL, NULL, NULL),
	(10, 2, 11, 'specjalizacje', NULL, NULL, NULL),
	(13, 9, 0, 'poziom B2', NULL, NULL, NULL),
	(17, 9, 0, 'dorośli', NULL, NULL, NULL),
	(19, 9, 0, 'dzieci', NULL, NULL, NULL),
	(21, 10, 0, 'medycyna', NULL, NULL, NULL),
	(22, 10, 0, 'biznes', NULL, NULL, NULL),
	(24, 0, 0, 'e-booki', NULL, NULL, NULL),
	(25, 0, 0, 'Tablice interaktywne', NULL, NULL, NULL),
	(27, 0, 0, 'Literatura dopasowana do poziomu', NULL, NULL, NULL),
	(28, 0, 0, 'Literatura w wersji oryginalnej', NULL, NULL, NULL),
	(29, 0, 0, 'Gry i zabawy językowe', NULL, NULL, NULL),
	(31, 0, 0, 'Słowniki', NULL, NULL, NULL),
	(32, 0, 0, 'Materiały dla nauczycieli', NULL, NULL, NULL),
	(33, 0, 0, 'Nauka samodzielna', NULL, NULL, NULL),
	(34, 0, 0, 'Fonetyka', NULL, NULL, NULL),
	(35, 0, 0, 'Gramatyka', NULL, NULL, NULL),
	(36, 0, 0, 'Egzaminy - materiały', NULL, NULL, NULL),
	(47, 36, 0, 'PET', NULL, NULL, NULL),
	(48, 36, 0, 'FCE', NULL, NULL, NULL),
	(49, 36, 0, 'CAE', NULL, NULL, NULL),
	(50, 36, 0, 'CPE', NULL, NULL, NULL),
	(66, 0, 0, 'Kursy/Warsztaty dla uczniów', NULL, NULL, NULL),
	(69, 0, 0, 'Słownictwo', NULL, NULL, NULL),
	(75, 27, 5, 'Poziom Intermediate', NULL, NULL, NULL),
	(77, 0, 0, 'Kursy/Warsztaty dla nauczycieli', NULL, NULL, NULL),
	(78, 0, 0, 'Kursy/Warsztaty dla metodyków', NULL, NULL, NULL),
	(79, 0, 0, 'Czasopisma', NULL, NULL, NULL),
	(80, 0, 0, 'Czasopisma dla nauczycieli', NULL, NULL, NULL),
	(87, 0, 0, 'Shopping - wyjazdy zagraniczne', NULL, NULL, NULL),
	(91, 36, 0, 'Matura, Matura rozszerzona', NULL, NULL, NULL),
	(92, 2, 5, 'przygotowanie do matury', NULL, NULL, NULL),
	(94, 0, 0, 'Fiszki', NULL, NULL, NULL),
	(95, 0, 0, 'Kursy językowe zagranicą', NULL, NULL, NULL),
	(98, 0, 0, 'Antykwariat', NULL, NULL, NULL),
	(99, 0, 0, 'Kursy/Warsztaty dla właścicieli szkół', NULL, NULL, NULL),
	(103, 95, 0, 'Bułgaria', NULL, NULL, NULL),
	(105, 0, 0, 'Obozy/kolonie językowe', NULL, NULL, NULL),
	(202, 36, 0, 'Egzamin gimnazjalny', NULL, NULL, NULL),
	(203, 0, 0, 'Język specjalistyczny', NULL, NULL, NULL),
	(206, 0, 0, 'Kursy/Szkolenia dla pracowników szkół', NULL, NULL, NULL);
/*!40000 ALTER TABLE `shop_category` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
