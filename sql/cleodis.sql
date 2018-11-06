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



#Ajout des CG/CP
CREATE TABLE `document_contrat` (
  `id_document_contrat` mediumint(8) UNSIGNED NOT NULL,
  `document_contrat` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `document_contrat` ADD PRIMARY KEY (`id_document_contrat`);
ALTER TABLE `document_contrat` MODIFY `id_document_contrat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `document_contrat` ADD `type_signature` ENUM('commune_avec_contrat','hors_contrat') NOT NULL DEFAULT 'commune_avec_contrat',
							   ADD `etat` ENUM('actif','inactif') NOT NULL DEFAULT 'actif' AFTER `type_signature`;

ALTER TABLE `pack_produit` ADD `id_document_contrat` mediumint(8) UNSIGNED DEFAULT NULL;
ALTER TABLE `pack_produit` ADD KEY `id_document_contrat` (`id_document_contrat`);
ALTER TABLE `pack_produit` ADD CONSTRAINT `pack_produit_document` FOREIGN KEY (`id_document_contrat`) REFERENCES `document_contrat` (`id_document_contrat`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `produit` ADD `id_document_contrat` mediumint(8) UNSIGNED DEFAULT NULL;
ALTER TABLE `produit` ADD KEY `id_document_contrat` (`id_document_contrat`);
ALTER TABLE `produit` ADD CONSTRAINT `produit_ibfk_document` FOREIGN KEY (`id_document_contrat`) REFERENCES `document_contrat` (`id_document_contrat`) ON DELETE SET NULL ON UPDATE CASCADE;