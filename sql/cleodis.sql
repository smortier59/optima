#17391 - Gestion des signataires et des modifications de contacts
ALTER TABLE `affaire` ADD `tel_signature` VARCHAR(20) NULL DEFAULT NULL AFTER `commentaire_facture3`,
					  ADD `mail_signataire` VARCHAR(100) NULL DEFAULT NULL AFTER `tel_signature`,
					  ADD `date_signature` DATETIME NULL DEFAULT NULL AFTER `mail_signature`,
					  ADD `signataire` VARCHAR(150) NULL DEFAULT NULL AFTER `date_signature`;


ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('reception_demande','reception_pj','preparation_commande','refus_dossier','expedition_en_cours','colis_recu','valide_administratif','comite_cleodis_valide','comite_cleodis_refuse','refus_administratif','autre','envoi_mail_relance') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

#17681 - Gestion des particuliers - adaptations Entité - Contacts - Rôles
INSERT INTO `famille` (`id_famille`, `famille`, `cle_externe`) VALUES (NULL, 'Foyer', NULL);
ALTER TABLE `societe` ADD `particulier_civilite` ENUM('M','Mme','Mlle') NULL DEFAULT NULL AFTER `lead`,
					  ADD `particulier_nom` VARCHAR(50) NULL DEFAULT NULL AFTER `particulier_civilite`,
					  ADD `particulier_prenom` VARCHAR(50) NULL DEFAULT NULL AFTER `particulier_nom`,
					  ADD `particulier_portable` VARCHAR(15) NULL DEFAULT NULL AFTER `particulier_prenom`,
					  ADD `particulier_fixe` VARCHAR(20) NULL DEFAULT NULL AFTER `particulier_portable`,
					  ADD `particulier_fax` VARCHAR(20) NULL DEFAULT NULL AFTER `particulier_fixe`,
					  ADD `particulier_email` VARCHAR(255) NULL DEFAULT NULL AFTER `particulier_fax`
					  ADD `num_carte_fidelite` VARCHAR(50) NULL DEFAULT NULL AFTER `particulier_portable`,
					  ADD `dernier_magasin` VARCHAR(50) NULL DEFAULT NULL AFTER `num_carte_fidelite`,
					  ADD `optin_offre_commerciales` ENUM('oui','non') NULL DEFAULT NULL AFTER `dernier_magasin`,
					  ADD `optin_offre_commerciale_partenaire` ENUM('oui','non') NULL DEFAULT NULL AFTER `optin_offre_commerciales`;

ALTER TABLE `contact` ADD `tel_perso` VARCHAR(20) NULL DEFAULT NULL AFTER `email`,
					  ADD `email_perso` VARCHAR(255) NULL DEFAULT NULL AFTER `tel_perso`,
					  ADD `gsm_perso` VARCHAR(20) NULL DEFAULT NULL AFTER `email_perso`;



#17759 - Location Evolutive : packs au panier + options dans les packs
ALTER TABLE `produit` CHANGE `type_offre` `type_offre` ENUM('bureautique','informatique','telephonie','multimedia','atol') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `produit` ADD `site_associe` ENUM('cleodis','toshiba') NULL DEFAULT NULL AFTER `description`;
UPDATE `produit` SET `site_associe`='cleodis' WHERE `visible_sur_site`='oui';
ALTER TABLE `pack_produit` ADD `avis_expert` TEXT NULL DEFAULT NULL AFTER `description`;


#DEVIS CLEODIS V2
ALTER TABLE `devis_ligne` ADD `options` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `commentaire`;
ALTER TABLE `devis` ADD `commentaire_offre_partenaire` TEXT NULL DEFAULT NULL AFTER `raison_refus`;
ALTER TABLE `devis` ADD `offre_partenaire` varchar(80) NULL DEFAULT NULL AFTER `raison_refus`;



ALTER TABLE `comite` CHANGE `etat` `etat` ENUM('refuse','favorable_cession','accord_portage_recherche_cession','accord_portage_recherche_cession_groupee','accord_non utilise','accepte','en_attente') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'en_attente';