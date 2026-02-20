SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `customFields` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(64) NOT NULL,
  `dataType` int(10) UNSIGNED NOT NULL,
  `default` varchar(64) DEFAULT NULL,
  `defaultVisible` tinyint(1) NOT NULL DEFAULT 0,
  `visibleIn` varchar(1024) DEFAULT NULL,
  `fieldValues` varchar(1280) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `database_rev` (
  `id` int(11) NOT NULL,
  `dbrev` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `database_rev` (`id`, `dbrev`) VALUES
(1, 10);

CREATE TABLE `fieldData` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
  `qrcode` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `headCategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` char(128) NOT NULL,
  `amount` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `itemId` bigint(20) UNSIGNED NOT NULL,
  `sizeX` int(11) NOT NULL,
  `sizeY` int(11) NOT NULL,
  `thumb` mediumblob NOT NULL,
  `imageData` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(64) NOT NULL,
  `comment` tinytext DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `serialnumber` varchar(64) DEFAULT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `headcategory` bigint(20) UNSIGNED NOT NULL,
  `subcategories` text DEFAULT NULL,
  `storageid` bigint(20) UNSIGNED DEFAULT NULL,
  `coverimage` bigint(20) UNSIGNED DEFAULT NULL,
  `checkedin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `namespace` varchar(64) NOT NULL,
  `jsondoc` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` (`id`, `namespace`, `jsondoc`) VALUES
(1, 'mail', '{}'),
(2, 'updater', '{\"githubuser\":\"jrie\",\"githubrepo\":\"sqstorage\",\"githubbranch\":\"main\"}');

CREATE TABLE `storages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(64) NOT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subCategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` char(128) NOT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `headcategory` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usergroups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usergroups` (`id`, `name`, `date`) VALUES
(1, 'Administrator', '2022-05-29 12:53:44'),
(2, 'Gast', '2022-05-29 12:53:44'),
(3, 'Benutzer', '2022-05-29 12:53:44');

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `mailaddress` varchar(254) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `api_access` int(1) NOT NULL DEFAULT 1,
  `failcount` int(11) NOT NULL DEFAULT 0,
  `lastfail` int(11) NOT NULL DEFAULT 0,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users_groups` (
  `id` int(11) NOT NULL,
  `userid` bigint(20) UNSIGNED NOT NULL,
  `usergroupid` bigint(20) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `userid` bigint(20) NOT NULL,
  `token` varchar(255) NOT NULL,
  `valid_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `customFields`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `database_rev`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dbr` (`dbrev`);

ALTER TABLE `fieldData`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `headCategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `namespace` (`namespace`);

ALTER TABLE `storages`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `subCategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subcategory` (`name`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `usergroups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userid` (`userid`);

ALTER TABLE `users_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`id`);


ALTER TABLE `customFields`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `database_rev`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `fieldData`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `headCategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `storages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `subCategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `users_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
