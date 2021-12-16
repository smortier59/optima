
ALTER TABLE `optima_cleodis`.`affaire` ADD `id_commercial` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_apporteur`, ADD INDEX (`id_commercial`);
ALTER TABLE `optima_cleodis`.`affaire` ADD FOREIGN KEY (`id_commercial`) REFERENCES `user`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `optima_cleodisbe`.`affaire` ADD `id_commercial` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_apporteur`, ADD INDEX (`id_commercial`);
ALTER TABLE `optima_cleodisbe`.`affaire` ADD FOREIGN KEY (`id_commercial`) REFERENCES `user`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `optima_assets`.`affaire` ADD `id_commercial` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_apporteur`, ADD INDEX (`id_commercial`);
ALTER TABLE `optima_assets`.`affaire` ADD FOREIGN KEY (`id_commercial`) REFERENCES `user`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;





ALTER TABLE `loyer` CHANGE `type` `type` ENUM('engagement','liberatoire','prolongation') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'engagement';





