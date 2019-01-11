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


-- Ajout des loyer et des infos de pack / produits au niveau de l'affaire
ALTER TABLE `devis_ligne` ADD `duree` INT(11) NULL DEFAULT NULL AFTER `options`, 
	ADD `loyer` FLOAT(6,3) NULL DEFAULT NULL AFTER `duree`, 
	ADD `ean` VARCHAR(14) NULL DEFAULT NULL AFTER `loyer`, 
	ADD `id_pack_produit` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `ean`, 
	ADD `id_sous_categorie` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_pack_produit`, 
	ADD INDEX (`id_sous_categorie`), ADD INDEX (`id_pack_produit`);

ALTER TABLE `commande_ligne` ADD `duree` INT(11) NULL DEFAULT NULL, 
	ADD `loyer` FLOAT(6,3) NULL DEFAULT NULL AFTER `duree`, 
	ADD `ean` VARCHAR(14) NULL DEFAULT NULL AFTER `loyer`, 
	ADD `id_pack_produit` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `ean`, 
	ADD `id_sous_categorie` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_pack_produit`, 
	ADD INDEX (`id_sous_categorie`), ADD INDEX (`id_pack_produit`);

ALTER TABLE `devis_ligne` ADD `pack_produit` VARCHAR(255) NULL DEFAULT NULL AFTER `id_sous_categorie`, ADD `sous_categorie` VARCHAR(255) NULL DEFAULT NULL AFTER `pack_produit`;
ALTER TABLE `commande_ligne` ADD `pack_produit` VARCHAR(255) NULL DEFAULT NULL AFTER `id_sous_categorie`, ADD `sous_categorie` VARCHAR(255) NULL DEFAULT NULL AFTER `pack_produit`;

ALTER TABLE `devis_ligne` ADD `id_categorie` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `sous_categorie`, 
	ADD `categorie` VARCHAR(64) NULL DEFAULT NULL AFTER `id_categorie`, 
	ADD `commentaire_produit` VARCHAR(512) NULL DEFAULT NULL AFTER `categorie`, 
	ADD `visible_sur_site` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `commentaire_produit`, 
	ADD `visible_pdf` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `visible_sur_site`, 
	ADD `ordre` INT NOT NULL DEFAULT '1' AFTER `visible_pdf`, 
	ADD INDEX (`id_categorie`);

ALTER TABLE `commande_ligne` ADD `id_categorie` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `sous_categorie`, 
	ADD `categorie` VARCHAR(64) NULL DEFAULT NULL AFTER `id_categorie`, 
	ADD `commentaire_produit` VARCHAR(512) NULL DEFAULT NULL AFTER `categorie`, 
	ADD `visible_sur_site` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `commentaire_produit`, 
	ADD `visible_pdf` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `visible_sur_site`, 
	ADD `ordre` INT NOT NULL DEFAULT '1' AFTER `visible_pdf`, 
	ADD INDEX (`id_categorie`);

ALTER TABLE `affaire` ADD `snapshot_pack_produit` TEXT NULL DEFAULT NULL AFTER `hash_panier`;