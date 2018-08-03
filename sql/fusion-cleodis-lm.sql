###
###	SQL A METTRE SUR LA BDD CLEODIS / CLEODIS BE
###

ALTER TABLE `affaire`
	ADD `date_diag` date   NULL after `id_fille` ,
	ADD `ref_slimpay` varchar(50)  COLLATE latin1_swedish_ci NULL after `date_recettage_cablage` ,
	ADD `ref_mandate` varchar(50)  COLLATE latin1_swedish_ci NULL after `ref_slimpay` ,
	ADD `adresse_livraison` text  COLLATE latin1_swedish_ci NOT NULL after `ref_mandate` ,
	ADD `adresse_livraison_2` text  COLLATE latin1_swedish_ci NULL after `adresse_livraison` ,
	ADD `adresse_livraison_3` text  COLLATE latin1_swedish_ci NULL after `adresse_livraison_2` ,
	ADD `cp_adresse_livraison` varchar(5)  COLLATE latin1_swedish_ci NOT NULL after `adresse_livraison_3` ,
	ADD `ville_adresse_livraison` varchar(100)  COLLATE latin1_swedish_ci NOT NULL after `cp_adresse_livraison` ,
	ADD `pays_livraison` char(2)  COLLATE utf8_general_ci NOT NULL DEFAULT 'FR' after `ville_adresse_livraison` ,
	ADD `adresse_facturation` text  COLLATE latin1_swedish_ci NOT NULL after `pays_livraison` ,
	ADD `adresse_facturation_2` text  COLLATE latin1_swedish_ci NULL after `adresse_facturation` ,
	ADD `adresse_facturation_3` text  COLLATE latin1_swedish_ci NULL after `adresse_facturation_2` ,
	ADD `cp_adresse_facturation` varchar(5)  COLLATE latin1_swedish_ci NOT NULL after `adresse_facturation_3` ,
	ADD `ville_adresse_facturation` varchar(100)  COLLATE latin1_swedish_ci NOT NULL after `cp_adresse_facturation` ,
	ADD `pays_facturation` char(2)  COLLATE utf8_general_ci NOT NULL DEFAULT 'FR' after `ville_adresse_facturation` ,
	ADD `status_prestataire` enum('commande_pris_en_compte','validation_commande','annulation_commande','installee','arret_service')  COLLATE latin1_swedish_ci NULL COMMENT 'status cotÃ© prestataire' after `pays_facturation` ,
	ADD `id_magasin` mediumint(8) unsigned   NULL after `status_prestataire` ,
	ADD `id_collaborateur` int(10) unsigned   NULL after `id_magasin` ,
	ADD `num_bdc_lm` varchar(20)  COLLATE latin1_swedish_ci NULL after `id_collaborateur` ,
	ADD `poseur` varchar(250)  COLLATE latin1_swedish_ci NULL after `num_bdc_lm` ,
	ADD `poseur_aggree` enum('oui','non')  COLLATE latin1_swedish_ci NULL after `poseur` ,
	ADD `type_souscription` enum('web','magasin')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'web' after `poseur_aggree` ,
	CHANGE `type_affaire` `type_affaire` enum('normal','2SI','LP','SP','LS')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'LS' after `type_souscription` ,
	ADD `ref_commande_lm` varchar(8)  COLLATE latin1_swedish_ci NULL after `mail_signataire` ,
	ADD `permalien` varchar(50)  COLLATE latin1_swedish_ci NULL after `ref_commande_lm` ,
	ADD `expire_permalien` timestamp   NULL after `permalien` ,
	ADD `id_pack_produit` mediumint(8) unsigned   NULL after `expire_permalien` ,
	ADD KEY `id_collaborateur`(`id_collaborateur`) ,
	ADD KEY `id_magasin`(`id_magasin`) ,
	ADD KEY `id_pack_produit`(`id_pack_produit`) ,
	ADD KEY `pays_facturation`(`pays_facturation`) ,
	ADD KEY `pays_livraison`(`pays_livraison`);
