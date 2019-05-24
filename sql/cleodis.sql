ALTER TABLE `pack_produit_ligne` ADD `principal` ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Défini le produit comme étant le produit principal du pack.' AFTER `ordre`;
ALTER TABLE `produit` ADD `livreur` VARCHAR(255) NULL DEFAULT NULL AFTER `id_document_contrat`, ADD `frais_livraison` FLOAT(6,3) NULL DEFAULT NULL AFTER `livreur`;
ALTER TABLE `produit` CHANGE `livreur` `livreur` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `optima_cleodis`.`produit` ADD INDEX (`livreur`);
ALTER TABLE `produit` ADD FOREIGN KEY (`livreur`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `pack_produit_ligne` ADD FOREIGN KEY (`id_fournisseur`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `produit` DROP FOREIGN KEY `produit_ibfk_41`; ALTER TABLE `produit` ADD CONSTRAINT `produit_ibfk_41` FOREIGN KEY (`livreur`) REFERENCES `fabriquant`(`id_fabriquant`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `produit` ADD `ref_garantie` VARCHAR(15) NULL DEFAULT NULL AFTER `frais_livraison`;
ALTER TABLE `pack_produit_ligne` ADD FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `pack_produit` CHANGE nom VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `produit` ADD `url_image` VARCHAR(500) NULL AFTER `id_document_contrat`;