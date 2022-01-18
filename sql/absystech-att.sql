ALTER TABLE `compte_absystech` ADD `etat` ENUM('actif','inactif') NOT NULL DEFAULT 'actif' AFTER `type`;


# Temps estimé
ALTER TABLE `optima_absystech`.`hotline` CHANGE COLUMN `estimation` `estimation` VARCHAR(50) NULL DEFAULT NULL ;
ALTER TABLE `optima_atoutcoms`.`hotline` CHANGE COLUMN `estimation` `estimation` VARCHAR(50) NULL DEFAULT NULL ;
ALTER TABLE `optima_att`.`hotline` CHANGE COLUMN `estimation` `estimation` VARCHAR(50) NULL DEFAULT NULL ;
ALTER TABLE `optima_nco`.`hotline` CHANGE COLUMN `estimation` `estimation` VARCHAR(50) NULL DEFAULT NULL ;