ALTER TABLE `affaire`
	ADD CONSTRAINT `affaire_ibfk_5` FOREIGN KEY (`pays_livraison`) REFERENCES `pays` (`id_pays`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `affaire_ibfk_6` FOREIGN KEY (`pays_facturation`) REFERENCES `pays` (`id_pays`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `affaire_ibfk_7` FOREIGN KEY (`id_collaborateur`) REFERENCES `collaborateur` (`id_collaborateur`) ON DELETE SET NULL ON UPDATE CASCADE ;

ALTER TABLE `affaire_etat` CHANGE `etat` `etat` enum('commande','commande_pris_en_compte','validation_commande','livraison_initie','livraison_en_cours','debut_contrat','fin_engagement','annulation_commande','installee','diagnostic_effectue','reception_demande','reception_pj','preparation_commande','refus_dossier','expedition_en_cours','colis_recu','valide_administratif','comite_cleodis_valide','comite_cleodis_refuse','refus_administratif','autre','envoi_mail_relance')  COLLATE utf8_general_ci NOT NULL after `date` ;


ALTER TABLE `bon_de_commande`
	ADD `num_bdc` varchar(50)  COLLATE utf8_general_ci NULL after `ref` ,
	ADD `id_magasin` mediumint(8) unsigned   NULL after `export_servantissimmo` ,
	ADD KEY `id_magasin`(`id_magasin`) ;
ALTER TABLE `bon_de_commande` ADD CONSTRAINT `bon_de_commande_ibfk_7` FOREIGN KEY (`id_magasin`) REFERENCES `magasin` (`id_magasin`) ;


ALTER TABLE `bon_de_commande_ligne`
	CHANGE `prix` `prix` decimal(10,4) unsigned   NULL COMMENT 'Prix HT' after `quantite` ,
	ADD `prix_ttc` decimal(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Prix TTC' after `prix`;

CREATE TABLE `cgl_article`(
	`id_cgl_article` mediumint(8) unsigned NOT NULL  auto_increment ,
	`numero` int(10) unsigned NOT NULL  ,
	`titre` varchar(250) COLLATE utf8mb4_general_ci NOT NULL  ,
	PRIMARY KEY (`id_cgl_article`)
);

CREATE TABLE `cgl_texte`(
	`id_cgl_texte` mediumint(11) unsigned NOT NULL  auto_increment ,
	`id_cgl_article` mediumint(8) unsigned NOT NULL  ,
	`numero` int(10) unsigned NOT NULL  ,
	`texte` text COLLATE utf8mb4_general_ci NOT NULL  ,
	PRIMARY KEY (`id_cgl_texte`) ,
	KEY `id_cgl_article`(`id_cgl_article`) ,
	CONSTRAINT `cgl_texte_ibfk_1`
	FOREIGN KEY (`id_cgl_article`) REFERENCES `cgl_article` (`id_cgl_article`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `collaborateur`(
	`id_collaborateur` int(10) unsigned NOT NULL  auto_increment ,
	`nom` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
	`prenom` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
	`email` varchar(255) COLLATE utf8_general_ci NOT NULL  ,
	`enable` tinyint(1) NOT NULL  ,
	`id_magasin` mediumint(10) unsigned NOT NULL  ,
	PRIMARY KEY (`id_collaborateur`) ,
	KEY `id_magasin`(`id_magasin`) ,
	CONSTRAINT `collaborateur_ibfk_1`
	FOREIGN KEY (`id_magasin`) REFERENCES `magasin` (`id_magasin`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

ALTER TABLE `comite` ADD `destinataire` varchar(500)  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'jerome.loison@cleodis.com,lma@cleodis.com,herve.anvroin@leroymerlin.fr' after `notifie_utilisateur` ;

ALTER TABLE `commande`
	CHANGE `etat` `etat` enum('non_loyer','mis_loyer','prolongation','AR','arreter','vente','restitution','mis_loyer_contentieux','prolongation_contentieux','restitution_contentieux','pending','abandon')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'non_loyer' after `id_devis` ,
	ADD `etat_service1` enum('client_order_received','user_created','site_created','service_enabled','mail_sent','client_order_confirmed','client_order_installed')  COLLATE latin1_swedish_ci NULL after `id_user` ,
	CHANGE `id_affaire` `id_affaire` mediumint(8) unsigned   NULL after `etat_service1` ,
	ADD `type_contrat` enum('LP','SP','LS')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'LS' after `date_restitution_effective` ;

ALTER TABLE `commande_ligne`
	ADD `prix_achat_ttc` decimal(10,2)   NOT NULL DEFAULT 0.00 after `id_fournisseur` ,
	CHANGE `prix_achat` `prix_achat` decimal(12,4)   NULL COMMENT 'Prix HT' after `prix_achat_ttc` ,
	ADD `charge_fournisseur` enum('oui','non')  COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non' after `commentaire` ,
	ADD `confirmation_arret_service` enum('oui','non')  COLLATE utf8_swedish_ci NULL after `charge_fournisseur` ,
	ADD `date_arret_service` date   NULL after `confirmation_arret_service` ,
	ADD KEY `charge_fournisseur`(`charge_fournisseur`) ;

CREATE TABLE `compte_produit`(
	`id_compte_produit` mediumint(8) unsigned NOT NULL  auto_increment ,
	`libelle` varchar(100) COLLATE utf8mb4_general_ci NOT NULL  ,
	`num_compte` varchar(20) COLLATE utf8mb4_general_ci NOT NULL  ,
	PRIMARY KEY (`id_compte_produit`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8mb4' COLLATE='utf8mb4_general_ci';


CREATE TABLE `courrier_information_pack`(
	`id_courrier_information_pack` smallint(5) unsigned NOT NULL  auto_increment ,
	`courrier_information_pack` varchar(255) COLLATE utf8mb4_general_ci NOT NULL  ,
	`template_mail_courrier` varchar(255) COLLATE utf8mb4_general_ci NOT NULL  ,
	PRIMARY KEY (`id_courrier_information_pack`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8mb4' COLLATE='utf8mb4_general_ci';


CREATE TABLE `description_produit`(
	`id_produit` varchar(3) COLLATE utf8_general_ci NULL  ,
	`produit` varchar(101) COLLATE utf8_general_ci NULL  ,
	`description` varchar(1076) COLLATE utf8_general_ci NULL
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


ALTER TABLE `devis` CHANGE `ref` `ref` varchar(32)  COLLATE latin1_swedish_ci NOT NULL after `id_devis`;


ALTER TABLE `devis_ligne`
	ADD `prix_achat_ttc` decimal(10,2)   NOT NULL DEFAULT 0.00 after `id_fournisseur` ,
	CHANGE `prix_achat` `prix_achat` decimal(12,4)   NULL COMMENT 'Prix HT' after `prix_achat_ttc` ;


CREATE TABLE `document_contrat`(
	`id_document_contrat` mediumint(8) unsigned NOT NULL  auto_increment ,
	`document_contrat` varchar(150) COLLATE utf8mb4_general_ci NOT NULL  ,
	PRIMARY KEY (`id_document_contrat`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8mb4' COLLATE='utf8mb4_general_ci';


ALTER TABLE `document_revendeur` CHANGE `site_associe` `site_associe` enum('toshiba')  COLLATE latin1_swedish_ci NOT NULL after `id_societe`;

ALTER TABLE `facturation`
	CHANGE `assurance` `assurance` float(6,2) unsigned   NULL DEFAULT 0.00 after `frais_de_gestion` ,
	CHANGE `type` `type` enum('contrat','prolongation','liberatoire')  COLLATE utf8_general_ci NOT NULL DEFAULT 'contrat' after `date_periode_debut` ,
	ADD `nature` enum('promo','majoration','engagement','prolongation','prolongation_probable')  COLLATE utf8_general_ci NOT NULL DEFAULT 'engagement' after `date_periode_fin` ,
	DROP COLUMN `serenite` ,
	DROP COLUMN `maintenance` ,
	DROP COLUMN `hotline` ,
	DROP COLUMN `support` ,
	DROP COLUMN `supervision` ;

CREATE TABLE `facturation_fournisseur`(
	`id_facturation_fournisseur` int(10) unsigned NOT NULL  auto_increment ,
	`id_affaire` mediumint(8) unsigned NOT NULL  ,
	`id_fournisseur` mediumint(8) unsigned NOT NULL  ,
	`id_facture_fournisseur` mediumint(8) unsigned NULL  ,
	`montant` float(8,2) NOT NULL  DEFAULT 0.00 ,
	`date_periode_debut` date NOT NULL  ,
	PRIMARY KEY (`id_facturation_fournisseur`) ,
	UNIQUE KEY `id_affaire_2`(`id_affaire`,`id_fournisseur`,`date_periode_debut`) ,
	KEY `id_affaire`(`id_affaire`) ,
	KEY `id_fournisseur`(`id_fournisseur`) ,
	KEY `id_facture_fournisseur`(`id_facture_fournisseur`) ,
	CONSTRAINT `affaire`
	FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE ,
	CONSTRAINT `facture_fournisseur`
	FOREIGN KEY (`id_facture_fournisseur`) REFERENCES `facture_fournisseur` (`id_facture_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE ,
	CONSTRAINT `fournisseur`
	FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `facturation_fournisseur_detail`(
	`id_facturation_fournisseur_detail` bigint(20) unsigned NOT NULL  auto_increment ,
	`id_facturation_fournisseur` int(10) unsigned NOT NULL  ,
	`id_produit_fournisseur_loyer` mediumint(8) unsigned NOT NULL  ,
	`quantite` tinyint(3) unsigned NOT NULL  DEFAULT 1 ,
	PRIMARY KEY (`id_facturation_fournisseur_detail`) ,
	KEY `fact four`(`id_facturation_fournisseur`) ,
	KEY `loyer`(`id_produit_fournisseur_loyer`) ,
	CONSTRAINT `id_facturation_fournisseur`
	FOREIGN KEY (`id_facturation_fournisseur`) REFERENCES `facturation_fournisseur` (`id_facturation_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE ,
	CONSTRAINT `loyer`
	FOREIGN KEY (`id_produit_fournisseur_loyer`) REFERENCES `produit_fournisseur_loyer` (`id_produit_fournisseur_loyer`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8mb4' COLLATE='utf8mb4_general_ci';



ALTER TABLE `facture`
	CHANGE `type_libre` `type_libre` enum('normale','retard','contentieux','prorata','liberatoire')  COLLATE utf8_general_ci NULL after `type_facture` ,
	CHANGE `nature` `nature` enum('promo','majoration','prolongation_probable','prorata','engagement','prolongation','contrat')  COLLATE utf8_general_ci NULL after `date_regularisation` ,
	ADD `id_slimpay` varchar(38)  COLLATE utf8_general_ci NULL after `nature` ,
	ADD `executionStatus` enum('processing','rejected','processed','notprocessed','transformed','contested','toreplay','togenerate','toprocess')  COLLATE utf8_general_ci NULL after `id_slimpay` ,
	ADD `executionDate` datetime   NULL after `executionStatus` ,
	ADD `DATE_EXPORT_VTE` date   NULL after `executionDate` ;

ALTER TABLE `facture_fournisseur`
	ADD `numero_cegid` int(8)   NULL after `ref` ,
	ADD `prix_ht` decimal(12,4)   NOT NULL DEFAULT 0.0000 after `prix` ,
	CHANGE `tva` `tva` decimal(4,3) unsigned   NOT NULL after `prix_ht` ,
	CHANGE `type` `type` enum('achat','maintenance','cout_copie','achat_non_immo','presta_ponctuelle')  COLLATE utf8_general_ci NOT NULL after `date_echeance` ,
	ADD `bap` tinyint(1)   NOT NULL DEFAULT 0 after `deja_exporte_cegid` ,
	ADD `date_bap` date   NULL after `bap` ,
	ADD `id_user` mediumint(8) unsigned   NULL after `date_bap` ,
	ADD `num_bap` varchar(15)  COLLATE utf8_general_ci NULL after `id_user` ,
	ADD `DATE_EXPORT_BAP` date   NULL after `num_bap` ,
	ADD `DATE_EXPORT_FACT` date   NULL after `DATE_EXPORT_BAP` ,
	ADD KEY `id_user`(`id_user`) ,
	DROP FOREIGN KEY `facture_fournisseur_ibfk_1`  ,
	DROP FOREIGN KEY `facture_fournisseur_ibfk_2`  ,
	DROP FOREIGN KEY `facture_fournisseur_ibfk_3`  ;
ALTER TABLE `facture_fournisseur`
	ADD CONSTRAINT `facture_fournisseur_ibfk_1`
	FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE ;


ALTER TABLE `facture_fournisseur_ligne`
	CHANGE `produit` `produit` varchar(128)  COLLATE latin1_swedish_ci NOT NULL after `ref` ,
	CHANGE `prix` `prix` decimal(10,4) unsigned   NULL COMMENT 'Prix HT' after `quantite` ,
	ADD `prix_ttc` decimal(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Prix TTC' after `prix` ,
	CHANGE `id_bon_de_commande_ligne` `id_bon_de_commande_ligne` mediumint(8) unsigned   NULL after `prix_ttc` ,
	CHANGE `serial` `serial` varchar(300)  COLLATE latin1_swedish_ci NULL after `id_bon_de_commande_ligne`;




ALTER TABLE `facture_fournisseur_ligne`
	CHANGE `prix` `prix` decimal(10,4) unsigned   NULL COMMENT 'Prix HT' after `quantite` ,
	ADD `prix_ttc` decimal(10,2)   NOT NULL DEFAULT 0.00 COMMENT 'Prix TTC' after `prix` ,
	CHANGE `id_bon_de_commande_ligne` `id_bon_de_commande_ligne` mediumint(8) unsigned   NULL after `prix_ttc` ,
	CHANGE `serial` `serial` varchar(300)  COLLATE latin1_swedish_ci NULL after `id_bon_de_commande_ligne`;


/* Alter table in target */
ALTER TABLE `facture_non_parvenue`
	ADD `prix_ht` decimal(12,4)   NOT NULL DEFAULT 0.0000 after `prix` ,
	CHANGE `tva` `tva` decimal(4,3) unsigned   NOT NULL after `prix_ht` ;
ALTER TABLE `facture_non_parvenue`
	ADD CONSTRAINT `facture_non_parvenue_ibfk_1`
	FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `facture_non_parvenue_ibfk_2`
	FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `facture_non_parvenue_ibfk_3`
	FOREIGN KEY (`id_facture_fournisseur`) REFERENCES `facture_fournisseur` (`id_facture_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `loyer`
	CHANGE `loyer` `loyer` decimal(10,4)   NOT NULL after `id_affaire` ,
	CHANGE `assurance` `assurance` decimal(10,4) unsigned   NULL after `duree` ,
	CHANGE `frais_de_gestion` `frais_de_gestion` decimal(10,4) unsigned   NULL after `assurance` ,
	CHANGE `frequence_loyer` `frequence_loyer` enum('jour','mois','trimestre','semestre','an')  COLLATE utf8_general_ci NOT NULL DEFAULT 'mois' after `frais_de_gestion` ,
	ADD `nature` enum('promo','majoration','engagement','prolongation','prolongation_probable')  COLLATE utf8_general_ci NULL after `frequence_loyer` ,
	CHANGE `type` `type` enum('engagement','liberatoire')  COLLATE utf8_general_ci NOT NULL DEFAULT 'engagement' after `nature` ,
	CHANGE `avec_option` `avec_option` enum('oui','non')  COLLATE utf8_general_ci NOT NULL DEFAULT 'non' after `type` ,
	DROP COLUMN `hotline` ,
	DROP COLUMN `maintenance` ,
	DROP COLUMN `serenite` ,
	DROP COLUMN `supervision` ,
	DROP COLUMN `support` ,
	DROP FOREIGN KEY `loyer_ibfk_1`  ;
ALTER TABLE `loyer_prolongation`
	CHANGE `frequence_loyer` `frequence_loyer` enum('mois','trimestre','semestre','an')  COLLATE utf8_general_ci NOT NULL DEFAULT 'mois' after `frais_de_gestion` ,
	DROP COLUMN `hotline` ,
	DROP COLUMN `serenite` ,
	DROP COLUMN `maintenance` ,
	DROP COLUMN `supervision` ,
	DROP COLUMN `support` ,
	DROP FOREIGN KEY `loyer_prolongation_ibfk_1`  ,
	DROP FOREIGN KEY `loyer_prolongation_ibfk_2`  ;
ALTER TABLE `magasin`
	ADD `entite_lm` varchar(10)  COLLATE utf8_general_ci NULL after `site_associe` ,
	ADD `langue` varchar(2)  COLLATE utf8_general_ci NULL DEFAULT 'FR' after `entite_lm` ,
	ADD `num_magasin_lm` int(4)   NULL after `langue` ,
	ADD `afficher` enum('oui','non')  COLLATE utf8_general_ci NOT NULL DEFAULT 'oui' after `num_magasin_lm` ,
	ADD `email` varchar(256)  COLLATE utf8_general_ci NULL after `afficher` ,
	ADD `password` varchar(255)  COLLATE utf8_general_ci NOT NULL after `email` ;
ALTER TABLE `pack_produit`
	CHANGE `id_pack_produit` `id_pack_produit` smallint(5) unsigned   NOT NULL auto_increment first ,
	ADD `libelle` varchar(500)  COLLATE utf8_general_ci NULL after `frequence` ,
	ADD `libelle_ecran_magasin` varchar(500)  COLLATE utf8_general_ci NULL after `libelle` ,
	ADD `popup` text  COLLATE utf8_general_ci NULL after `libelle_ecran_magasin` ,
	CHANGE `description` `description` text  COLLATE utf8_general_ci NULL after `popup` ,
	ADD `type_pack` enum('achat_abo','abo')  COLLATE utf8_general_ci NULL after `avis_expert` ,
	ADD `ref_lm_principale` int(11)   NULL after `type_pack` ,
	CHANGE `etat` `etat` enum('actif','inactif')  COLLATE utf8_general_ci NOT NULL DEFAULT 'actif' after `ref_lm_principale` ,
	ADD `url_redirection` varchar(100)  COLLATE utf8_general_ci NULL after `url` ,
	ADD `message_redirection` varchar(200)  COLLATE utf8_general_ci NULL after `url_redirection` ,
	ADD `id_produit` mediumint(8) unsigned   NULL after `message_redirection` ,
	ADD `fin_formulaire` text  COLLATE utf8_general_ci NULL after `id_produit` ,
	ADD `service_inclus` set('diagnostique_tel','appel','livraison','pose','installation','assistance','entretien','garantie')  COLLATE utf8_general_ci NULL after `fin_formulaire` ,
	ADD `id_document_contrat` mediumint(8) unsigned   NULL after `service_inclus` ,
	ADD `id_rayon` smallint(5) unsigned   NULL after `id_document_contrat` ,
	ADD `type_contrat` enum('LP','SP','LS')  COLLATE utf8_general_ci NOT NULL DEFAULT 'LS' after `id_rayon` ,
	ADD `type_pack_magasin` enum('chaudiere','adoucisseur','alarme')  COLLATE utf8_general_ci NULL after `type_contrat` ,
	ADD `pack_alarme` enum('maison','appartement')  COLLATE utf8_general_ci NULL after `type_pack_magasin` ,
	ADD `id_courrier_information_pack` smallint(5) unsigned   NULL after `pack_alarme` ,
	ADD `afficher_tout_les_produits` enum('oui','non')  COLLATE utf8_general_ci NOT NULL DEFAULT 'oui' after `id_courrier_information_pack` ,
	ADD `prix_min_avec_produit` decimal(8,2) unsigned   NULL after `afficher_tout_les_produits` ,
	ADD `prix_min_sans_produit` decimal(8,2) unsigned   NULL after `prix_min_avec_produit` ,
	ADD KEY `id_courrier_information_pack`(`id_courrier_information_pack`) ,
	ADD KEY `id_courrier_information_pack_2`(`id_courrier_information_pack`) ,
	ADD KEY `id_document_contrat`(`id_document_contrat`) ,
	ADD KEY `id_produit`(`id_produit`) ,
	ADD KEY `id_rayon`(`id_rayon`) ,
	ADD UNIQUE KEY `pack_alarme`(`pack_alarme`);

ALTER TABLE `pack_produit`
	ADD CONSTRAINT `pack_produit_ibfk_2`
	FOREIGN KEY (`id_document_contrat`) REFERENCES `document_contrat` (`id_document_contrat`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `pack_produit_ibfk_3`
	FOREIGN KEY (`id_rayon`) REFERENCES `rayon` (`id_rayon`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `pack_produit_ibfk_4`
	FOREIGN KEY (`id_courrier_information_pack`) REFERENCES `courrier_information_pack` (`id_courrier_information_pack`) ON DELETE SET NULL ON UPDATE CASCADE ;

/* Alter table in target */
ALTER TABLE `parc`
	CHANGE `serial` `serial` varchar(64)  COLLATE latin1_swedish_ci NULL after `divers` ,
	CHANGE `etat` `etat` enum('broke','loue','reloue','vole','vendu','attente_location')  COLLATE latin1_swedish_ci NOT NULL after `serial` ,
	ADD `provenanceParcReloue` mediumint(8) unsigned   NULL COMMENT 'Parc repris du stock (dont l\'etat est attente_location)' after `provenance` ,
	CHANGE `existence` `existence` enum('actif','inactif')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'actif' after `provenanceParcReloue` ,
	ADD `date_recuperation` date   NULL after `existence` ,
	ADD KEY `provenanceParcReloue`(`provenanceParcReloue`);
ALTER TABLE `parc`
	ADD CONSTRAINT `parc_ibfk_1`
	FOREIGN KEY (`provenanceParcReloue`) REFERENCES `parc` (`id_parc`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `parc_ibfk_2`
	FOREIGN KEY (`provenance`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE ;


/* Alter table in target */
ALTER TABLE `produit`
	ADD `url_produit` varchar(255)  COLLATE latin1_swedish_ci NULL after `prix_achat` ,
	CHANGE `id_fabriquant` `id_fabriquant` mediumint(8) unsigned   NULL after `url_produit` ,
	ADD `tva_prix_achat` decimal(3,2)   NOT NULL DEFAULT 1.20 after `ean` ,
	ADD `id_pack_produit` smallint(5) unsigned   NOT NULL after `tva_prix_achat` ,
	ADD `id_produit_principal` mediumint(8) unsigned   NULL after `id_pack_produit` ,
	ADD `qte_lie_principal` enum('oui','non')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'non' COMMENT 'Permet de savoir si la quantité du sous produit est lié au produit principal (qté sous produit * qté commandé du produit principale)' after `id_produit_principal` ,
	ADD `ref_lm` varchar(50)  COLLATE latin1_swedish_ci NULL after `qte_lie_principal` ,
	ADD `ref_fournisseur` varchar(50)  COLLATE latin1_swedish_ci NULL after `ref_lm` ,
	ADD `nature` enum('produit','service','surcout')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'produit' after `ref_fournisseur` ,
	ADD `libelle_a_revoyer_lm` enum('oui','non')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'oui' after `nature` ,
	ADD `controle_fournisseur` enum('mail')  COLLATE latin1_swedish_ci NULL after `libelle_a_revoyer_lm` ,
	ADD `declencheur_mep` enum('oui')  COLLATE latin1_swedish_ci NULL after `controle_fournisseur` ,
	ADD `min` int(11)   NOT NULL after `declencheur_mep` ,
	ADD `max` int(11)   NOT NULL after `min` ,
	ADD `defaut` int(11)   NOT NULL after `max` ,
	ADD `pas` int(11)   NOT NULL DEFAULT 1 COMMENT 'Pas pour les listes déroulantes' after `defaut` ,
	ADD `tva_loyer` decimal(3,2)   NOT NULL DEFAULT 1.20 after `pas` ,
	ADD `ordre` int(11)   NOT NULL DEFAULT 1 after `tva_loyer` ,
	ADD `afficher` enum('oui','non')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'oui' COMMENT 'Afficher on non sur le site client/magasin' after `ordre` ,
	ADD `visible_pdf` enum('oui','non')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'oui' COMMENT 'Afficher ou non sur les PDF' after `afficher` ,
	ADD `mode_paiement` enum('achat','mensuel','a_la_demande')  COLLATE latin1_swedish_ci NULL after `visible_pdf` ,
	ADD `element_declencheur` enum('acceptation_comite')  COLLATE latin1_swedish_ci NULL after `mode_paiement` ,
	ADD `id_compte_produit` mediumint(8) unsigned   NULL after `element_declencheur` ,
	ADD `sous_produit_unique` enum('oui','non')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'non' after `id_compte_produit` ,
	ADD `text` varchar(500)  COLLATE latin1_swedish_ci NULL after `sous_produit_unique` ,
	ADD `popin` text  COLLATE latin1_swedish_ci NULL after `text` ,
	ADD `question` varchar(500)  COLLATE latin1_swedish_ci NULL after `popin` ,
	ADD `nb_produit_inclus` int(11)   NOT NULL DEFAULT 0 after `question` ,
	ADD `seuil` mediumint(8) unsigned   NULL COMMENT '	Seuil en € a partir duquel le produit s\'affiche' after `nb_produit_inclus` ,
	ADD `prevenir_presta_arret` enum('oui','non')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'non' COMMENT 'Prevenir le prestataire en cas d\'arret du service? Permet de savoir si on envoi, un mail pour prevenir le presta lors de l\'arret du contrat' after `seuil` ,
	ADD KEY `id_compte_produit`(`id_compte_produit`) ,
	ADD KEY `id_pack_produit`(`id_pack_produit`) ,
	ADD KEY `id_produit_principal`(`id_produit_principal`);
ALTER TABLE `produit`
	ADD CONSTRAINT `produit_ibfk_1`
	FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit` (`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_2`
	FOREIGN KEY (`id_compte_produit`) REFERENCES `compte_produit` (`id_compte_produit`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_3`
	FOREIGN KEY (`id_produit_principal`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE `produit_fournisseur`(
	`id_produit_fournisseur` mediumint(8) unsigned NOT NULL  auto_increment ,
	`id_produit` mediumint(8) unsigned NOT NULL  ,
	`id_fournisseur` mediumint(8) unsigned NOT NULL  ,
	`prix_ttc` decimal(8,2) NOT NULL  DEFAULT 0.00 ,
	`prix_prestation` decimal(10,4) NOT NULL  COMMENT 'Montant HT' ,
	`recurrence` enum('mensuel','ponctuel','a_la_demande','achat') COLLATE utf8_general_ci NOT NULL  DEFAULT 'mensuel' ,
	`departement` varchar(512) COLLATE utf8_general_ci NULL  ,
	PRIMARY KEY (`id_produit_fournisseur`) ,
	KEY `id_produit`(`id_produit`) ,
	KEY `id_fournisseur`(`id_fournisseur`) ,
	CONSTRAINT `produit_fournisseur_ibfk_1`
	FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE ,
	CONSTRAINT `produit_fournisseur_ibfk_2`
	FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


ALTER TABLE `produit_fournisseur_loyer`
	ADD `nb_loyer` int(11)   NOT NULL after `id_produit` ,
	CHANGE `loyer` `loyer` decimal(6,2)   NOT NULL after `nb_loyer` ,
	ADD `ordre` int(11)   NOT NULL after `loyer` ,
	ADD `periodicite` enum('mois','trimestre','semestre','an')  COLLATE utf8_general_ci NOT NULL after `ordre` ,
	CHANGE `id_fournisseur` `id_fournisseur` mediumint(8) unsigned   NOT NULL after `periodicite` ,
	ADD `departement` varchar(512)  COLLATE utf8_general_ci NULL after `id_fournisseur` ,
	ADD `nature` enum('engagement','prolongation','prolongation_probable')  COLLATE utf8_general_ci NOT NULL after `departement` ,
	DROP COLUMN `frequence_loyer` ;

CREATE TABLE `produit_links`(
	`id_produit_links` mediumint(8) unsigned NOT NULL  auto_increment ,
	`id_produit` mediumint(8) unsigned NOT NULL  ,
	`id_produit_cible` mediumint(8) unsigned NOT NULL  ,
	`etat` enum('dependant','exclude') COLLATE utf8mb4_general_ci NOT NULL  COMMENT 'produit cible dépendant du produit / produit cible exclu du produit' ,
	PRIMARY KEY (`id_produit_links`) ,
	KEY `id_produit`(`id_produit`) ,
	KEY `id_produit_cible`(`id_produit_cible`) ,
	CONSTRAINT `produit_links_ibfk_1`
	FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE ,
	CONSTRAINT `produit_links_ibfk_2`
	FOREIGN KEY (`id_produit_cible`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8mb4' COLLATE='utf8mb4_general_ci';


CREATE TABLE `produit_loyer`(
	`id_produit_loyer` mediumint(8) unsigned NOT NULL  auto_increment ,
	`id_produit` mediumint(8) unsigned NOT NULL  ,
	`duree` int(11) NOT NULL  ,
	`loyer` decimal(10,4) NOT NULL  ,
	`ordre` tinyint(3) unsigned NOT NULL  ,
	`nature` enum('promo','majoration','engagement','prolongation','prolongation_probable') COLLATE utf8_general_ci NOT NULL  ,
	`periodicite` enum('mois','trimestre','semestre','an') COLLATE utf8_general_ci NOT NULL  ,
	PRIMARY KEY (`id_produit_loyer`) ,
	KEY `id_produit`(`id_produit`) ,
	CONSTRAINT `produit_loyer_ibfk_1`
	FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


CREATE TABLE `rayon`(
	`id_rayon` smallint(5) unsigned NOT NULL  auto_increment ,
	`rayon` varchar(100) COLLATE utf8mb4_general_ci NOT NULL  ,
	`centre_cout_profit` varchar(5) COLLATE utf8mb4_general_ci NOT NULL  ,
	PRIMARY KEY (`id_rayon`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8mb4' COLLATE='utf8mb4_general_ci';



/* Alter table in target */
ALTER TABLE `site_article`
	ADD `id_parent` smallint(5) unsigned   NULL after `id_site_article` ,
	CHANGE `titre` `titre` varchar(512)  COLLATE utf8_general_ci NOT NULL after `id_parent` ,
	ADD KEY `id_parent`(`id_parent`) ;

/* Alter table in target */
ALTER TABLE `societe`
	ADD `numero_site` varchar(50)  COLLATE utf8_general_ci NULL after `adresse` ,
	CHANGE `adresse_2` `adresse_2` varchar(64)  COLLATE utf8_general_ci NULL after `numero_site` ,
	CHANGE `divers_2` `divers_2` enum('prelevement','mandat','virement','cheque')  COLLATE utf8_general_ci NULL DEFAULT 'prelevement' after `code_client_partenaire` ,
	CHANGE `divers_3` `divers_3` enum('Midas','Optic_2000','Norauto','Atol','-')  COLLATE utf8_general_ci NOT NULL DEFAULT '-' after `divers_2` ,
	ADD `id_lm` int(11)   NULL after `sms_tentative` ,
	ADD `id_carte_maison` varchar(20)  COLLATE utf8_general_ci NULL after `id_lm` ,
	ADD `id_magasin` int(11)   NULL after `id_carte_maison` ,
	ADD `type_societe` enum('client','societe')  COLLATE utf8_general_ci NOT NULL DEFAULT 'client' after `id_magasin` ,
	ADD `civilite` enum('m','mme','mlle')  COLLATE utf8_general_ci NULL after `type_societe` ,
	ADD `nom` varchar(100)  COLLATE utf8_general_ci NULL after `civilite` ,
	ADD `prenom` varchar(100)  COLLATE utf8_general_ci NULL after `nom` ,
	ADD `mdp` varchar(64)  COLLATE utf8_general_ci NULL after `prenom` ,
	ADD `date_naissance` date   NULL after `mdp` ,
	ADD `offre_lmA` enum('oui','non')  COLLATE utf8_general_ci NOT NULL DEFAULT 'non' after `date_naissance` ,
	ADD `offre_lm` enum('oui','non')  COLLATE utf8_general_ci NOT NULL DEFAULT 'non' after `offre_lmA` ,
	ADD `client_id` varchar(32)  COLLATE utf8_general_ci NULL after `offre_lm` ,
	ADD `client_secret` varchar(32)  COLLATE utf8_general_ci NULL after `client_id` ,
	ADD `id_client_externe` varchar(16)  COLLATE utf8_general_ci NULL COMMENT 'Code client de la base de données externe' after `client_secret` ,
	ADD `email_notification` varchar(128)  COLLATE utf8_general_ci NULL after `id_client_externe` ,
	ADD `token` varchar(50)  COLLATE utf8_general_ci NULL after `email_notification` ,
	ADD `expiration_token` datetime   NULL after `token` ,
	ADD UNIQUE KEY `email`(`email`) ,
	ADD UNIQUE KEY `email_2`(`email`) ,
	ADD UNIQUE KEY `id_client_externe`(`id_client_externe`);

ALTER TABLE `suivi`
	CHANGE `origine` `origine` enum('societe_devis','societe_commande','societe_location','notification_slimpay','portail_presta')  COLLATE latin1_swedish_ci NULL DEFAULT 'societe_devis' COMMENT 'endroit ou le suivi a Ã©tÃ© crÃ©Ã©' after `id_societe` ,
	CHANGE `type` `type` enum('note','fichier','RDV','appel','courrier','prestataire')  COLLATE latin1_swedish_ci NOT NULL DEFAULT 'note' after `origine` ,
	CHANGE `type_suivi` `type_suivi` enum('Devis','Contrat','Refinancement','Comptabilité','Broke','Contentieux','Mis en place','Restitution','Autre','Prolongation','Resiliation','Sinistre','Transfert','Fournisseur','Requête','BDC','Flottes','Installation','Passage_comite','demande_comite','Audit en cours','Assurance','Formation','Maintenance','Livraison','Commentaire')  COLLATE latin1_swedish_ci NULL after `id_affaire` ;

CREATE TABLE `token`(
	`id_token` int(10) unsigned NOT NULL  auto_increment ,
	`token` varchar(25) COLLATE utf8mb4_general_ci NOT NULL  ,
	`expire_time` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
	`id_societe` mediumint(8) unsigned NOT NULL  ,
	PRIMARY KEY (`id_token`) ,
	KEY `id_societe`(`id_societe`) ,
	CONSTRAINT `token_ibfk_1`
	FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8mb4' COLLATE='utf8mb4_general_ci';


