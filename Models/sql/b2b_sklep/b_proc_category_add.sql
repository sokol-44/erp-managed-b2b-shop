# --------------------------------------------------------
# Host:                         localhost
# Server version:               5.1.40-community-log
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-07 15:16:47
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for procedure b2b_sklep.b_proc_category_add
DROP PROCEDURE IF EXISTS `b_proc_category_add`;
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `b_proc_category_add`(`id_category_in` INT, `id_category_parent_in` INT, `sort_order_in` INT, `root_number_in` TINYINT, `name_in` TEXT, `description_in` TEXT)
BEGIN
	INSERT INTO shop_category (`id_category`, `id_category_parent`, `sort_order`, `root_number`, `name`, `description`)
	VALUES (id_category_in, id_category_parent_in, sort_order_in, root_number_in, name_in, description_in)
	ON DUPLICATE KEY UPDATE
	`id_category` = name_in, `id_category_parent` = id_category_in, `sort_order` = sort_order_in, `root_number` = root_number_in,
	`name` = name_in, `description` = description_in;
	SET @status_proc = 'SUCCESS';
END//
DELIMITER ;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
