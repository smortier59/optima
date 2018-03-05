
ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('reception_demande','reception_pj','preparation_commande','refus_dossier','expedition_en_cours','colis_recu','valide_administratif','comite_cleodis_valide','comite_cleodis_refuse','refus_administratif','autre','envoi_mail_relance') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


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