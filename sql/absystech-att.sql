ALTER TABLE `echeancier_ligne_periodique` ADD `offset` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `ref`;
ALTER TABLE `echeancier_ligne_ponctuelle` ADD `offset` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `ref`;
ALTER TABLE `echeancier_ligne_periodique` CHANGE `offset` `offset` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '99999';
ALTER TABLE `echeancier_ligne_ponctuelle` CHANGE `offset` `offset` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '99999';
