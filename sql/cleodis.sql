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


#Multi magasin BTWIN
ALTER TABLE `magasin` ADD `code` VARCHAR(25) NULL DEFAULT NULL AFTER `magasin`;
ALTER TABLE `magasin`
  DROP `entite_lm`,
  DROP `langue`,
  DROP `num_magasin_lm`,
  DROP `afficher`,
  DROP `email`,
  DROP `password`;
ALTER TABLE `magasin` CHANGE `site_associe` `site_associe` ENUM('toshiba','btwin') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `magasin` ADD `id_societe` MEDIUMINT UNSIGNED NOT NULL;
ALTER TABLE `magasin` ADD INDEX(`id_societe`);
ALTER TABLE `magasin` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `affaire` ADD `id_magasin` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `pays_facturation`;
ALTER TABLE `affaire` ADD FOREIGN KEY (`id_magasin`) REFERENCES `magasin`(`id_magasin`) ON DELETE RESTRICT ON UPDATE RESTRICT;