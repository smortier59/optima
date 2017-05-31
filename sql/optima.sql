
ALTER TABLE `consommable_imprimante` ADD `couleur_consommable` ENUM('noir','cyan','magenta','jaune') NULL AFTER `ref_imprimante`;

ALTER TABLE `alerte_imprimante` Change `id_alerte_imprimante` `id_print_alerte` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
RENAME TABLE `alerte_imprimante` TO `print_alerte`;

ALTER TABLE `consommable_imprimante` Change `id_consommable_imprimante` `id_print_consommable` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT
CHANGE `ref_imprimante` `ref_stock` varchar(32)  NOT NULL
CHANGE `couleur_consommable` `couleur` enum('noir', 'cyan', 'magenta', 'jaune')  NULL;

RENAME TABLE `consommable_imprimante` TO `print_consommable`;

ALTER TABLE `etat_imprimante` Change `id_etat_imprimante` `id_print_etat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
RENAME TABLE `etat_imprimante` TO `print_etat`;

ALTER TABLE `etat_consommable_imprimante` 
Change `id_etat_consommable_imprimante` `id_print_etat_consommable` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT
Change `id_consommable_imprimante` `id_print_consommable` mediumint(8) UNSIGNED NOT NULL;

RENAME TABLE `etat_consommable_imprimante` TO `print_etat_consommable`;


ALTER TABLE `extranet_v3_absystech`.`print_consommable` DROP INDEX `consommable_imprimante_ibfk_1`, ADD INDEX `consommable_imprimante_ibfk_1` (`ref_stock`) USING BTREE;

ALTER TABLE `extranet_v3_absystech`.`print_etat_consommable` DROP INDEX `id_consommable`, ADD INDEX `id_consommable` (`id_print_consommable`) USING BTREE;
ALTER TABLE `print_etat_consommable` ADD FOREIGN KEY (`id_print_consommable`) REFERENCES `print_consommable`(`id_print_consommable`) ON DELETE CASCADE ON UPDATE CASCADE;
