#DEVIS CLEODIS V2
ALTER TABLE `devis_ligne` ADD `options` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `commentaire`;
ALTER TABLE `devis` ADD `commentaire_offre_partenaire` TEXT NULL DEFAULT NULL AFTER `raison_refus`;
ALTER TABLE `devis` ADD `offre_partenaire` varchar(80) NULL DEFAULT NULL AFTER `raison_refus`;

-- SMALLINT5 TO MEDIUMINT8
ALTER TABLE `pack_produit` CHANGE `id_pack_produit` `id_pack_produit` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;
-- Nettoyage lignes orphelines
DELETE FROM `pack_produit_ligne` WHERE id_pack_produit NOT IN (SELECT id_pack_produit FROM pack_produit);
-- Contraintes
ALTER TABLE `pack_produit_ligne` ADD FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit`(`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `pack_produit_ligne` ADD FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE;


-- Boulanger PRO
ALTER TABLE `produit` ADD `new_prix` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL AFTER `prix_achat`;

ALTER TABLE `produit`
	ADD `taxe_ecotaxe` DECIMAL(10.2) UNSIGNED NULL DEFAULT NULL AFTER `prix_achat`,
	ADD `taxe_ecomob` DECIMAL(10.2) UNSIGNED NULL DEFAULT NULL AFTER `taxe_ecotaxe`;


ALTER TABLE `affaire` ADD `ref_externe` VARCHAR(50) NULL DEFAULT NULL AFTER `ref` COMMENT 'Reference externe provenant d\'un partenaire';
ALTER TABLE `affaire` ADD `etat_cmd_externe` ENUM('attente','valide') NULL AFTER `ref_externe`;