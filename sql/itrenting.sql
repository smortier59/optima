ALTER TABLE `societe` ADD `province` VARCHAR(200) NULL DEFAULT NULL AFTER `ville`;
ALTER TABLE `societe` ADD `facturation_province` VARCHAR(200) NULL DEFAULT NULL AFTER `facturation_ville`;