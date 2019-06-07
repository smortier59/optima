UPDATE `licence` SET `id_commande_ligne` = NULL;

CREATE TABLE `export_facture` ( `id_export_facture` mediumint(8) UNSIGNED NOT NULL,
								 `id_facture` mediumint(8) UNSIGNED NOT NULL,
								 `date_export` timestamp NOT NULL DEFAULT current_timestamp(),
								 `fichier_export` enum('flux_vente') NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `export_facture` ADD PRIMARY KEY (`id_export_facture`), ADD KEY `id_facture` (`id_facture`);
ALTER TABLE `export_facture` MODIFY `id_export_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `facture_non_parvenue` CHANGE `ref` `ref` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `facture` ADD `ref_magasin` VARCHAR(32) NULL AFTER `ref_externe`;


CREATE TABLE facture_magasin ( `id_facture_magasin` MEDIUMINT NOT NULL AUTO_INCREMENT , `ref_facture` VARCHAR(25) NOT NULL , `id_affaire` MEDIUMINT NOT NULL , PRIMARY KEY (`id_facture_magasin`), INDEX (`id_affaire`)) ENGINE = InnoDB;
ALTER TABLE `facture_magasin` CHANGE `id_affaire` `id_affaire` MEDIUMINT(9) UNSIGNED NOT NULL;
ALTER TABLE `facture_magasin` CHANGE `id_facture_magasin` `id_facture_magasin` MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `facture_magasin` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `facture_magasin` ADD `etat` ENUM('non_recu','paye') NOT NULL DEFAULT 'non_recu' AFTER `id_affaire`;

ALTER TABLE `magasin` ADD `statut` ENUM('ouvert','ferme') NOT NULL DEFAULT 'ouvert' AFTER `id_societe`;



ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('reception_demande','reception_pj','preparation_commande','refus_dossier','expedition_en_cours','colis_recu','valide_administratif','comite_cleodis_valide','comite_cleodis_refuse','refus_administratif','autre','envoi_mail_relance','signature_document','signature_document_ok','finalisation_souscription') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `affaire` CHANGE `mail_signature` `mail_signature` ENUM('oui','non') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'non' COMMENT 'Mail signataire',
					  CHANGE `date_signature` `date_signature` DATETIME NULL DEFAULT NULL COMMENT 'Date de la demande de signature',
					  CHANGE `tel_signature` `tel_signature` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Téléphone signataire';