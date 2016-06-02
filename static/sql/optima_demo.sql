CREATE TABLE `affaire` (
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('devis','commande','facture','terminee','perdue') CHARACTER SET latin1 NOT NULL DEFAULT 'devis',
  `date` datetime DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `affaire` varchar(255) CHARACTER SET latin1 NOT NULL,
  `forecast` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `date_fin_maintenance` date DEFAULT NULL,
  `contrat_maintenance` text DEFAULT NULL,
  `id_termes` tinyint(3) UNSIGNED DEFAULT 1,
  `code_commande_client` varchar(128) DEFAULT NULL COMMENT 'Numéro de commande propre au client pour les grands comptes (ex Kiloutou)',
  `id_commercial` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Responsable de l''affaire',
  `rappel_annee` tinyint(3) UNSIGNED DEFAULT NULL,
  `jours_inclus` decimal(3,1) DEFAULT NULL,
  `nature` enum('vente','consommable') NOT NULL DEFAULT 'vente',
  `suivi_ec` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Flag d''activation du suivi sur l''espace client',
  `date_fin` datetime DEFAULT NULL COMMENT 'Date prévisionnel de fin de l''affaire'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE `agence` (
  `id_agence` mediumint(8) UNSIGNED NOT NULL,
  `agence` varchar(64) NOT NULL,
  `adresse` varchar(64) DEFAULT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(8) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `id_pays` char(2) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `bon_de_commande` (
  `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) NOT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `resume` varchar(200) NOT NULL,
  `prix_achat` decimal(10,2) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `etat` enum('en_cours','recu') NOT NULL DEFAULT 'en_cours',
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_fin` timestamp NULL DEFAULT NULL,
  `id_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL,
  `frais_de_port` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bon_de_commande_ligne` (
  `id_bon_de_commande_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `produit` varchar(768) NOT NULL,
  `quantite` decimal(7,1) UNSIGNED NOT NULL,
  `prix` decimal(10,2) UNSIGNED NOT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `tva` decimal(4,3) UNSIGNED NOT NULL,
  `etat` enum('en_cours','recu') NOT NULL DEFAULT 'en_cours'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `categorie` (
  `id_categorie` mediumint(8) UNSIGNED NOT NULL,
  `categorie` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `commande` (
  `id_commande` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(16) CHARACTER SET utf8 NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `resume` varchar(200) DEFAULT NULL,
  `prix_achat` decimal(10,2) UNSIGNED DEFAULT NULL,
  `prix` decimal(10,2) UNSIGNED DEFAULT 0.00,
  `date` date DEFAULT NULL,
  `id_devis` mediumint(8) UNSIGNED DEFAULT NULL,
  `etat` enum('en_cours','facturee','annulee') CHARACTER SET utf8 NOT NULL DEFAULT 'en_cours',
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL,
  `frais_de_port` decimal(10,2) UNSIGNED DEFAULT 0.00,
  `divers_1` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `commande_facture` (
  `id_commande_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_commande` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `commande_ligne` (
  `id_commande_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
  `serial` varchar(512) CHARACTER SET utf8 DEFAULT NULL,
  `produit` varchar(2048) COLLATE utf8_swedish_ci NOT NULL,
  `quantite` decimal(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `prix` decimal(10,2) NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `id_compte_absystech` mediumint(8) UNSIGNED NOT NULL DEFAULT 9,
  `prix_nb` float(7,5) DEFAULT NULL,
  `prix_couleur` float(7,5) DEFAULT NULL,
  `prix_achat_nb` float(7,5) DEFAULT NULL,
  `prix_achat_couleur` float(7,5) DEFAULT NULL,
  `index_nb` int(11) DEFAULT NULL,
  `index_couleur` int(11) DEFAULT NULL,
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `constante` (
  `id_constante` smallint(5) UNSIGNED NOT NULL,
  `constante` varchar(128) NOT NULL,
  `valeur` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `contact` (
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `civilite` enum('M','Mme','Mlle') CHARACTER SET utf8 DEFAULT 'M',
  `nom` varchar(32) CHARACTER SET utf8 NOT NULL,
  `prenom` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `private` enum('oui','non') NOT NULL DEFAULT 'non',
  `adresse` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_2` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_3` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cp` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `ville` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `id_pays` varchar(2) NOT NULL DEFAULT 'FR',
  `tel` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `gsm` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `fonction` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `departement` varchar(255) DEFAULT NULL,
  `anniversaire` date DEFAULT NULL,
  `loisir` varchar(255) DEFAULT NULL,
  `langue` varchar(255) DEFAULT NULL,
  `assistant` varchar(128) DEFAULT NULL,
  `assistant_tel` varchar(32) DEFAULT NULL,
  `tel_autres` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_autres` varchar(255) DEFAULT NULL,
  `forecast` enum('0','20','40','60','80','100') NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `cle_externe` varchar(32) DEFAULT NULL,
  `disponibilite` set('LunAM','LunPM','MarAM','MarPM','MerAM','MerPM','JeuAM','JeuPM','VenAM','VenPM','SamAM','SamPM') DEFAULT NULL,
  `login` varchar(15) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Login espace client',
  `pwd` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Mot de passe espace client'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `devis` (
  `id_devis` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(16) CHARACTER SET utf8 NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `type_devis` enum('normal','location','consommable') NOT NULL DEFAULT 'normal',
  `prix` decimal(8,2) UNSIGNED DEFAULT 0.00,
  `prix_achat` decimal(8,2) UNSIGNED DEFAULT 0.00,
  `date` datetime DEFAULT NULL,
  `resume` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `revision` enum('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z') CHARACTER SET utf8 NOT NULL DEFAULT 'A',
  `etat` enum('gagne','attente','perdu','annule','bloque','remplace') CHARACTER SET utf8 DEFAULT 'attente' COMMENT 'bloque=montant elevé, attente supérieur | remplace=par un autre devis',
  `cause_perdu` text CHARACTER SET utf8 DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_opportunite` mediumint(8) UNSIGNED DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL,
  `id_politesse_pref` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_politesse_post` mediumint(8) UNSIGNED DEFAULT NULL,
  `validite` date NOT NULL,
  `frais_de_port` decimal(10,2) UNSIGNED DEFAULT 0.00,
  `divers_1` varchar(128) DEFAULT NULL,
  `id_user_technique` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_user_admin` mediumint(8) UNSIGNED DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'permet de dater la dernière modif de l''état du devis',
  `id_remplacant` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Devis qui remplace celui-ci, utile pour éviter de compter comme persu un devis qui est en fait absorbé par un autre',
  `mail` varchar(100) DEFAULT NULL,
  `mail_copy` varchar(100) DEFAULT NULL,
  `mail_text` text DEFAULT NULL,
  `id_delai_de_realisation` tinyint(3) UNSIGNED DEFAULT NULL,
  `duree_financement` tinyint(3) UNSIGNED DEFAULT NULL,
  `cout_total_financement` decimal(10,3) UNSIGNED DEFAULT NULL,
  `maintenance_financement` decimal(5,2) DEFAULT NULL,
  `financement_cleodis` enum('oui','non') NOT NULL DEFAULT 'oui',
  `acompte` decimal(5,2) DEFAULT NULL,
  `duree_location` int(11) DEFAULT NULL,
  `prix_location` float(15,2) DEFAULT NULL,
  `duree_contrat_cout_copie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

CREATE TABLE `devise` (
  `id_devise` mediumint(8) UNSIGNED NOT NULL,
  `devise` varchar(32) DEFAULT NULL,
  `symbole` varchar(8) DEFAULT NULL,
  `ratio_eur` float UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `devis_ligne` (
  `id_devis_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `produit` varchar(2048) NOT NULL,
  `quantite` decimal(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `prix` decimal(10,2) DEFAULT NULL,
  `periode` enum('mois','trimestre','semestre','an') DEFAULT NULL,
  `poids` decimal(8,2) DEFAULT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `id_compte_absystech` mediumint(8) UNSIGNED DEFAULT 9,
  `prix_nb` float(7,5) DEFAULT NULL,
  `prix_couleur` float(7,5) DEFAULT NULL,
  `prix_achat_nb` float(7,5) DEFAULT NULL,
  `prix_achat_couleur` float(7,5) DEFAULT NULL,
  `index_nb` int(11) DEFAULT NULL,
  `index_couleur` int(11) DEFAULT NULL,
  `visible` enum('oui','non') NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `facture` (
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `type_facture` enum('facture','solde','avoir','acompte','factor','facture_periodique') DEFAULT 'facture',
  `ref` varchar(16) CHARACTER SET utf8 NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '= prix HT + frais de port',
  `etat` enum('payee','impayee','perte') CHARACTER SET utf8 NOT NULL DEFAULT 'impayee',
  `id_termes` tinyint(3) UNSIGNED DEFAULT NULL,
  `date_previsionnelle` date DEFAULT NULL,
  `date_effective` date DEFAULT NULL,
  `date_relance` date DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL,
  `frais_de_port` decimal(10,2) UNSIGNED DEFAULT 0.00,
  `divers_1` varchar(128) DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL COMMENT 'permet de dater la dernière modif de l''état de la facture',
  `infosSup` varchar(100) DEFAULT NULL,
  `date_debut_periode` date DEFAULT NULL,
  `date_fin_periode` date DEFAULT NULL,
  `periodicite` enum('mensuelle','trimestrielle','annuelle','semestrielle') DEFAULT NULL,
  `id_facture_parente` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_echeancier` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_export_comptable` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `facture_fournisseur` (
  `id_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_bon_de_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` date DEFAULT NULL,
  `OCR` longtext DEFAULT NULL,
  `achat` enum('oui','non') NOT NULL DEFAULT 'non',
  `fourniture` enum('oui','non') NOT NULL DEFAULT 'non',
  `immo` enum('oui','non') NOT NULL DEFAULT 'non',
  `frais_generaux` enum('oui','non') NOT NULL DEFAULT 'non',
  `montant_ht` float DEFAULT NULL,
  `statut` enum('ok_paye_pour','litige','paye','attente') NOT NULL DEFAULT 'attente',
  `date_paiement` date DEFAULT NULL,
  `nb_page` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `facture_fournisseur_affaire` (
  `id_facture_fournisseur_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `nb_produit` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `facture_ligne` (
  `id_facture_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) COLLATE utf8_swedish_ci NOT NULL,
  `produit` varchar(2048) COLLATE utf8_swedish_ci NOT NULL,
  `quantite` decimal(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `prix` decimal(10,2) NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `serial` varchar(512) COLLATE utf8_swedish_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_compte_absystech` mediumint(8) UNSIGNED NOT NULL DEFAULT 9,
  `prix_nb` float(7,5) DEFAULT NULL,
  `prix_couleur` float(7,5) DEFAULT NULL,
  `prix_achat_nb` float(7,5) DEFAULT NULL,
  `prix_achat_couleur` float(7,5) DEFAULT NULL,
  `index_nb` int(11) DEFAULT NULL,
  `index_couleur` int(11) DEFAULT NULL,
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

CREATE TABLE `facture_paiement` (
  `id_facture_paiement` mediumint(8) NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement` enum('cheque','virement','lettre_de_change','paypal','prelevement','lettrage','OD','factor','perte','espece','avoir') NOT NULL DEFAULT 'cheque',
  `date` date NOT NULL,
  `remarques` varchar(255) DEFAULT NULL,
  `num_cheque` varchar(128) DEFAULT NULL,
  `num_compte` varchar(128) DEFAULT NULL,
  `num_bordereau` varchar(128) DEFAULT NULL,
  `id_facture_avoir` mediumint(8) UNSIGNED DEFAULT NULL,
  `montant_interet` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `facture_parente` (
  `id_facture_parente` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_parente` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `filtre_defaut` (
  `id_filtre_defaut` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `div` varchar(128) NOT NULL,
  `filter_key` varchar(32) DEFAULT NULL,
  `order` varchar(256) DEFAULT NULL,
  `page` tinyint(3) UNSIGNED DEFAULT NULL,
  `limit` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `filtre_optima` (
  `id_filtre_optima` mediumint(8) UNSIGNED NOT NULL,
  `filtre_optima` varchar(32) NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `options` text NOT NULL,
  `type` enum('public','prive') NOT NULL DEFAULT 'public'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `filtre_user` (
  `id_filtre_user` mediumint(8) UNSIGNED NOT NULL,
  `id_filtre_optima` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `frais_kilometrique` (
  `id_frais_kilometrique` mediumint(8) UNSIGNED NOT NULL,
  `annee` year(4) NOT NULL,
  `cv` mediumint(8) NOT NULL,
  `coeff` decimal(4,3) NOT NULL,
  `type` enum('auto','moto','cyclo') NOT NULL DEFAULT 'auto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `livraison` (
  `id_livraison` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(16) CHARACTER SET utf8 NOT NULL,
  `livraison` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_devis` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_transporteur` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_expediteur` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'id_user de celui qui va livrer',
  `etat` enum('en_cours','termine','termine_partiel') CHARACTER SET utf8 NOT NULL DEFAULT 'en_cours' COMMENT 'etat de la livraison',
  `code_de_tracabilite` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `livraison_ligne` (
  `id_livraison_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_livraison` mediumint(8) UNSIGNED NOT NULL,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('en_cours_de_livraison','endommage','perdu','termine') NOT NULL DEFAULT 'en_cours_de_livraison'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mission` (
  `id_mission` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `tel_contact` varchar(20) DEFAULT NULL,
  `id_salarie` mediumint(8) UNSIGNED NOT NULL COMMENT '<> id_user',
  `ref` varchar(16) DEFAULT NULL,
  `date_odm` date NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `id_responsable_activite` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_responsable_site` mediumint(8) UNSIGNED DEFAULT NULL,
  `type` enum('ETAM','IC') NOT NULL DEFAULT 'ETAM',
  `prix` varchar(50) NOT NULL,
  `mission` varchar(255) NOT NULL,
  `prestation` varchar(255) NOT NULL,
  `id_pays` char(2) NOT NULL DEFAULT 'FR',
  `horaire_client` decimal(10,2) NOT NULL DEFAULT 35.00,
  `site` varchar(50) DEFAULT NULL,
  `lieu_affectation` varchar(50) DEFAULT NULL,
  `commentaire` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `module` (
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_parent` mediumint(8) UNSIGNED DEFAULT NULL,
  `module` varchar(40) NOT NULL,
  `abstrait` tinyint(1) DEFAULT 0,
  `priorite` tinyint(3) UNSIGNED DEFAULT 0,
  `visible` tinyint(3) UNSIGNED DEFAULT 1 COMMENT '2=target _blank',
  `import` tinyint(1) UNSIGNED DEFAULT 0 COMMENT 'on peut ou non importer des éléments pour ce module',
  `couleur_fond` char(6) DEFAULT 'FFFFFF',
  `couleur_texte` char(6) DEFAULT '000000',
  `couleur` enum('red','green','brown','yellow','blue','purple') NOT NULL DEFAULT 'green',
  `description` varchar(512) DEFAULT NULL,
  `construct` text CHARACTER SET utf8 DEFAULT NULL COMMENT 'Sert de constructeur par défaut si aucune classe n''existe pour ce module (desactive mais en commentaire)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `module_privilege` (
  `id_module_privilege` mediumint(8) UNSIGNED NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_privilege` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `privilege` (
  `id_privilege` smallint(5) UNSIGNED NOT NULL,
  `privilege` varchar(32) NOT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Privileges pour les droits des profils';

CREATE TABLE `profil` (
  `id_profil` tinyint(3) UNSIGNED NOT NULL,
  `profil` varchar(256) NOT NULL,
  `seuil` int(4) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `profil_privilege` (
  `id_profil_privilege` mediumint(8) UNSIGNED NOT NULL,
  `id_profil` tinyint(3) UNSIGNED NOT NULL,
  `id_privilege` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Privilège associé (action ou ressource distincte) NULL si tous les privilèges',
  `id_module` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Module sollicité, NULL si cela concerne autre chose',
  `field` varchar(32) DEFAULT NULL COMMENT 'Champ particulier du module spécifié'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Droits sur les ressources par profil';

CREATE TABLE `societe` (
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `code_groupe` varchar(32) DEFAULT NULL,
  `ref` varchar(11) DEFAULT NULL,
  `ref_comptable` varchar(12) DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_pays` char(2) NOT NULL DEFAULT 'FR',
  `id_famille` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `siren` varchar(9) DEFAULT NULL,
  `siret` varchar(32) DEFAULT NULL,
  `naf` varchar(5) DEFAULT NULL,
  `societe` varchar(128) NOT NULL,
  `nom_commercial` varchar(64) DEFAULT NULL,
  `adresse` varchar(64) DEFAULT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `ville` varchar(32) DEFAULT NULL,
  `id_contact_facturation` mediumint(8) UNSIGNED DEFAULT NULL,
  `facturation_id_pays` varchar(2) DEFAULT 'FR',
  `facturation_adresse` varchar(64) DEFAULT NULL,
  `facturation_adresse_2` varchar(64) DEFAULT NULL,
  `facturation_adresse_3` varchar(64) DEFAULT NULL,
  `facturation_cp` varchar(5) DEFAULT NULL,
  `facturation_ville` varchar(27) DEFAULT NULL,
  `reference_tva` varchar(24) DEFAULT NULL,
  `iban` varchar(31) DEFAULT NULL,
  `id_termes` tinyint(3) UNSIGNED DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `web` varchar(128) DEFAULT NULL,
  `activite` varchar(255) DEFAULT NULL,
  `etat` enum('actif','douteux','inactif') CHARACTER SET latin1 NOT NULL DEFAULT 'actif',
  `nb_employe` mediumint(8) DEFAULT NULL,
  `effectif` enum('1','10','50','100','500','1000') DEFAULT NULL,
  `id_secteur_geographique` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact_commercial` mediumint(8) DEFAULT NULL,
  `id_secteur_commercial` mediumint(8) UNSIGNED DEFAULT NULL,
  `liens` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `ca` varchar(32) DEFAULT NULL,
  `id_devise` mediumint(8) UNSIGNED NOT NULL DEFAULT 1,
  `solde` decimal(16,2) NOT NULL DEFAULT 0.00,
  `structure` varchar(64) DEFAULT NULL,
  `capital` bigint(20) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `id_filiale` mediumint(8) UNSIGNED DEFAULT NULL,
  `facturer_le_siege` enum('oui','non') NOT NULL DEFAULT 'non',
  `notes` text DEFAULT NULL,
  `fournisseur` enum('oui','non') CHARACTER SET latin1 NOT NULL DEFAULT 'non',
  `partenaire` enum('oui','non') CHARACTER SET latin1 NOT NULL DEFAULT 'non',
  `delai_relance` tinyint(3) UNSIGNED DEFAULT NULL,
  `code_fournisseur` varchar(32) DEFAULT NULL,
  `cle_externe` varchar(32) DEFAULT NULL,
  `divers_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `divers_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL COMMENT 'mot de passe pour le portail hotline',
  `relation` enum('prospect','client','suspect') NOT NULL DEFAULT 'prospect',
  `banque` varchar(64) DEFAULT NULL COMMENT 'Nom de la banque correspondant aux rib et bic de facturation',
  `rib` varchar(32) DEFAULT NULL COMMENT 'RIB de facturation',
  `bic` varchar(32) DEFAULT NULL COMMENT 'BIC de facturation',
  `swift` varchar(32) DEFAULT NULL COMMENT 'Code SWIFT pour facturation',
  `meteo` decimal(10,4) DEFAULT NULL,
  `meteo_calcul` varchar(256) DEFAULT NULL,
  `rib_affacturage` varchar(32) DEFAULT NULL,
  `iban_affacturage` varchar(35) DEFAULT NULL,
  `bic_affacturage` varchar(32) DEFAULT NULL,
  `mdp_client` varchar(32) DEFAULT NULL COMMENT 'Mot de passe du client chez le client',
  `mdp_absystech` varchar(32) DEFAULT NULL COMMENT 'Mot de passe absystech chez le client',
  `recallCounter` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Compteur pour le rappel de prospection par mobile',
  `dematerialisation` enum('oui','non') NOT NULL DEFAULT 'non',
  `id_apporteur_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `derogation_credit_negatif` enum('oui','non') NOT NULL DEFAULT 'non',
  `forfait_dep` decimal(4,2) NOT NULL DEFAULT 1.00,
  `est_sous_contrat_maintenance` enum('sous_contrat','sous_contrat_partiel','sans_contrat') DEFAULT NULL,
  `commentaire_contrat_maintenance` text DEFAULT NULL,
  `option_contrat_maintenance` enum('aucune','a_caliner','nouvelle_installation') NOT NULL DEFAULT 'aucune',
  `date_fin_contrat_maintenance` date DEFAULT NULL,
  `date_fin_option` date DEFAULT NULL,
  `id_commercial` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `societe_domaine` (
  `id_societe_domaine` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_domaine` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `societe_frais_port` (
  `id_societe_frais_port` mediumint(8) UNSIGNED NOT NULL,
  `borne1` decimal(8,2) DEFAULT NULL,
  `borne2` decimal(8,2) DEFAULT NULL,
  `prix` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `sous_categorie` (
  `id_sous_categorie` mediumint(8) UNSIGNED NOT NULL,
  `id_categorie` mediumint(8) UNSIGNED NOT NULL,
  `sous_categorie` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `suivi` (
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `type` enum('tel','email','reunion','note') CHARACTER SET utf8 NOT NULL DEFAULT 'tel',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `texte` text CHARACTER SET utf8 NOT NULL,
  `id_opportunite` mediumint(8) UNSIGNED DEFAULT NULL,
  `temps_passe` time NOT NULL DEFAULT '00:00:00',
  `ponderation` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `suivi_contact` (
  `id_suivi_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre contact et suivi';

CREATE TABLE `suivi_notifie` (
  `id_suivi_notifie` mediumint(8) UNSIGNED NOT NULL,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `suivi_societe` (
  `id_suivi_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre user et suivi';

CREATE TABLE `tache` (
  `id_tache` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_suivi` mediumint(8) UNSIGNED DEFAULT NULL,
  `tache` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `horaire_debut` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `horaire_fin` datetime NOT NULL,
  `date_validation` datetime DEFAULT NULL,
  `etat` enum('en_cours','fini','annule') NOT NULL DEFAULT 'en_cours',
  `id_aboutisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `type` enum('vevent','vtodo') NOT NULL DEFAULT 'vtodo',
  `lieu` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `priorite` enum('pas_specifie','petite','moyenne','grande') NOT NULL DEFAULT 'pas_specifie',
  `complete` enum('0','20','40','60','80','100') NOT NULL DEFAULT '0',
  `periodique` enum('hebdomadaire','mensuel','trimestriel','annuel') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='id_user = créateur !!!';

CREATE TABLE `tache_user` (
  `id_tache_user` mediumint(8) UNSIGNED NOT NULL,
  `id_tache` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre tache et user, permet d''assigner une';

CREATE TABLE `tracabilite` (
  `id_tracabilite` mediumint(8) UNSIGNED NOT NULL,
  `tracabilite` enum('insert','update','delete') NOT NULL DEFAULT 'insert',
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_module` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_element` mediumint(8) UNSIGNED DEFAULT NULL,
  `nom_element` varchar(256) DEFAULT NULL,
  `avant_modification` longtext DEFAULT NULL,
  `modification` longtext DEFAULT NULL COMMENT 'Enregistrement de ce qui a été modifié',
  `id_tracabilite_parent` mediumint(8) UNSIGNED DEFAULT NULL,
  `rollback` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'détermine si l''on a fait un rollback sur cette trace'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `user` (
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `login` varchar(16) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `date_connection` datetime DEFAULT NULL,
  `date_activity` datetime DEFAULT NULL,
  `etat` enum('normal','inactif') NOT NULL DEFAULT 'normal',
  `id_profil` tinyint(3) UNSIGNED DEFAULT NULL,
  `civilite` enum('M','Mme') NOT NULL DEFAULT 'M',
  `prenom` varchar(32) NOT NULL,
  `nom` varchar(32) NOT NULL,
  `adresse` varchar(64) DEFAULT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `gsm` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `id_pays` varchar(2) DEFAULT 'FR',
  `id_agence` mediumint(8) UNSIGNED DEFAULT NULL,
  `custom` text DEFAULT NULL,
  `id_superieur` mediumint(8) UNSIGNED DEFAULT NULL,
  `pole` set('dev','system','telecom') DEFAULT NULL,
  `id_phone` mediumint(8) UNSIGNED DEFAULT NULL,
  `last_news` timestamp NULL DEFAULT NULL,
  `newsletter` enum('oui','non') NOT NULL DEFAULT 'oui',
  `id_localisation_langue` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `zid` varchar(50) DEFAULT NULL,
  `temps_partiel` decimal(3,2) NOT NULL DEFAULT 1.00,
  `api_key` varchar(255) DEFAULT NULL,
  `password_mail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `user_fonction` (
  `id_user_fonction` tinyint(3) UNSIGNED NOT NULL,
  `fonction` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
