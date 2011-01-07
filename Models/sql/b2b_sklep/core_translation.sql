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

# Dumping structure for table b2b_sklep.core_translation
DROP TABLE IF EXISTS `core_translation`;
CREATE TABLE IF NOT EXISTS `core_translation` (
  `id_translation` int(11) NOT NULL AUTO_INCREMENT,
  `language` enum('pl','en') DEFAULT 'pl',
  `definition` text NOT NULL,
  `com` enum('catalog','basket','login','logout') DEFAULT NULL,
  `translation` text NOT NULL,
  PRIMARY KEY (`id_translation`),
  KEY `search` (`language`,`definition`(10),`com`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

# Dumping data for table b2b_sklep.core_translation: ~86 rows (approximately)
/*!40000 ALTER TABLE `core_translation` DISABLE KEYS */;
INSERT INTO `core_translation` (`id_translation`, `language`, `definition`, `com`, `translation`) VALUES
	(1, 'pl', 'TEXT_TOP_CATEGORY', NULL, 'top'),
	(2, 'pl', 'TEXT_NAME', NULL, 'Nazwa'),
	(3, 'pl', 'TEXT_DESCRIPTION', NULL, 'opis'),
	(4, 'pl', 'TEXT_PICTURE', NULL, 'Obrazek'),
	(5, 'pl', 'TEXT_PRICE', NULL, 'Cena'),
	(6, 'pl', 'TEXT_ADD_TO_BASKET', NULL, 'Do&nbsp;koszyka'),
	(7, 'pl', 'TEXT_CATALOG', NULL, 'Katalog'),
	(8, 'pl', 'TEXT_BASKET', NULL, 'Koszyk'),
	(9, 'pl', 'TEXT_TOTAL_PRODUCTS', NULL, 'Produktów'),
	(10, 'pl', 'TEXT_PRODUCTS_TYPES', NULL, 'Typ. produktów'),
	(11, 'pl', 'TEXT_SUM_GROSS', NULL, 'Suma brutto'),
	(12, 'pl', 'TEXT_SUM_NETTO', NULL, 'Suma netto'),
	(13, 'pl', 'TEXT_QUANTITY', NULL, 'Liczba'),
	(14, 'pl', 'TEXT_REMOVE_FROM_BASKET', NULL, 'Usunąć z koszyka'),
	(15, 'pl', 'TEXT_UPDATE_BASKET', NULL, 'Aktualizuj koszyk'),
	(16, 'pl', 'TEXT_SHOW_BIG_IMAGE', NULL, 'Pokaż duży obrazek'),
	(17, 'pl', 'TEXT_MAIN_PAGE', NULL, 'Główna strona'),
	(18, 'pl', 'TEXT_LOGIN', NULL, 'Zaloguj'),
	(19, 'pl', 'TEXT_LOGOUT', NULL, 'Wyloguj'),
	(20, 'pl', 'TEXT_REMOVE_BASKET', NULL, 'Usuń koszyk'),
	(21, 'pl', 'TEXT_CLEAN_BASKET', NULL, 'Wyczyść koszyk'),
	(22, 'pl', 'TEXT_CLEAN_PRODUCT_IN_BASKET', NULL, 'Wyczyśc artykuły w koszyku'),
	(23, 'pl', 'TEXT_SWITCH_WORKING_BASKET', NULL, 'Zamień roboczy koszyk'),
	(24, 'pl', 'TEXT_PASSWORD', NULL, 'Hasło'),
	(25, 'pl', 'TEXT_ADD_BASKET', NULL, 'Dodaj Koszyk'),
	(26, 'pl', 'TEXT_ADD_TO_MAIN_BASKET', NULL, 'Dodaj do głównego koszyka'),
	(27, 'pl', 'TEXT_ORDERING_BASKET', NULL, 'Zmawianie koszyka'),
	(28, 'pl', 'TEXT_ORDER_BASKET', NULL, 'Zamów koszyk'),
	(29, 'pl', 'TEXT_ORDER_NR', NULL, 'Zamówienie nr:'),
	(30, 'pl', 'TEXT_ORDER_DESCRIPTION', NULL, 'Opis zamówienia'),
	(31, 'pl', 'TEXT_BASKET_DESCRIPTION', NULL, 'Opis Koszyka'),
	(32, 'pl', 'TEXT_DATE', NULL, 'Data'),
	(33, 'pl', 'TEXT_ACCOUNT', NULL, 'Konto'),
	(34, 'pl', 'TEXT_SHOW_BASKET', NULL, 'Pokaż Koszyk'),
	(35, 'pl', 'TEXT_PREPARE_ORDER_BASKET', NULL, 'Przygotuj zamówienie z koszyka'),
	(36, 'pl', 'TEXT_USER', NULL, 'Użytkownik'),
	(37, 'pl', 'TEXT_MY_ACCOUNT', NULL, 'Moje konto'),
	(38, 'pl', 'TEXT_CHANGE_PASSWORD', NULL, 'Zmiana hasła'),
	(39, 'pl', 'TEXT_SHOW_DETAILS', NULL, 'Pokaż szczegóły'),
	(40, 'pl', 'TEXT_CHANGE_DETAILS', NULL, 'Zmień szczegóły'),
	(41, 'pl', 'TEXT_ALL_ORDERS', NULL, 'Wszystkie zamówienia'),
	(42, 'pl', 'TEXT_ALL_ACCOUNT_LIST', NULL, 'Lista wszystkich kont'),
	(43, 'pl', 'TEXT_ADMINISTRATORS', NULL, 'Administratorzy'),
	(44, 'pl', 'TEXT_OPERATORS', NULL, 'Operatorzy'),
	(45, 'pl', 'TEXT_USERS', NULL, 'Użytkownicy'),
	(46, 'pl', 'TEXT_ADD_ACCOUNT', NULL, 'Dodaj konto'),
	(47, 'pl', 'TEXT_DISABLED_ACCOUNT', NULL, 'Konto zawieszone'),
	(48, 'pl', 'TEXT_BASKET_HELP_FOR_ICONS', NULL, 'Pomoc dla ikonek koszyków'),
	(49, 'pl', 'TEXT_SWITCH_WORKING_BASKET_TO_THIS_BASKET', NULL, 'Zamieeń koszyk roboczy na ten koszyk'),
	(50, 'pl', 'TEXT_ADD_THIS_BASKET_TO_WORKING_BASKET', NULL, 'Dodaj zawartość tego koszyka do głównego koszyka'),
	(51, 'pl', 'TEXT_BASKET_HELP_FOR_COLORS', NULL, 'Pomoc do kolorów koszyka'),
	(52, 'pl', 'TEXT_CURRENT_WORKING_BASKET', NULL, 'Obecny roboczy koszyka'),
	(53, 'pl', 'TEXT_NORMAL_BASKET', NULL, 'Zwykły koszyk'),
	(54, 'pl', 'TEXT_BASKET_USED_BY_SOMEBODY_ELSE', NULL, 'Koszyk używany przez kogoś innego'),
	(55, 'pl', 'TEXT_BASKET_LIST', NULL, 'Lista koszyków'),
	(56, 'pl', 'TEXT_BASKET_NUMBER', NULL, 'Numer Koszyka'),
	(57, 'pl', 'TEXT_AVAIABLE_ACTIONS', NULL, 'Dostępne akcję'),
	(58, 'pl', 'TEXT_SHOW_ALL_BASKETS', NULL, 'Pokaż wszystkie koszyki'),
	(59, 'pl', 'TEXT_ID', NULL, 'ID'),
	(60, 'pl', 'TEXT_EMAIL', NULL, 'Email'),
	(61, 'pl', 'TEXT_CREATED', NULL, 'Utworzone'),
	(62, 'pl', 'TEXT_LAST_LOGIN', NULL, 'Ostatni Login'),
	(63, 'pl', 'TEXT_ROLES', NULL, 'Role'),
	(64, 'pl', 'TEXT_STATE', NULL, 'Stan'),
	(65, 'pl', 'TEXT_ACCOUNT_LIST', NULL, 'Lista kont'),
	(66, 'pl', 'TEXT_ACCOUNT_PARAMETERS', NULL, 'Parametry konta'),
	(67, 'pl', 'TEXT_ACCOUNT_CHANGE_PASSWORD', NULL, 'Zmień hasło do konta'),
	(68, 'pl', 'TEXT_FIELD', NULL, 'Pole'),
	(69, 'pl', 'TEXT_VALUE', NULL, 'Wartość'),
	(70, 'pl', 'TEXT_THIS_ACCOUNT_DATA', NULL, 'Danę konta'),
	(71, 'pl', 'TEXT_ORDERS', NULL, 'Zamówienia'),
	(72, 'pl', 'TEXT_ORDER_STATISTIC', NULL, 'Statystyki zamówienia'),
	(73, 'pl', 'TEXT_ORDER_DATE', NULL, 'Data złożenia zamówienia'),
	(74, 'pl', 'TEXT_ORDER_UPDATE', NULL, 'Data aktualizacji zamówienia'),
	(75, 'pl', 'TEXT_ORDER_STATE', NULL, 'Stan zamówienia'),
	(76, 'pl', 'TEXT_ORDERS_LIST', NULL, 'Lista zamówień'),
	(77, 'pl', 'TEXT_ORDER_DETAILS', NULL, 'Szczegóły zamówienia'),
	(78, 'pl', 'TEXT_SHOW_ORDER_DETAILS', NULL, 'Pokaż szczegóły zamówienia'),
	(79, 'pl', 'TEXT_ORDERS_DETAILS', NULL, 'Szczegóły zamówienia'),
	(80, 'pl', 'TEXT_ORDER_PRODUCTS', NULL, 'Produkty w zamówieniu'),
	(81, 'pl', 'TEXT_UPDATE', NULL, 'Aktualizuj'),
	(82, 'pl', 'TEXT_OLD_PASSWORD', NULL, 'Stare hasło'),
	(83, 'pl', 'TEXT_NEW_PASSWORD', NULL, 'Nowe hasło'),
	(84, 'pl', 'TEXT_REPEAT_NEW_PASSWORD', NULL, 'Powtórzone nowe hasło'),
	(85, 'pl', 'TEXT_QUANTITY_IN_STOCK', NULL, 'Liczba sztuk w zapasie'),
	(86, 'pl', 'TEXT_QUANTITY_IN_WAREHAUSE', NULL, 'Stan magazynu');
/*!40000 ALTER TABLE `core_translation` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
