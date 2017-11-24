INSERT INTO `campagne` (`id_campagne`, `campagne`) VALUES (NULL, 'OB RESEAUX');







ALTER TABLE `mandat` CHANGE `nature` `nature` ENUM('normal','cedre','orange_bleue') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'normal';
ALTER TABLE `mandat_ligne` CHANGE `mandat_type` `mandat_type` ENUM('btob','btoc','cedre','orange_bleue') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


UPDATE `mandat_ligne` SET `mandat_type` = 'orange_bleue' WHERE `mandat_ligne`.`id_mandat_ligne` = 6354;
UPDATE `mandat_ligne` SET `mandat_type` = 'orange_bleue' WHERE `mandat_ligne`.`id_mandat_ligne` = 6355;
UPDATE `mandat_ligne` SET `mandat_type` = 'orange_bleue' WHERE `mandat_ligne`.`id_mandat_ligne` = 6356;
UPDATE `mandat_ligne` SET `mandat_type` = 'orange_bleue' WHERE `mandat_ligne`.`id_mandat_ligne` = 6357;
UPDATE `mandat_ligne` SET `mandat_type` = 'orange_bleue' WHERE `mandat_ligne`.`id_mandat_ligne` = 6358;
UPDATE `mandat_ligne` SET `mandat_type` = 'orange_bleue' WHERE `mandat_ligne`.`id_mandat_ligne` = 6359;
UPDATE `mandat_ligne` SET `mandat_type` = 'orange_bleue' WHERE `mandat_ligne`.`id_mandat_ligne` = 6360;