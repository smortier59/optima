###
###	SQL A METTRE SUR LA BDD LMA
###

ALTER TABLE `affaire`   CHANGE `type_affaire` `type_affaire` ENUM('normal', '2SI' , 'LP','SP', 'LS') NOT NULL DEFAULT 'LS';

ALTER TABLE `affaire`
	ADD `id_filiale` mediumint(8) unsigned  NOT NULL DEFAULT 2 after `id_societe` ,
	ADD `mail_signature` enum('oui','non') NOT NULL DEFAULT 'non' after `nature` ,
	ADD `date_signature` datetime   NULL after `mail_signature` ,
	ADD `signataire` varchar(150) NULL after `date_signature` ,
	ADD `mail_document` enum('oui','non') NOT NULL DEFAULT 'non' after `signataire` ,
	ADD `langue` enum('FR','NL') NOT NULL DEFAULT 'FR' after `type_affaire` ,
	ADD `id_contract_sellandsign` int(10) unsigned   NULL after `langue` ,
	ADD `site_associe` enum('toshiba','btwin','location_evolutive') NULL after `id_contract_sellandsign` ,
	ADD `etat_comite` enum('accepte','refuse','attente','accord_non utilise','favorable_cession') NOT NULL DEFAULT 'attente' after `site_associe` ,
	ADD `provenance` enum('toshiba','cleodis','vendeur','partenaire','la_poste','btwin') NULL after `etat_comite` ,
	ADD `pieces` enum('NOK','OK') NULL after `provenance` ,
	ADD `date_verification` date   NULL after `pieces` ,
	ADD `id_partenaire` mediumint(8) unsigned   NULL after `date_verification` ,
	ADD `commentaire_facture` varchar(500) NULL after `id_partenaire` ,
	ADD `commentaire_facture2` varchar(500) NULL after `commentaire_facture` ,
	ADD `commentaire_facture3` varchar(500) NULL after `commentaire_facture2` ,
	ADD `tel_signature` varchar(20) NULL after `commentaire_facture3` ,
	ADD `mail_signataire` varchar(100) NULL after `tel_signature` ;

ALTER TABLE `affaire`
  ADD KEY `id_filiale` (`id_filiale`),
  ADD KEY `id_partenaire` (`id_partenaire`);


