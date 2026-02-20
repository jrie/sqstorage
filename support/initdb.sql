SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE IF NOT EXISTS `customFields` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` varchar(64) NOT NULL,
  `dataType` int(10) UNSIGNED NOT NULL,
  `default` varchar(64) DEFAULT NULL,
  `defaultVisible` tinyint(1) NOT NULL DEFAULT 0,
  `visibleIn` varchar(1024) DEFAULT NULL,
  `fieldValues` varchar(1280) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `database_rev` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dbrev` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dbr` (`dbrev`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `fieldData` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fieldId` bigint(20) UNSIGNED NOT NULL,
  `itemId` bigint(20) UNSIGNED NOT NULL,
  `intNeg` bigint(20) DEFAULT NULL,
  `intPos` bigint(20) UNSIGNED DEFAULT NULL,
  `intNegPos` bigint(20) DEFAULT NULL,
  `floatNeg` double DEFAULT NULL,
  `floatPos` double UNSIGNED DEFAULT NULL,
  `string` varchar(256) DEFAULT NULL,
  `selection` varchar(1280) DEFAULT NULL,
  `mselection` varchar(1280) DEFAULT NULL,
  `qrcode` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `headCategories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL,
  `amount` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `images` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `itemId` bigint(20) UNSIGNED NOT NULL,
  `sizeX` int(11) NOT NULL,
  `sizeY` int(11) NOT NULL,
  `thumb` mediumblob NOT NULL,
  `imageData` mediumblob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` varchar(64) NOT NULL,
  `comment` tinytext DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `serialnumber` varchar(64) DEFAULT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `headcategory` bigint(20) UNSIGNED NOT NULL,
  `subcategories` text DEFAULT NULL,
  `storageid` bigint(20) UNSIGNED DEFAULT NULL,
  `coverimage` bigint(20) UNSIGNED DEFAULT NULL,
  `checkedin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `namespace` varchar(64) NOT NULL,
  `jsondoc` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `namespace` (`namespace`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `storages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` varchar(64) NOT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `subCategories` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` char(128) NOT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `headcategory` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subcategory` (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `usergroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usergroups` (`id`, `name`, `date`) VALUES
(1, 'Administrator', '2022-05-29 12:53:44'),
(2, 'Gast', '2022-05-29 12:53:44'),
(3, 'Benutzer', '2022-05-29 12:53:44');

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `mailaddress` varchar(254) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `api_access` int(1) NOT NULL DEFAULT 1,
  `failcount` int(11) NOT NULL DEFAULT 0,
  `lastfail` int(11) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) UNSIGNED NOT NULL,
  `usergroupid` bigint(20) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `users_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `token` varchar(255) NOT NULL,
  `valid_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
