# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2010-12-03 15:44:58
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping data for table b2b_sklep.global_admin: ~9 rows (approximately)
DELETE FROM `global_admin`;
/*!40000 ALTER TABLE `global_admin` DISABLE KEYS */;
INSERT INTO `global_admin` (`id_admin`, `login`, `password`, `description`, `email`, `created`, `last_login`, `state`) VALUES
	(1, 'admin', 'd416b11c07591e8110c8e235ace521ce5da74be29229af683b5b059b927402be3c54f9468f75f165:1b9c0e339fd5827ba532f432f74fbe2133bb1efd', 'main admin', 'aaaa@wp.pl', '0000-00-00 00:00:00', NULL, 'ACTIVE'),
	(2, 'admin2', '368ee9b07376d0e28a8d81b2f58d6bf629a053cf48bb89bc8fdf8e28bb2cb0053a2a32ff4ebbb3a8:c3e67f8bfe85a1e147e285f4bca173ecd9dde091', 'saldjl;asjdlasj dlsajldjsaldjsalkjdsaljk-021=-40mo[', 'test@wp.pl', '0000-00-00 00:00:00', NULL, 'ACTIVE'),
	(3, 'ADMIN3', 'ef74892677089a3ff9a945d12d29d525a17b990e87199a196249d9aadf74af04c29793f96c31a859:ac5551796a04d317264069d1f4d89a5858cb0be1', '33333333', '333333333@wp.pl', '0000-00-00 00:00:00', NULL, 'ACTIVE'),
	(4, 'nowy', '81e2abcbd5fca42c7c5e1dcb039960f23629f5f2743ce680a2f5dd2c79019b098d6e6b2950e91ebc:7d11e1516a867f371bd6868718fc8937ed5b2a2e', '1nowy', 'admin', '2010-08-13 10:16:58', NULL, 'ACTIVE'),
	(5, 'nowy', '81e2abcbd5fca42c7c5e1dcb039960f23629f5f2743ce680a2f5dd2c79019b098d6e6b2950e91ebc:7d11e1516a867f371bd6868718fc8937ed5b2a2e', '1nowy', 'admin', '2010-08-13 10:16:58', NULL, 'ACTIVE'),
	(6, 'lll', '2304877a75374a4bb7c29a3e71511080329de0c0173c2c3d29e69b66de40434d7a9bb49f9065fb07:166f38e3ca707090544eace13bb1417ad0d2c98a', 'lll', 'lll', '2010-08-13 14:25:25', NULL, 'ACTIVE'),
	(7, 'lll', '2304877a75374a4bb7c29a3e71511080329de0c0173c2c3d29e69b66de40434d7a9bb49f9065fb07:166f38e3ca707090544eace13bb1417ad0d2c98a', 'lll', 'lll', '2010-08-13 14:25:25', NULL, 'ACTIVE'),
	(8, 'ooo', 'f8eb03220b0e8e0d320dce5fecac64fa93e8fe6ab9602d388ee50f6ad364a264b72ef8eebe17c076:65fa1bd44423a58dd10e25bcd4704f47eca609fc', 'oo', 'oo', '2010-08-13 14:25:59', NULL, 'ACTIVE'),
	(9, 'ooo', 'f8eb03220b0e8e0d320dce5fecac64fa93e8fe6ab9602d388ee50f6ad364a264b72ef8eebe17c076:65fa1bd44423a58dd10e25bcd4704f47eca609fc', 'oo', 'oo', '2010-08-13 14:25:59', NULL, 'ERASED');
/*!40000 ALTER TABLE `global_admin` ENABLE KEYS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
