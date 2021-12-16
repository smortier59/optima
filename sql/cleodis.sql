
ALTER TABLE `loyer` CHANGE `type` `type` ENUM('engagement','liberatoire','prolongation') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'engagement';