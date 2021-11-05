ALTER TABLE `affaire` ADD `id_commercial` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_apporteur`, ADD INDEX (`id_commercial`);
ALTER TABLE `affaire` ADD FOREIGN KEY (`id_commercial`) REFERENCES `user`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `affaire` ADD `renouveller` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `id_type_affaire`