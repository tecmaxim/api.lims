-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versi�n del servidor:         5.6.21 - MySQL Community Server (GPL)
-- SO del servidor:              Win32
-- HeidiSQL Versi�n:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura de base de datos para dow.crm
CREATE DATABASE IF NOT EXISTS `dow.crm` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `dow.crm`;


-- Volcando estructura para tabla dow.crm.auth_assignment
CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `FkAuthAssigment_UserId` (`user_id`),
  CONSTRAINT `FkAuthAssigment_UserId` FOREIGN KEY (`user_id`) REFERENCES `user` (`UserId`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla dow.crm.auth_assignment: ~0 rows (aproximadamente)
DELETE FROM `auth_assignment`;
/*!40000 ALTER TABLE `auth_assignment` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_assignment` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.auth_item
CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla dow.crm.auth_item: ~0 rows (aproximadamente)
DELETE FROM `auth_item`;
/*!40000 ALTER TABLE `auth_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_item` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.auth_item_child
CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla dow.crm.auth_item_child: ~0 rows (aproximadamente)
DELETE FROM `auth_item_child`;
/*!40000 ALTER TABLE `auth_item_child` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_item_child` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.auth_rule
CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla dow.crm.auth_rule: ~0 rows (aproximadamente)
DELETE FROM `auth_rule`;
/*!40000 ALTER TABLE `auth_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_rule` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.category
CREATE TABLE IF NOT EXISTS `category` (
  `CategoryId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `IsActive` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`CategoryId`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla dow.crm.category: ~0 rows (aproximadamente)
DELETE FROM `category`;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
/*!40000 ALTER TABLE `category` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.migration
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla dow.crm.migration: ~2 rows (aproximadamente)
DELETE FROM `migration`;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` (`version`, `apply_time`) VALUES
	('m000000_000000_base', 1420727557),
	('m140506_102106_rbac_init', 1420727975);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.performance
CREATE TABLE IF NOT EXISTS `performance` (
  `PerformanceId` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryId` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `IsActive` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`PerformanceId`),
  UNIQUE KEY `Name` (`Name`),
  KEY `FkPerformance_CategoryId` (`CategoryId`),
  CONSTRAINT `FkPerformance_CategoryId` FOREIGN KEY (`CategoryId`) REFERENCES `category` (`CategoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla dow.crm.performance: ~0 rows (aproximadamente)
DELETE FROM `performance`;
/*!40000 ALTER TABLE `performance` DISABLE KEYS */;
/*!40000 ALTER TABLE `performance` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.product
CREATE TABLE IF NOT EXISTS `product` (
  `ProductId` int(11) NOT NULL AUTO_INCREMENT,
  `PerformanceId` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `IsActive` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`ProductId`),
  CONSTRAINT `FkProduct_PerformanceId` FOREIGN KEY (`ProductId`) REFERENCES `performance` (`PerformanceId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Volcando datos para la tabla dow.crm.product: ~0 rows (aproximadamente)
DELETE FROM `product`;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
/*!40000 ALTER TABLE `product` ENABLE KEYS */;


-- Volcando estructura para tabla dow.crm.user
CREATE TABLE IF NOT EXISTS `user` (
  `UserId` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `AuthKey` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `PasswordHash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `PasswordResetToken` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CreatedAt` datetime NOT NULL,
  `UpdatedAt` datetime NOT NULL,
  `IsActive` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`UserId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla dow.crm.user: ~5 rows (aproximadamente)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`UserId`, `Username`, `AuthKey`, `PasswordHash`, `PasswordResetToken`, `Email`, `CreatedAt`, `UpdatedAt`, `IsActive`) VALUES
	(1, 'stc1', 'DNW-8zIw8U89uDlfX2-qrb9-3o2Bw8GJ', '$2y$13$7knV2.gck0q67xu6T/MC4.otG.7vdB/V9D8KLhQnNujFoyHQtVCzW', NULL, 'lgomez@getcs.com', '0000-00-00 00:00:00', '0000-00-00 00:00:00', b'1'),
	(2, 'stc2', 'yoqbpOqVKR-TBUrnETvt4gqpai-V3h7h', '$2y$13$TitKFv3R3hYWrYDvdj2gfOHBmHHrxtyC4wunHk.8bAJE21OgyrIZW', NULL, 'w@a.com', '0000-00-00 00:00:00', '0000-00-00 00:00:00', b'1'),
	(3, 'stc3', 'yv99A6Z5hZYrQtzA9CUs9LZERtf4uXB6', '$2y$13$cYEc8AyoEFqwUBaqpza.ledPqKb5nISH91HsjBFMtBlHBAxy7nuK2', NULL, 'a@a.com', '2015-01-06 13:00:46', '2015-01-06 13:00:46', b'1'),
	(4, 'stc4', 'PRk5BAWwnKhYILuP9NN8IVnupLhWLB_i', '$2y$13$uUdVzSrMzpJPDgTOwaO03.Y55Q2GPkX926KpDMxORO/IWAiFgGF86', NULL, 'a@a.com.e', '2015-01-06 13:01:04', '2015-01-06 13:01:04', b'1'),
	(5, 'stc5', '1_69gthYDbPBZXDBn9LHFFZ6QzKNjKSb', '$2y$13$7knV2.gck0q67xu6T/MC4.otG.7vdB/V9D8KLhQnNujFoyHQtVCzW', NULL, 'lgomez@getcs.com.tres', '2015-01-06 15:04:22', '2015-01-06 15:04:22', b'1');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
