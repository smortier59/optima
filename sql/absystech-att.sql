ALTER TABLE `affaire` ADD `date_fin` DATETIME NULL DEFAULT NULL COMMENT 'Date prévisionnel de fin de l\'affaire' AFTER `suivi_ec`;
ALTER TABLE `echeancier_ligne_periodique` ADD `offset` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `ref`;
ALTER TABLE `echeancier_ligne_ponctuelle` ADD `offset` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `ref`;
ALTER TABLE `echeancier_ligne_periodique` CHANGE `offset` `offset` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '99999';
ALTER TABLE `echeancier_ligne_ponctuelle` CHANGE `offset` `offset` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '99999';