ALTER TABLE `affaire` ADD CONSTRAINT `affaire_ibfk_2` FOREIGN KEY (`id_filiale`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE;



ALTER TABLE `affaire_etat`
	CHANGE `etat` `etat` enum('commande','commande_pris_en_compte','validation_commande','livraison_initie','livraison_en_cours','debut_contrat','fin_engagement','annulation_commande','installee','diagnostic_effectue','reception_demande','reception_pj','preparation_commande','refus_dossier','expedition_en_cours','colis_recu','valide_administratif','comite_cleodis_valide','comite_cleodis_refuse','refus_administratif','autre','envoi_mail_relance')  NOT NULL after `date` ,
	ADD `id_user` mediumint(8) unsigned   NULL after `commentaire` ,
	ADD KEY `id_user`(`id_user`) ;
ALTER TABLE `affaire_etat` 	ADD CONSTRAINT `affaire_etat_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;



CREATE TABLE `assurance`(
	`id_assurance` mediumint(8) unsigned NOT NULL  auto_increment ,
	`ref` varchar(16) NOT NULL  ,
	`date` date NULL  ,
	`id_affaire` mediumint(8) unsigned NOT NULL  ,
	`montant` decimal(8,2) unsigned NULL  DEFAULT 0.00 ,
	PRIMARY KEY (`id_assurance`) ,
	KEY `id_affaire`(`id_affaire`) ,
	CONSTRAINT `assurance_ibfk_1`
	FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


CREATE TABLE `asterisk`(
	`id_asterisk` mediumint(8) unsigned NOT NULL  auto_increment ,
	`asterisk` varchar(32) NOT NULL  ,
	`host` varchar(32) NOT NULL  ,
	`url_webservice` varchar(255) NOT NULL  ,
	`login` varchar(16) NOT NULL  ,
	`password` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_asterisk`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci' COMMENT='Gestion des serveurs asterisk';


CREATE TABLE `base_de_connaissance`(
	`id_base_de_connaissance` mediumint(8) unsigned NOT NULL  auto_increment ,
	`base_de_connaissance` varchar(255) NOT NULL  ,
	`id_user` mediumint(8) unsigned NULL  ,
	`date` datetime NOT NULL  ,
	`last_seen` datetime NULL  ,
	`texte` text NOT NULL  ,
	`frequentation` smallint(5) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_base_de_connaissance`) ,
	KEY `id_user`(`id_user`) ,
	CONSTRAINT `base_de_connaissance_ibfk_1`
	FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE SET NULL
);


ALTER TABLE `bon_de_commande` ADD `export_cegid` datetime   NULL after `date_installation_demande`;
ALTER TABLE `bon_de_commande` ADD `export_servantissimmo` datetime   NULL after `export_cegid`;

ALTER TABLE `bon_de_commande_ligne` CHANGE `produit` `produit` varchar(500) NOT NULL ;


CREATE TABLE `client`(
	`id_client` mediumint(8) unsigned NOT NULL  auto_increment ,
	`client` varchar(150) NOT NULL  ,
	`etat` enum('actif','inactif') NOT NULL  DEFAULT 'inactif' ,
	`client_id` varchar(60) NOT NULL  COMMENT 'Clé sur 32 carac non crypté' ,
	`client_secret` varchar(60) NOT NULL  COMMENT 'Clé sur 32 caractères bcrypté' ,
	PRIMARY KEY (`id_client`)
);


ALTER TABLE `comite`
	ADD `id_refinanceur` mediumint(8) unsigned   NOT NULL after `date` ,
	ADD `id_contact` mediumint(8) unsigned   NULL after `id_refinanceur` ,
	ADD `activite` varchar(250) NULL after `id_societe` ,
	ADD `valeur_residuelle` decimal(10,2)   NOT NULL DEFAULT 0.00 after `prix` ,
	ADD `pourcentage_materiel` decimal(5,2)   NOT NULL DEFAULT 0.00 after `valeur_residuelle` ,
	ADD `pourcentage_logiciel` decimal(5,2)   NOT NULL DEFAULT 0.00 after `pourcentage_materiel` ,
	ADD `description` varchar(255) NOT NULL after `pourcentage_logiciel` ,
	ADD `marque_materiel` varchar(255) NULL after `description` ,
	CHANGE `etat` `etat` enum('refuse','favorable_cession','accord_portage_recherche_cession','accord_portage_recherche_cession_groupee','accord_non utilise','accepte','en_attente') NOT NULL DEFAULT 'en_attente',
	ADD `taux` decimal(5,2)   NULL after `etat` ,
	ADD `coefficient` varchar(16) NULL after `taux` ,
	ADD `encours` decimal(10,2)   NULL after `coefficient` ,
	ADD `frais_de_gestion` decimal(10,2)   NULL after `encours` ,
	ADD `validite_accord` date   NULL after `frais_de_gestion` ,
	ADD `observations` varchar(255) NULL after `validite_accord` ,
	ADD `loyer_actualise` decimal(10,2)   NULL after `observations` ,
	ADD `date_cession` date   NULL after `loyer_actualise` ,
	ADD `duree_refinancement` varchar(20) NULL after `date_cession` ,
	ADD `note` varchar(11) NULL after `duree_refinancement` ,
	ADD `limite` varchar(11) NULL after `note` ,
	ADD `ca` decimal(20,2)   NULL after `limite` ,
	ADD `resultat_exploitation` decimal(20,2)   NULL after `ca` ,
	ADD `capital_social` decimal(20,2)   NULL after `resultat_exploitation` ,
	ADD `capitaux_propres` decimal(20,2)   NULL after `capital_social` ,
	ADD `dettes_financieres` decimal(20,2)   NULL after `capitaux_propres` ,
	ADD `maison_mere1` varchar(150) NULL after `dettes_financieres` ,
	ADD `maison_mere2` varchar(150) NULL after `maison_mere1` ,
	ADD `maison_mere3` varchar(150) NULL after `maison_mere2` ,
	ADD `maison_mere4` varchar(150) NULL after `maison_mere3` ,
	ADD `date_compte` varchar(15) NULL after `maison_mere4` ,
	ADD KEY `id_contact`(`id_contact`) ,
	ADD KEY `id_refinanceur`(`id_refinanceur`) ;
ALTER TABLE `comite`
	ADD CONSTRAINT `comite_ibfk_1` FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur` (`id_refinanceur`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `comite_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE ;


ALTER TABLE `commande`
	CHANGE `etat` `etat` enum('non_loyer','mis_loyer','prolongation','AR','arreter','vente','restitution','mis_loyer_contentieux','prolongation_contentieux','restitution_contentieux','pending','abandon')  NOT NULL DEFAULT 'non_loyer' after `id_devis` ,
	CHANGE `id_user` `id_user` mediumint(8) unsigned   NOT NULL after `etat`;

ALTER TABLE `commande_ligne`
	CHANGE `produit` `produit` varchar(500)  COLLATE utf8_swedish_ci NOT NULL after `ref` ,
	CHANGE `quantite` `quantite` int(10) unsigned   NOT NULL after `produit`;


ALTER TABLE `contact`
	CHANGE `langue` `langue` enum('FR','NL')  NOT NULL DEFAULT 'FR' after `id_owner` ,
	CHANGE `private` `private` enum('oui','non')  NOT NULL DEFAULT 'non' after `langue` ,
	ADD `tel_perso` varchar(20)  NULL after `email` ,
	ADD `email_perso` varchar(255)  NULL after `tel_perso` ,
	ADD `gsm_perso` varchar(20)  NULL after `email_perso` ,
	ADD `est_dirigeant` enum('oui','non')  NOT NULL DEFAULT 'non' COMMENT 'Est un dirigeant de la société (récupéré de l\'interogation CreditSafe)' after `fonction` ,
	CHANGE `departement` `departement` varchar(255)  NULL after `est_dirigeant` ,
	CHANGE `assistant` `assistant` varchar(128)  NULL after `loisir` ,
	CHANGE `login` `login` varchar(50)  NULL after `num_siret_perso` ,
	CHANGE `pwd` `pwd` varchar(64)  NULL after `login` ,
	ADD `situation_maritale` varchar(255)  NULL after `compte` ,
	ADD `situation_pro` varchar(255)  NULL after `situation_maritale` ,
	ADD `situation_perso` varchar(255)  NULL after `situation_pro` ,
	ADD UNIQUE KEY `login`(`login`) ;

ALTER TABLE `devis`
	ADD `id_filiale` mediumint(8) unsigned   NOT NULL DEFAULT 2 after `id_societe` ,
	CHANGE `type_contrat` `type_contrat` enum('lld','lrp','presta','loa','logiciel','speciaux','vente','mat_clt','cout_copie') NOT NULL after `devis` ,
	ADD `type_devis` enum('normal','optic_2000') NOT NULL DEFAULT 'normal' after `type_contrat` ,
	ADD `offre_partenaire` varchar(80) NULL after `raison_refus` ,
	ADD `commentaire_offre_partenaire` text NULL after `offre_partenaire` ,
	ADD KEY `id_filiale`(`id_filiale`) ;

ALTER TABLE `devis`
	ADD CONSTRAINT `devis_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ibfk_3` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ibfk_4` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ibfk_5` FOREIGN KEY (`id_opportunite`) REFERENCES `opportunite` (`id_opportunite`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ibfk_6` FOREIGN KEY (`id_filiale`) REFERENCES `societe` (`id_societe`) ;


ALTER TABLE `devis_ligne` ADD COLUMN `options` enum('oui','non') NOT NULL DEFAULT 'non';
ALTER TABLE `devis_ligne`
	ADD CONSTRAINT `devis_ligne_ibfk_1`	FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id_devis`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ligne_ibfk_3`	FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ligne_ibfk_4`	FOREIGN KEY (`id_affaire_provenance`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `devis_ligne_ibfk_5`	FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE SET NULL ON UPDATE CASCADE;


CREATE TABLE `document`(
	`id_document` mediumint(8) unsigned NOT NULL  auto_increment ,
	`document` varchar(200) NOT NULL  ,
	`filename` varchar(200) NOT NULL  ,
	PRIMARY KEY (`id_document`)
);

CREATE TABLE `document_revendeur`(
	`id_document_revendeur` mediumint(8) unsigned NOT NULL  auto_increment ,
	`id_societe` mediumint(8) unsigned NULL  ,
	`site_associe` enum('toshiba') NOT NULL  ,
	PRIMARY KEY (`id_document_revendeur`) ,
	KEY `id_societe`(`id_societe`) ,
	CONSTRAINT `document_revendeur_ibfk_1`
	FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE
);




ALTER TABLE `facturation`
	CHANGE `type` `type` enum('contrat','prolongation','liberatoire') NOT NULL DEFAULT 'contrat' after `date_periode_debut` ,
	DROP FOREIGN KEY `facturation_ibfk_1`  ,
	DROP FOREIGN KEY `facturation_ibfk_2`  ,
	DROP FOREIGN KEY `facturation_ibfk_3`  ;
ALTER TABLE `facturation`
	ADD CONSTRAINT `facturation_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `facturation_ibfk_4` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `facturation_ibfk_5` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE SET NULL ON UPDATE CASCADE ;

ALTER TABLE `facturation_attente`
	CHANGE `envoye` `envoye` enum('oui','non','erreur') NOT NULL DEFAULT 'non',
	ADD `erreur` text NULL after `id_facturation`;



ALTER TABLE `facture`
	CHANGE `type_libre` `type_libre` enum('normale','retard','contentieux','prorata','liberatoire')  NULL ,
	CHANGE `mode_paiement` `mode_paiement` enum('prelevement','mandat','virement','remboursement','compensation','cheque','cb','pre-paiement')  NULL,
	ADD `id_fournisseur_prepaiement` mediumint(8) unsigned   NULL after `mode_paiement` ,
	CHANGE `id_demande_refi` `id_demande_refi` mediumint(8) unsigned   NULL after `id_fournisseur_prepaiement` ,
	CHANGE `nature` `nature` enum('promo','majoration','prolongation_probable','prorata','engagement','prolongation','contrat')  NULL after `date_regularisation` ,
	ADD KEY `id_fournisseur_prepaiement`(`id_fournisseur_prepaiement`) ;
ALTER TABLE `facture`
	ADD CONSTRAINT `facture_ibfk_2` FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur` (`id_refinanceur`) ,
	ADD CONSTRAINT `facture_ibfk_3` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ,
	ADD CONSTRAINT `facture_ibfk_4` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ,
	ADD CONSTRAINT `facture_ibfk_5` FOREIGN KEY (`id_demande_refi`) REFERENCES `demande_refi` (`id_demande_refi`) ,
	ADD CONSTRAINT `facture_ibfk_6` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ,
	ADD CONSTRAINT `facture_ibfk_7` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ,
	ADD CONSTRAINT `facture_ibfk_8` FOREIGN KEY (`id_fournisseur_prepaiement`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE ;



ALTER TABLE `facture_fournisseur`
	CHANGE `type` `type` enum('achat','maintenance','cout_copie','achat_non_immo','presta_ponctuelle') NOT NULL ,
	ADD `deja_exporte_cegid` enum('oui','non') NOT NULL DEFAULT 'non' after `deja_exporte_achat`;
ALTER TABLE `facture_fournisseur`
	ADD CONSTRAINT `facture_fournisseur_ibfk_1` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `facture_fournisseur_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `facture_fournisseur_ibfk_3` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE ;


ALTER TABLE `facture_fournisseur_ligne`
	CHANGE `produit` `produit` varchar(256) NOT NULL;

ALTER TABLE `facture_ligne` CHANGE `produit` `produit` varchar(500)  COLLATE utf8_swedish_ci NOT NULL;


ALTER TABLE `loyer`
	ADD `type` enum('engagement','liberatoire')  NOT NULL DEFAULT 'engagement',
	ADD `avec_option` enum('oui','non')  NOT NULL DEFAULT 'non';


ALTER TABLE `magasin`
	CHANGE `magasin` `magasin` varchar(100)  NOT NULL after `id_magasin` ,
	ADD `site_associe` enum('toshiba')  NOT NULL after `magasin`;

CREATE TABLE `magasin_vendeur`(
	`id_magasin_vendeur` mediumint(8) unsigned NOT NULL  auto_increment ,
	`login` varchar(100) NOT NULL  ,
	`password` varchar(256) NOT NULL  ,
	`email` varchar(255) NOT NULL  ,
	`id_magasin` mediumint(8) unsigned NOT NULL  ,
	`etat` enum('actif','inactif') NOT NULL  DEFAULT 'actif' ,
	PRIMARY KEY (`id_magasin_vendeur`) ,
	KEY `id_magasin`(`id_magasin`) ,
	CONSTRAINT `magasin_vendeur_ibfk_1`
	FOREIGN KEY (`id_magasin`) REFERENCES `magasin` (`id_magasin`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


CREATE TABLE `transaction_banque`(
	`id_transaction_banque` mediumint(8) unsigned NOT NULL  auto_increment ,
	`id_affaire` mediumint(8) unsigned NOT NULL  ,
	`date` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP ,
	`data` text NOT NULL  ,
	`response_code` varchar(2) NOT NULL  ,
	`amount` varchar(10) NOT NULL  ,
	`transaction_id` varchar(10) NOT NULL  ,
	`merchant_id` varchar(20) NOT NULL  ,
	PRIMARY KEY (`id_transaction_banque`) ,
	KEY `id_affaire`(`id_affaire`) ,
	CONSTRAINT `id_affaire`
	FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `pack_produit_besoin`(
	`id_pack_produit_besoin` mediumint(8) unsigned NOT NULL  auto_increment ,
	`pack_produit_besoin` varchar(200) NOT NULL  ,
	`ordre` int(11) NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_pack_produit_besoin`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `pack_produit_produit`(
	`id_pack_produit_produit` mediumint(8) unsigned NOT NULL  auto_increment ,
	`pack_produit_produit` varchar(200) NOT NULL  ,
	`ordre` int(11) NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_pack_produit_produit`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `password_reset`(
	`id_password_reset` int(10) unsigned NOT NULL  auto_increment ,
	`code` varchar(62) NOT NULL  ,
	`expire` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP ,
	`id_magasin_vendeur` int(10) unsigned NOT NULL  ,
	`id_contact` mediumint(8) unsigned NULL  ,
	`enable` int(11) NOT NULL  ,
	PRIMARY KEY (`id_password_reset`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `pdf_societe`(
	`id_pdf_societe` mediumint(8) unsigned NOT NULL  auto_increment ,
	`id_societe` mediumint(8) unsigned NOT NULL  ,
	`nom_document` varchar(150) NOT NULL  ,
	PRIMARY KEY (`id_pdf_societe`) ,
	KEY `id_societe`(`id_societe`) ,
	CONSTRAINT `pdf_societe_ibfk_1`
	FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `processeur`(
	`id_processeur` mediumint(8) unsigned NOT NULL  auto_increment ,
	`processeur` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_processeur`)
) ENGINE=InnoDB DEFAULT CHARSET='latin1' COLLATE='latin1_swedish_ci';


CREATE TABLE `produit_besoins`(
	`id_produit_besoins` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_besoins` varchar(50) NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_besoins`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_dd`(
	`id_produit_dd` smallint(3) unsigned NOT NULL  auto_increment ,
	`produit_dd` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_dd`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_dotpitch`(
	`id_produit_dotpitch` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_dotpitch` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_dotpitch`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_env`(
	`id_produit_env` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_env` varchar(50) NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_env`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_format`(
	`id_produit_format` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_format` varchar(32) NOT NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_format`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_garantie`(
	`id_produit_garantie` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_garantie` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_garantie`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_lan`(
	`id_produit_lan` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_lan` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_lan`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_lecteur`(
	`id_produit_lecteur` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_lecteur` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_lecteur`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_os`(
	`id_produit_OS` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_OS` varchar(32) NOT NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_OS`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_puissance`(
	`id_produit_puissance` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_puissance` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_puissance`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_ram`(
	`id_produit_ram` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_ram` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_ram`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_technique`(
	`id_produit_technique` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_technique` varchar(32) NOT NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_technique`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_tel_produit`(
	`id_produit_tel_produit` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_tel_produit` varchar(50) NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_tel_produit`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_tel_type`(
	`id_produit_tel_type` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_tel_type` varchar(50) NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_tel_type`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_type`(
	`id_produit_type` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_type` varchar(32) NOT NULL  ,
	`ordre` int(10) unsigned NOT NULL  DEFAULT 0 ,
	PRIMARY KEY (`id_produit_type`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_typeecran`(
	`id_produit_typeecran` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_typeecran` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_typeecran`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';

CREATE TABLE `produit_viewable`(
	`id_produit_viewable` smallint(6) unsigned NOT NULL  auto_increment ,
	`produit_viewable` varchar(32) NOT NULL  ,
	PRIMARY KEY (`id_produit_viewable`)
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


ALTER TABLE `filtre_defaut`	ADD CONSTRAINT `filtre_defaut_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `filtre_optima`
	ADD CONSTRAINT `filtre_optima_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `filtre_optima_ibfk_2` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE ;





ALTER TABLE `pack_produit`
	ADD `nom` varchar(50) NOT NULL after `id_pack_produit` ,
	ADD `site_associe` enum('cleodis','top office','burger king','flunch','toshiba','btwin') NOT NULL after `nom` ,
	ADD `type_offre` enum('multimedia','atol','midas','bv','moa','domino','dafy','gifar','heytens','glastint','osilog-axa','atol-table-vente','atol-impression','atol-digital') NULL after `site_associe` ,
	ADD `loyer` float(8,2)   NULL after `type_offre` ,
	ADD `duree` int(8)   NULL after `loyer` ,
	ADD `frequence` enum('jour','mois','trimestre','semestre','an') NOT NULL DEFAULT 'mois' after `duree` ,
	ADD `visible_sur_site` enum('oui','non') NOT NULL DEFAULT 'non' after `etat` ,
	ADD `id_pack_produit_besoin` mediumint(8) unsigned   NULL after `visible_sur_site` ,
	ADD `id_pack_produit_produit` mediumint(8) unsigned   NULL after `id_pack_produit_besoin` ,
	ADD `url` varchar(255) NULL after `id_pack_produit_produit` ,
	ADD `avis_expert` text NULL after `description` ,
	ADD KEY `id_pack_produit_besoin`(`id_pack_produit_besoin`) ,
	ADD KEY `id_pack_produit_produit`(`id_pack_produit_produit`);

ALTER TABLE `pack_produit` ADD CONSTRAINT `pack_produit_ibfk_1` FOREIGN KEY (`id_pack_produit_besoin`) REFERENCES `pack_produit_besoin` (`id_pack_produit_besoin`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `pack_produit`	ADD CONSTRAINT `pack_produit_ibfk_2` FOREIGN KEY (`id_pack_produit_produit`) REFERENCES `pack_produit_produit` (`id_pack_produit_produit`) ON DELETE SET NULL ON UPDATE CASCADE ;


ALTER TABLE `pack_produit_ligne`
	ADD `min` int(11)   NOT NULL DEFAULT 0 after `quantite` ,
	ADD `max` int(11)   NOT NULL DEFAULT 0 after `min` ,
	ADD `option_incluse` enum('oui','non')  COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non' after `max` ,
	ADD `option_incluse_obligatoire` enum('oui','non')  COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui' after `option_incluse` ,
	ADD `id_partenaire` mediumint(8) unsigned   NULL after `id_fournisseur` ,
	ADD `visible_sur_pdf` enum('oui','non')  COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui' after `visible` ,
	ADD `ordre` int(11)   NULL after `commentaire` ,
	ADD KEY `id_partenaire`(`id_partenaire`) ;
ALTER TABLE `pack_produit_ligne`ADD CONSTRAINT `pack_produit_ligne_ibfk_1` FOREIGN KEY (`id_partenaire`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE ;


ALTER TABLE `produit`
	ADD `ref` varchar(32) NOT NULL after `id_produit` ,
	CHANGE `produit` `produit` varchar(500) NOT NULL after `ref` ,
	ADD `prix_achat` decimal(10,2) unsigned   NOT NULL after `produit` ,
	ADD `id_sous_categorie` mediumint(8) unsigned   NOT NULL after `id_fabriquant` ,
	ADD `type` enum('fixe','portable','sans_objet','immateriel') NOT NULL DEFAULT 'fixe' after `id_sous_categorie` ,
	ADD `id_fournisseur` mediumint(8) unsigned   NULL after `type` ,
	ADD `code` varchar(255) NULL after `id_fournisseur` ,
	ADD `obsolete` enum('oui','non') NOT NULL DEFAULT 'non' after `code` ,
	ADD `id_produit_dd` smallint(3) unsigned   NULL after `obsolete` ,
	ADD `id_produit_dotpitch` smallint(6) unsigned   NULL after `id_produit_dd` ,
	ADD `id_produit_format` smallint(6) unsigned   NULL after `id_produit_dotpitch` ,
	ADD `id_produit_garantie_uc` smallint(6) unsigned   NULL after `id_produit_format` ,
	ADD `id_produit_garantie_ecran` smallint(6) unsigned   NULL after `id_produit_garantie_uc` ,
	ADD `id_produit_garantie_imprimante` smallint(6) unsigned   NULL after `id_produit_garantie_ecran` ,
	ADD `id_produit_lan` smallint(6) unsigned   NULL after `id_produit_garantie_imprimante` ,
	ADD `id_produit_lecteur` smallint(6) unsigned   NULL after `id_produit_lan` ,
	ADD `id_produit_OS` smallint(6) unsigned   NULL after `id_produit_lecteur` ,
	ADD `id_produit_puissance` smallint(6) unsigned   NULL after `id_produit_OS` ,
	ADD `id_produit_ram` smallint(6) unsigned   NULL after `id_produit_puissance` ,
	ADD `id_produit_technique` smallint(6) unsigned   NULL after `id_produit_ram` ,
	ADD `id_produit_type` smallint(6) unsigned   NULL after `id_produit_technique` ,
	ADD `id_produit_typeecran` smallint(6) unsigned   NULL after `id_produit_type` ,
	ADD `id_produit_viewable` smallint(6) unsigned   NULL after `id_produit_typeecran` ,
	ADD `id_processeur` mediumint(8) unsigned   NULL after `id_produit_viewable` ,
	CHANGE `etat` `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif' after `id_processeur` ,
	ADD `commentaire` varchar(512) NULL after `etat` ,
	ADD `type_offre` enum('bureautique','informatique','telephonie','multimedia','atol','osilog-axa','services_complementaires_atol','services_complementaires_osilog-axa') NULL after `commentaire` ,
	CHANGE `description` `description` text NULL after `type_offre` ,
	ADD `site_associe` enum('cleodis','toshiba','btwin') NULL after `description` ,
	ADD `loyer` float(6,3)   NULL after `site_associe` ,
	ADD `duree` int(11)   NULL after `loyer` ,
	ADD `loyer1` float(6,3)   NULL after `duree` ,
	ADD `duree2` int(11)   NULL after `loyer1` ,
	ADD `duree1` int(11)   NULL after `duree2` ,
	ADD `loyer2` float(6,3)   NULL after `duree1` ,
	ADD `visible_sur_site` enum('oui','non') NOT NULL DEFAULT 'non' after `loyer2` ,
	ADD `avis_expert` text NULL after `visible_sur_site` ,
	ADD `services` set('installation_sur_site','evolutivite_offre','garantie_maintenance','intervention_site','support_utilisateur','reprise_recyclage') NULL after `avis_expert` ,
	ADD `id_produit_env` smallint(6) unsigned   NULL after `services` ,
	ADD `id_produit_besoins` smallint(6) unsigned   NULL after `id_produit_env` ,
	ADD `id_produit_tel_produit` smallint(6) unsigned   NULL after `id_produit_besoins` ,
	ADD `id_produit_tel_type` smallint(6) unsigned   NULL after `id_produit_tel_produit` ,
	ADD `url` varchar(255) NULL after `id_produit_tel_type` ,
	ADD `ean` varchar(14) NULL after `url` ,

	ADD UNIQUE KEY `ean`(`ean`) ,
	ADD KEY `id_fournisseur`(`id_fournisseur`) ,
	ADD KEY `id_processeur`(`id_processeur`) ,
	ADD KEY `id_produit_besoins`(`id_produit_besoins`) ,
	ADD KEY `id_produit_dd`(`id_produit_dd`) ,
	ADD KEY `id_produit_dotpitch`(`id_produit_dotpitch`) ,
	ADD KEY `id_produit_env`(`id_produit_env`) ,
	ADD KEY `id_produit_format`(`id_produit_format`) ,
	ADD KEY `id_produit_garantie_ecran`(`id_produit_garantie_ecran`) ,
	ADD KEY `id_produit_garantie_imprimante`(`id_produit_garantie_imprimante`) ,
	ADD KEY `id_produit_garantie_uc`(`id_produit_garantie_uc`) ,
	ADD KEY `id_produit_lan`(`id_produit_lan`) ,
	ADD KEY `id_produit_lecteur`(`id_produit_lecteur`) ,
	ADD KEY `id_produit_OS`(`id_produit_OS`) ,
	ADD KEY `id_produit_puissance`(`id_produit_puissance`) ,
	ADD KEY `id_produit_ram`(`id_produit_ram`) ,
	ADD KEY `id_produit_technique`(`id_produit_technique`) ,
	ADD KEY `id_produit_tel_produit`(`id_produit_tel_produit`) ,
	ADD KEY `id_produit_tel_type`(`id_produit_tel_type`) ,
	ADD KEY `id_produit_type`(`id_produit_type`) ,
	ADD KEY `id_produit_typeecran`(`id_produit_typeecran`) ,
	ADD KEY `id_produit_viewable`(`id_produit_viewable`) ,
	ADD KEY `id_sous_categorie`(`id_sous_categorie`) ,
	ADD UNIQUE KEY `ref`(`ref`);

ALTER TABLE `produit`
	ADD CONSTRAINT `produit_ibfk_10` FOREIGN KEY (`id_produit_lecteur`) REFERENCES `produit_lecteur` (`id_produit_lecteur`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_11` FOREIGN KEY (`id_produit_OS`) REFERENCES `produit_os` (`id_produit_OS`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_12` FOREIGN KEY (`id_produit_puissance`) REFERENCES `produit_puissance` (`id_produit_puissance`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_13` FOREIGN KEY (`id_produit_ram`) REFERENCES `produit_ram` (`id_produit_ram`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_14` FOREIGN KEY (`id_produit_technique`) REFERENCES `produit_technique` (`id_produit_technique`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_15` FOREIGN KEY (`id_produit_type`) REFERENCES `produit_type` (`id_produit_type`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_16` FOREIGN KEY (`id_produit_typeecran`) REFERENCES `produit_typeecran` (`id_produit_typeecran`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_17` FOREIGN KEY (`id_produit_viewable`) REFERENCES `produit_viewable` (`id_produit_viewable`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_19` FOREIGN KEY (`id_processeur`) REFERENCES `processeur` (`id_processeur`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`id_sous_categorie`) REFERENCES `sous_categorie` (`id_sous_categorie`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_20` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_21` FOREIGN KEY (`id_produit_dd`) REFERENCES `produit_dd` (`id_produit_dd`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_22` FOREIGN KEY (`id_produit_dotpitch`) REFERENCES `produit_dotpitch` (`id_produit_dotpitch`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_23` FOREIGN KEY (`id_produit_format`) REFERENCES `produit_format` (`id_produit_format`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_24` FOREIGN KEY (`id_produit_garantie_uc`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_25` FOREIGN KEY (`id_produit_garantie_ecran`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_26` FOREIGN KEY (`id_produit_garantie_imprimante`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_27` FOREIGN KEY (`id_produit_lan`) REFERENCES `produit_lan` (`id_produit_lan`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_28` FOREIGN KEY (`id_produit_lecteur`) REFERENCES `produit_lecteur` (`id_produit_lecteur`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_29` FOREIGN KEY (`id_produit_OS`) REFERENCES `produit_os` (`id_produit_OS`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_2` FOREIGN KEY (`id_fabriquant`) REFERENCES `fabriquant` (`id_fabriquant`) ON DELETE CASCADE ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_30` FOREIGN KEY (`id_produit_puissance`) REFERENCES `produit_puissance` (`id_produit_puissance`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_31` FOREIGN KEY (`id_produit_ram`) REFERENCES `produit_ram` (`id_produit_ram`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_32` FOREIGN KEY (`id_produit_technique`) REFERENCES `produit_technique` (`id_produit_technique`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_33` FOREIGN KEY (`id_produit_type`) REFERENCES `produit_type` (`id_produit_type`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_34` FOREIGN KEY (`id_produit_typeecran`) REFERENCES `produit_typeecran` (`id_produit_typeecran`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_35` FOREIGN KEY (`id_produit_viewable`) REFERENCES `produit_viewable` (`id_produit_viewable`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_36` FOREIGN KEY (`id_processeur`) REFERENCES `processeur` (`id_processeur`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_37` FOREIGN KEY (`id_produit_env`) REFERENCES `produit_env` (`id_produit_env`) ON DELETE SET NULL ON UPDATE SET NULL ,
	ADD CONSTRAINT `produit_ibfk_38` FOREIGN KEY (`id_produit_besoins`) REFERENCES `produit_besoins` (`id_produit_besoins`) ON DELETE SET NULL ON UPDATE SET NULL ,
	ADD CONSTRAINT `produit_ibfk_39` FOREIGN KEY (`id_produit_tel_type`) REFERENCES `produit_tel_type` (`id_produit_tel_type`) ON DELETE SET NULL ON UPDATE SET NULL ,
	ADD CONSTRAINT `produit_ibfk_3` FOREIGN KEY (`id_produit_dd`) REFERENCES `produit_dd` (`id_produit_dd`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_40` FOREIGN KEY (`id_produit_tel_produit`) REFERENCES `produit_tel_produit` (`id_produit_tel_produit`) ON DELETE SET NULL ON UPDATE SET NULL ,
	ADD CONSTRAINT `produit_ibfk_4` FOREIGN KEY (`id_produit_dotpitch`) REFERENCES `produit_dotpitch` (`id_produit_dotpitch`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_5` FOREIGN KEY (`id_produit_format`) REFERENCES `produit_format` (`id_produit_format`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_6` FOREIGN KEY (`id_produit_garantie_uc`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_7` FOREIGN KEY (`id_produit_garantie_ecran`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_8` FOREIGN KEY (`id_produit_garantie_imprimante`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `produit_ibfk_9` FOREIGN KEY (`id_produit_lan`) REFERENCES `produit_lan` (`id_produit_lan`) ON UPDATE CASCADE ;



ALTER TABLE `societe`
	ADD `langue` enum('FR','NL')  COLLATE utf8_general_ci NOT NULL DEFAULT 'FR' after `id_pays` ,
	ADD `id_famille` tinyint(3) unsigned   NOT NULL DEFAULT 2 after `langue` ,
	CHANGE `siren` `siren` varchar(9)  COLLATE utf8_general_ci NULL after `id_famille` ,
	CHANGE `societe` `societe` varchar(128)  COLLATE utf8_general_ci NOT NULL after `naf` ,
	CHANGE `adresse` `adresse` varchar(64)  COLLATE utf8_general_ci NULL after `nom_commercial` ,
	ADD `ville_rcs` varchar(50)  COLLATE utf8_general_ci NULL after `reference_tva` ,
	CHANGE `tva` `tva` decimal(4,3)   NOT NULL DEFAULT 1.200 after `ville_rcs` ,
	ADD `revendeur` enum('oui','non')  COLLATE utf8_general_ci NOT NULL DEFAULT 'non' after `partenaire` ,
	CHANGE `code_fournisseur` `code_fournisseur` varchar(32)  COLLATE utf8_general_ci NULL after `revendeur` ,
	ADD `code_client_partenaire` varchar(255)  COLLATE utf8_general_ci NULL after `code_client` ,
	CHANGE `divers_2` `divers_2` enum('prelevement','mandat','virement','cheque') NULL DEFAULT 'prelevement' after `code_client_partenaire` ,
	CHANGE `divers_3` `divers_3` enum('Midas','Optic_2000','Norauto','Atol','-') NOT NULL DEFAULT '-' after `divers_2` ,
	ADD `RUM` varchar(32)  COLLATE utf8_general_ci NULL after `BIC` ,
	CHANGE `recallCounter` `recallCounter` tinyint(3) unsigned   NOT NULL DEFAULT 0 COMMENT 'Compteur pour le rappel de prospection par mobile' after `RUM` ,
	ADD `fournisseur_delai_rav` int(11)   NULL COMMENT 'Date limite avant RAV (NbJ negatif)' after `ville_banque` ,
	ADD `fournisseur_delai_livraison` int(11)   NULL COMMENT 'Date limite livraison (NbJ negatif)' after `fournisseur_delai_rav` ,
	ADD `fournisseur_delai_installation` int(11)   NULL COMMENT 'Date limite installation (NbJ negatif)' after `fournisseur_delai_livraison` ,
	ADD `fournisseur_arav_orange` int(11)   NULL COMMENT 'ARAV orange (NbJ negatif)' after `fournisseur_delai_installation` ,
	ADD `fournisseur_arav_rouge` int(11)   NULL COMMENT 'ARAV rouge (NbJ negatif)' after `fournisseur_arav_orange` ,
	ADD `fournisseur_nbj_livraison` int(10) unsigned   NULL COMMENT 'Nombre de jour necessaire pour que le fournisseur livre' after `fournisseur_arav_rouge` ,
	ADD `fournisseur_nbj_installation` int(10) unsigned   NULL COMMENT 'Nombre de jour necessaire pour que le fournisseur installe' after `fournisseur_nbj_livraison` ,
	CHANGE `lastaccountdate` `lastaccountdate` varchar(20)  COLLATE utf8_general_ci NULL after `fournisseur_nbj_installation` ,
	ADD `verriers` enum('ESSILOR','ZEISS','BBGR','AUTRE')  COLLATE utf8_general_ci NULL after `financialcharges` ,
	ADD `typologie` enum('association','commune','divers','education','pme','prof_lib','public','sante')  COLLATE utf8_general_ci NULL after `verriers` ,
	ADD `lead` varchar(32)  COLLATE utf8_general_ci NULL after `typologie` ,
	ADD `particulier_civilite` enum('M','Mme','Mlle')  COLLATE utf8_general_ci NULL after `lead` ,
	ADD `particulier_nom` varchar(50)  COLLATE utf8_general_ci NULL after `particulier_civilite` ,
	ADD `particulier_prenom` varchar(50)  COLLATE utf8_general_ci NULL after `particulier_nom` ,
	ADD `particulier_portable` varchar(15)  COLLATE utf8_general_ci NULL after `particulier_prenom` ,
	ADD `num_carte_fidelite` varchar(50)  COLLATE utf8_general_ci NULL after `particulier_portable` ,
	ADD `dernier_magasin` varchar(50)  COLLATE utf8_general_ci NULL after `num_carte_fidelite` ,
	ADD `optin_offre_commerciales` enum('oui','non')  COLLATE utf8_general_ci NULL after `dernier_magasin` ,
	ADD `optin_offre_commerciale_partenaire` enum('oui','non')  COLLATE utf8_general_ci NULL after `optin_offre_commerciales` ,
	ADD `particulier_fixe` varchar(20)  COLLATE utf8_general_ci NULL after `optin_offre_commerciale_partenaire` ,
	ADD `particulier_fax` varchar(20)  COLLATE utf8_general_ci NULL after `particulier_fixe` ,
	ADD `particulier_email` varchar(255)  COLLATE utf8_general_ci NULL after `particulier_fax` ,
	ADD `sms` varchar(6)  COLLATE utf8_general_ci NULL COMMENT 'Code SMS pour confirmer l\'identité d\'un particulier' after `particulier_email` ,
	ADD `sms_validite` timestamp   NULL after `sms` ,
	ADD `sms_tentative` tinyint(4)   NOT NULL DEFAULT 0 after `sms_validite` ,
	ADD KEY `id_famille`(`id_famille`) ,
	ADD UNIQUE KEY `lead`(`lead`) ,
	ADD KEY `revendeur`(`revendeur`) ,
	ADD KEY `sms`(`sms`) ,
	ADD UNIQUE KEY `societe`(`societe`,`adresse`) ;
ALTER TABLE `societe`
	ADD CONSTRAINT `societe_ibfk_10` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_11` FOREIGN KEY (`id_devise`) REFERENCES `devise` (`id_devise`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_12` FOREIGN KEY (`id_filiale`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_13` FOREIGN KEY (`id_accompagnateur`) REFERENCES `accompagnateur` (`id_accompagnateur`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_14` FOREIGN KEY (`id_contact_signataire`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_15` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_16` FOREIGN KEY (`id_famille`) REFERENCES `famille` (`id_famille`) ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_17` FOREIGN KEY (`id_contact_facturation`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_18` FOREIGN KEY (`id_secteur_geographique`) REFERENCES `secteur_geographique` (`id_secteur_geographique`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_19` FOREIGN KEY (`id_campagne`) REFERENCES `campagne` (`id_campagne`) ON DELETE SET NULL ON UPDATE CASCADE ,
	ADD CONSTRAINT `societe_ibfk_20` FOREIGN KEY (`id_apporteur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE ;


ALTER TABLE `suivi`
	CHANGE `origine` `origine` enum('societe_devis','societe_commande','societe_location','notification_slimpay','portail_presta')  NULL DEFAULT 'societe_devis' COMMENT 'endroit ou le suivi a Ã©tÃ© crÃ©Ã©' after `id_societe` ,
	CHANGE `type` `type` enum('note','fichier','RDV','appel','courrier','prestataire') NOT NULL DEFAULT 'note' after `origine` ,
	CHANGE `type_suivi` `type_suivi` enum('Devis','Contrat','Refinancement','Comptabilité','Broke','Contentieux','Mis en place','Restitution','Autre','Prolongation','Resiliation','Sinistre','Transfert','Fournisseur','Requête','BDC','Flottes','Installation','Passage_comite','demande_comite','Audit en cours','Assurance','Formation','Maintenance','Livraison','Commentaire') NULL after `id_affaire` ;

