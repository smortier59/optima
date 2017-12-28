#17390 - Ajout message sur la facture
ALTER TABLE `affaire` ADD `commentaire_facture` TEXT NULL COMMENT 'Commentaire qui sera afficher sur les factures clients' AFTER `id_partenaire`;



#DEVIS CLEODIS V2
ALTER TABLE `devis_ligne` ADD `options` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `commentaire`;
ALTER TABLE `devis` ADD `commentaire_offre_partenaire` TEXT NULL DEFAULT NULL AFTER `raison_refus`;
ALTER TABLE `devis` ADD `offre_partenaire` varchar(80) NULL DEFAULT NULL AFTER `raison_refus`;



ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `affaire` ADD `id_partenaire` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `date_verification`;
ALTER TABLE `affaire` ADD FOREIGN KEY (`id_partenaire`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE CASCADE;