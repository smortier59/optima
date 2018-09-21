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

#Export comptable
ALTER TABLE `bon_de_commande`ADD `export_cegid` DATETIME NULL DEFAULT NULL, ADD `export_servantissimmo` DATETIME NULL DEFAULT NULL;
ALTER TABLE `facture_fournisseur` ADD `deja_exporte_cegid` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `deja_exporte_achat`;


#ATOL
ALTER TABLE `pack_produit`
CHANGE `type_offre` `type_offre`
ENUM('multimedia','atol','midas','bv','moa','domino','dafy','gifar','heytens','glastint','osilog-axa','atol-table-vente','atol-impression','atol-digital')
CHARACTER
SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- Pour BTWIN
ALTER TABLE `produit` CHANGE `loyer` `loyer` FLOAT(6,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `loyer1` `loyer1` FLOAT(6,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `loyer2` `loyer2` FLOAT(6,3) NULL DEFAULT NULL;

ALTER TABLE `societe` ADD `date_blocage` TIMESTAMP NULL COMMENT 'Date de fin du blocage d\'un compte particulier (BtoB) pour les applications utilisant le tunnel de souscription (btwin, ...)' AFTER `sms_tentative`;
