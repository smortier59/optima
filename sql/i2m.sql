SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


CREATE DATABASE IF NOT EXISTS `optima_i2m` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `optima_i2m`;

-- --------------------------------------------------------

--
-- Structure de la table `abonnement`
--

DROP TABLE IF EXISTS `abonnement`;
CREATE TABLE IF NOT EXISTS `abonnement` (
  `id_abonnement` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `abonnement` varchar(64) DEFAULT NULL,
  `codename` varchar(64) NOT NULL,
  `espace_utilise` int(10) UNSIGNED DEFAULT NULL,
  `espace_reserve` tinyint(4) UNSIGNED DEFAULT NULL,
  `nbre_user_actif` tinyint(4) UNSIGNED DEFAULT NULL,
  `liste_user` text DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_abonnement`),
  KEY `affaire` (`id_affaire`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `affaire`
--

DROP TABLE IF EXISTS `affaire`;
CREATE TABLE IF NOT EXISTS `affaire` (
  `id_affaire` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `date_fin` datetime DEFAULT NULL COMMENT 'Date prévisionnel de fin de l''affaire',
  PRIMARY KEY (`id_affaire`),
  KEY `id_societe` (`id_societe`),
  KEY `id_termes` (`id_termes`),
  KEY `id_commercial` (`id_commercial`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `affaire_client`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `affaire_client`;
CREATE TABLE IF NOT EXISTS `affaire_client` (
`id_societe` mediumint(8) unsigned
,`id_affaire` mediumint(8) unsigned
,`ref` char(0)
,`ref_externe` char(0)
,`date` datetime
,`affaire` varchar(255)
,`id_parent` char(0)
,`id_fille` char(0)
,`nature` enum('vente','consommable')
,`date_garantie` char(0)
,`site_associe` char(0)
,`mail_signature` char(0)
,`date_signature` char(0)
,`signataire` char(0)
,`langue` char(0)
,`adresse_livraison` char(0)
,`adresse_livraison_2` char(0)
,`adresse_livraison_3` char(0)
,`adresse_livraison_cp` char(0)
,`adresse_livraison_ville` char(0)
,`adresse_facturation` char(0)
,`adresse_facturation_2` char(0)
,`adresse_facturation_3` char(0)
,`adresse_facturation_cp` char(0)
,`adresse_facturation_ville` char(0)
,`id_partenaire` char(0)
,`id_magasin` char(0)
,`vendeur` char(0)
,`magasin` char(0)
,`partenaire` char(0)
,`id_apporteur` char(0)
);

-- --------------------------------------------------------

--
-- Structure de la table `affaire_etat`
--

DROP TABLE IF EXISTS `affaire_etat`;
CREATE TABLE IF NOT EXISTS `affaire_etat` (
  `id_affaire_etat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_jalon` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL,
  PRIMARY KEY (`id_affaire_etat`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_user` (`id_user`),
  KEY `id_contact` (`id_contact`),
  KEY `id_jalon` (`id_jalon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `affaire_ged_dossier`
--

DROP TABLE IF EXISTS `affaire_ged_dossier`;
CREATE TABLE IF NOT EXISTS `affaire_ged_dossier` (
  `id_affaire_ged_dossier` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_ged_dossier` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_affaire_ged_dossier`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_ged_dossier` (`id_ged_dossier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `affaire_ged_fichier`
--

DROP TABLE IF EXISTS `affaire_ged_fichier`;
CREATE TABLE IF NOT EXISTS `affaire_ged_fichier` (
  `id_affaire_ged_fichier` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_ged_fichier` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_affaire_ged_fichier`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_ged_fichier` (`id_ged_fichier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `agence`
--

DROP TABLE IF EXISTS `agence`;
CREATE TABLE IF NOT EXISTS `agence` (
  `id_agence` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `agence` varchar(64) NOT NULL,
  `adresse` varchar(64) DEFAULT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(8) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `id_pays` char(2) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_agence`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `agence`
--

INSERT INTO `agence` (`id_agence`, `agence`, `adresse`, `adresse_2`, `adresse_3`, `cp`, `ville`, `id_pays`, `tel`, `fax`) VALUES
(1, 'Oignies', 'Rue Alain Bashung', NULL, NULL, '62590', 'Oignes', 'FR', '0320509902', '0320745005');

-- --------------------------------------------------------

--
-- Structure de la table `alerte`
--

DROP TABLE IF EXISTS `alerte`;
CREATE TABLE IF NOT EXISTS `alerte` (
  `id_alerte` mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
  `alerte` text NOT NULL,
  `id_user` mediumint(9) UNSIGNED NOT NULL,
  `id_hotline` mediumint(9) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `nature` enum('interaction','materiel') NOT NULL DEFAULT 'interaction',
  `id_hotline_interaction` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_alerte`),
  UNIQUE KEY `id_hotline_2` (`id_hotline`,`nature`,`id_hotline_interaction`),
  KEY `id_user` (`id_user`),
  KEY `id_hotline` (`id_hotline`),
  KEY `id_hotline_interaction` (`id_hotline_interaction`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `alerte_materiel`
--

DROP TABLE IF EXISTS `alerte_materiel`;
CREATE TABLE IF NOT EXISTS `alerte_materiel` (
  `id_alerte_materiel` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_hotline_interaction` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `vue` enum('oui','non') NOT NULL DEFAULT 'non',
  PRIMARY KEY (`id_alerte_materiel`),
  KEY `id_hotline_interaction` (`id_hotline_interaction`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `asterisk`
--

DROP TABLE IF EXISTS `asterisk`;
CREATE TABLE IF NOT EXISTS `asterisk` (
  `id_asterisk` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `asterisk` varchar(32) NOT NULL,
  `host` varchar(32) NOT NULL,
  `url_webservice` varchar(255) NOT NULL,
  `token` varchar(40) NOT NULL,
  PRIMARY KEY (`id_asterisk`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Gestion des serveurs asterisk';

-- --------------------------------------------------------

--
-- Structure de la table `base_de_connaissance`
--

DROP TABLE IF EXISTS `base_de_connaissance`;
CREATE TABLE IF NOT EXISTS `base_de_connaissance` (
  `id_base_de_connaissance` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `base_de_connaissance` varchar(255) NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `last_seen` datetime DEFAULT NULL,
  `texte` text NOT NULL,
  `frequentation` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_base_de_connaissance`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `bon_de_commande`
--

DROP TABLE IF EXISTS `bon_de_commande`;
CREATE TABLE IF NOT EXISTS `bon_de_commande` (
  `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `frais_de_port` decimal(10,0) NOT NULL,
  PRIMARY KEY (`id_bon_de_commande`),
  KEY `id_societe` (`id_societe`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_commande` (`id_commande`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bon_de_commande_ligne`
--

DROP TABLE IF EXISTS `bon_de_commande_ligne`;
CREATE TABLE IF NOT EXISTS `bon_de_commande_ligne` (
  `id_bon_de_commande_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `produit` varchar(1000) NOT NULL,
  `quantite` decimal(7,1) UNSIGNED NOT NULL,
  `prix` decimal(10,2) UNSIGNED NOT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `tva` decimal(4,3) UNSIGNED NOT NULL,
  `etat` enum('en_cours','recu') NOT NULL DEFAULT 'en_cours',
  PRIMARY KEY (`id_bon_de_commande_ligne`),
  KEY `id_bon_de_commande` (`id_bon_de_commande`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bon_de_pret`
--

DROP TABLE IF EXISTS `bon_de_pret`;
CREATE TABLE IF NOT EXISTS `bon_de_pret` (
  `id_bon_de_pret` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref` varchar(16) CHARACTER SET utf8 NOT NULL,
  `bon_de_pret` varchar(128) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `etat` enum('en_cours','termine','termine_partiel') CHARACTER SET utf8 NOT NULL DEFAULT 'en_cours' COMMENT 'etat de la livraison',
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_bon_de_pret`),
  UNIQUE KEY `ref` (`ref`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_contact` (`id_contact`),
  KEY `id_societe` (`id_societe`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `bon_de_pret_ligne`
--

DROP TABLE IF EXISTS `bon_de_pret_ligne`;
CREATE TABLE IF NOT EXISTS `bon_de_pret_ligne` (
  `id_bon_de_pret_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_bon_de_pret` mediumint(8) UNSIGNED NOT NULL,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `stock` varchar(255) DEFAULT NULL,
  `serial` varchar(32) DEFAULT NULL,
  `serialAT` varchar(24) DEFAULT NULL,
  PRIMARY KEY (`id_bon_de_pret_ligne`),
  KEY `id_bon_de_pret` (`id_bon_de_pret`),
  KEY `id_stock` (`id_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `candidat`
--

DROP TABLE IF EXISTS `candidat`;
CREATE TABLE IF NOT EXISTS `candidat` (
  `id_candidat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `civilite` enum('M','Mme','Mlle') NOT NULL DEFAULT 'M',
  `nom` varchar(40) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `prenom` varchar(40) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT '',
  `adresse` text DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `ville` varchar(500) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `id_pays` varchar(2) NOT NULL DEFAULT 'FR',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `annee_de_naissance` varchar(4) NOT NULL,
  `niveau_diplome` varchar(7) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `reference` text CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `competences` varchar(800) CHARACTER SET utf8 COLLATE utf8_swedish_ci DEFAULT NULL,
  `etam_ic` enum('ETAM','IC') DEFAULT NULL COMMENT 'technicien_ingénieur',
  `pretention` varchar(32) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `type_contrat` enum('stage','cdi','cdd') NOT NULL DEFAULT 'cdi',
  `nbmois_stage` varchar(32) DEFAULT NULL,
  `nbmois_experiences` varchar(32) DEFAULT NULL,
  `id_jobs` mediumint(8) DEFAULT NULL,
  `etat` enum('non_rep','oui','non') NOT NULL DEFAULT 'non_rep',
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `raison` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id_candidat`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `categorie` varchar(64) NOT NULL,
  PRIMARY KEY (`id_categorie`),
  UNIQUE KEY `categorie` (`categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `categorie`) VALUES
(NULL, 'Ecran LCD'),
(NULL, 'Frais de Gestion'),
(NULL, 'Ordinateur'),
(NULL, 'Prestation'),
(NULL, 'Services Web');

-- --------------------------------------------------------

--
-- Structure de la table `cgv`
--

DROP TABLE IF EXISTS `cgv`;
CREATE TABLE IF NOT EXISTS `cgv` (
  `id_cgv` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cgv` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `resume` text DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_cgv`),
  KEY `id_societe` (`id_societe`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `cgv_article`
--

DROP TABLE IF EXISTS `cgv_article`;
CREATE TABLE IF NOT EXISTS `cgv_article` (
  `id_cgv_article` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cgv` mediumint(8) UNSIGNED NOT NULL,
  `cgv_article` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `resume` text DEFAULT NULL,
  PRIMARY KEY (`id_cgv_article`),
  KEY `id_cgv` (`id_cgv`),
  KEY `id_cgv_2` (`id_cgv`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `cgv_article_second`
--

DROP TABLE IF EXISTS `cgv_article_second`;
CREATE TABLE IF NOT EXISTS `cgv_article_second` (
  `id_cgv_article_second` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cgv_article` mediumint(8) UNSIGNED NOT NULL,
  `cgv_article_second` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `resume` text DEFAULT NULL,
  PRIMARY KEY (`id_cgv_article_second`),
  KEY `id_cgv_article` (`id_cgv_article`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `colonne`
--

DROP TABLE IF EXISTS `colonne`;
CREATE TABLE IF NOT EXISTS `colonne` (
  `id_colonne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_vue` mediumint(8) UNSIGNED NOT NULL,
  `champs` varchar(256) DEFAULT NULL,
  `taille` smallint(4) UNSIGNED DEFAULT NULL,
  `tri` enum('asc','desc') DEFAULT NULL,
  PRIMARY KEY (`id_colonne`),
  KEY `id_vue` (`id_vue`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id_commande` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `divers_1` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id_commande`),
  UNIQUE KEY `ref` (`ref`),
  KEY `id_societe` (`id_societe`),
  KEY `id_devis` (`id_devis`),
  KEY `id_user` (`id_user`),
  KEY `id_affaire` (`id_affaire`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commande_facture`
--

DROP TABLE IF EXISTS `commande_facture`;
CREATE TABLE IF NOT EXISTS `commande_facture` (
  `id_commande_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_commande` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_commande_facture`),
  UNIQUE KEY `id_commande` (`id_commande`,`id_facture`),
  KEY `id_facture` (`id_facture`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commande_ligne`
--

DROP TABLE IF EXISTS `commande_ligne`;
CREATE TABLE IF NOT EXISTS `commande_ligne` (
  `id_commande_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id_commande_ligne`),
  UNIQUE KEY `id_commande_2` (`id_commande`,`ref`),
  KEY `id_produit` (`id_produit`),
  KEY `id_commande` (`id_commande`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_compte_absystech` (`id_compte_absystech`),
  KEY `serial` (`serial`(255))
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `compte_absystech`
--

DROP TABLE IF EXISTS `compte_absystech`;
CREATE TABLE IF NOT EXISTS `compte_absystech` (
  `id_compte_absystech` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `compte_absystech` varchar(32) DEFAULT NULL,
  `code` varchar(16) DEFAULT NULL,
  `type` enum('marchandise','service') DEFAULT NULL,
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  PRIMARY KEY (`id_compte_absystech`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `compte_absystech`
--

INSERT INTO `compte_absystech` (`id_compte_absystech`, `compte_absystech`, `code`, `type`, `etat`) VALUES
(1, 'VENTES DE MARCHANDISES', '707100', 'marchandise', 'actif'),
(2, 'VENTE DE DOMAINES', '707200', 'service', 'inactif'),
(3, 'VENTE ABONNEMENT TELEPHONIQUE', '707300', 'service', 'inactif'),
(4, 'PRESTATIONS INFRA', '706100', 'service', 'actif'),
(5, 'PRESTATIONS DE DEVELOPPEMENT', '706200', 'service', 'actif'),
(6, 'LOCATION HEBERGEMENT', '706300', 'service', 'inactif'),
(7, 'SUPPORT', '706400', 'service', 'inactif'),
(8, 'FRAIS DE PORT', '708500', 'service', 'actif'),
(9, 'INCONNUE', NULL, NULL, 'actif'),
(10, 'COUT COPIE', '707140', 'service', 'inactif'),
(11, 'PRESTATIONS - ABO TACITE', '706120', 'service', 'inactif'),
(12, 'PRESTATIONS - ABO RENOUV', '706130', 'service', 'inactif'),
(13, 'VENTE D\'ABONNEMENTS', '707120', 'marchandise', 'actif'),
(14, 'VENTES MARCHANDISES - ABO RENOUV', '707130', 'marchandise', 'inactif'),
(15, 'DEPOT DE GARANTIE', '708200', NULL, 'actif'),
(16, 'MAINTENANCE INFRA', '706700', 'service', 'actif'),
(17, 'MAINTENANCE DEVELOPPEMENT', '706701', 'service', 'actif'),
(18, 'PRODUITS ACTIVITES ANNEX', '708800', NULL, 'actif'),
(19, 'VENTES DE MARCHANDISES EXPORT', '707101', 'marchandise', 'actif'),
(20, 'VENTES DE MARCHANDISES INTRACOM', '707102', 'marchandise', 'actif'),
(21, 'PRESTATIONS INFRA INTRACOM', '706102', 'service', 'actif'),
(22, 'FRAIS DE PORT INTRACOM', '708502', 'service', 'actif'),
(23, 'VENTE D\'ABONNEMENTS EXPORT', '707121', 'marchandise', 'actif'),
(24, 'VENTE MARCHANDISES - ACOMPTE', '707000', 'marchandise', 'actif');

-- --------------------------------------------------------

--
-- Structure de la table `conge`
--

DROP TABLE IF EXISTS `conge`;
CREATE TABLE IF NOT EXISTS `conge` (
  `id_conge` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `conge` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `type` enum('paye','sans_solde','maladie','enfant_malade','autre') CHARACTER SET utf8 NOT NULL DEFAULT 'paye',
  `periode` enum('am','pm','jour','autre') CHARACTER SET utf8 NOT NULL DEFAULT 'autre',
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('nok','ok','en_cours','annule') CHARACTER SET utf8 NOT NULL DEFAULT 'en_cours',
  `raison` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `commentaire` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_conge`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `constante`
--

DROP TABLE IF EXISTS `constante`;
CREATE TABLE IF NOT EXISTS `constante` (
  `id_constante` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `constante` varchar(128) NOT NULL,
  `valeur` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_constante`),
  UNIQUE KEY `constante` (`constante`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `constante`
--

INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES
(1, '__SOCIETE__', 'i2M'),
(2, '__RECORD_BY_PAGE__', '50'),
(3, '__CLEODIS_ASSURANCE_FIXE__', '0.33'),
(4, '__CLEODIS_ASSURANCE_PORTABLE__', '0.66'),
(6, '__WEBDAV_PATH__', '/var/dav/'),
(7, '__TVA__', '1.2'),
(8, '__GMAP_KEY__', 'ABQIAAAAC8ZV9rQjYy1QRp1TdgdOQRQ--2Ve0yp6hOXFbJIJqHnuUTSunRSLJkPYUGwP_0lCufTnIBJAUtMUxA'),
(9, '__STATUT_AGENCE__', 'S.A.R.L. au capital de 8.000 Euros'),
(10, '__ADRESSE_FACTURATION__', 'ABSYSTECH - 82, Lieudit Haeghe Meulen - 59380 Warhem'),
(12, '__LAST_SPEEDMAIL_SENDER__', '2020-12-05 07:20:01'),
(13, '__HOTLINE_URL__', 'https://espaceclient.i2m.fr/'),
(15, '__SEUIL_AUTOCOMPLETION__', '30'),
(16, '__RECORD_BY_PAGE_MORE__', '100'),
(17, '__NB_DAYS_FACTURATION__', '2'),
(21, '__METEO_MOYENNE__', '12'),
(22, '__METEO_BIG__', '10381.0242'),
(23, '__METEO_SMALL__', '-4829.2162'),
(24, '__METEO_COEFF_DATEDIFF__', '30'),
(25, '__METEO_COEFF_SOLDE_TOTAL__', '15'),
(26, '__METEO_COEFF_CREDIT__', '50'),
(27, '__METEO_COEFF_MARGE__', '5'),
(28, '__METEO_COEFF_DEVIS_PERDU__', '1'),
(29, '__METEO_LIMITE_DATEDIFF__', '30'),
(30, '__METEO_LIMITE_SOLDE_TOTAL__', '1000'),
(31, '__METEO_LIMITE_MARGE__', '5000'),
(32, '__METEO_LIMITE_DEVIS_PERDU__', '10'),
(37, '__RATIO_ROI_CREDIT__', '1'),
(38, '__RATIO_ROI_CREDIT2__', '0.5'),
(39, '__COUT_HORAIRE_TECH__', '50'),
(40, '__TAUX_HORAIRE_TICKET__', '65'),
(41, '__REGLE_MDP__', '/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\\W)(.{8,})/'),
(42, '__REGLE_MDP_ERROR_MSG__', 'Le mot de passe doit être d\'au moins 8 caratères avec 1 lettre en majuscule, 1 lettre en minuscule, 1 chiffre et 1 caractere spécial'),
(43, '__URL_MATTERMOST__', 'https://mm.absystech.net/hooks/6xnsr64mtfgmbktxkwazmxuj6e'),
(44, '__CHANNEL_MATTERMOST__', 'Hotline'),
(45, '__API_CREDIT_SAFE_USERNAME__', 'jerome.loison@cleodis.com'),
(46, '__API_CREDIT_SAFE_PASSWORD__', '70O8384_DT4E1c996320'),
(47, '__API_CREDIT_SAFE_BASEURL__', 'https://connect.creditsafe.com/v1'),
(48, '__API_CREDIT_PAYS_RECHERCHE__', 'FR'),
(49, '__MS_GRAPH_CLIENT_ID__', 'd1f6e0a0-a23c-4611-91b7-a9eff8385414'),
(50, '__MS_GRAPH_CLIENT_SECRET__', '_FV7Q~b6-uA_~4cU2pSbG21Lb1jCif99SHFFV'),
(51, '__MS_GRAPH_TENANT_ID__', '92435d98-99f6-449b-b313-528fba7ad851');

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `pwd` varchar(64) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Mot de passe espace client',
  PRIMARY KEY (`id_contact`),
  UNIQUE KEY `cle_externe` (`cle_externe`),
  KEY `id_societe` (`id_societe`),
  KEY `id_owner` (`id_owner`),
  KEY `tel` (`tel`),
  KEY `gsm` (`gsm`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `contact_client`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `contact_client`;
CREATE TABLE IF NOT EXISTS `contact_client` (
`id_contact` mediumint(8) unsigned
,`id_societe` mediumint(8) unsigned
,`email` varchar(255)
,`anniversaire` date
,`civilite` enum('M','Mme','Mlle')
,`situation_maritale` char(0)
,`situation_perso` char(0)
,`fonction` varchar(255)
,`situation_pro` char(0)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `coordonnees_client`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `coordonnees_client`;
CREATE TABLE IF NOT EXISTS `coordonnees_client` (
`id_societe` mediumint(8) unsigned
,`ref` varchar(11)
,`societe` varchar(128)
,`type_client` varchar(32)
,`id_famille` tinyint(3) unsigned
,`nom_commercial` varchar(64)
,`adresse` varchar(64)
,`adresse_2` varchar(64)
,`adresse_3` varchar(64)
,`cp` varchar(5)
,`ville` varchar(32)
,`facturation_adresse` varchar(64)
,`facturation_adresse_2` varchar(64)
,`facturation_adresse_3` varchar(64)
,`facturation_cp` varchar(5)
,`facturation_ville` varchar(27)
,`livraison_adresse` char(0)
,`livraison_adresse_2` char(0)
,`livraison_adresse_3` char(0)
,`livraison_cp` char(0)
,`livraison_ville` char(0)
,`email` varchar(255)
,`tel` varchar(20)
,`particulier_civilite` char(0)
,`particulier_nom` char(0)
,`particulier_prenom` char(0)
,`particulier_portable` char(0)
,`num_carte_fidelite` char(0)
,`particulier_fixe` char(0)
,`particulier_email` char(0)
,`code_client` char(0)
,`id_apporteur` char(0)
,`langue` char(0)
,`siret` varchar(32)
);

-- --------------------------------------------------------

--
-- Structure de la table `delai_de_realisation`
--

DROP TABLE IF EXISTS `delai_de_realisation`;
CREATE TABLE IF NOT EXISTS `delai_de_realisation` (
  `id_delai_de_realisation` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `delai_de_realisation` varchar(255) NOT NULL,
  PRIMARY KEY (`id_delai_de_realisation`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='Termes de paiements pour les devis smart proposal' PACK_KEYS=0;

--
-- Déchargement des données de la table `delai_de_realisation`
--

INSERT INTO `delai_de_realisation` (`id_delai_de_realisation`, `delai_de_realisation`) VALUES
(1, '1 semaine'),
(2, '2 semaines'),
(3, '3 semaines'),
(4, '6 semaines'),
(5, '6 à 8 semaines'),
(6, '10 à 12 semaines'),
(7, '10 à 16 semaines'),
(8, '4 semaines');

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

DROP TABLE IF EXISTS `departement`;
CREATE TABLE IF NOT EXISTS `departement` (
  `id_departement` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL,
  `id_region` tinyint(3) UNSIGNED NOT NULL,
  `departement` varchar(64) NOT NULL,
  `chef_lieu` varchar(64) NOT NULL,
  PRIMARY KEY (`id_departement`),
  KEY `id_region` (`id_region`),
  KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `departement`
--

INSERT INTO `departement` (`id_departement`, `code`, `id_region`, `departement`, `chef_lieu`) VALUES
(1, '01', 1, 'Ain ', 'Bourg-en-Bresse '),
(2, '02', 2, 'Aisne', 'Laon'),
(3, '03', 3, 'Allier', 'Moulins'),
(4, '04', 4, 'Alpes de Hautes-Provence', 'Digne'),
(5, '05', 4, 'Hautes-Alpes', 'Gap'),
(6, '06', 4, 'Alpes-Maritimes', 'Nice'),
(7, '07', 1, 'Ardèche', 'Privas'),
(8, '08', 5, 'Ardennes', 'Charleville-Mézières'),
(9, '09', 6, 'Ariège', 'Foix'),
(10, '10', 5, 'Aube', 'Troyes'),
(11, '11', 7, 'Aude', 'Carcassonne'),
(12, '12', 6, 'Aveyron', 'Rodez'),
(13, '13', 4, 'Bouches-du-Rhône', 'Marseille'),
(14, '14', 8, 'Calvados', 'Caen'),
(15, '15', 3, 'Cantal', 'Aurillac'),
(16, '16', 9, 'Charente', 'Angoulême'),
(17, '17', 9, 'Charente-Maritime', 'La Rochelle'),
(18, '18', 10, 'Cher', 'Bourges'),
(19, '19', 11, 'Corrèze', 'Tulle'),
(20, '2A', 12, 'Corse-du-Sud', 'Ajaccio'),
(21, '2B', 12, 'Haute-Corse', 'Bastia'),
(22, '21', 13, 'Côte-d\'Or', 'Dijon'),
(23, '22', 14, 'Côtes d\'Armor', 'Saint-Brieuc'),
(24, '23', 11, 'Creuse', 'Guéret'),
(25, '24', 15, 'Dordogne', 'Périgueux'),
(26, '25', 16, 'Doubs', 'Besançon'),
(27, '26', 1, 'Drôme', 'Valence'),
(28, '27', 17, 'Eure', 'Évreux'),
(29, '28', 10, 'Eure-et-Loir', 'Chartres'),
(30, '29', 14, 'Finistère', 'Quimper'),
(31, '30', 7, 'Gard', 'Nîmes'),
(32, '31', 6, 'Haute-Garonne', 'Toulouse'),
(33, '32', 6, 'Gers', 'Auch'),
(34, '33', 15, 'Gironde', 'Bordeaux'),
(35, '34', 7, 'Hérault', 'Montpellier'),
(36, '35', 14, 'Ille-et-Vilaine', 'Rennes'),
(37, '36', 10, 'Indre', 'Châteauroux'),
(38, '37', 10, 'Indre-et-Loire', 'Tours'),
(39, '38', 1, 'Isère', 'Grenoble'),
(40, '39', 16, 'Jura', 'Lons-le-Saunier'),
(41, '40', 15, 'Landes', 'Mont-de-Marsan'),
(42, '41', 10, 'Loir-et-Cher', 'Blois'),
(43, '42', 1, 'Loire', 'Saint-Étienne'),
(44, '43', 3, 'Haute-Loire', 'Le Puy-en-Velay'),
(45, '44', 18, 'Loire-Atlantique', 'Nantes'),
(46, '45', 10, 'Loiret', 'Orléans'),
(47, '46', 6, 'Lot', 'Cahors'),
(48, '47', 15, 'Lot-et-Garonne', 'Agen'),
(49, '48', 7, 'Lozère', 'Mende'),
(50, '49', 18, 'Maine-et-Loire', 'Angers'),
(51, '50', 8, 'Manche', 'Saint-Lô'),
(52, '51', 5, 'Marne', 'Châlons-en-Champagne'),
(53, '52', 5, 'Haute-Marne', 'Chaumont'),
(54, '53', 18, 'Mayenne', 'Laval'),
(55, '54', 19, 'Meurthe-et-Moselle', 'Nancy'),
(56, '55', 19, 'Meuse', 'Bar-le-Duc'),
(57, '56', 14, 'Morbihan', 'Vannes'),
(58, '57', 19, 'Moselle', 'Metz'),
(59, '58', 13, 'Nièvre', 'Nevers'),
(60, '59', 20, 'Nord', 'Lille'),
(61, '60', 2, 'Oise', 'Beauvais'),
(62, '61', 8, 'Orne', 'Alençon'),
(63, '62', 20, 'Pas-de-Calais', 'Arras'),
(64, '63', 3, 'Puy-de-Dôme', 'Clermont-Ferrand'),
(65, '64', 15, 'Pyrénées-Atlantiques', 'Pau'),
(66, '65', 6, 'Hautes-Pyrénées', 'Tarbes'),
(67, '66', 7, 'Pyrénées-Orientales', 'Perpignan'),
(68, '67', 21, 'Bas-Rhin', 'Strasbourg'),
(69, '68', 21, 'Haut-Rhin', 'Colmar'),
(70, '69', 1, 'Rhône', 'Lyon'),
(71, '70', 16, 'Haute-Saône', 'Vesoul'),
(72, '71', 13, 'Saône-et-Loire', 'Mâcon'),
(73, '72', 18, 'Sarthe', 'Le Mans'),
(74, '73', 1, 'Savoie', 'Chambéry'),
(75, '74', 1, 'Haute-Savoie', 'Annecy'),
(76, '75', 22, 'Paris', 'Paris'),
(77, '76', 17, 'Seine-Maritime', 'Rouen'),
(78, '77', 22, 'Seine-et-Marne', 'Melun'),
(79, '78', 22, 'Yvelines', 'Versailles'),
(80, '79', 9, 'Deux-Sèvres', 'Niort'),
(81, '80', 2, 'Somme', 'Amiens'),
(82, '81', 6, 'Tarn', 'Albi'),
(83, '82', 6, 'Tarn-et-Garonne', 'Montauban'),
(84, '83', 4, 'Var', 'Toulon'),
(85, '84', 4, 'Vaucluse', 'Avignon'),
(86, '85', 18, 'Vendée', 'La Roche-sur-Yon'),
(87, '86', 9, 'Vienne', 'Poitiers'),
(88, '87', 11, 'Haute-Vienne', 'Limoges'),
(89, '88', 19, 'Vosges', 'Épinal'),
(90, '89', 13, 'Yonne', 'Auxerre'),
(91, '90', 16, 'Territoire-de-Belfort', 'Belfort'),
(92, '91', 22, 'Essonne', 'Évry'),
(93, '92', 22, 'Hauts-de-Seine', 'Nanterre'),
(94, '93', 22, 'Seine-Saint-Denis', 'Bobigny'),
(95, '94', 22, 'Val-de-Marne', 'Créteil'),
(96, '95', 22, 'Val-d\'Oise', 'Pontoise');

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

DROP TABLE IF EXISTS `devis`;
CREATE TABLE IF NOT EXISTS `devis` (
  `id_devis` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `duree_contrat_cout_copie` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_devis`),
  UNIQUE KEY `ref` (`ref`,`revision`),
  KEY `id_user` (`id_user`),
  KEY `id_societe` (`id_societe`),
  KEY `id_contact` (`id_contact`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_opportunite` (`id_opportunite`),
  KEY `etat` (`etat`),
  KEY `id_user_technique` (`id_user_technique`,`id_user_admin`),
  KEY `id_user_admin` (`id_user_admin`),
  KEY `id_remplacant` (`id_remplacant`),
  KEY `id_delai_de_realisation` (`id_delai_de_realisation`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `devise`
--

DROP TABLE IF EXISTS `devise`;
CREATE TABLE IF NOT EXISTS `devise` (
  `id_devise` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `devise` varchar(32) DEFAULT NULL,
  `symbole` varchar(8) DEFAULT NULL,
  `ratio_eur` float UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_devise`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `devise`
--

INSERT INTO `devise` (`id_devise`, `devise`, `symbole`, `ratio_eur`) VALUES
(1, 'Euro', '€', 1);

-- --------------------------------------------------------

--
-- Structure de la table `devis_ligne`
--

DROP TABLE IF EXISTS `devis_ligne`;
CREATE TABLE IF NOT EXISTS `devis_ligne` (
  `id_devis_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `visible` enum('oui','non') NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id_devis_ligne`),
  UNIQUE KEY `id_devis` (`id_devis`,`ref`),
  KEY `id_produit` (`id_produit`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_compte_absystech` (`id_compte_absystech`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `didacticiel`
--

DROP TABLE IF EXISTS `didacticiel`;
CREATE TABLE IF NOT EXISTS `didacticiel` (
  `id_didacticiel` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `didacticiel` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id_didacticiel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `document`
--

DROP TABLE IF EXISTS `document`;
CREATE TABLE IF NOT EXISTS `document` (
  `id_document` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `document` varchar(200) NOT NULL,
  `filename` varchar(200) NOT NULL,
  PRIMARY KEY (`id_document`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `domaine`
--

DROP TABLE IF EXISTS `domaine`;
CREATE TABLE IF NOT EXISTS `domaine` (
  `id_domaine` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `domaine` varchar(64) NOT NULL,
  PRIMARY KEY (`id_domaine`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `echeancier`
--

DROP TABLE IF EXISTS `echeancier`;
CREATE TABLE IF NOT EXISTS `echeancier` (
  `id_echeancier` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `designation` varchar(100) NOT NULL,
  `debut` date NOT NULL,
  `fin` date DEFAULT NULL,
  `variable` enum('oui','non') NOT NULL DEFAULT 'non',
  `periodicite` enum('mensuelle','trimestrielle','annuelle','semestrielle') NOT NULL DEFAULT 'mensuelle',
  `jour_facture` enum('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','fin_mois') NOT NULL DEFAULT '1',
  `actif` enum('oui','non') NOT NULL DEFAULT 'oui',
  `commentaire` text DEFAULT NULL,
  `id_termes` tinyint(3) UNSIGNED DEFAULT NULL,
  `prochaine_echeance` date NOT NULL,
  PRIMARY KEY (`id_echeancier`),
  KEY `id_societe` (`id_societe`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_termes` (`id_termes`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `echeancier_ligne_periodique`
--

DROP TABLE IF EXISTS `echeancier_ligne_periodique`;
CREATE TABLE IF NOT EXISTS `echeancier_ligne_periodique` (
  `id_echeancier_ligne_periodique` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `designation` varchar(512) NOT NULL,
  `quantite` decimal(8,2) NOT NULL,
  `puht` float NOT NULL,
  `valeur_variable` enum('oui','non') NOT NULL,
  `facture_prorata` enum('oui','non') NOT NULL,
  `mise_en_service` date NOT NULL,
  `id_echeancier` mediumint(8) UNSIGNED NOT NULL,
  `id_compte_absystech` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) NOT NULL,
  `offset` mediumint(8) UNSIGNED NOT NULL DEFAULT 99999,
  PRIMARY KEY (`id_echeancier_ligne_periodique`),
  KEY `id_echeancier` (`id_echeancier`),
  KEY `id_compte_absystech` (`id_compte_absystech`),
  KEY `ref` (`ref`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `echeancier_ligne_ponctuelle`
--

DROP TABLE IF EXISTS `echeancier_ligne_ponctuelle`;
CREATE TABLE IF NOT EXISTS `echeancier_ligne_ponctuelle` (
  `id_echeancier_ligne_ponctuelle` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `designation` varchar(512) NOT NULL,
  `id_echeancier` mediumint(8) UNSIGNED NOT NULL,
  `id_compte_absystech` mediumint(8) UNSIGNED NOT NULL,
  `quantite` decimal(8,2) NOT NULL,
  `puht` float NOT NULL,
  `date_valeur` date NOT NULL,
  `ref` varchar(32) NOT NULL,
  `offset` mediumint(8) UNSIGNED NOT NULL DEFAULT 99999,
  PRIMARY KEY (`id_echeancier_ligne_ponctuelle`),
  KEY `id_echeancier` (`id_echeancier`),
  KEY `id_compte_absystech` (`id_compte_absystech`),
  KEY `ref` (`ref`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `email`
--

DROP TABLE IF EXISTS `email`;
CREATE TABLE IF NOT EXISTS `email` (
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_contact`
--

DROP TABLE IF EXISTS `emailing_contact`;
CREATE TABLE IF NOT EXISTS `emailing_contact` (
  `id_emailing_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `sollicitation` mediumint(8) UNSIGNED DEFAULT 0,
  `tracking` mediumint(8) UNSIGNED DEFAULT 0,
  `last_tracking` datetime DEFAULT NULL,
  `erreur` mediumint(8) UNSIGNED DEFAULT 0,
  `civilite` enum('M','Mme','Mlle') CHARACTER SET utf8 DEFAULT NULL,
  `nom` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `prenom` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `opt_in` enum('oui','non') DEFAULT 'oui',
  `societe` varchar(128) DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `adresse` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_2` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_3` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cp` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `ville` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `id_pays` varchar(2) DEFAULT 'FR',
  `tel` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `gsm` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `fonction` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `divers_1` varchar(255) DEFAULT NULL,
  `divers_2` varchar(255) DEFAULT NULL,
  `divers_3` varchar(255) DEFAULT NULL,
  `divers_4` varchar(255) DEFAULT NULL,
  `divers_5` varchar(255) DEFAULT NULL,
  `id_emailing_source` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_emailing_contact`),
  UNIQUE KEY `id_source` (`id_emailing_source`,`email`),
  KEY `id_societe` (`societe`),
  KEY `id_owner` (`id_owner`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_erreur`
--

DROP TABLE IF EXISTS `emailing_erreur`;
CREATE TABLE IF NOT EXISTS `emailing_erreur` (
  `id_erreur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL DEFAULT '',
  `groupe` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) NOT NULL DEFAULT '',
  `definition` text NOT NULL,
  PRIMARY KEY (`id_erreur`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_forms`
--

DROP TABLE IF EXISTS `emailing_forms`;
CREATE TABLE IF NOT EXISTS `emailing_forms` (
  `id_emailing_forms` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siren` varchar(20) DEFAULT NULL,
  `rs` varchar(100) DEFAULT NULL,
  `fonction` varchar(100) DEFAULT NULL,
  `civilite` enum('m','mme','mlle') DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `adresse1` varchar(150) DEFAULT NULL,
  `adresse2` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `ville` varchar(50) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `id_emailing_projet` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_emailing_forms`),
  KEY `id_emailing_projet` (`id_emailing_projet`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_job`
--

DROP TABLE IF EXISTS `emailing_job`;
CREATE TABLE IF NOT EXISTS `emailing_job` (
  `id_emailing_job` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emailing_job` varchar(64) NOT NULL,
  `id_emailing_projet` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_liste` mediumint(8) UNSIGNED DEFAULT NULL,
  `depart` datetime NOT NULL,
  `fin` datetime DEFAULT NULL,
  `etat` enum('wait','sending','sent','cancelled') DEFAULT 'wait',
  `nb_visu_online` int(11) NOT NULL DEFAULT 0,
  `nbMailToSend` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_emailing_job`),
  KEY `FK_emailing_job_1` (`id_emailing_projet`),
  KEY `FK_emailing_job_2` (`id_emailing_liste`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_job_email`
--

DROP TABLE IF EXISTS `emailing_job_email`;
CREATE TABLE IF NOT EXISTS `emailing_job_email` (
  `id_emailing_job_email` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_emailing_job` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_liste_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `tracking` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `last_tracking` datetime DEFAULT NULL,
  `retour` enum('oui','non') NOT NULL DEFAULT 'non',
  `permanent_failure` set('5.0.0','5.1.0','5.1.1','5.1.2','5.1.3','5.1.4','5.1.5','5.1.6','5.1.7','5.1.8','5.2.0','5.2.1','5.2.2','5.2.3','5.2.4','5.3.0','5.3.1','5.3.2','5.3.3','5.3.4','5.3.5','5.4.0','5.4.1','5.4.2','5.4.3','5.4.4','5.4.5','5.4.6','5.4.7','5.5.0','5.5.1','5.5.2','5.5.3','5.5.4','5.5.5','5.6.0','5.6.1','5.6.2','5.6.3','5.6.4','5.6.5','5.7.0','5.7.1','5.7.2','5.7.3','5.7.4','5.7.5','5.7.6','5.7.7') DEFAULT NULL,
  `persistent_failure` set('4.0.0','4.1.0','4.1.1','4.1.2','4.1.3','4.1.4','4.1.5','4.1.6','4.1.7','4.1.8','4.2.0','4.2.1','4.2.2','4.2.3','4.2.4','4.3.0','4.3.1','4.3.2','4.3.3','4.3.4','4.3.5','4.4.0','4.4.1','4.4.2','4.4.3','4.4.4','4.4.5','4.4.6','4.4.7','4.5.0','4.5.1','4.5.2','4.5.3','4.5.4','4.5.5','4.6.0','4.6.1','4.6.2','4.6.3','4.6.4','4.6.5','4.7.0','4.7.1','4.7.2','4.7.3','4.7.4','4.7.5','4.7.6','4.7.7') DEFAULT NULL,
  `success` set('2.0.0','2.1.0','2.1.1','2.1.2','2.1.3','2.1.4','2.1.5','2.1.6','2.1.7','2.1.8','2.2.0','2.2.1','2.2.2','2.2.3','2.2.4','2.3.0','2.3.1','2.3.2','2.3.3','2.3.4','2.3.5','2.4.0','2.4.1','2.4.2','2.4.3','2.4.4','2.4.5','2.4.6','2.4.7','2.5.0','2.5.1','2.5.2','2.5.3','2.5.4','2.5.5','2.6.0','2.6.1','2.6.2','2.6.3','2.6.4','2.6.5','2.7.0','2.7.1','2.7.2','2.7.3','2.7.4','2.7.5','2.7.6','2.7.7') DEFAULT NULL,
  `erreur_brute` text DEFAULT NULL,
  PRIMARY KEY (`id_emailing_job_email`),
  UNIQUE KEY `id_emailing_job` (`id_emailing_job`,`id_emailing_liste_contact`),
  KEY `FK_emailing_job_email_2` (`id_emailing_liste_contact`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_lien`
--

DROP TABLE IF EXISTS `emailing_lien`;
CREATE TABLE IF NOT EXISTS `emailing_lien` (
  `id_emailing_lien` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emailing_lien` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id_emailing_lien`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_liste`
--

DROP TABLE IF EXISTS `emailing_liste`;
CREATE TABLE IF NOT EXISTS `emailing_liste` (
  `id_emailing_liste` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emailing_liste` varchar(128) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `etat` enum('open','close') NOT NULL DEFAULT 'open',
  `sollicitation` mediumint(8) UNSIGNED DEFAULT 0,
  `tracking` mediumint(8) UNSIGNED DEFAULT 0,
  `last_tracking` datetime DEFAULT NULL,
  `erreur` mediumint(8) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id_emailing_liste`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_liste_contact`
--

DROP TABLE IF EXISTS `emailing_liste_contact`;
CREATE TABLE IF NOT EXISTS `emailing_liste_contact` (
  `id_emailing_liste_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_emailing_liste` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `sollicitation` mediumint(8) UNSIGNED DEFAULT 0,
  `tracking` mediumint(8) UNSIGNED DEFAULT 0,
  `last_tracking` date DEFAULT NULL,
  `erreur` mediumint(8) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id_emailing_liste_contact`),
  UNIQUE KEY `id_emailing_liste` (`id_emailing_liste`,`id_emailing_contact`),
  KEY `id_emailing_contact` (`id_emailing_contact`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_projet`
--

DROP TABLE IF EXISTS `emailing_projet`;
CREATE TABLE IF NOT EXISTS `emailing_projet` (
  `id_emailing_projet` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emailing_projet` varchar(128) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `mail_from` varchar(128) NOT NULL,
  `nom_expediteur` varchar(64) NOT NULL,
  `couleur_fond` varchar(6) DEFAULT NULL,
  `couleur_footer` varchar(6) DEFAULT NULL,
  `couleur_link` varchar(6) DEFAULT NULL,
  `corps` text DEFAULT NULL,
  `embed` text DEFAULT NULL,
  `corps_txt` text DEFAULT NULL COMMENT 'Le corps du mail en plain text',
  `embed_image` enum('oui','non') NOT NULL DEFAULT 'oui',
  `afficher_infos_societe` enum('oui','non') NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id_emailing_projet`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_source`
--

DROP TABLE IF EXISTS `emailing_source`;
CREATE TABLE IF NOT EXISTS `emailing_source` (
  `id_emailing_source` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emailing_source` varchar(200) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_emailing_source`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_tracking`
--

DROP TABLE IF EXISTS `emailing_tracking`;
CREATE TABLE IF NOT EXISTS `emailing_tracking` (
  `id_emailing_tracking` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_emailing_job_email` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_lien` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(15) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_emailing_tracking`),
  KEY `FK_emailing_tracking_1` (`id_emailing_job_email`),
  KEY `FK_emailing_tracking_2` (`id_emailing_lien`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `espace_client_inscription`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `espace_client_inscription`;
CREATE TABLE IF NOT EXISTS `espace_client_inscription` (
`dossier` varchar(255)
,`societe` varchar(128)
,`code_client` varchar(11)
,`nom` varchar(64)
,`prenom` varchar(64)
,`gsm` varchar(20)
,`email` varchar(255)
,`id_societe` mediumint(8) unsigned
,`id_contact` mediumint(8) unsigned
,`id_famille` tinyint(3) unsigned
,`famille` varchar(32)
);

-- --------------------------------------------------------

--
-- Structure de la table `export_comptable`
--

DROP TABLE IF EXISTS `export_comptable`;
CREATE TABLE IF NOT EXISTS `export_comptable` (
  `id_export_comptable` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `factures` text NOT NULL,
  PRIMARY KEY (`id_export_comptable`),
  KEY `date_debut` (`date_debut`,`date_fin`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fabriquant`
--

DROP TABLE IF EXISTS `fabriquant`;
CREATE TABLE IF NOT EXISTS `fabriquant` (
  `id_fabriquant` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fabriquant` varchar(64) NOT NULL,
  PRIMARY KEY (`id_fabriquant`),
  UNIQUE KEY `fabriquant` (`fabriquant`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `fabriquant`
--

INSERT INTO `fabriquant` (`id_fabriquant`, `fabriquant`) VALUES
(1, 'AbsysTech'),
(2, 'Hewlett Packard'),
(3, 'NEC Mitsubishi');

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `id_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `infosSup` varchar(250) DEFAULT NULL,
  `date_debut_periode` date DEFAULT NULL,
  `date_fin_periode` date DEFAULT NULL,
  `periodicite` enum('mensuelle','trimestrielle','annuelle','semestrielle') DEFAULT NULL,
  `id_facture_parente` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_echeancier` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_export_comptable` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_facture`),
  UNIQUE KEY `ref` (`ref`),
  KEY `id_societe` (`id_societe`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_user` (`id_user`),
  KEY `id_termes` (`id_termes`),
  KEY `id_facture_parente` (`id_facture_parente`),
  KEY `id_echeancier` (`id_echeancier`),
  KEY `id_export_comptable` (`id_export_comptable`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `factures_client`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `factures_client`;
CREATE TABLE IF NOT EXISTS `factures_client` (
`id_facture` mediumint(8) unsigned
,`ref` varchar(16)
,`ref_externe` char(0)
,`id_societe` mediumint(8) unsigned
,`prix` decimal(10,2)
,`etat` enum('payee','impayee','perte')
,`date` date
,`date_paiement` char(0)
,`type_facture` enum('facture','solde','avoir','acompte','factor','facture_periodique')
,`date_periode_debut` date
,`date_periode_fin` date
,`tva` decimal(4,3)
,`id_affaire` mediumint(8) unsigned
,`mode_paiement` char(0)
,`nature` char(0)
,`rejet` char(0)
,`date_rejet` char(0)
,`date_regularisation` char(0)
);

-- --------------------------------------------------------

--
-- Structure de la table `facture_fournisseur`
--

DROP TABLE IF EXISTS `facture_fournisseur`;
CREATE TABLE IF NOT EXISTS `facture_fournisseur` (
  `id_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `nb_page` tinyint(3) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_facture_fournisseur`),
  KEY `id_societe` (`id_societe`),
  KEY `id_bon_de_commande` (`id_bon_de_commande`),
  KEY `id_societe_2` (`id_societe`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facture_fournisseur_affaire`
--

DROP TABLE IF EXISTS `facture_fournisseur_affaire`;
CREATE TABLE IF NOT EXISTS `facture_fournisseur_affaire` (
  `id_facture_fournisseur_affaire` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `nb_produit` int(3) NOT NULL,
  PRIMARY KEY (`id_facture_fournisseur_affaire`),
  KEY `id_facture_fournisseur` (`id_facture_fournisseur`,`id_affaire`),
  KEY `id_facture_fournisseur_2` (`id_facture_fournisseur`),
  KEY `id_affaire` (`id_affaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facture_ligne`
--

DROP TABLE IF EXISTS `facture_ligne`;
CREATE TABLE IF NOT EXISTS `facture_ligne` (
  `id_facture_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id_facture_ligne`),
  KEY `id_facture` (`id_facture`),
  KEY `id_produit` (`id_produit`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_compte_absystech` (`id_compte_absystech`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facture_paiement`
--

DROP TABLE IF EXISTS `facture_paiement`;
CREATE TABLE IF NOT EXISTS `facture_paiement` (
  `id_facture_paiement` mediumint(8) NOT NULL AUTO_INCREMENT,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `mode_paiement` enum('cheque','virement','lettre_de_change','paypal','prelevement','lettrage','OD','factor','perte','espece','avoir') NOT NULL DEFAULT 'cheque',
  `date` date NOT NULL,
  `remarques` varchar(255) DEFAULT NULL,
  `num_cheque` varchar(128) DEFAULT NULL,
  `num_compte` varchar(128) DEFAULT NULL,
  `num_bordereau` varchar(128) DEFAULT NULL,
  `id_facture_avoir` mediumint(8) UNSIGNED DEFAULT NULL,
  `montant_interet` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_facture_paiement`),
  KEY `id_facture` (`id_facture`),
  KEY `id_facture_avoir` (`id_facture_avoir`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facture_parente`
--

DROP TABLE IF EXISTS `facture_parente`;
CREATE TABLE IF NOT EXISTS `facture_parente` (
  `id_facture_parente` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_parente` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_facture_parente`),
  KEY `id_facture` (`id_facture`),
  KEY `id_parente` (`id_parente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `famille`
--

DROP TABLE IF EXISTS `famille`;
CREATE TABLE IF NOT EXISTS `famille` (
  `id_famille` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `famille` varchar(32) NOT NULL,
  `cle_externe` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_famille`),
  UNIQUE KEY `cle_externe` (`cle_externe`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `famille`
--

INSERT INTO `famille` (`id_famille`, `famille`, `cle_externe`) VALUES
(1, 'Entreprise', NULL),
(2, 'Particulier', NULL),
(3, 'Banque', NULL),
(4, 'Association', NULL),
(5, 'Collectivité', NULL),
(6, 'Ecole', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `faq`
--

DROP TABLE IF EXISTS `faq`;
CREATE TABLE IF NOT EXISTS `faq` (
  `id_faq` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `faq` varchar(255) NOT NULL,
  `explication` text NOT NULL,
  PRIMARY KEY (`id_faq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fichier_de_localisation`
--

DROP TABLE IF EXISTS `fichier_de_localisation`;
CREATE TABLE IF NOT EXISTS `fichier_de_localisation` (
  `id_fichier_de_localisation` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fichier_de_localisation` enum('fr','en','es','de','it') CHARACTER SET utf8 NOT NULL,
  `index` varchar(255) NOT NULL,
  `valeur` varchar(1024) CHARACTER SET utf8 NOT NULL,
  `module` varchar(50) DEFAULT NULL,
  `site` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id_fichier_de_localisation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Structure de la table `filtre_defaut`
--

DROP TABLE IF EXISTS `filtre_defaut`;
CREATE TABLE IF NOT EXISTS `filtre_defaut` (
  `id_filtre_defaut` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `div` varchar(128) NOT NULL,
  `filter_key` varchar(32) DEFAULT NULL,
  `order` varchar(256) DEFAULT NULL,
  `page` tinyint(3) UNSIGNED DEFAULT NULL,
  `limit` tinyint(3) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_filtre_defaut`),
  UNIQUE KEY `id_user_2` (`id_user`,`div`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `filtre_optima`
--

DROP TABLE IF EXISTS `filtre_optima`;
CREATE TABLE IF NOT EXISTS `filtre_optima` (
  `id_filtre_optima` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filtre_optima` varchar(32) NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `options` text NOT NULL,
  `type` enum('public','prive') NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id_filtre_optima`),
  KEY `id_user` (`id_user`),
  KEY `id_module` (`id_module`,`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `filtre_user`
--

DROP TABLE IF EXISTS `filtre_user`;
CREATE TABLE IF NOT EXISTS `filtre_user` (
  `id_filtre_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_filtre_optima` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_filtre_user`),
  UNIQUE KEY `id_filtre_optima_2` (`id_filtre_optima`,`id_user`,`id_module`),
  KEY `id_filtre_optima` (`id_filtre_optima`),
  KEY `id_user` (`id_user`),
  KEY `id_module` (`id_module`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `frais_kilometrique`
--

DROP TABLE IF EXISTS `frais_kilometrique`;
CREATE TABLE IF NOT EXISTS `frais_kilometrique` (
  `id_frais_kilometrique` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `annee` year(4) NOT NULL,
  `cv` mediumint(8) NOT NULL,
  `coeff` decimal(4,3) NOT NULL,
  `type` enum('auto','moto','cyclo') NOT NULL DEFAULT 'auto',
  PRIMARY KEY (`id_frais_kilometrique`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--
-- Structure de la table `ged`
--

DROP TABLE IF EXISTS `ged`;
CREATE TABLE IF NOT EXISTS `ged` (
  `id_ged` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `ged` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nom du fichier',
  `format` varchar(8) DEFAULT NULL,
  `version` varchar(16) CHARACTER SET utf8 DEFAULT '1',
  `weight` decimal(5,2) UNSIGNED DEFAULT NULL,
  `commentaires` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `dossier` tinyint(1) NOT NULL DEFAULT 0,
  `id_parent` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_gep_projet` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_ged`),
  UNIQUE KEY `id_owner` (`id_owner`,`id_societe`,`ged`,`version`,`weight`,`id_parent`),
  KEY `id_parent` (`id_parent`),
  KEY `id_gep_projet` (`id_gep_projet`),
  KEY `id_societe` (`id_societe`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ged_dossier`
--

DROP TABLE IF EXISTS `ged_dossier`;
CREATE TABLE IF NOT EXISTS `ged_dossier` (
  `id_ged_dossier` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ged_dossier` varchar(255) NOT NULL,
  `id_parent` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_ged_dossier`),
  KEY `id_parent` (`id_parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ged_fichier`
--

DROP TABLE IF EXISTS `ged_fichier`;
CREATE TABLE IF NOT EXISTS `ged_fichier` (
  `id_ged_fichier` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ged_fichier` varchar(255) NOT NULL,
  `id_ged_dossier` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `format` varchar(8) DEFAULT NULL,
  `version` tinyint(2) NOT NULL DEFAULT 1,
  `weight` decimal(5,2) UNSIGNED DEFAULT NULL,
  `commentaires` varchar(1024) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_ged_fichier`),
  KEY `id_ged_dossier` (`id_ged_dossier`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ged_user`
--

DROP TABLE IF EXISTS `ged_user`;
CREATE TABLE IF NOT EXISTS `ged_user` (
  `id_ged_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_ged_dossier` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `droit` enum('read','write') NOT NULL DEFAULT 'read',
  `date_valid_prevu` date DEFAULT NULL,
  `valid` enum('en_cours','valide','refuse') NOT NULL DEFAULT 'en_cours',
  `date_valid` datetime DEFAULT NULL,
  `date_visu` datetime DEFAULT NULL,
  PRIMARY KEY (`id_ged_user`),
  KEY `id_ged_dossier` (`id_ged_dossier`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gep_equipe`
--

DROP TABLE IF EXISTS `gep_equipe`;
CREATE TABLE IF NOT EXISTS `gep_equipe` (
  `id_gep_equipe` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `gep_equipe` varchar(64) NOT NULL,
  `id_gep_projet` mediumint(8) UNSIGNED DEFAULT NULL,
  `fonction` text DEFAULT NULL,
  `couleur` varchar(6) DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Leader',
  PRIMARY KEY (`id_gep_equipe`),
  KEY `FK_gep_equipe_1` (`id_user`),
  KEY `FK_gep_equipe_2` (`id_gep_projet`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `gep_processus`
--

DROP TABLE IF EXISTS `gep_processus`;
CREATE TABLE IF NOT EXISTS `gep_processus` (
  `id_gep_processus` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gep_processus` varchar(128) NOT NULL,
  `id_gep_projet` mediumint(8) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id_gep_processus`),
  KEY `FK_gep_processus_1` (`id_gep_projet`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `gep_projet`
--

DROP TABLE IF EXISTS `gep_projet`;
CREATE TABLE IF NOT EXISTS `gep_projet` (
  `id_gep_projet` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `gep_projet` varchar(128) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_projet_parent` mediumint(9) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date NOT NULL DEFAULT '0000-00-00',
  `date_fin` date NOT NULL DEFAULT '0000-00-00',
  `nature` enum('production','rd') NOT NULL DEFAULT 'production',
  `id_contact_facturation` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_gep_projet`),
  KEY `FK_gep_1` (`id_societe`),
  KEY `FK_gep_2` (`id_affaire`),
  KEY `FK_gep_3` (`id_owner`),
  KEY `id_contact_facturation` (`id_contact_facturation`),
  KEY `id_projet_parent` (`id_projet_parent`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='Gestion de projets';

-- --------------------------------------------------------

--
-- Structure de la table `gep_tache`
--

DROP TABLE IF EXISTS `gep_tache`;
CREATE TABLE IF NOT EXISTS `gep_tache` (
  `id_gep_tache` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_gep_processus` mediumint(8) UNSIGNED NOT NULL,
  `gep_tache` varchar(128) NOT NULL,
  `date` datetime NOT NULL,
  `id_gep_equipe` mediumint(8) UNSIGNED DEFAULT NULL,
  `prerequis` text CHARACTER SET utf8 DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  PRIMARY KEY (`id_gep_tache`),
  KEY `FK_gep_tache_1` (`id_gep_equipe`),
  KEY `FK_gep_tache_2` (`id_gep_processus`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `gep_tache_dependance`
--

DROP TABLE IF EXISTS `gep_tache_dependance`;
CREATE TABLE IF NOT EXISTS `gep_tache_dependance` (
  `id_gep_tache_dependance` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_tache_dependante` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `id_tache_liee` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_gep_tache_dependance`),
  KEY `FK_gep_tache_dependance_2` (`id_tache_liee`),
  KEY `id_tache_dependante` (`id_tache_dependante`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `gestion_ticket`
--

DROP TABLE IF EXISTS `gestion_ticket`;
CREATE TABLE IF NOT EXISTS `gestion_ticket` (
  `id_gestion_ticket` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `operation` smallint(5) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `type` enum('ajout','retrait') NOT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `id_hotline` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_facture` mediumint(8) UNSIGNED DEFAULT NULL,
  `nbre_tickets` decimal(8,2) DEFAULT NULL,
  `solde` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id_gestion_ticket`),
  KEY `id_societe` (`id_societe`),
  KEY `id_hotline` (`id_hotline`),
  KEY `id_facture` (`id_facture`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `gie_contact`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `gie_contact`;
CREATE TABLE IF NOT EXISTS `gie_contact` (
`id_gie_contact` int(9) unsigned
,`id_gie_societe` int(9) unsigned
,`codename` varchar(9)
,`id_societe` mediumint(8) unsigned
,`id_contact` mediumint(8) unsigned
,`date` timestamp
,`civilite` varchar(4)
,`nom` varchar(32)
,`prenom` varchar(32)
,`etat` varchar(7)
,`id_owner` mediumint(8) unsigned
,`private` varchar(3)
,`adresse` varchar(64)
,`adresse_2` varchar(64)
,`adresse_3` varchar(64)
,`cp` varchar(8)
,`ville` varchar(32)
,`id_pays` varchar(2)
,`tel` varchar(20)
,`gsm` varchar(20)
,`fax` varchar(20)
,`email` varchar(255)
,`fonction` varchar(255)
,`departement` varchar(255)
,`anniversaire` date
,`loisir` varchar(255)
,`langue` varchar(255)
,`assistant` varchar(128)
,`assistant_tel` varchar(32)
,`tel_autres` varchar(20)
,`adresse_autres` varchar(255)
,`forecast` varchar(3)
,`description` varchar(255)
,`disponibilite` varchar(71)
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `gie_societe`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `gie_societe`;
CREATE TABLE IF NOT EXISTS `gie_societe` (
`id_gie_societe` int(9) unsigned
,`codename` varchar(9)
,`id_societe` mediumint(8) unsigned
,`societe` varchar(128)
,`nom_commercial` varchar(64)
,`activite` varchar(255)
,`etat` varchar(7)
,`relation` varchar(8)
,`latitude` float
,`longitude` float
,`siren` varchar(9)
,`siret` varchar(32)
,`naf` varchar(5)
,`adresse` varchar(64)
,`adresse_2` varchar(64)
,`adresse_3` varchar(64)
,`cp` varchar(5)
,`ville` varchar(32)
,`id_pays` char(2)
,`tel` varchar(20)
,`fax` varchar(20)
,`email` varchar(255)
,`web` varchar(128)
,`ca` varchar(32)
,`structure` varchar(64)
,`capital` bigint(20)
,`nb_employe` mediumint(8)
,`effectif` varchar(4)
,`fournisseur` varchar(3)
,`partenaire` varchar(3)
);

-- --------------------------------------------------------

--
-- Structure de la table `hebergement`
--

DROP TABLE IF EXISTS `hebergement`;
CREATE TABLE IF NOT EXISTS `hebergement` (
  `id_hebergement` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `hebergement` varchar(128) NOT NULL,
  `date_creation` date NOT NULL,
  `date_expiration` date NOT NULL,
  `hostname` varchar(128) NOT NULL,
  PRIMARY KEY (`id_hebergement`),
  KEY `id_societe` (`id_societe`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `historique_affaire`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `historique_affaire`;
CREATE TABLE IF NOT EXISTS `historique_affaire` (
`id_affaire` mediumint(8) unsigned
,`date` timestamp
,`etat` char(0)
,`commentaire` text
);

-- --------------------------------------------------------

--
-- Structure de la table `hotline`
--

DROP TABLE IF EXISTS `hotline`;
CREATE TABLE IF NOT EXISTS `hotline` (
  `id_hotline` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `hotline` varchar(255) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `detail` text DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL COMMENT 'Date de création',
  `date_debut` timestamp NULL DEFAULT NULL COMMENT 'Date de prise en charge',
  `date_fin` timestamp NULL DEFAULT NULL COMMENT 'Date de résolution',
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pole_concerne` enum('system','dev','telecom') DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `etat` enum('free','fixing','wait','done','payee','annulee') CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT 'free',
  `facturation_ticket` enum('oui','non') DEFAULT NULL,
  `ok_facturation` enum('oui','non') DEFAULT NULL,
  `indice_satisfaction` enum('1','2','3','4','5') DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `urgence` enum('detail','genant','bloquant') NOT NULL DEFAULT 'detail',
  `id_gep_projet` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `wait_mep` enum('oui','non') NOT NULL DEFAULT 'non',
  `priorite` tinyint(4) NOT NULL DEFAULT 20 COMMENT 'priorité de 0 à 20 (plus prioritaire)',
  `avancement` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'Avancement en poucentage (de 0 à 100)',
  `estimation` varchar(50) DEFAULT NULL,
  `date_terminee` timestamp NULL DEFAULT NULL COMMENT 'Dead line du ticket',
  `charge` enum('rd','maintenance','intervention') NOT NULL DEFAULT 'intervention',
  `visible` enum('oui','non') NOT NULL DEFAULT 'oui',
  PRIMARY KEY (`id_hotline`),
  KEY `id_societe` (`id_societe`),
  KEY `id_user` (`id_user`),
  KEY `id_contact` (`id_contact`),
  KEY `urgence` (`urgence`),
  KEY `id_gep_projet` (`id_gep_projet`),
  KEY `id_affaire` (`id_affaire`),
  KEY `hotline` (`hotline`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `hotline_interaction`
--

DROP TABLE IF EXISTS `hotline_interaction`;
CREATE TABLE IF NOT EXISTS `hotline_interaction` (
  `id_hotline_interaction` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_hotline` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `temps` time DEFAULT '00:00:00',
  `temps_passe` time DEFAULT '00:00:00',
  `detail` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_ordre_de_mission` mediumint(8) UNSIGNED DEFAULT NULL,
  `visible` enum('oui','non') NOT NULL DEFAULT 'oui',
  `nature` enum('internal','mail','reunion','tel','interaction','relation_client') NOT NULL DEFAULT 'interaction',
  `heure_debut_presta` time DEFAULT NULL,
  `heure_fin_presta` time DEFAULT NULL,
  `duree_presta` time NOT NULL DEFAULT '00:00:00',
  `heure_depart_dep` time DEFAULT NULL,
  `duree_pause` time DEFAULT '00:00:00',
  `heure_arrive_dep` time DEFAULT NULL,
  `duree_dep` time NOT NULL DEFAULT '00:00:00',
  `credit_dep` decimal(5,3) DEFAULT 0.000,
  `credit_presta` decimal(5,3) NOT NULL DEFAULT 0.000,
  `matos` enum('oui','non') NOT NULL DEFAULT 'non',
  `teamviewer` enum('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Utilisation de teamviewer',
  `recipient` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id_hotline_interaction`),
  KEY `id_hotline` (`id_hotline`),
  KEY `id_user` (`id_user`),
  KEY `id_contact` (`id_contact`),
  KEY `id_ordre_de_mission` (`id_ordre_de_mission`),
  KEY `date` (`date`),
  KEY `type` (`nature`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `im`
--

DROP TABLE IF EXISTS `im`;
CREATE TABLE IF NOT EXISTS `im` (
  `id_im` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `im` varchar(512) NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_user_recipient` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_im`),
  KEY `id_user` (`id_user`),
  KEY `id_user_recipient` (`id_user_recipient`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `importer`
--

DROP TABLE IF EXISTS `importer`;
CREATE TABLE IF NOT EXISTS `importer` (
  `id_importer` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `importer` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `nb` mediumint(8) UNSIGNED DEFAULT NULL,
  `mapping` text CHARACTER SET utf8 NOT NULL,
  `etat` enum('en_attente','annule','fini','probleme') CHARACTER SET utf8 NOT NULL DEFAULT 'en_attente',
  `date_import` datetime DEFAULT NULL,
  `separateur` enum(',',';') NOT NULL DEFAULT ',',
  `complement` text CHARACTER SET utf8 DEFAULT NULL,
  `filename` varchar(150) DEFAULT NULL,
  `erreur` text DEFAULT NULL,
  `lignes_inserer` int(10) UNSIGNED DEFAULT NULL,
  `lignes_ignore` int(10) UNSIGNED DEFAULT NULL,
  `lignes_update` int(10) UNSIGNED DEFAULT NULL,
  `options` enum('ignore','update') NOT NULL DEFAULT 'ignore',
  PRIMARY KEY (`id_importer`),
  KEY `id_module` (`id_module`),
  KEY `user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `importer_ligne`
--

DROP TABLE IF EXISTS `importer_ligne`;
CREATE TABLE IF NOT EXISTS `importer_ligne` (
  `id_importer_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_importer` mediumint(8) UNSIGNED DEFAULT NULL,
  `id` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_importer_ligne`),
  KEY `id_importer` (`id_importer`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `jalon`
--

DROP TABLE IF EXISTS `jalon`;
CREATE TABLE IF NOT EXISTS `jalon` (
  `id_jalon` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jalon` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_jalon`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `jalon`
--

INSERT INTO `jalon` (`id_jalon`, `jalon`, `module`, `category`) VALUES
(1, 'Préparation en cours', 'affaire', 'absystech'),
(2, 'Préparation terminée. Expédition programmée', 'affaire', 'absystech'),
(3, 'Colis remis au transporteur', 'affaire', 'absystech'),
(4, 'Colis livré par le transporteur', 'affaire', 'absystech'),
(5, 'Le système a détecté les téléphones', 'affaire', 'absystech'),
(6, 'Portabilité effectuée', 'affaire', 'absystech'),
(7, 'Renvoi d\'appel effectué', 'affaire', 'absystech'),
(8, 'Portabilité en cours', 'affaire', 'absystech'),
(9, 'Date prévisionnelle de mise en service (ADSL/SDSL/Fibre)', 'affaire', 'ergatel');

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id_jobs` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jobs` text NOT NULL,
  `intitule` varchar(128) NOT NULL,
  `pole` enum('Développement','Système','Télécom') NOT NULL DEFAULT 'Développement',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` enum('en_cours','fini') NOT NULL DEFAULT 'en_cours',
  PRIMARY KEY (`id_jobs`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `jungledisk`
--

DROP TABLE IF EXISTS `jungledisk`;
CREATE TABLE IF NOT EXISTS `jungledisk` (
  `id_jungledisk` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jungledisk` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  PRIMARY KEY (`id_jungledisk`),
  UNIQUE KEY `jungledisk` (`jungledisk`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `licence_affaire`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `licence_affaire`;
CREATE TABLE IF NOT EXISTS `licence_affaire` (
`id_affaire` mediumint(8) unsigned
,`id_societe` mediumint(8) unsigned
,`ref` char(0)
,`ref_externe` char(0)
,`licence_part1` char(0)
,`licence_part2` char(0)
,`date_envoi` char(0)
,`id_licence` char(0)
,`licence_type` char(0)
,`url_telechargement` char(0)
);

-- --------------------------------------------------------

--
-- Structure de la table `livraison`
--

DROP TABLE IF EXISTS `livraison`;
CREATE TABLE IF NOT EXISTS `livraison` (
  `id_livraison` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `code_de_tracabilite` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id_livraison`),
  UNIQUE KEY `ref` (`ref`),
  KEY `id_commande` (`id_commande`),
  KEY `id_transporteur` (`id_transporteur`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_devis` (`id_devis`),
  KEY `id_expediteur` (`id_expediteur`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `livraison_ligne`
--

DROP TABLE IF EXISTS `livraison_ligne`;
CREATE TABLE IF NOT EXISTS `livraison_ligne` (
  `id_livraison_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_livraison` mediumint(8) UNSIGNED NOT NULL,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('en_cours_de_livraison','endommage','perdu','termine') NOT NULL DEFAULT 'en_cours_de_livraison',
  PRIMARY KEY (`id_livraison_ligne`),
  KEY `id_stock` (`id_stock`),
  KEY `id_livraison` (`id_livraison`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `localisation_langue`
--

DROP TABLE IF EXISTS `localisation_langue`;
CREATE TABLE IF NOT EXISTS `localisation_langue` (
  `id_localisation_langue` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `localisation_langue` varchar(2) NOT NULL,
  `libelle` varchar(64) NOT NULL,
  `id_pays` char(2) NOT NULL DEFAULT 'FR',
  PRIMARY KEY (`id_localisation_langue`),
  KEY `id_pays` (`id_pays`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

INSERT INTO `localisation_langue` (`id_localisation_langue`, `localisation_langue`, `libelle`, `id_pays`) VALUES
(1, 'fr', 'Français', 'FR'),
(2, 'en', 'English', 'UK'),
(3, 'es', 'Español', 'ES'),
(4, 'it', 'Italiano', 'IT'),
(5, 'de', 'Deutsch', 'DE'),
(6, 'zh', '国语', 'CN'),
(7, 'hu', 'Magyar', 'HU'),
(8, 'hi', 'हिंदी', 'IN'),
(9, 'nl', 'Nederlands', 'NL'),
(10, 'pl', 'Polski', 'PL'),
(11, 'pt', 'Português', 'PT'),
(12, 'ro', 'Român', 'RO'),
(13, 'ru', 'русский', 'RU'),
(14, 'tr', 'Türk', 'TR'),
(15, 'br', 'Brasileiro', 'BR'),
(16, 'cz', 'český', 'CZ'),
(17, 'sw', 'Svenska', 'SE'),
(18, 'jp', '日本', 'JP');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `loyer_affaire`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `loyer_affaire`;
CREATE TABLE IF NOT EXISTS `loyer_affaire` (
`id_affaire` mediumint(8) unsigned
,`type` char(0)
,`loyer` char(0)
,`frequence` char(0)
,`duree` char(0)
,`tva` decimal(4,3)
,`id_loyer` char(0)
);

-- --------------------------------------------------------

--
-- Structure de la table `messagerie`
--

DROP TABLE IF EXISTS `messagerie`;
CREATE TABLE IF NOT EXISTS `messagerie` (
  `id_messagerie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `from` varchar(255) DEFAULT NULL,
  `to` varchar(512) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `message_id` varchar(255) NOT NULL,
  `size` mediumint(8) UNSIGNED NOT NULL,
  `uid` mediumint(8) UNSIGNED NOT NULL,
  `msgno` mediumint(8) UNSIGNED NOT NULL,
  `recent` tinyint(1) NOT NULL,
  `flagged` tinyint(1) NOT NULL,
  `answered` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `seen` tinyint(1) NOT NULL,
  `draft` tinyint(1) NOT NULL,
  `udate` int(10) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `attachments` text DEFAULT NULL,
  `attachmentsRealName` text DEFAULT NULL,
  PRIMARY KEY (`id_messagerie`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `mission`
--

DROP TABLE IF EXISTS `mission`;
CREATE TABLE IF NOT EXISTS `mission` (
  `id_mission` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `commentaire` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_mission`),
  KEY `id_affaire` (`id_affaire`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `module`
--

DROP TABLE IF EXISTS `module`;
CREATE TABLE IF NOT EXISTS `module` (
  `id_module` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `construct` text CHARACTER SET utf8 DEFAULT NULL COMMENT 'Sert de constructeur par défaut si aucune classe n''existe pour ce module (desactive mais en commentaire)',
  PRIMARY KEY (`id_module`),
  UNIQUE KEY `module` (`module`),
  KEY `id_parent` (`id_parent`)
) ENGINE=InnoDB AUTO_INCREMENT=2058 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `module`
--

INSERT INTO `module` (`id_module`, `id_parent`, `module`, `abstrait`, `priorite`, `visible`, `import`, `couleur_fond`, `couleur_texte`, `couleur`, `description`, `construct`) VALUES
(2, NULL, 'crm', 1, 5, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(3, 2, 'societe', 0, 0, 1, 1, 'D31C0B', 'FFFFFF', 'blue', NULL, NULL),
(4, 2, 'contact', 0, 0, 1, 1, '000086', 'FFFFFF', 'blue', NULL, NULL),
(9, 2, 'suivi', 0, 0, 1, 0, '2A9B34', 'FFFFFF', 'blue', NULL, NULL),
(37, NULL, 'my', 1, 6, 1, 0, 'ffc000', '000000', 'blue', NULL, NULL),
(38, 37, 'calendrier', 1, 0, 1, 0, '2B9CBE', 'FFFFFF', 'blue', NULL, NULL),
(39, 37, 'messagerie', 1, 0, 1, 0, '418662', 'FFFFFF', 'blue', NULL, NULL),
(41, 37, 'stats', 0, 0, 1, 0, 'D65E92', 'FFFFFF', 'blue', NULL, NULL),
(42, 37, 'note_de_frais', 0, 0, 1, 0, 'A70099', 'FFFFFF', 'blue', NULL, NULL),
(43, 37, 'pointage', 0, 0, 1, 0, '39D496', 'FFFFFF', 'blue', NULL, NULL),
(44, 37, 'conge', 0, 0, 1, 0, '2752FF', 'FFFFFF', 'blue', NULL, NULL),
(45, 37, 'preference', 1, 0, 0, 0, 'F0E35B', '000000', 'blue', NULL, NULL),
(46, 2, 'tache', 0, 0, 1, 0, '018F19', 'FFFFFF', 'blue', NULL, NULL),
(47, NULL, 'commerce', 1, 4, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(48, NULL, 'technique', 1, 3, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(49, NULL, 'qualite', 1, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(50, NULL, 'administration', 1, 1, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(51, 47, 'opportunite', 0, 0, 1, 0, 'DD9999', '000000', 'blue', NULL, NULL),
(52, 47, 'affaire', 0, 0, 1, 0, 'D8AEAE', '000000', 'blue', NULL, NULL),
(53, 52, 'devis', 0, 1, 1, 0, 'FF0000', '000000', 'blue', NULL, NULL),
(54, 52, 'commande', 0, 10, 1, 0, 'E6951D', '000000', 'blue', NULL, NULL),
(55, 52, 'facture', 0, 50, 1, 0, '26E71F', '000000', 'blue', NULL, NULL),
(59, 48, 'livraison', 0, 15, 1, 0, '69BB87', 'FFFFFF', 'blue', NULL, NULL),
(60, 48, 'hotline', 0, 5, 1, 0, 'FF8585', 'FFFFFF', 'blue', NULL, NULL),
(62, 48, 'services_web', 1, 50, 1, 0, '5B49E0', 'EBDFA7', 'blue', NULL, NULL),
(68, 50, 'user', 0, 0, 1, 0, '00D4FF', '0022A8', 'blue', NULL, NULL),
(69, 50, 'agence', 0, 0, 1, 0, '6E83A3', 'FFFFFF', 'blue', NULL, NULL),
(70, 50, 'module', 0, 0, 1, 0, 'D10000', 'FFFFFF', 'blue', NULL, NULL),
(71, 50, 'constante', 0, 0, 1, 0, '858585', 'FFFFFF', 'blue', NULL, NULL),
(72, 37, 'accueil', 1, 1, 0, 0, '018F19', 'FFFFFF', 'blue', NULL, NULL),
(75, 47, 'produit', 0, 10, 1, 0, 'E3D6B3', '000000', 'blue', NULL, NULL),
(82, 55, 'relance', 0, 1, 1, 0, 'EE7213', '010E0D', 'blue', NULL, NULL),
(83, NULL, 'drh', 1, NULL, 1, 0, 'F3F30F', '000000', 'blue', NULL, NULL),
(84, 83, 'candidat', 0, NULL, 1, 0, '16A22C', 'FFFFFF', 'blue', NULL, NULL),
(86, 62, 'nom_de_domaine', 0, NULL, 1, 0, '5B49E0', 'EBDFA7', 'blue', NULL, NULL),
(87, 68, 'profil', 0, NULL, 1, 0, 'E1FF00', '000000', 'blue', NULL, NULL),
(89, 62, 'hebergement', 0, NULL, 1, 0, '5B49E0', 'EBDFA7', 'blue', NULL, NULL),
(92, 62, 'vhost', 0, NULL, 1, 0, '5B49E0', 'EBDFA7', 'blue', NULL, NULL),
(93, 62, 'registrar', 0, NULL, 1, 0, '5B49E0', 'EBDFA7', 'blue', NULL, NULL),
(97, 47, 'politesse', 0, 10, 1, 0, '2C56FF', 'F7FF00', 'blue', NULL, NULL),
(98, 47, 'termes', 0, 10, 1, 0, 'FF6F6F', 'CCCCCC', 'blue', NULL, NULL),
(101, 75, 'categorie', 0, 1, 1, 0, '0C5856', 'FFFFFF', 'blue', NULL, NULL),
(102, 101, 'sous_categorie', 0, 2, 1, 0, '137A3C', 'FFFFFF', 'blue', NULL, NULL),
(107, 50, 'fax', 0, NULL, 1, 0, 'E0D1D1', '000000', 'brown', NULL, NULL),
(111, 2, 'geolocalisation', 1, 10, 1, 0, '62DB8E', 'FFFFFF', 'blue', NULL, NULL),
(116, NULL, 'emailing', 1, NULL, 1, 0, 'F78383', '008330', 'blue', NULL, NULL),
(117, 116, 'emailing_projet', 0, 1, 1, 0, '3A50F3', 'FFFFFF', 'blue', NULL, NULL),
(118, 116, 'emailing_job', 0, 20, 1, 0, 'F75454', 'FFFFFF', 'blue', NULL, NULL),
(119, 116, 'emailing_liste', 0, 10, 1, 0, 'E2E2E2', '000000', 'blue', NULL, NULL),
(120, 116, 'emailing_contact', 0, 2, 1, 1, '8B79B8', 'FFFFFF', 'blue', NULL, NULL),
(121, 119, 'emailing_liste_contact', NULL, NULL, 0, 0, '505DE7', 'ADFF99', 'blue', NULL, NULL),
(122, 116, 'emailing_tracking', NULL, 30, 1, 0, '791FEB', 'E1EBD2', 'blue', NULL, NULL),
(123, 117, 'emailing_projet_lien', NULL, NULL, 0, 0, '00FF51', 'A0CEFF', 'blue', NULL, NULL),
(124, 118, 'emailing_job_email', 0, NULL, 1, 0, '00FF47', 'FFFFFF', 'blue', NULL, NULL),
(125, 3, 'domaine', 0, NULL, 1, 0, 'CFE5E6', '2800AC', 'blue', NULL, NULL),
(126, 3, 'famille', NULL, NULL, 1, 0, '0006A8', 'FFFDC4', 'blue', NULL, NULL),
(132, 50, 'importer', NULL, NULL, 1, 0, '3F2D85', 'AAFFC5', 'blue', NULL, NULL),
(134, 3, 'secteur_geographique', NULL, NULL, 1, 0, 'D4BABA', 'FFFFFF', 'blue', NULL, NULL),
(135, 3, 'secteur_commercial', NULL, NULL, 1, 0, 'F8BDBD', '09006F', 'blue', NULL, NULL),
(136, 50, 'devise', NULL, NULL, 1, 0, '0015FF', 'FFFFFF', 'blue', NULL, NULL),
(139, 52, 'bon_de_commande', 0, 15, 1, 0, 'E6951D', '000000', 'blue', NULL, NULL),
(140, 3, 'ged', 0, 10, 0, 0, 'D4A7FF', '110057', 'blue', NULL, NULL),
(141, NULL, 'gep', 1, 0, 1, 0, '606020', 'ffffe0', 'blue', NULL, NULL),
(142, 141, 'gep_equipe', 0, 5, 0, 0, 'c0e0c0', '000000', 'blue', NULL, NULL),
(143, 141, 'gep_projet', 0, 1, 1, 0, '606020', 'ffffe0', 'blue', NULL, NULL),
(144, 148, 'gep_tache', 0, 10, 0, 0, 'a0ffc0', '202060', 'blue', NULL, NULL),
(145, 75, 'fabriquant', 0, 2, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(147, 50, 'fichier_de_localisation', 0, 18, 1, 0, 'efefef', '000000', 'blue', NULL, NULL),
(148, 141, 'gep_processus', 0, 8, 0, 0, '400080', 'eff804', 'blue', NULL, NULL),
(149, 144, 'gep_tache_dependance', 0, 10, 0, 0, 'a0ffc0', '202060', 'blue', NULL, NULL),
(150, 48, 'gestion_ticket', 0, 80, 1, 0, 'FF8585', 'FFFFFF', 'blue', NULL, NULL),
(151, 60, 'ordre_de_mission', 0, 0, 1, 0, 'E020E0', '000000', 'blue', NULL, NULL),
(152, 60, 'hotline_interaction', 0, 0, 1, 0, 'FF8585', 'FFFFFF', 'blue', NULL, NULL),
(153, 55, 'facture_paiement', NULL, NULL, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(154, 49, 'cgv', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(155, 154, 'cgv_article', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(156, 155, 'cgv_article_second', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(157, 50, 'societe_frais_port', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(159, 52, 'liste_materiel', 1, 0, 1, 0, '00ff00', '000000', 'blue', NULL, NULL),
(160, 47, 'compte_absystech', 0, 0, 1, 0, '808080', '000000', 'blue', NULL, NULL),
(161, 50, 'localisation_traduction', 0, NULL, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(162, 161, 'localisation', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(163, 161, 'localisation_langue', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(167, 37, 'news', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(168, 53, 'devis_ligne', 0, 0, 0, 0, 'FF0000', '000000', 'blue', NULL, NULL),
(169, 54, 'commande_ligne', 0, 0, 0, 0, 'E6951D', '000000', 'blue', NULL, NULL),
(170, 55, 'facture_ligne', 0, 0, 0, 0, '26E71F', '000000', 'blue', NULL, NULL),
(171, 50, 'tracabilite', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(172, 87, 'profil_privilege', 0, NULL, 0, 0, '9FFF5F', '000000', 'blue', NULL, NULL),
(173, 48, 'jungledisk', 0, 90, 1, 0, 'FF8585', 'FFFFFF', 'blue', NULL, NULL),
(174, 116, 'emailing_lien', 0, 2, 1, 0, '', '', 'blue', NULL, NULL),
(175, 50, 'abonnement', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(176, 50, 'asterisk', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(177, 50, 'phone', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(178, 70, 'module_privilege', 0, 0, 0, 0, 'D10000', 'FFFFFF', 'blue', 'Attribution des privlèges spécifique au module', NULL),
(179, 116, 'emailing_source', 0, 1, 1, 0, '', '', 'blue', NULL, NULL),
(190, 83, 'jobs', 0, NULL, 1, 1, '16A22C', 'FFFFFF', 'blue', 'offres d\'emploi', NULL),
(191, 42, 'note_de_frais_ligne', 0, 0, 0, 0, '000000', '000000', 'blue', NULL, NULL),
(192, 42, 'frais_kilometrique', 0, 0, 1, 1, '000000', '000000', 'blue', NULL, NULL),
(193, 139, 'bon_de_commande_ligne', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(194, 59, 'livraison_ligne', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(195, 48, 'stock', 0, 30, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(196, 195, 'stock_etat', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(197, 47, 'transporteur', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(198, 48, 'changelog', 0, 25, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(2044, 48, 'bon_de_pret', 0, 16, 1, 0, '69BB87', 'FFFFFF', 'blue', NULL, NULL),
(2045, 2044, 'bon_de_pret_ligne', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(2046, 47, 'delai', 0, 10, 1, 0, 'FF6F6F', 'CCCCCC', 'blue', NULL, NULL),
(2047, 48, 'scanner', 0, 100, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(2048, 52, 'facture_fournisseur', 0, 8, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(2052, NULL, 'gie', 1, 5, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(2053, 2052, 'gie_societe', 0, 0, 1, 1, 'D31C0B', 'FFFFFF', 'blue', NULL, NULL),
(2054, 2052, 'gie_contact', 0, 0, 0, 1, 'D31C0B', 'FFFFFF', 'blue', NULL, NULL),
(2055, 2048, 'facture_fournisseur_affaire', 0, 0, 1, 0, 'FFFFFF', '000000', 'green', NULL, NULL),
(2056, 47, 'delai_de_realisation', 0, 10, 1, 0, 'FFFFFF', '000000', 'green', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `module_privilege`
--

DROP TABLE IF EXISTS `module_privilege`;
CREATE TABLE IF NOT EXISTS `module_privilege` (
  `id_module_privilege` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_privilege` smallint(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_module_privilege`),
  KEY `id_module` (`id_module`),
  KEY `id_privilege` (`id_privilege`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `module_privilege`
--

INSERT INTO `module_privilege` (`id_module_privilege`, `id_module`, `id_privilege`) VALUES
(1, 193, 9),
(2, 193, 8),
(3, 193, 7),
(4, 193, 6),
(5, 193, 5),
(6, 193, 4),
(7, 193, 3),
(8, 193, 2),
(9, 193, 1),
(10, 194, 9),
(11, 194, 8),
(12, 194, 7),
(13, 194, 6),
(14, 194, 5),
(15, 194, 4),
(16, 194, 3),
(17, 194, 2),
(18, 194, 1),
(19, 195, 9),
(20, 195, 8),
(21, 195, 7),
(22, 195, 6),
(23, 195, 5),
(24, 195, 4),
(25, 195, 3),
(26, 195, 2),
(27, 195, 1),
(28, 196, 9),
(29, 196, 8),
(30, 196, 7),
(31, 196, 6),
(32, 196, 5),
(33, 196, 4),
(34, 196, 3),
(35, 196, 2),
(36, 196, 1),
(37, 198, 9),
(38, 198, 8),
(39, 198, 7),
(40, 198, 6),
(41, 198, 5),
(42, 198, 4),
(43, 198, 3),
(44, 198, 2),
(45, 198, 1),
(46, 87, 10),
(47, 87, 9),
(48, 87, 8),
(49, 87, 7),
(50, 87, 6),
(51, 87, 5),
(52, 87, 4),
(53, 87, 3),
(54, 87, 2),
(55, 87, 1),
(56, 2047, 10),
(57, 2047, 9),
(58, 2047, 8),
(59, 2047, 7),
(60, 2047, 6),
(61, 2047, 5),
(62, 2047, 4),
(63, 2047, 3),
(64, 2047, 2),
(65, 2047, 1),
(66, 2048, 10),
(67, 2048, 9),
(68, 2048, 8),
(69, 2048, 7),
(70, 2048, 6),
(71, 2048, 5),
(72, 2048, 4),
(73, 2048, 3),
(74, 2048, 2),
(75, 2048, 1),
(76, 2055, 10),
(77, 2055, 9),
(78, 2055, 8),
(79, 2055, 7),
(80, 2055, 6),
(81, 2055, 5),
(82, 2055, 4),
(83, 2055, 3),
(84, 2055, 2),
(85, 2055, 1),
(86, 2056, 10),
(87, 2056, 9),
(88, 2056, 8),
(89, 2056, 7),
(90, 2056, 6),
(91, 2056, 5),
(92, 2056, 4),
(93, 2056, 3),
(94, 2056, 2),
(95, 2056, 1);

-- --------------------------------------------------------

--
-- Structure de la table `nom_de_domaine`
--

DROP TABLE IF EXISTS `nom_de_domaine`;
CREATE TABLE IF NOT EXISTS `nom_de_domaine` (
  `id_nom_de_domaine` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `nom_de_domaine` varchar(128) NOT NULL,
  `id_registrar` mediumint(8) UNSIGNED NOT NULL,
  `date_creation` date NOT NULL,
  `date_expiry` date NOT NULL,
  `serveur_dns` varchar(255) NOT NULL DEFAULT 'lithium.absystech.net.',
  `etat` enum('en_cours','expire') NOT NULL DEFAULT 'en_cours',
  PRIMARY KEY (`id_nom_de_domaine`),
  KEY `id_societe` (`id_societe`,`id_registrar`),
  KEY `id_registrar` (`id_registrar`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `note_de_frais`
--

DROP TABLE IF EXISTS `note_de_frais`;
CREATE TABLE IF NOT EXISTS `note_de_frais` (
  `id_note_de_frais` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `note_de_frais` varchar(100) CHARACTER SET utf8 NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('nok','ok','en_cours') NOT NULL DEFAULT 'en_cours',
  PRIMARY KEY (`id_note_de_frais`),
  UNIQUE KEY `note_de_frais` (`note_de_frais`,`id_user`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `note_de_frais_ligne`
--

DROP TABLE IF EXISTS `note_de_frais_ligne`;
CREATE TABLE IF NOT EXISTS `note_de_frais_ligne` (
  `id_note_de_frais_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_note_de_frais` mediumint(8) UNSIGNED NOT NULL,
  `objet` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `montant` decimal(6,2) NOT NULL,
  `etat` enum('nok','ok','en_cours') CHARACTER SET utf8 NOT NULL DEFAULT 'en_cours',
  `raison` varchar(255) DEFAULT NULL,
  `id_frais_kilometrique` mediumint(8) UNSIGNED DEFAULT NULL,
  `km` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_note_de_frais_ligne`),
  KEY `id_note_de_frais` (`id_note_de_frais`),
  KEY `id_societe` (`id_societe`),
  KEY `id_frais_kilometrique` (`id_frais_kilometrique`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `opportunite`
--

DROP TABLE IF EXISTS `opportunite`;
CREATE TABLE IF NOT EXISTS `opportunite` (
  `id_opportunite` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `opportunite` varchar(128) NOT NULL,
  `etat` enum('en_cours','fini','mort') NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `id_owner` mediumint(8) UNSIGNED NOT NULL,
  `source` enum('appel','email','reseau','prive','autre') DEFAULT NULL,
  `source_detail` varchar(128) DEFAULT NULL,
  `ca` mediumint(9) DEFAULT NULL,
  `marge` mediumint(9) DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  `echeance` date DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `prive` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_opportunite`),
  KEY `id_user` (`id_user`),
  KEY `id_societe` (`id_societe`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_owner` (`id_owner`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ordre_de_mission`
--

DROP TABLE IF EXISTS `ordre_de_mission`;
CREATE TABLE IF NOT EXISTS `ordre_de_mission` (
  `id_ordre_de_mission` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ordre_de_mission` varchar(255) NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `moyen_transport` varchar(50) DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `adresse` varchar(64) NOT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(8) NOT NULL,
  `ville` varchar(32) NOT NULL,
  `etat` enum('en_cours','termine','annule') NOT NULL DEFAULT 'en_cours',
  `id_hotline` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_pays` char(2) NOT NULL DEFAULT 'FR',
  PRIMARY KEY (`id_ordre_de_mission`),
  KEY `id_societe` (`id_societe`),
  KEY `id_contact` (`id_contact`),
  KEY `id_user` (`id_user`),
  KEY `id_hotline` (`id_hotline`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `parc_client`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `parc_client`;
CREATE TABLE IF NOT EXISTS `parc_client` (
`id_societe` mediumint(8) unsigned
,`id_affaire` mediumint(8) unsigned
,`ref_affaire` char(0)
,`ref` char(0)
,`libelle` char(0)
,`divers` char(0)
,`serial` char(0)
,`code` char(0)
,`date` datetime
,`date_inactif` char(0)
,`date_garantie` char(0)
,`date_achat` char(0)
,`existence` char(0)
,`etat` enum('devis','commande','facture','terminee','perdue')
);

-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

DROP TABLE IF EXISTS `pays`;
CREATE TABLE IF NOT EXISTS `pays` (
  `id_pays` char(2) CHARACTER SET utf8 NOT NULL,
  `pays` varchar(128) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id_pays`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `pays`
--

INSERT INTO `pays` (`id_pays`, `pays`) VALUES
('AD', 'Andorre'),
('AE', 'Émirats arabes unis'),
('AF', 'Afghanistan'),
('AG', 'Antigua-et-Barbuda'),
('AI', 'Anguilla'),
('AL', 'Albanie'),
('AM', 'Arménie'),
('AN', 'Antilles néerlandaises'),
('AO', 'Angola'),
('AQ', 'Antarctique'),
('AR', 'Argentine'),
('AS', 'Samoa américaines'),
('AT', 'Autriche'),
('AU', 'Australie'),
('AW', 'Aruba'),
('AZ', 'Azerbaïdjan'),
('BA', 'Bosnie-Herzégovine'),
('BB', 'Barbade'),
('BD', 'Bangladesh'),
('BE', 'Belgique'),
('BF', 'Burkina Faso'),
('BG', 'Bulgarie'),
('BH', 'Bahreïn'),
('BI', 'Burundi'),
('BJ', 'Bénin'),
('BM', 'Bermudes'),
('BN', 'Brunei'),
('BO', 'Bolivie'),
('BR', 'Brésil'),
('BS', 'Bahamas'),
('BT', 'Bhoutan'),
('BV', 'Bouvet &#40;île&#41;'),
('BW', 'Botswana'),
('BY', 'Biélorussie'),
('BZ', 'Belize'),
('CA', 'Canada'),
('CC', 'Cocos &#40;îles&#41;/Keeling &#40;îles&#41;'),
('CD', 'Congo &#40;RDC&#41;'),
('CF', 'République centrafricaine'),
('CG', 'Congo'),
('CH', 'Suisse'),
('CI', 'Côte D&#39;Ivoire'),
('CK', 'Cook &#40;îles&#41;'),
('CL', 'Chili'),
('CM', 'Cameroun'),
('CN', 'Chine'),
('CO', 'Colombie'),
('CR', 'Costa Rica'),
('CU', 'Cuba'),
('CV', 'Cap-Vert'),
('CX', 'Christmas &#40;île&#41;'),
('CY', 'Chypre'),
('CZ', 'République tchèque'),
('DE', 'Allemagne'),
('DJ', 'Djibouti'),
('DK', 'Danemark'),
('DM', 'Dominique'),
('DO', 'République dominicaine'),
('DZ', 'Algérie'),
('EC', 'Équateur'),
('EE', 'Estonie'),
('EG', 'Égypte'),
('ER', 'Érythrée'),
('ES', 'Espagne'),
('ET', 'Éthiopie'),
('FI', 'Finlande'),
('FJ', 'Fidji &#40;îles&#41;'),
('FK', 'Malouines &#40;îles&#41;'),
('FM', 'Micronésie'),
('FO', 'Féroé &#40;îles&#41;'),
('FR', 'France'),
('GA', 'Gabon'),
('GD', 'Grenade'),
('GE', 'Géorgie'),
('GF', 'Guyane française'),
('GH', 'Ghana'),
('GI', 'Gibraltar'),
('GL', 'Groenland'),
('GM', 'Gambie'),
('GN', 'Guinée'),
('GP', 'Guadeloupe'),
('GQ', 'Guinée équatoriale'),
('GR', 'Grèce'),
('GS', 'Géorgie du Sud et Sandwich du Sud &#40;îles&#41;'),
('GT', 'Guatemala'),
('GU', 'Guam'),
('GW', 'Guinée-Bissau'),
('GY', 'Guyane'),
('HK', 'RAS de Hong Kong'),
('HM', 'Heard et McDonald &#40;îles&#41;'),
('HN', 'Honduras'),
('HR', 'Croatie &#40;Hrvatska&#41;'),
('HT', 'Haïti'),
('HU', 'Hongrie'),
('ID', 'Indonésie'),
('IE', 'Irlande'),
('IL', 'Israël'),
('IN', 'Inde'),
('IO', 'Territoires britanniques de l&#39;Océan Indien'),
('IQ', 'Irak'),
('IR', 'Iran'),
('IS', 'Islande'),
('IT', 'Italie'),
('JM', 'Jamaïque'),
('JO', 'Jordanie'),
('JP', 'Japon'),
('KE', 'Kenya'),
('KG', 'Kirghizistan'),
('KH', 'Cambodge'),
('KI', 'Kiribati'),
('KM', 'Comores'),
('KN', 'Saint-Christophe-et-Niévès'),
('KP', 'Corée du Nord'),
('KR', 'Corée'),
('KW', 'Koweït'),
('KY', 'Caïmans &#40;îles&#41;'),
('KZ', 'Kazakhstan'),
('LA', 'Laos'),
('LB', 'Liban'),
('LC', 'Sainte-Lucie'),
('LI', 'Liechtenstein'),
('LK', 'Sri Lanka'),
('LR', 'Liberia'),
('LS', 'Lesotho'),
('LT', 'Lituanie'),
('LU', 'Luxembourg'),
('LV', 'Lettonie'),
('LY', 'Libye'),
('MA', 'Maroc'),
('MC', 'Monaco'),
('MD', 'Moldavie'),
('MG', 'Madagascar'),
('MH', 'Marshall &#40;îles&#41;'),
('MK', 'Ex-République yougoslave de Macédoine'),
('ML', 'Mali'),
('MM', 'Myanmar'),
('MN', 'Mongolie'),
('MO', 'RAS de Macao'),
('MP', 'Mariannes du Nord &#40;îles&#41;'),
('MQ', 'Martinique'),
('MR', 'Mauritanie'),
('MS', 'Montserrat'),
('MT', 'Malte'),
('MU', 'Maurice'),
('MV', 'Maldives'),
('MW', 'Malawi'),
('MX', 'Mexique'),
('MY', 'Malaisie'),
('MZ', 'Mozambique'),
('NA', 'Namibie'),
('NC', 'Nouvelle-Calédonie'),
('NE', 'Niger'),
('NF', 'Norfolk &#40;île&#41;'),
('NG', 'Nigéria'),
('NI', 'Nicaragua'),
('NL', 'Pays-Bas'),
('NO', 'Norvège'),
('NP', 'Népal'),
('NR', 'Nauru'),
('NU', 'Niue'),
('NZ', 'Nouvelle-Zélande'),
('OM', 'Oman'),
('PA', 'Panama'),
('PE', 'Pérou'),
('PF', 'Polynésie française'),
('PG', 'Papouasie-Nouvelle-Guinée'),
('PH', 'Philippines'),
('PK', 'Pakistan'),
('PL', 'Pologne'),
('PM', 'Saint-Pierre-et-Miquelon'),
('PN', 'Pitcairn &#40;îles&#41;'),
('PR', 'Porto Rico'),
('PT', 'Portugal'),
('PW', 'Palau'),
('PY', 'Paraguay'),
('QA', 'Qatar'),
('RE', 'La Réunion'),
('RO', 'Roumanie'),
('RU', 'Russie'),
('RW', 'Rwanda'),
('SA', 'Arabie Saoudite'),
('SB', 'Salomon &#40;îles&#41;'),
('SC', 'Seychelles'),
('SD', 'Soudan'),
('SE', 'Suède'),
('SG', 'Singapour'),
('SH', 'Sainte-Hélène'),
('SI', 'Slovénie'),
('SJ', 'Svalbard et Jan Mayen &#40;îles&#41;'),
('SK', 'Slovaquie'),
('SL', 'Sierra Leone'),
('SM', 'Saint-Marin'),
('SN', 'Sénégal'),
('SO', 'Somalie'),
('SR', 'Surinam'),
('ST', 'Sao Tomé-et-Principe'),
('SV', 'Salvador'),
('SY', 'Syrie'),
('SZ', 'Swaziland'),
('TC', 'Turks et Caicos &#40;îles&#41;'),
('TD', 'Tchad'),
('TF', 'Terres Australes et Antarctiques françaises'),
('TG', 'Togo'),
('TH', 'Thaïlande'),
('TJ', 'Tadjikistan'),
('TK', 'Tokelau'),
('TM', 'Turkménistan'),
('TN', 'Tunisie'),
('TO', 'Tonga'),
('TP', 'Timor-Oriental'),
('TR', 'Turquie'),
('TT', 'Trinité-et-Tobago'),
('TV', 'Tuvalu'),
('TW', 'Taiwan'),
('TZ', 'Tanzanie'),
('UA', 'Ukraine'),
('UG', 'Ouganda'),
('UK', 'Royaume-Uni'),
('UM', 'Dépendances américaines du Pacifique'),
('US', 'États-Unis'),
('UY', 'Uruguay'),
('UZ', 'Ouzbékistan'),
('VA', 'Cité du Vatican'),
('VC', 'Saint-Vincent-et-les Grenadines'),
('VE', 'Venezuela'),
('VG', 'Vierges britanniques &#40;îles&#41;'),
('VI', 'Vierges &#40;îles&#41;'),
('VN', 'Vietnam'),
('VU', 'Vanuatu'),
('WF', 'Wallis-et-Futuna'),
('WS', 'Samoa'),
('YE', 'Yémen'),
('YT', 'Mayotte'),
('YU', 'Yougoslavie'),
('ZA', 'Afrique du Sud'),
('ZM', 'Zambie'),
('ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Structure de la table `phone`
--

DROP TABLE IF EXISTS `phone`;
CREATE TABLE IF NOT EXISTS `phone` (
  `id_phone` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone` varchar(64) NOT NULL,
  `sip` varchar(32) NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_asterisk` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_phone`),
  KEY `id_user` (`id_user`),
  KEY `id_asterisk` (`id_asterisk`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pointage`
--

DROP TABLE IF EXISTS `pointage`;
CREATE TABLE IF NOT EXISTS `pointage` (
  `id_pointage` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_conge` mediumint(8) UNSIGNED DEFAULT NULL,
  `sujet` varchar(50) NOT NULL DEFAULT 'default',
  `id_gep_projet` int(11) DEFAULT NULL,
  `type` enum('production','rd','conge_annuel','conge_legaux','conge_sans_solde','conge_paye','arret','cours','conge','hotline','reunion') NOT NULL DEFAULT 'production',
  `temps` time NOT NULL DEFAULT '00:00:00',
  `id_hotline` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_hotline_interaction` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Un pointage par interaction !',
  PRIMARY KEY (`id_pointage`),
  UNIQUE KEY `id_hotline_interaction` (`id_hotline_interaction`),
  KEY `conge` (`id_conge`),
  KEY `id_gep_projet` (`id_gep_projet`),
  KEY `id_hotline` (`id_hotline`),
  KEY `id_user` (`id_user`),
  KEY `id_affaire` (`id_affaire`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `politesse`
--

DROP TABLE IF EXISTS `politesse`;
CREATE TABLE IF NOT EXISTS `politesse` (
  `id_politesse` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('prefixee','postfixee') NOT NULL DEFAULT 'prefixee',
  `politesse` varchar(255) NOT NULL,
  PRIMARY KEY (`id_politesse`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='Formules de politesse' PACK_KEYS=0;

--
-- Déchargement des données de la table `politesse`
--

INSERT INTO `politesse` (`id_politesse`, `type`, `politesse`) VALUES
(8, 'prefixee', 'Suite à notre conversation téléphonique, veuillez trouver ci-dessous nos meilleurs tarifs pour l\'affaire nous concernant. Les délais à titre indicatif sont de 4 semaines environ.'),
(2, 'prefixee', 'Veuillez trouver ci-dessous notre proposition concernant la fourniture des matériels demandés, ainsi que de leur installation.'),
(3, 'prefixee', 'Veuillez trouver ci-dessous notre proposition concernant l\'affaire évoquée récemment.'),
(4, 'postfixee', 'Nous restons à votre entière disposition pour tous compléments d\'information. Veuillez agréer, Madame, l\'expression de nos sentiments les meilleurs.'),
(5, 'postfixee', 'Nous restons à votre entière disposition pour tous compléments d\'information. Veuillez agréer, Monsieur, l\'expression de nos sentiments les meilleurs.'),
(6, 'postfixee', 'Veuillez agréer, Monsieur, l\'expression de nos sentiments les meilleurs.'),
(7, 'postfixee', 'Veuillez agréer, Madame, l\'expression de nos sentiments les meilleurs.'),
(9, 'prefixee', 'Suite à notre conversation téléphonique, veuillez trouver ci-dessous nos meilleurs tarifs pour l\'affaire nous concernant.'),
(10, 'prefixee', 'Votre domaine arrive à échéance dans 30 jours. Si vous le désirez, vous pouvez nous renvoyer le bon pour accord ci-joint afin de renouveler votre domaine pour une durée d\'un an, sans quoi celui-ci redeviendra disponible à l\'enregistrement au public.'),
(11, 'prefixee', 'Votre domaine arrive à échéance dans 15 jours. Si vous le désirez, vous pouvez nous renvoyer le bon pour accord ci-joint afin de renouveler votre domaine pour une durée d\'un an, sans quoi celui-ci redeviendra disponible à l\'enregistrement au public.'),
(12, 'postfixee', 'Veuillez agréer, Mademoiselle, l\'expression de nos sentiments les meilleurs.'),
(14, 'prefixee', 'Votre domaine arrive à échéance dans moins d\'une semaine. Si vous le désirez, vous pouvez nous renvoyer le bon pour accord ci-joint afin de renouveler votre domaine pour une durée d\'un an, sans quoi celui-ci redeviendra disponible au public.'),
(15, 'prefixee', 'Dans le cadre de la réponse à l\'appel d\'offres en cours, vous trouverez ci-dessous notre proposition détaillée.'),
(16, 'prefixee', 'Vos licences Dell SonicWall sont arrivées à échéance. Nous vous préconisons pour des questions de sécurité d\'y souscrire de nouveau pour une durée de 3 ans en nous renvoyant le bon pour accord ci-joint.'),
(17, 'prefixee', 'Veuillez trouver ci-dessous notre proposition pour créditer votre compte en tickets de maintenance et de support technique.'),
(18, 'prefixee', 'Vos licences Dell SonicWall sont arrivées à échéance. Nous vous préconisons pour des questions de sécurité réseau d\'y souscrire de nouveau en nous renvoyant le bon pour accord ci-joint.'),
(19, 'prefixee', 'Vos licences antivirus Kaspersky arrivent à échéance. Nous vous préconisons pour des questions de sécurité d\'y souscrire de nouveau pour une durée de 3 ans en nous renvoyant le bon pour accord ci-joint.'),
(20, 'prefixee', 'Vos licences antivirus Kaspersky arrivent à échéance. Nous vous préconisons pour des questions de sécurité d\'y souscrire de nouveau pour une durée de 1 an en nous renvoyant le bon pour accord ci-joint.');

-- --------------------------------------------------------

--
-- Structure de la table `print_alerte`
--

DROP TABLE IF EXISTS `print_alerte`;
CREATE TABLE IF NOT EXISTS `print_alerte` (
  `id_print_alerte` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `notification` enum('oui','non') NOT NULL DEFAULT 'oui',
  `date_cloture` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_print_alerte`),
  KEY `id_stock` (`id_stock`),
  KEY `date_cloture` (`date_cloture`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `print_consommable`
--

DROP TABLE IF EXISTS `print_consommable`;
CREATE TABLE IF NOT EXISTS `print_consommable` (
  `id_print_consommable` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `designation` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `duree` int(10) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `ref_stock` varchar(32) NOT NULL,
  `couleur` enum('noir','cyan','magenta','jaune','autre') DEFAULT NULL,
  PRIMARY KEY (`id_print_consommable`),
  KEY `consommable_imprimante_ibfk_1` (`ref_stock`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `print_etat`
--

DROP TABLE IF EXISTS `print_etat`;
CREATE TABLE IF NOT EXISTS `print_etat` (
  `id_print_etat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `color` enum('other','unknow','cyan','magenta','yellow','black') DEFAULT NULL,
  `current` bigint(20) NOT NULL,
  `max` int(11) DEFAULT NULL,
  `type` enum('toner','copie_noir','copie_couleur') NOT NULL,
  PRIMARY KEY (`id_print_etat`),
  KEY `id_stock` (`id_stock`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `print_etat_consommable`
--

DROP TABLE IF EXISTS `print_etat_consommable`;
CREATE TABLE IF NOT EXISTS `print_etat_consommable` (
  `id_print_etat_consommable` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `id_print_consommable` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_print_etat_consommable`),
  UNIQUE KEY `date` (`date`,`id_stock`,`id_print_consommable`),
  KEY `id_stock` (`id_stock`),
  KEY `id_consommable` (`id_print_consommable`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `privilege`
--

DROP TABLE IF EXISTS `privilege`;
CREATE TABLE IF NOT EXISTS `privilege` (
  `id_privilege` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `privilege` varchar(32) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_privilege`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Privileges pour les droits des profils';

--
-- Déchargement des données de la table `privilege`
--

INSERT INTO `privilege` (`id_privilege`, `privilege`, `note`) VALUES
(1, 'select', ''),
(2, 'insert', ''),
(3, 'update', ''),
(4, 'delete', ''),
(5, 'view', 'Modification de la vue des listing (personnaliser la vue)'),
(6, 'filter_insert', 'Création de filtres'),
(7, 'filter_select', 'Utilisation des filtres'),
(8, 'export', 'Exporter un listing'),
(9, 'import', 'Importer des données'),
(10, 'geolocalisation', 'Google map');

-- --------------------------------------------------------

--
-- Structure de la table `processeur`
--

DROP TABLE IF EXISTS `processeur`;
CREATE TABLE IF NOT EXISTS `processeur` (
  `id_processeur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `processeur` varchar(32) NOT NULL,
  PRIMARY KEY (`id_processeur`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

DROP TABLE IF EXISTS `produit`;
CREATE TABLE IF NOT EXISTS `produit` (
  `id_produit` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref` varchar(32) NOT NULL,
  `produit` varchar(128) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `prix` decimal(10,2) UNSIGNED NOT NULL,
  `prix_achat` decimal(10,2) UNSIGNED DEFAULT NULL,
  `id_fabriquant` mediumint(8) UNSIGNED NOT NULL,
  `id_sous_categorie` mediumint(8) UNSIGNED NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `id_compte_absystech` mediumint(8) UNSIGNED DEFAULT 1,
  PRIMARY KEY (`id_produit`),
  UNIQUE KEY `ref` (`ref`),
  KEY `id_fabriquant` (`id_fabriquant`),
  KEY `id_sous_categorie` (`id_sous_categorie`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_compte_absystech` (`id_compte_absystech`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `profil`
--

DROP TABLE IF EXISTS `profil`;
CREATE TABLE IF NOT EXISTS `profil` (
  `id_profil` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profil` varchar(256) NOT NULL,
  `seuil` int(4) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`id_profil`),
  UNIQUE KEY `profil` (`profil`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `profil`
--

INSERT INTO `profil` (`id_profil`, `profil`, `seuil`) VALUES
(1, 'Admin', NULL),
(4, 'Technicien', 5000),
(7, 'Stagiaire', 0),
(9, 'Assistant de direction', 2000),
(10, 'Développeur', 2000),
(11, 'Apporteur d\'affaire', 1000),
(13, 'Apple Store', 0),
(14, 'Power Développeur', 5000),
(15, 'Commercial', 0),
(16, 'Développeur Extérieur', 0),
(17, 'Visu Societe', 0);

-- --------------------------------------------------------

--
-- Structure de la table `profil_privilege`
--

DROP TABLE IF EXISTS `profil_privilege`;
CREATE TABLE IF NOT EXISTS `profil_privilege` (
  `id_profil_privilege` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_profil` tinyint(3) UNSIGNED NOT NULL,
  `id_privilege` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Privilège associé (action ou ressource distincte) NULL si tous les privilèges',
  `id_module` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Module sollicité, NULL si cela concerne autre chose',
  `field` varchar(32) DEFAULT NULL COMMENT 'Champ particulier du module spécifié',
  PRIMARY KEY (`id_profil_privilege`),
  UNIQUE KEY `privilege` (`id_profil`,`id_privilege`,`id_module`,`field`),
  KEY `id_module` (`id_module`),
  KEY `id_profil` (`id_profil`),
  KEY `id_privilege` (`id_privilege`)
) ENGINE=InnoDB AUTO_INCREMENT=17514 DEFAULT CHARSET=utf8 COMMENT='Droits sur les ressources par profil';

--
-- Déchargement des données de la table `profil_privilege`
--

INSERT INTO `profil_privilege` (`id_profil_privilege`, `id_profil`, `id_privilege`, `id_module`, `field`) VALUES
(12332, 1, NULL, 153, NULL),
(12944, 1, 1, 2, NULL),
(12964, 1, 1, 3, NULL),
(12954, 1, 1, 4, NULL),
(12974, 1, 1, 9, NULL),
(12854, 1, 1, 37, NULL),
(12864, 1, 1, 38, NULL),
(12884, 1, 1, 39, NULL),
(12924, 1, 1, 41, NULL),
(12894, 1, 1, 42, NULL),
(12904, 1, 1, 43, NULL),
(12874, 1, 1, 44, NULL),
(12914, 1, 1, 45, NULL),
(17485, 1, 1, 46, NULL),
(12984, 1, 1, 47, NULL),
(13044, 1, 1, 48, NULL),
(13697, 1, 1, 49, NULL),
(13194, 1, 1, 50, NULL),
(8820, 1, 1, 51, NULL),
(12994, 1, 1, 52, NULL),
(13024, 1, 1, 53, NULL),
(13034, 1, 1, 54, NULL),
(8818, 1, 1, 55, NULL),
(13084, 1, 1, 59, NULL),
(13054, 1, 1, 60, NULL),
(13124, 1, 1, 62, NULL),
(13234, 1, 1, 68, NULL),
(8868, 1, 1, 69, NULL),
(13224, 1, 1, 70, NULL),
(8869, 1, 1, 71, NULL),
(8858, 1, 1, 72, NULL),
(8823, 1, 1, 75, NULL),
(8821, 1, 1, 82, NULL),
(8880, 1, 1, 83, NULL),
(8882, 1, 1, 84, NULL),
(13134, 1, 1, 86, NULL),
(13244, 1, 1, 87, NULL),
(8844, 1, 1, 89, NULL),
(8847, 1, 1, 92, NULL),
(8846, 1, 1, 93, NULL),
(8822, 1, 1, 97, NULL),
(8827, 1, 1, 98, NULL),
(8824, 1, 1, 101, NULL),
(8825, 1, 1, 102, NULL),
(8811, 1, 1, 111, NULL),
(8883, 1, 1, 116, NULL),
(8884, 1, 1, 117, NULL),
(8889, 1, 1, 118, NULL),
(8887, 1, 1, 119, NULL),
(8886, 1, 1, 120, NULL),
(8888, 1, 1, 121, NULL),
(8891, 1, 1, 122, NULL),
(8885, 1, 1, 123, NULL),
(8890, 1, 1, 124, NULL),
(8805, 1, 1, 125, NULL),
(8806, 1, 1, 126, NULL),
(13214, 1, 1, 132, NULL),
(8808, 1, 1, 134, NULL),
(8807, 1, 1, 135, NULL),
(14306, 1, 1, 139, NULL),
(8809, 1, 1, 140, NULL),
(13254, 1, 1, 141, NULL),
(8876, 1, 1, 142, NULL),
(13264, 1, 1, 143, NULL),
(8878, 1, 1, 144, NULL),
(8826, 1, 1, 145, NULL),
(8877, 1, 1, 148, NULL),
(8879, 1, 1, 149, NULL),
(8830, 1, 1, 150, NULL),
(13094, 1, 1, 151, NULL),
(13064, 1, 1, 152, NULL),
(13371, 1, 1, 153, NULL),
(8851, 1, 1, 154, NULL),
(8852, 1, 1, 155, NULL),
(8853, 1, 1, 156, NULL),
(8871, 1, 1, 157, NULL),
(8854, 1, 1, 160, NULL),
(12322, 1, 1, 161, NULL),
(13638, 1, 1, 167, NULL),
(13666, 1, 1, 168, NULL),
(13676, 1, 1, 169, NULL),
(13391, 1, 1, 170, NULL),
(13401, 1, 1, 173, NULL),
(13655, 1, 1, 174, NULL),
(13687, 1, 1, 175, NULL),
(13496, 1, 1, 176, NULL),
(13506, 1, 1, 177, NULL),
(14214, 1, 1, 179, NULL),
(14258, 1, 1, 190, NULL),
(17105, 1, 1, 191, NULL),
(14248, 1, 1, 192, NULL),
(14315, 1, 1, 193, NULL),
(14324, 1, 1, 194, NULL),
(14524, 1, 1, 195, NULL),
(14887, 1, 1, 196, NULL),
(14630, 1, 1, 197, NULL),
(14963, 1, 1, 198, NULL),
(17125, 1, 1, 2046, NULL),
(16282, 1, 1, 2047, NULL),
(16505, 1, 1, 2048, NULL),
(16766, 1, 1, 2052, NULL),
(16786, 1, 1, 2053, NULL),
(16776, 1, 1, 2054, NULL),
(16979, 1, 1, 2055, NULL),
(17115, 1, 1, 2056, NULL),
(12943, 1, 2, 2, NULL),
(12963, 1, 2, 3, NULL),
(12953, 1, 2, 4, NULL),
(12973, 1, 2, 9, NULL),
(12853, 1, 2, 37, NULL),
(12863, 1, 2, 38, NULL),
(12883, 1, 2, 39, NULL),
(12923, 1, 2, 41, NULL),
(12893, 1, 2, 42, NULL),
(12903, 1, 2, 43, NULL),
(12873, 1, 2, 44, NULL),
(12913, 1, 2, 45, NULL),
(17484, 1, 2, 46, NULL),
(12983, 1, 2, 47, NULL),
(13043, 1, 2, 48, NULL),
(13193, 1, 2, 50, NULL),
(9173, 1, 2, 51, NULL),
(12993, 1, 2, 52, NULL),
(13023, 1, 2, 53, NULL),
(13033, 1, 2, 54, NULL),
(9171, 1, 2, 55, NULL),
(13083, 1, 2, 59, NULL),
(13053, 1, 2, 60, NULL),
(13123, 1, 2, 62, NULL),
(13233, 1, 2, 68, NULL),
(9221, 1, 2, 69, NULL),
(13223, 1, 2, 70, NULL),
(9222, 1, 2, 71, NULL),
(9211, 1, 2, 72, NULL),
(9176, 1, 2, 75, NULL),
(9174, 1, 2, 82, NULL),
(9233, 1, 2, 83, NULL),
(9235, 1, 2, 84, NULL),
(13133, 1, 2, 86, NULL),
(13243, 1, 2, 87, NULL),
(9197, 1, 2, 89, NULL),
(9200, 1, 2, 92, NULL),
(9199, 1, 2, 93, NULL),
(9175, 1, 2, 97, NULL),
(9180, 1, 2, 98, NULL),
(9177, 1, 2, 101, NULL),
(9178, 1, 2, 102, NULL),
(9164, 1, 2, 111, NULL),
(9236, 1, 2, 116, NULL),
(9237, 1, 2, 117, NULL),
(9242, 1, 2, 118, NULL),
(9240, 1, 2, 119, NULL),
(9239, 1, 2, 120, NULL),
(9241, 1, 2, 121, NULL),
(9244, 1, 2, 122, NULL),
(9238, 1, 2, 123, NULL),
(9243, 1, 2, 124, NULL),
(9158, 1, 2, 125, NULL),
(9159, 1, 2, 126, NULL),
(13213, 1, 2, 132, NULL),
(9161, 1, 2, 134, NULL),
(9160, 1, 2, 135, NULL),
(14305, 1, 2, 139, NULL),
(9162, 1, 2, 140, NULL),
(13253, 1, 2, 141, NULL),
(9229, 1, 2, 142, NULL),
(13263, 1, 2, 143, NULL),
(9231, 1, 2, 144, NULL),
(9179, 1, 2, 145, NULL),
(9230, 1, 2, 148, NULL),
(9232, 1, 2, 149, NULL),
(9183, 1, 2, 150, NULL),
(13093, 1, 2, 151, NULL),
(13063, 1, 2, 152, NULL),
(13370, 1, 2, 153, NULL),
(9204, 1, 2, 154, NULL),
(9205, 1, 2, 155, NULL),
(9206, 1, 2, 156, NULL),
(9224, 1, 2, 157, NULL),
(9207, 1, 2, 160, NULL),
(12323, 1, 2, 161, NULL),
(13637, 1, 2, 167, NULL),
(13665, 1, 2, 168, NULL),
(13675, 1, 2, 169, NULL),
(13390, 1, 2, 170, NULL),
(13400, 1, 2, 173, NULL),
(13654, 1, 2, 174, NULL),
(13688, 1, 2, 175, NULL),
(13495, 1, 2, 176, NULL),
(13505, 1, 2, 177, NULL),
(14213, 1, 2, 179, NULL),
(14257, 1, 2, 190, NULL),
(17104, 1, 2, 191, NULL),
(14247, 1, 2, 192, NULL),
(14314, 1, 2, 193, NULL),
(14323, 1, 2, 194, NULL),
(14523, 1, 2, 195, NULL),
(14886, 1, 2, 196, NULL),
(14629, 1, 2, 197, NULL),
(14962, 1, 2, 198, NULL),
(17124, 1, 2, 2046, NULL),
(16504, 1, 2, 2048, NULL),
(16765, 1, 2, 2052, NULL),
(16785, 1, 2, 2053, NULL),
(16775, 1, 2, 2054, NULL),
(16978, 1, 2, 2055, NULL),
(17114, 1, 2, 2056, NULL),
(12942, 1, 3, 2, NULL),
(12962, 1, 3, 3, NULL),
(12952, 1, 3, 4, NULL),
(12972, 1, 3, 9, NULL),
(12852, 1, 3, 37, NULL),
(12862, 1, 3, 38, NULL),
(12882, 1, 3, 39, NULL),
(12922, 1, 3, 41, NULL),
(12892, 1, 3, 42, NULL),
(12902, 1, 3, 43, NULL),
(12872, 1, 3, 44, NULL),
(12912, 1, 3, 45, NULL),
(17483, 1, 3, 46, NULL),
(12982, 1, 3, 47, NULL),
(13042, 1, 3, 48, NULL),
(13192, 1, 3, 50, NULL),
(9526, 1, 3, 51, NULL),
(12992, 1, 3, 52, NULL),
(13022, 1, 3, 53, NULL),
(13032, 1, 3, 54, NULL),
(9524, 1, 3, 55, NULL),
(13082, 1, 3, 59, NULL),
(13052, 1, 3, 60, NULL),
(13122, 1, 3, 62, NULL),
(13232, 1, 3, 68, NULL),
(9574, 1, 3, 69, NULL),
(13222, 1, 3, 70, NULL),
(9575, 1, 3, 71, NULL),
(9564, 1, 3, 72, NULL),
(9529, 1, 3, 75, NULL),
(9527, 1, 3, 82, NULL),
(9586, 1, 3, 83, NULL),
(9588, 1, 3, 84, NULL),
(13132, 1, 3, 86, NULL),
(13242, 1, 3, 87, NULL),
(9550, 1, 3, 89, NULL),
(9553, 1, 3, 92, NULL),
(9552, 1, 3, 93, NULL),
(9528, 1, 3, 97, NULL),
(9533, 1, 3, 98, NULL),
(9530, 1, 3, 101, NULL),
(9531, 1, 3, 102, NULL),
(9517, 1, 3, 111, NULL),
(9589, 1, 3, 116, NULL),
(9590, 1, 3, 117, NULL),
(9595, 1, 3, 118, NULL),
(9593, 1, 3, 119, NULL),
(9592, 1, 3, 120, NULL),
(9594, 1, 3, 121, NULL),
(9597, 1, 3, 122, NULL),
(9591, 1, 3, 123, NULL),
(9596, 1, 3, 124, NULL),
(9511, 1, 3, 125, NULL),
(9512, 1, 3, 126, NULL),
(13212, 1, 3, 132, NULL),
(9514, 1, 3, 134, NULL),
(9513, 1, 3, 135, NULL),
(14304, 1, 3, 139, NULL),
(9515, 1, 3, 140, NULL),
(13252, 1, 3, 141, NULL),
(9582, 1, 3, 142, NULL),
(13262, 1, 3, 143, NULL),
(9584, 1, 3, 144, NULL),
(9532, 1, 3, 145, NULL),
(9583, 1, 3, 148, NULL),
(9585, 1, 3, 149, NULL),
(9536, 1, 3, 150, NULL),
(13092, 1, 3, 151, NULL),
(13062, 1, 3, 152, NULL),
(13369, 1, 3, 153, NULL),
(9557, 1, 3, 154, NULL),
(9558, 1, 3, 155, NULL),
(9559, 1, 3, 156, NULL),
(9577, 1, 3, 157, NULL),
(9560, 1, 3, 160, NULL),
(12324, 1, 3, 161, NULL),
(13636, 1, 3, 167, NULL),
(13664, 1, 3, 168, NULL),
(13674, 1, 3, 169, NULL),
(13389, 1, 3, 170, NULL),
(13399, 1, 3, 173, NULL),
(13653, 1, 3, 174, NULL),
(13689, 1, 3, 175, NULL),
(13494, 1, 3, 176, NULL),
(13504, 1, 3, 177, NULL),
(14212, 1, 3, 179, NULL),
(14256, 1, 3, 190, NULL),
(17103, 1, 3, 191, NULL),
(14246, 1, 3, 192, NULL),
(14313, 1, 3, 193, NULL),
(14322, 1, 3, 194, NULL),
(14522, 1, 3, 195, NULL),
(14885, 1, 3, 196, NULL),
(14628, 1, 3, 197, NULL),
(14961, 1, 3, 198, NULL),
(17123, 1, 3, 2046, NULL),
(16503, 1, 3, 2048, NULL),
(16764, 1, 3, 2052, NULL),
(16784, 1, 3, 2053, NULL),
(16774, 1, 3, 2054, NULL),
(16977, 1, 3, 2055, NULL),
(17113, 1, 3, 2056, NULL),
(12941, 1, 4, 2, NULL),
(12961, 1, 4, 3, NULL),
(12951, 1, 4, 4, NULL),
(12971, 1, 4, 9, NULL),
(12851, 1, 4, 37, NULL),
(12861, 1, 4, 38, NULL),
(12881, 1, 4, 39, NULL),
(12921, 1, 4, 41, NULL),
(12891, 1, 4, 42, NULL),
(12901, 1, 4, 43, NULL),
(12871, 1, 4, 44, NULL),
(12911, 1, 4, 45, NULL),
(12981, 1, 4, 47, NULL),
(13041, 1, 4, 48, NULL),
(13191, 1, 4, 50, NULL),
(9879, 1, 4, 51, NULL),
(12991, 1, 4, 52, NULL),
(13021, 1, 4, 53, NULL),
(13031, 1, 4, 54, NULL),
(9877, 1, 4, 55, NULL),
(13081, 1, 4, 59, NULL),
(13051, 1, 4, 60, NULL),
(13121, 1, 4, 62, NULL),
(13231, 1, 4, 68, NULL),
(9927, 1, 4, 69, NULL),
(13221, 1, 4, 70, NULL),
(9928, 1, 4, 71, NULL),
(9917, 1, 4, 72, NULL),
(9882, 1, 4, 75, NULL),
(9880, 1, 4, 82, NULL),
(9939, 1, 4, 83, NULL),
(9941, 1, 4, 84, NULL),
(13131, 1, 4, 86, NULL),
(13241, 1, 4, 87, NULL),
(9903, 1, 4, 89, NULL),
(9906, 1, 4, 92, NULL),
(9905, 1, 4, 93, NULL),
(9881, 1, 4, 97, NULL),
(9886, 1, 4, 98, NULL),
(9883, 1, 4, 101, NULL),
(9884, 1, 4, 102, NULL),
(9870, 1, 4, 111, NULL),
(9942, 1, 4, 116, NULL),
(9943, 1, 4, 117, NULL),
(9948, 1, 4, 118, NULL),
(9946, 1, 4, 119, NULL),
(9945, 1, 4, 120, NULL),
(9947, 1, 4, 121, NULL),
(9950, 1, 4, 122, NULL),
(9944, 1, 4, 123, NULL),
(9949, 1, 4, 124, NULL),
(9864, 1, 4, 125, NULL),
(9865, 1, 4, 126, NULL),
(13211, 1, 4, 132, NULL),
(9867, 1, 4, 134, NULL),
(9866, 1, 4, 135, NULL),
(14303, 1, 4, 139, NULL),
(9868, 1, 4, 140, NULL),
(13251, 1, 4, 141, NULL),
(9935, 1, 4, 142, NULL),
(13261, 1, 4, 143, NULL),
(9937, 1, 4, 144, NULL),
(9885, 1, 4, 145, NULL),
(9936, 1, 4, 148, NULL),
(9938, 1, 4, 149, NULL),
(9889, 1, 4, 150, NULL),
(13091, 1, 4, 151, NULL),
(13061, 1, 4, 152, NULL),
(13368, 1, 4, 153, NULL),
(9910, 1, 4, 154, NULL),
(9911, 1, 4, 155, NULL),
(9912, 1, 4, 156, NULL),
(9930, 1, 4, 157, NULL),
(9913, 1, 4, 160, NULL),
(12325, 1, 4, 161, NULL),
(13635, 1, 4, 167, NULL),
(13663, 1, 4, 168, NULL),
(13673, 1, 4, 169, NULL),
(13388, 1, 4, 170, NULL),
(13398, 1, 4, 173, NULL),
(13652, 1, 4, 174, NULL),
(13690, 1, 4, 175, NULL),
(13493, 1, 4, 176, NULL),
(13503, 1, 4, 177, NULL),
(14211, 1, 4, 179, NULL),
(14255, 1, 4, 190, NULL),
(17102, 1, 4, 191, NULL),
(14245, 1, 4, 192, NULL),
(14312, 1, 4, 193, NULL),
(14321, 1, 4, 194, NULL),
(14521, 1, 4, 195, NULL),
(14884, 1, 4, 196, NULL),
(14627, 1, 4, 197, NULL),
(14960, 1, 4, 198, NULL),
(17122, 1, 4, 2046, NULL),
(16281, 1, 4, 2047, NULL),
(16502, 1, 4, 2048, NULL),
(16763, 1, 4, 2052, NULL),
(16783, 1, 4, 2053, NULL),
(16773, 1, 4, 2054, NULL),
(16976, 1, 4, 2055, NULL),
(17112, 1, 4, 2056, NULL),
(12940, 1, 5, 2, NULL),
(12960, 1, 5, 3, NULL),
(12950, 1, 5, 4, NULL),
(12970, 1, 5, 9, NULL),
(12850, 1, 5, 37, NULL),
(12860, 1, 5, 38, NULL),
(12880, 1, 5, 39, NULL),
(12920, 1, 5, 41, NULL),
(12890, 1, 5, 42, NULL),
(12900, 1, 5, 43, NULL),
(12870, 1, 5, 44, NULL),
(12910, 1, 5, 45, NULL),
(17482, 1, 5, 46, NULL),
(12980, 1, 5, 47, NULL),
(13040, 1, 5, 48, NULL),
(13190, 1, 5, 50, NULL),
(10232, 1, 5, 51, NULL),
(12990, 1, 5, 52, NULL),
(13020, 1, 5, 53, NULL),
(13030, 1, 5, 54, NULL),
(10230, 1, 5, 55, NULL),
(13080, 1, 5, 59, NULL),
(13050, 1, 5, 60, NULL),
(13120, 1, 5, 62, NULL),
(13230, 1, 5, 68, NULL),
(10280, 1, 5, 69, NULL),
(13220, 1, 5, 70, NULL),
(10281, 1, 5, 71, NULL),
(10270, 1, 5, 72, NULL),
(10235, 1, 5, 75, NULL),
(10233, 1, 5, 82, NULL),
(10292, 1, 5, 83, NULL),
(10294, 1, 5, 84, NULL),
(13130, 1, 5, 86, NULL),
(13240, 1, 5, 87, NULL),
(10256, 1, 5, 89, NULL),
(10259, 1, 5, 92, NULL),
(10258, 1, 5, 93, NULL),
(10234, 1, 5, 97, NULL),
(10239, 1, 5, 98, NULL),
(10236, 1, 5, 101, NULL),
(10237, 1, 5, 102, NULL),
(10223, 1, 5, 111, NULL),
(10295, 1, 5, 116, NULL),
(10296, 1, 5, 117, NULL),
(10301, 1, 5, 118, NULL),
(10299, 1, 5, 119, NULL),
(10298, 1, 5, 120, NULL),
(10300, 1, 5, 121, NULL),
(10303, 1, 5, 122, NULL),
(10297, 1, 5, 123, NULL),
(10302, 1, 5, 124, NULL),
(10217, 1, 5, 125, NULL),
(10218, 1, 5, 126, NULL),
(13210, 1, 5, 132, NULL),
(10220, 1, 5, 134, NULL),
(10219, 1, 5, 135, NULL),
(14302, 1, 5, 139, NULL),
(10221, 1, 5, 140, NULL),
(13250, 1, 5, 141, NULL),
(10288, 1, 5, 142, NULL),
(13260, 1, 5, 143, NULL),
(10290, 1, 5, 144, NULL),
(10238, 1, 5, 145, NULL),
(10289, 1, 5, 148, NULL),
(10291, 1, 5, 149, NULL),
(10242, 1, 5, 150, NULL),
(13090, 1, 5, 151, NULL),
(13060, 1, 5, 152, NULL),
(13367, 1, 5, 153, NULL),
(10263, 1, 5, 154, NULL),
(10264, 1, 5, 155, NULL),
(10265, 1, 5, 156, NULL),
(10283, 1, 5, 157, NULL),
(10266, 1, 5, 160, NULL),
(12326, 1, 5, 161, NULL),
(13634, 1, 5, 167, NULL),
(13662, 1, 5, 168, NULL),
(13672, 1, 5, 169, NULL),
(13387, 1, 5, 170, NULL),
(13397, 1, 5, 173, NULL),
(13651, 1, 5, 174, NULL),
(13691, 1, 5, 175, NULL),
(13492, 1, 5, 176, NULL),
(13502, 1, 5, 177, NULL),
(14210, 1, 5, 179, NULL),
(14254, 1, 5, 190, NULL),
(17101, 1, 5, 191, NULL),
(14244, 1, 5, 192, NULL),
(14311, 1, 5, 193, NULL),
(14320, 1, 5, 194, NULL),
(14520, 1, 5, 195, NULL),
(14883, 1, 5, 196, NULL),
(14626, 1, 5, 197, NULL),
(14959, 1, 5, 198, NULL),
(17121, 1, 5, 2046, NULL),
(16280, 1, 5, 2047, NULL),
(16501, 1, 5, 2048, NULL),
(16762, 1, 5, 2052, NULL),
(16782, 1, 5, 2053, NULL),
(16772, 1, 5, 2054, NULL),
(16975, 1, 5, 2055, NULL),
(17111, 1, 5, 2056, NULL),
(12939, 1, 6, 2, NULL),
(12959, 1, 6, 3, NULL),
(12949, 1, 6, 4, NULL),
(12969, 1, 6, 9, NULL),
(12849, 1, 6, 37, NULL),
(12859, 1, 6, 38, NULL),
(12879, 1, 6, 39, NULL),
(12919, 1, 6, 41, NULL),
(12889, 1, 6, 42, NULL),
(12899, 1, 6, 43, NULL),
(12869, 1, 6, 44, NULL),
(12909, 1, 6, 45, NULL),
(17481, 1, 6, 46, NULL),
(12979, 1, 6, 47, NULL),
(13039, 1, 6, 48, NULL),
(13189, 1, 6, 50, NULL),
(10585, 1, 6, 51, NULL),
(12989, 1, 6, 52, NULL),
(13019, 1, 6, 53, NULL),
(13029, 1, 6, 54, NULL),
(10583, 1, 6, 55, NULL),
(13079, 1, 6, 59, NULL),
(13049, 1, 6, 60, NULL),
(13119, 1, 6, 62, NULL),
(13229, 1, 6, 68, NULL),
(10633, 1, 6, 69, NULL),
(13219, 1, 6, 70, NULL),
(10634, 1, 6, 71, NULL),
(10623, 1, 6, 72, NULL),
(10588, 1, 6, 75, NULL),
(10586, 1, 6, 82, NULL),
(10645, 1, 6, 83, NULL),
(10647, 1, 6, 84, NULL),
(13129, 1, 6, 86, NULL),
(13239, 1, 6, 87, NULL),
(10609, 1, 6, 89, NULL),
(10612, 1, 6, 92, NULL),
(10611, 1, 6, 93, NULL),
(10587, 1, 6, 97, NULL),
(10592, 1, 6, 98, NULL),
(10589, 1, 6, 101, NULL),
(10590, 1, 6, 102, NULL),
(10576, 1, 6, 111, NULL),
(10648, 1, 6, 116, NULL),
(10649, 1, 6, 117, NULL),
(10654, 1, 6, 118, NULL),
(10652, 1, 6, 119, NULL),
(10651, 1, 6, 120, NULL),
(10653, 1, 6, 121, NULL),
(10656, 1, 6, 122, NULL),
(10650, 1, 6, 123, NULL),
(10655, 1, 6, 124, NULL),
(10570, 1, 6, 125, NULL),
(10571, 1, 6, 126, NULL),
(13209, 1, 6, 132, NULL),
(10573, 1, 6, 134, NULL),
(10572, 1, 6, 135, NULL),
(14301, 1, 6, 139, NULL),
(10574, 1, 6, 140, NULL),
(13249, 1, 6, 141, NULL),
(10641, 1, 6, 142, NULL),
(13259, 1, 6, 143, NULL),
(10643, 1, 6, 144, NULL),
(10591, 1, 6, 145, NULL),
(10642, 1, 6, 148, NULL),
(10644, 1, 6, 149, NULL),
(10595, 1, 6, 150, NULL),
(13089, 1, 6, 151, NULL),
(13059, 1, 6, 152, NULL),
(13366, 1, 6, 153, NULL),
(10616, 1, 6, 154, NULL),
(10617, 1, 6, 155, NULL),
(10618, 1, 6, 156, NULL),
(10636, 1, 6, 157, NULL),
(10619, 1, 6, 160, NULL),
(12327, 1, 6, 161, NULL),
(13633, 1, 6, 167, NULL),
(13661, 1, 6, 168, NULL),
(13671, 1, 6, 169, NULL),
(13386, 1, 6, 170, NULL),
(13396, 1, 6, 173, NULL),
(13650, 1, 6, 174, NULL),
(13692, 1, 6, 175, NULL),
(13491, 1, 6, 176, NULL),
(13501, 1, 6, 177, NULL),
(14209, 1, 6, 179, NULL),
(14253, 1, 6, 190, NULL),
(17100, 1, 6, 191, NULL),
(14243, 1, 6, 192, NULL),
(14310, 1, 6, 193, NULL),
(14319, 1, 6, 194, NULL),
(14519, 1, 6, 195, NULL),
(14882, 1, 6, 196, NULL),
(14625, 1, 6, 197, NULL),
(14958, 1, 6, 198, NULL),
(17120, 1, 6, 2046, NULL),
(16500, 1, 6, 2048, NULL),
(16761, 1, 6, 2052, NULL),
(16781, 1, 6, 2053, NULL),
(16771, 1, 6, 2054, NULL),
(16974, 1, 6, 2055, NULL),
(17110, 1, 6, 2056, NULL),
(12938, 1, 7, 2, NULL),
(12958, 1, 7, 3, NULL),
(12948, 1, 7, 4, NULL),
(12968, 1, 7, 9, NULL),
(12848, 1, 7, 37, NULL),
(12858, 1, 7, 38, NULL),
(12878, 1, 7, 39, NULL),
(12918, 1, 7, 41, NULL),
(12888, 1, 7, 42, NULL),
(12898, 1, 7, 43, NULL),
(12868, 1, 7, 44, NULL),
(12908, 1, 7, 45, NULL),
(17480, 1, 7, 46, NULL),
(12978, 1, 7, 47, NULL),
(13038, 1, 7, 48, NULL),
(13188, 1, 7, 50, NULL),
(10938, 1, 7, 51, NULL),
(12988, 1, 7, 52, NULL),
(13018, 1, 7, 53, NULL),
(13028, 1, 7, 54, NULL),
(10936, 1, 7, 55, NULL),
(13078, 1, 7, 59, NULL),
(13048, 1, 7, 60, NULL),
(13118, 1, 7, 62, NULL),
(13228, 1, 7, 68, NULL),
(10986, 1, 7, 69, NULL),
(13218, 1, 7, 70, NULL),
(10987, 1, 7, 71, NULL),
(10976, 1, 7, 72, NULL),
(10941, 1, 7, 75, NULL),
(10939, 1, 7, 82, NULL),
(10998, 1, 7, 83, NULL),
(11000, 1, 7, 84, NULL),
(13128, 1, 7, 86, NULL),
(13238, 1, 7, 87, NULL),
(10962, 1, 7, 89, NULL),
(10965, 1, 7, 92, NULL),
(10964, 1, 7, 93, NULL),
(10940, 1, 7, 97, NULL),
(10945, 1, 7, 98, NULL),
(10942, 1, 7, 101, NULL),
(10943, 1, 7, 102, NULL),
(10929, 1, 7, 111, NULL),
(11001, 1, 7, 116, NULL),
(11002, 1, 7, 117, NULL),
(11007, 1, 7, 118, NULL),
(11005, 1, 7, 119, NULL),
(11004, 1, 7, 120, NULL),
(11006, 1, 7, 121, NULL),
(11009, 1, 7, 122, NULL),
(11003, 1, 7, 123, NULL),
(11008, 1, 7, 124, NULL),
(10923, 1, 7, 125, NULL),
(10924, 1, 7, 126, NULL),
(13208, 1, 7, 132, NULL),
(10926, 1, 7, 134, NULL),
(10925, 1, 7, 135, NULL),
(14300, 1, 7, 139, NULL),
(10927, 1, 7, 140, NULL),
(13248, 1, 7, 141, NULL),
(10994, 1, 7, 142, NULL),
(13258, 1, 7, 143, NULL),
(10996, 1, 7, 144, NULL),
(10944, 1, 7, 145, NULL),
(10995, 1, 7, 148, NULL),
(10997, 1, 7, 149, NULL),
(10948, 1, 7, 150, NULL),
(13088, 1, 7, 151, NULL),
(13058, 1, 7, 152, NULL),
(13365, 1, 7, 153, NULL),
(10969, 1, 7, 154, NULL),
(10970, 1, 7, 155, NULL),
(10971, 1, 7, 156, NULL),
(10989, 1, 7, 157, NULL),
(10972, 1, 7, 160, NULL),
(12328, 1, 7, 161, NULL),
(13632, 1, 7, 167, NULL),
(13660, 1, 7, 168, NULL),
(13670, 1, 7, 169, NULL),
(13385, 1, 7, 170, NULL),
(13395, 1, 7, 173, NULL),
(13649, 1, 7, 174, NULL),
(13693, 1, 7, 175, NULL),
(13490, 1, 7, 176, NULL),
(13500, 1, 7, 177, NULL),
(14208, 1, 7, 179, NULL),
(14252, 1, 7, 190, NULL),
(17099, 1, 7, 191, NULL),
(14242, 1, 7, 192, NULL),
(14309, 1, 7, 193, NULL),
(14318, 1, 7, 194, NULL),
(14518, 1, 7, 195, NULL),
(14881, 1, 7, 196, NULL),
(14624, 1, 7, 197, NULL),
(14957, 1, 7, 198, NULL),
(17119, 1, 7, 2046, NULL),
(16499, 1, 7, 2048, NULL),
(16760, 1, 7, 2052, NULL),
(16780, 1, 7, 2053, NULL),
(16770, 1, 7, 2054, NULL),
(16973, 1, 7, 2055, NULL),
(17109, 1, 7, 2056, NULL),
(12937, 1, 8, 2, NULL),
(12957, 1, 8, 3, NULL),
(12947, 1, 8, 4, NULL),
(12967, 1, 8, 9, NULL),
(12847, 1, 8, 37, NULL),
(12857, 1, 8, 38, NULL),
(12877, 1, 8, 39, NULL),
(12917, 1, 8, 41, NULL),
(12887, 1, 8, 42, NULL),
(12897, 1, 8, 43, NULL),
(12867, 1, 8, 44, NULL),
(12907, 1, 8, 45, NULL),
(17479, 1, 8, 46, NULL),
(12977, 1, 8, 47, NULL),
(13037, 1, 8, 48, NULL),
(13187, 1, 8, 50, NULL),
(11291, 1, 8, 51, NULL),
(12987, 1, 8, 52, NULL),
(13017, 1, 8, 53, NULL),
(13027, 1, 8, 54, NULL),
(11289, 1, 8, 55, NULL),
(13077, 1, 8, 59, NULL),
(13047, 1, 8, 60, NULL),
(13117, 1, 8, 62, NULL),
(13227, 1, 8, 68, NULL),
(11339, 1, 8, 69, NULL),
(13217, 1, 8, 70, NULL),
(11340, 1, 8, 71, NULL),
(11329, 1, 8, 72, NULL),
(11294, 1, 8, 75, NULL),
(11292, 1, 8, 82, NULL),
(11351, 1, 8, 83, NULL),
(11353, 1, 8, 84, NULL),
(13127, 1, 8, 86, NULL),
(13237, 1, 8, 87, NULL),
(11315, 1, 8, 89, NULL),
(11318, 1, 8, 92, NULL),
(11317, 1, 8, 93, NULL),
(11293, 1, 8, 97, NULL),
(11298, 1, 8, 98, NULL),
(11295, 1, 8, 101, NULL),
(11296, 1, 8, 102, NULL),
(11282, 1, 8, 111, NULL),
(11354, 1, 8, 116, NULL),
(11355, 1, 8, 117, NULL),
(11360, 1, 8, 118, NULL),
(11358, 1, 8, 119, NULL),
(11357, 1, 8, 120, NULL),
(11359, 1, 8, 121, NULL),
(11362, 1, 8, 122, NULL),
(11356, 1, 8, 123, NULL),
(11361, 1, 8, 124, NULL),
(11276, 1, 8, 125, NULL),
(11277, 1, 8, 126, NULL),
(13207, 1, 8, 132, NULL),
(11279, 1, 8, 134, NULL),
(11278, 1, 8, 135, NULL),
(14299, 1, 8, 139, NULL),
(11280, 1, 8, 140, NULL),
(13247, 1, 8, 141, NULL),
(11347, 1, 8, 142, NULL),
(13257, 1, 8, 143, NULL),
(11349, 1, 8, 144, NULL),
(11297, 1, 8, 145, NULL),
(11348, 1, 8, 148, NULL),
(11350, 1, 8, 149, NULL),
(11301, 1, 8, 150, NULL),
(13087, 1, 8, 151, NULL),
(13057, 1, 8, 152, NULL),
(13364, 1, 8, 153, NULL),
(11322, 1, 8, 154, NULL),
(11323, 1, 8, 155, NULL),
(11324, 1, 8, 156, NULL),
(11342, 1, 8, 157, NULL),
(11325, 1, 8, 160, NULL),
(12329, 1, 8, 161, NULL),
(13631, 1, 8, 167, NULL),
(13659, 1, 8, 168, NULL),
(13669, 1, 8, 169, NULL),
(13384, 1, 8, 170, NULL),
(13394, 1, 8, 173, NULL),
(13648, 1, 8, 174, NULL),
(13694, 1, 8, 175, NULL),
(13489, 1, 8, 176, NULL),
(13499, 1, 8, 177, NULL),
(14207, 1, 8, 179, NULL),
(14251, 1, 8, 190, NULL),
(17098, 1, 8, 191, NULL),
(14241, 1, 8, 192, NULL),
(14308, 1, 8, 193, NULL),
(14317, 1, 8, 194, NULL),
(14517, 1, 8, 195, NULL),
(14880, 1, 8, 196, NULL),
(14623, 1, 8, 197, NULL),
(14956, 1, 8, 198, NULL),
(17118, 1, 8, 2046, NULL),
(16498, 1, 8, 2048, NULL),
(16972, 1, 8, 2055, NULL),
(17108, 1, 8, 2056, NULL),
(12936, 1, 9, 2, NULL),
(12956, 1, 9, 3, NULL),
(12946, 1, 9, 4, NULL),
(12966, 1, 9, 9, NULL),
(12846, 1, 9, 37, NULL),
(12856, 1, 9, 38, NULL),
(12876, 1, 9, 39, NULL),
(12916, 1, 9, 41, NULL),
(12886, 1, 9, 42, NULL),
(12896, 1, 9, 43, NULL),
(12866, 1, 9, 44, NULL),
(12906, 1, 9, 45, NULL),
(17478, 1, 9, 46, NULL),
(12976, 1, 9, 47, NULL),
(13036, 1, 9, 48, NULL),
(13186, 1, 9, 50, NULL),
(11644, 1, 9, 51, NULL),
(12986, 1, 9, 52, NULL),
(13016, 1, 9, 53, NULL),
(13026, 1, 9, 54, NULL),
(11642, 1, 9, 55, NULL),
(13076, 1, 9, 59, NULL),
(13046, 1, 9, 60, NULL),
(13116, 1, 9, 62, NULL),
(13226, 1, 9, 68, NULL),
(11692, 1, 9, 69, NULL),
(13216, 1, 9, 70, NULL),
(11693, 1, 9, 71, NULL),
(11682, 1, 9, 72, NULL),
(11647, 1, 9, 75, NULL),
(11645, 1, 9, 82, NULL),
(11704, 1, 9, 83, NULL),
(11706, 1, 9, 84, NULL),
(13126, 1, 9, 86, NULL),
(13236, 1, 9, 87, NULL),
(11668, 1, 9, 89, NULL),
(11671, 1, 9, 92, NULL),
(11670, 1, 9, 93, NULL),
(11646, 1, 9, 97, NULL),
(11651, 1, 9, 98, NULL),
(11648, 1, 9, 101, NULL),
(11649, 1, 9, 102, NULL),
(11635, 1, 9, 111, NULL),
(11707, 1, 9, 116, NULL),
(11708, 1, 9, 117, NULL),
(11713, 1, 9, 118, NULL),
(11711, 1, 9, 119, NULL),
(11710, 1, 9, 120, NULL),
(11712, 1, 9, 121, NULL),
(11715, 1, 9, 122, NULL),
(11709, 1, 9, 123, NULL),
(11714, 1, 9, 124, NULL),
(11629, 1, 9, 125, NULL),
(11630, 1, 9, 126, NULL),
(13206, 1, 9, 132, NULL),
(11632, 1, 9, 134, NULL),
(11631, 1, 9, 135, NULL),
(14298, 1, 9, 139, NULL),
(11633, 1, 9, 140, NULL),
(13246, 1, 9, 141, NULL),
(11700, 1, 9, 142, NULL),
(13256, 1, 9, 143, NULL),
(11702, 1, 9, 144, NULL),
(11650, 1, 9, 145, NULL),
(11701, 1, 9, 148, NULL),
(11703, 1, 9, 149, NULL),
(11654, 1, 9, 150, NULL),
(13086, 1, 9, 151, NULL),
(13056, 1, 9, 152, NULL),
(13363, 1, 9, 153, NULL),
(11675, 1, 9, 154, NULL),
(11676, 1, 9, 155, NULL),
(11677, 1, 9, 156, NULL),
(11695, 1, 9, 157, NULL),
(11678, 1, 9, 160, NULL),
(12330, 1, 9, 161, NULL),
(13630, 1, 9, 167, NULL),
(13658, 1, 9, 168, NULL),
(13668, 1, 9, 169, NULL),
(13383, 1, 9, 170, NULL),
(13393, 1, 9, 173, NULL),
(13647, 1, 9, 174, NULL),
(13695, 1, 9, 175, NULL),
(13488, 1, 9, 176, NULL),
(13498, 1, 9, 177, NULL),
(14206, 1, 9, 179, NULL),
(14250, 1, 9, 190, NULL),
(17097, 1, 9, 191, NULL),
(14240, 1, 9, 192, NULL),
(14307, 1, 9, 193, NULL),
(14316, 1, 9, 194, NULL),
(14516, 1, 9, 195, NULL),
(14879, 1, 9, 196, NULL),
(14622, 1, 9, 197, NULL),
(14955, 1, 9, 198, NULL),
(17117, 1, 9, 2046, NULL),
(16497, 1, 9, 2048, NULL),
(16758, 1, 9, 2052, NULL),
(16778, 1, 9, 2053, NULL),
(16768, 1, 9, 2054, NULL),
(16971, 1, 9, 2055, NULL),
(17107, 1, 9, 2056, NULL),
(12935, 1, 10, 2, NULL),
(12955, 1, 10, 3, NULL),
(12945, 1, 10, 4, NULL),
(12965, 1, 10, 9, NULL),
(12845, 1, 10, 37, NULL),
(12855, 1, 10, 38, NULL),
(12875, 1, 10, 39, NULL),
(12915, 1, 10, 41, NULL),
(12885, 1, 10, 42, NULL),
(12895, 1, 10, 43, NULL),
(12865, 1, 10, 44, NULL),
(12905, 1, 10, 45, NULL),
(17477, 1, 10, 46, NULL),
(12975, 1, 10, 47, NULL),
(13035, 1, 10, 48, NULL),
(13185, 1, 10, 50, NULL),
(11997, 1, 10, 51, NULL),
(12985, 1, 10, 52, NULL),
(13015, 1, 10, 53, NULL),
(13025, 1, 10, 54, NULL),
(11995, 1, 10, 55, NULL),
(13075, 1, 10, 59, NULL),
(13045, 1, 10, 60, NULL),
(13115, 1, 10, 62, NULL),
(13225, 1, 10, 68, NULL),
(12045, 1, 10, 69, NULL),
(13215, 1, 10, 70, NULL),
(12046, 1, 10, 71, NULL),
(12035, 1, 10, 72, NULL),
(12000, 1, 10, 75, NULL),
(11998, 1, 10, 82, NULL),
(12057, 1, 10, 83, NULL),
(12059, 1, 10, 84, NULL),
(13125, 1, 10, 86, NULL),
(13235, 1, 10, 87, NULL),
(12021, 1, 10, 89, NULL),
(12024, 1, 10, 92, NULL),
(12023, 1, 10, 93, NULL),
(11999, 1, 10, 97, NULL),
(12004, 1, 10, 98, NULL),
(12001, 1, 10, 101, NULL),
(12002, 1, 10, 102, NULL),
(11988, 1, 10, 111, NULL),
(12060, 1, 10, 116, NULL),
(12061, 1, 10, 117, NULL),
(12066, 1, 10, 118, NULL),
(12064, 1, 10, 119, NULL),
(12063, 1, 10, 120, NULL),
(12065, 1, 10, 121, NULL),
(12068, 1, 10, 122, NULL),
(12062, 1, 10, 123, NULL),
(12067, 1, 10, 124, NULL),
(11982, 1, 10, 125, NULL),
(11983, 1, 10, 126, NULL),
(13205, 1, 10, 132, NULL),
(11985, 1, 10, 134, NULL),
(11984, 1, 10, 135, NULL),
(14297, 1, 10, 139, NULL),
(11986, 1, 10, 140, NULL),
(13245, 1, 10, 141, NULL),
(12053, 1, 10, 142, NULL),
(13255, 1, 10, 143, NULL),
(12055, 1, 10, 144, NULL),
(12003, 1, 10, 145, NULL),
(12054, 1, 10, 148, NULL),
(12056, 1, 10, 149, NULL),
(12007, 1, 10, 150, NULL),
(13085, 1, 10, 151, NULL),
(13055, 1, 10, 152, NULL),
(13362, 1, 10, 153, NULL),
(12028, 1, 10, 154, NULL),
(12029, 1, 10, 155, NULL),
(12030, 1, 10, 156, NULL),
(12048, 1, 10, 157, NULL),
(12031, 1, 10, 160, NULL),
(12331, 1, 10, 161, NULL),
(13629, 1, 10, 167, NULL),
(13657, 1, 10, 168, NULL),
(13667, 1, 10, 169, NULL),
(13382, 1, 10, 170, NULL),
(13392, 1, 10, 173, NULL),
(13646, 1, 10, 174, NULL),
(13696, 1, 10, 175, NULL),
(13507, 1, 10, 176, NULL),
(13497, 1, 10, 177, NULL),
(14205, 1, 10, 179, NULL),
(14249, 1, 10, 190, NULL),
(17096, 1, 10, 191, NULL),
(14239, 1, 10, 192, NULL),
(14621, 1, 10, 197, NULL),
(17116, 1, 10, 2046, NULL),
(16496, 1, 10, 2048, NULL),
(16757, 1, 10, 2052, NULL),
(16777, 1, 10, 2053, NULL),
(16767, 1, 10, 2054, NULL),
(16970, 1, 10, 2055, NULL),
(17106, 1, 10, 2056, NULL),
(8978, 4, 1, 2, NULL),
(8980, 4, 1, 3, NULL),
(8979, 4, 1, 4, NULL),
(8981, 4, 1, 9, NULL),
(8969, 4, 1, 37, NULL),
(15128, 4, 1, 38, NULL),
(8972, 4, 1, 39, NULL),
(8976, 4, 1, 41, NULL),
(8973, 4, 1, 42, NULL),
(8974, 4, 1, 43, NULL),
(8971, 4, 1, 44, NULL),
(8975, 4, 1, 45, NULL),
(8977, 4, 1, 46, NULL),
(14201, 4, 1, 47, NULL),
(8988, 4, 1, 48, NULL),
(13514, 4, 1, 50, NULL),
(13463, 4, 1, 52, NULL),
(13450, 4, 1, 53, NULL),
(13746, 4, 1, 54, NULL),
(16456, 4, 1, 55, NULL),
(8993, 4, 1, 59, NULL),
(8989, 4, 1, 60, NULL),
(8997, 4, 1, 62, NULL),
(13436, 4, 1, 68, NULL),
(13483, 4, 1, 75, NULL),
(16464, 4, 1, 82, NULL),
(14259, 4, 1, 83, NULL),
(14260, 4, 1, 84, NULL),
(8998, 4, 1, 86, NULL),
(14296, 4, 1, 87, NULL),
(13521, 4, 1, 97, NULL),
(13522, 4, 1, 97, NULL),
(13523, 4, 1, 98, NULL),
(13411, 4, 1, 111, NULL),
(13548, 4, 1, 116, NULL),
(13558, 4, 1, 117, NULL),
(13598, 4, 1, 118, NULL),
(13588, 4, 1, 119, NULL),
(13568, 4, 1, 120, NULL),
(13618, 4, 1, 121, NULL),
(13608, 4, 1, 122, NULL),
(13628, 4, 1, 124, NULL),
(14514, 4, 1, 125, NULL),
(14513, 4, 1, 126, NULL),
(14235, 4, 1, 132, NULL),
(14515, 4, 1, 135, NULL),
(14383, 4, 1, 139, NULL),
(13759, 4, 1, 140, NULL),
(9012, 4, 1, 141, NULL),
(9013, 4, 1, 143, NULL),
(8994, 4, 1, 151, NULL),
(8991, 4, 1, 152, NULL),
(16460, 4, 1, 153, NULL),
(13524, 4, 1, 157, NULL),
(13526, 4, 1, 160, NULL),
(13639, 4, 1, 167, NULL),
(13473, 4, 1, 168, NULL),
(13747, 4, 1, 169, NULL),
(16461, 4, 1, 170, NULL),
(13578, 4, 1, 174, NULL),
(13513, 4, 1, 176, NULL),
(13510, 4, 1, 177, NULL),
(14234, 4, 1, 179, NULL),
(14261, 4, 1, 190, NULL),
(14954, 4, 1, 192, NULL),
(14387, 4, 1, 193, NULL),
(14875, 4, 1, 194, NULL),
(14553, 4, 1, 195, NULL),
(14554, 4, 1, 196, NULL),
(16415, 4, 1, 196, NULL),
(14874, 4, 1, 197, NULL),
(14972, 4, 1, 198, NULL),
(16279, 4, 1, 2047, NULL),
(16796, 4, 1, 2052, NULL),
(16816, 4, 1, 2053, NULL),
(16806, 4, 1, 2054, NULL),
(9331, 4, 2, 2, NULL),
(9333, 4, 2, 3, NULL),
(9332, 4, 2, 4, NULL),
(9334, 4, 2, 9, NULL),
(9322, 4, 2, 37, NULL),
(15127, 4, 2, 38, NULL),
(9325, 4, 2, 39, NULL),
(9329, 4, 2, 41, NULL),
(9326, 4, 2, 42, NULL),
(9327, 4, 2, 43, NULL),
(9324, 4, 2, 44, NULL),
(9328, 4, 2, 45, NULL),
(9330, 4, 2, 46, NULL),
(14200, 4, 2, 47, NULL),
(9341, 4, 2, 48, NULL),
(13462, 4, 2, 52, NULL),
(13449, 4, 2, 53, NULL),
(14202, 4, 2, 54, NULL),
(16455, 4, 2, 55, NULL),
(9346, 4, 2, 59, NULL),
(9342, 4, 2, 60, NULL),
(9350, 4, 2, 62, NULL),
(13435, 4, 2, 68, NULL),
(13482, 4, 2, 75, NULL),
(9351, 4, 2, 86, NULL),
(13547, 4, 2, 116, NULL),
(13557, 4, 2, 117, NULL),
(13597, 4, 2, 118, NULL),
(13587, 4, 2, 119, NULL),
(13567, 4, 2, 120, NULL),
(13617, 4, 2, 121, NULL),
(13607, 4, 2, 122, NULL),
(13627, 4, 2, 124, NULL),
(14236, 4, 2, 132, NULL),
(16536, 4, 2, 139, NULL),
(13760, 4, 2, 140, NULL),
(9365, 4, 2, 141, NULL),
(9366, 4, 2, 143, NULL),
(9347, 4, 2, 151, NULL),
(9344, 4, 2, 152, NULL),
(16463, 4, 2, 153, NULL),
(13640, 4, 2, 167, NULL),
(13472, 4, 2, 168, NULL),
(16462, 4, 2, 170, NULL),
(13577, 4, 2, 174, NULL),
(13509, 4, 2, 177, NULL),
(14233, 4, 2, 179, NULL),
(14278, 4, 2, 190, NULL),
(14953, 4, 2, 192, NULL),
(14898, 4, 2, 195, NULL),
(16416, 4, 2, 196, NULL),
(14971, 4, 2, 198, NULL),
(16795, 4, 2, 2052, NULL),
(16815, 4, 2, 2053, NULL),
(16805, 4, 2, 2054, NULL),
(9684, 4, 3, 2, NULL),
(9686, 4, 3, 3, NULL),
(9685, 4, 3, 4, NULL),
(9687, 4, 3, 9, NULL),
(9675, 4, 3, 37, NULL),
(15126, 4, 3, 38, NULL),
(9678, 4, 3, 39, NULL),
(9682, 4, 3, 41, NULL),
(9679, 4, 3, 42, NULL),
(9680, 4, 3, 43, NULL),
(14978, 4, 3, 44, NULL),
(9681, 4, 3, 45, NULL),
(9683, 4, 3, 46, NULL),
(14199, 4, 3, 47, NULL),
(9694, 4, 3, 48, NULL),
(13461, 4, 3, 52, NULL),
(13448, 4, 3, 53, NULL),
(14203, 4, 3, 54, NULL),
(9699, 4, 3, 59, NULL),
(9695, 4, 3, 60, NULL),
(9703, 4, 3, 62, NULL),
(13515, 4, 3, 68, NULL),
(13481, 4, 3, 75, NULL),
(9704, 4, 3, 86, NULL),
(13546, 4, 3, 116, NULL),
(13556, 4, 3, 117, NULL),
(13596, 4, 3, 118, NULL),
(13586, 4, 3, 119, NULL),
(13566, 4, 3, 120, NULL),
(13616, 4, 3, 121, NULL),
(13606, 4, 3, 122, NULL),
(13626, 4, 3, 124, NULL),
(14237, 4, 3, 132, NULL),
(16537, 4, 3, 139, NULL),
(13761, 4, 3, 140, NULL),
(9718, 4, 3, 141, NULL),
(9719, 4, 3, 143, NULL),
(9700, 4, 3, 151, NULL),
(9697, 4, 3, 152, NULL),
(13525, 4, 3, 157, NULL),
(13471, 4, 3, 168, NULL),
(13576, 4, 3, 174, NULL),
(13520, 4, 3, 177, NULL),
(14232, 4, 3, 179, NULL),
(14279, 4, 3, 190, NULL),
(14952, 4, 3, 192, NULL),
(17379, 4, 3, 195, NULL),
(16417, 4, 3, 196, NULL),
(14970, 4, 3, 198, NULL),
(16794, 4, 3, 2052, NULL),
(16814, 4, 3, 2053, NULL),
(16804, 4, 3, 2054, NULL),
(10037, 4, 4, 2, NULL),
(10039, 4, 4, 3, NULL),
(10038, 4, 4, 4, NULL),
(10040, 4, 4, 9, NULL),
(10028, 4, 4, 37, NULL),
(15125, 4, 4, 38, NULL),
(10031, 4, 4, 39, NULL),
(10035, 4, 4, 41, NULL),
(10032, 4, 4, 42, NULL),
(10033, 4, 4, 43, NULL),
(14977, 4, 4, 44, NULL),
(10034, 4, 4, 45, NULL),
(14198, 4, 4, 47, NULL),
(10047, 4, 4, 48, NULL),
(13460, 4, 4, 52, NULL),
(13447, 4, 4, 53, NULL),
(10052, 4, 4, 59, NULL),
(10056, 4, 4, 62, NULL),
(13480, 4, 4, 75, NULL),
(10057, 4, 4, 86, NULL),
(13545, 4, 4, 116, NULL),
(13555, 4, 4, 117, NULL),
(13595, 4, 4, 118, NULL),
(13585, 4, 4, 119, NULL),
(13565, 4, 4, 120, NULL),
(13615, 4, 4, 121, NULL),
(13605, 4, 4, 122, NULL),
(13625, 4, 4, 124, NULL),
(14238, 4, 4, 132, NULL),
(13762, 4, 4, 140, NULL),
(10071, 4, 4, 141, NULL),
(10072, 4, 4, 143, NULL),
(10053, 4, 4, 151, NULL),
(10050, 4, 4, 152, NULL),
(13470, 4, 4, 168, NULL),
(13575, 4, 4, 174, NULL),
(14231, 4, 4, 179, NULL),
(14951, 4, 4, 192, NULL),
(17380, 4, 4, 195, NULL),
(17381, 4, 4, 196, NULL),
(14969, 4, 4, 198, NULL),
(16277, 4, 4, 2047, NULL),
(16793, 4, 4, 2052, NULL),
(16813, 4, 4, 2053, NULL),
(16803, 4, 4, 2054, NULL),
(10390, 4, 5, 2, NULL),
(10392, 4, 5, 3, NULL),
(10391, 4, 5, 4, NULL),
(10393, 4, 5, 9, NULL),
(10381, 4, 5, 37, NULL),
(15124, 4, 5, 38, NULL),
(10384, 4, 5, 39, NULL),
(10388, 4, 5, 41, NULL),
(10385, 4, 5, 42, NULL),
(10386, 4, 5, 43, NULL),
(10383, 4, 5, 44, NULL),
(10387, 4, 5, 45, NULL),
(10389, 4, 5, 46, NULL),
(14197, 4, 5, 47, NULL),
(10400, 4, 5, 48, NULL),
(13459, 4, 5, 52, NULL),
(13446, 4, 5, 53, NULL),
(16457, 4, 5, 55, NULL),
(10405, 4, 5, 59, NULL),
(10401, 4, 5, 60, NULL),
(10409, 4, 5, 62, NULL),
(13432, 4, 5, 68, NULL),
(13479, 4, 5, 75, NULL),
(14263, 4, 5, 84, NULL),
(10410, 4, 5, 86, NULL),
(13544, 4, 5, 116, NULL),
(13554, 4, 5, 117, NULL),
(13594, 4, 5, 118, NULL),
(13584, 4, 5, 119, NULL),
(13564, 4, 5, 120, NULL),
(13614, 4, 5, 121, NULL),
(13604, 4, 5, 122, NULL),
(13624, 4, 5, 124, NULL),
(14382, 4, 5, 139, NULL),
(13763, 4, 5, 140, NULL),
(10424, 4, 5, 141, NULL),
(10425, 4, 5, 143, NULL),
(10406, 4, 5, 151, NULL),
(10403, 4, 5, 152, NULL),
(13469, 4, 5, 168, NULL),
(13574, 4, 5, 174, NULL),
(14230, 4, 5, 179, NULL),
(14262, 4, 5, 190, NULL),
(14950, 4, 5, 192, NULL),
(14386, 4, 5, 193, NULL),
(14549, 4, 5, 195, NULL),
(14888, 4, 5, 196, NULL),
(16418, 4, 5, 196, NULL),
(14968, 4, 5, 198, NULL),
(16276, 4, 5, 2047, NULL),
(16792, 4, 5, 2052, NULL),
(16812, 4, 5, 2053, NULL),
(16802, 4, 5, 2054, NULL),
(10743, 4, 6, 2, NULL),
(10745, 4, 6, 3, NULL),
(10744, 4, 6, 4, NULL),
(17465, 4, 6, 9, NULL),
(10734, 4, 6, 37, NULL),
(15123, 4, 6, 38, NULL),
(10737, 4, 6, 39, NULL),
(10741, 4, 6, 41, NULL),
(10738, 4, 6, 42, NULL),
(10739, 4, 6, 43, NULL),
(10736, 4, 6, 44, NULL),
(10740, 4, 6, 45, NULL),
(17467, 4, 6, 46, NULL),
(14196, 4, 6, 47, NULL),
(10753, 4, 6, 48, NULL),
(13458, 4, 6, 52, NULL),
(13445, 4, 6, 53, NULL),
(14204, 4, 6, 54, NULL),
(16458, 4, 6, 55, NULL),
(10758, 4, 6, 59, NULL),
(10754, 4, 6, 60, NULL),
(10762, 4, 6, 62, NULL),
(13431, 4, 6, 68, NULL),
(13478, 4, 6, 75, NULL),
(14264, 4, 6, 84, NULL),
(10763, 4, 6, 86, NULL),
(13543, 4, 6, 116, NULL),
(13553, 4, 6, 117, NULL),
(13593, 4, 6, 118, NULL),
(13583, 4, 6, 119, NULL),
(13563, 4, 6, 120, NULL),
(13613, 4, 6, 121, NULL),
(13603, 4, 6, 122, NULL),
(13623, 4, 6, 124, NULL),
(14381, 4, 6, 139, NULL),
(13764, 4, 6, 140, NULL),
(10759, 4, 6, 151, NULL),
(10756, 4, 6, 152, NULL),
(13468, 4, 6, 168, NULL),
(13573, 4, 6, 174, NULL),
(14229, 4, 6, 179, NULL),
(14265, 4, 6, 190, NULL),
(14949, 4, 6, 192, NULL),
(14385, 4, 6, 193, NULL),
(14548, 4, 6, 195, NULL),
(14889, 4, 6, 196, NULL),
(16419, 4, 6, 196, NULL),
(14967, 4, 6, 198, NULL),
(16791, 4, 6, 2052, NULL),
(16811, 4, 6, 2053, NULL),
(16801, 4, 6, 2054, NULL),
(11096, 4, 7, 2, NULL),
(11098, 4, 7, 3, NULL),
(11097, 4, 7, 4, NULL),
(11099, 4, 7, 9, NULL),
(11087, 4, 7, 37, NULL),
(15122, 4, 7, 38, NULL),
(11090, 4, 7, 39, NULL),
(11094, 4, 7, 41, NULL),
(11091, 4, 7, 42, NULL),
(11092, 4, 7, 43, NULL),
(11089, 4, 7, 44, NULL),
(11093, 4, 7, 45, NULL),
(17466, 4, 7, 46, NULL),
(14195, 4, 7, 47, NULL),
(11106, 4, 7, 48, NULL),
(13457, 4, 7, 52, NULL),
(13444, 4, 7, 53, NULL),
(13748, 4, 7, 54, NULL),
(16459, 4, 7, 55, NULL),
(11111, 4, 7, 59, NULL),
(11107, 4, 7, 60, NULL),
(11115, 4, 7, 62, NULL),
(13430, 4, 7, 68, NULL),
(13477, 4, 7, 75, NULL),
(14267, 4, 7, 84, NULL),
(11116, 4, 7, 86, NULL),
(13542, 4, 7, 116, NULL),
(13552, 4, 7, 117, NULL),
(13592, 4, 7, 118, NULL),
(13582, 4, 7, 119, NULL),
(13562, 4, 7, 120, NULL),
(13612, 4, 7, 121, NULL),
(13602, 4, 7, 122, NULL),
(13622, 4, 7, 124, NULL),
(14380, 4, 7, 139, NULL),
(13765, 4, 7, 140, NULL),
(11130, 4, 7, 141, NULL),
(11131, 4, 7, 143, NULL),
(11112, 4, 7, 151, NULL),
(11109, 4, 7, 152, NULL),
(13467, 4, 7, 168, NULL),
(13572, 4, 7, 174, NULL),
(14228, 4, 7, 179, NULL),
(14266, 4, 7, 190, NULL),
(14948, 4, 7, 192, NULL),
(14384, 4, 7, 193, NULL),
(14547, 4, 7, 195, NULL),
(14890, 4, 7, 196, NULL),
(16420, 4, 7, 196, NULL),
(14966, 4, 7, 198, NULL),
(16790, 4, 7, 2052, NULL),
(16810, 4, 7, 2053, NULL),
(16800, 4, 7, 2054, NULL),
(11449, 4, 8, 2, NULL),
(11451, 4, 8, 3, NULL),
(11450, 4, 8, 4, NULL),
(11452, 4, 8, 9, NULL),
(11440, 4, 8, 37, NULL),
(15121, 4, 8, 38, NULL),
(11443, 4, 8, 39, NULL),
(11447, 4, 8, 41, NULL),
(11444, 4, 8, 42, NULL),
(11445, 4, 8, 43, NULL),
(11442, 4, 8, 44, NULL),
(11446, 4, 8, 45, NULL),
(11448, 4, 8, 46, NULL),
(14194, 4, 8, 47, NULL),
(11459, 4, 8, 48, NULL),
(13456, 4, 8, 52, NULL),
(13443, 4, 8, 53, NULL),
(11464, 4, 8, 59, NULL),
(11460, 4, 8, 60, NULL),
(11468, 4, 8, 62, NULL),
(13476, 4, 8, 75, NULL),
(11469, 4, 8, 86, NULL),
(13541, 4, 8, 116, NULL),
(13551, 4, 8, 117, NULL),
(13591, 4, 8, 118, NULL),
(13581, 4, 8, 119, NULL),
(13561, 4, 8, 120, NULL),
(13611, 4, 8, 121, NULL),
(13601, 4, 8, 122, NULL),
(13621, 4, 8, 124, NULL),
(13766, 4, 8, 140, NULL),
(11483, 4, 8, 141, NULL),
(11484, 4, 8, 143, NULL),
(11465, 4, 8, 151, NULL),
(11462, 4, 8, 152, NULL),
(13466, 4, 8, 168, NULL),
(13571, 4, 8, 174, NULL),
(14227, 4, 8, 179, NULL),
(14947, 4, 8, 192, NULL),
(14546, 4, 8, 195, NULL),
(14891, 4, 8, 196, NULL),
(16421, 4, 8, 196, NULL),
(14965, 4, 8, 198, NULL),
(11802, 4, 9, 2, NULL),
(11804, 4, 9, 3, NULL),
(11803, 4, 9, 4, NULL),
(11805, 4, 9, 9, NULL),
(11793, 4, 9, 37, NULL),
(15120, 4, 9, 38, NULL),
(11796, 4, 9, 39, NULL),
(11800, 4, 9, 41, NULL),
(11797, 4, 9, 42, NULL),
(11798, 4, 9, 43, NULL),
(11795, 4, 9, 44, NULL),
(11799, 4, 9, 45, NULL),
(11801, 4, 9, 46, NULL),
(14193, 4, 9, 47, NULL),
(11812, 4, 9, 48, NULL),
(13455, 4, 9, 52, NULL),
(13442, 4, 9, 53, NULL),
(11817, 4, 9, 59, NULL),
(11813, 4, 9, 60, NULL),
(11821, 4, 9, 62, NULL),
(13475, 4, 9, 75, NULL),
(11822, 4, 9, 86, NULL),
(13540, 4, 9, 116, NULL),
(13550, 4, 9, 117, NULL),
(13590, 4, 9, 118, NULL),
(13580, 4, 9, 119, NULL),
(13560, 4, 9, 120, NULL),
(13610, 4, 9, 121, NULL),
(13600, 4, 9, 122, NULL),
(13620, 4, 9, 124, NULL),
(13767, 4, 9, 140, NULL),
(11836, 4, 9, 141, NULL),
(11837, 4, 9, 143, NULL),
(11818, 4, 9, 151, NULL),
(11815, 4, 9, 152, NULL),
(13465, 4, 9, 168, NULL),
(13570, 4, 9, 174, NULL),
(14226, 4, 9, 179, NULL),
(14946, 4, 9, 192, NULL),
(14545, 4, 9, 195, NULL),
(14892, 4, 9, 196, NULL),
(16422, 4, 9, 196, NULL),
(14964, 4, 9, 198, NULL),
(16788, 4, 9, 2052, NULL),
(16808, 4, 9, 2053, NULL),
(16798, 4, 9, 2054, NULL),
(12157, 4, 10, 3, NULL),
(12156, 4, 10, 4, NULL),
(12158, 4, 10, 9, NULL),
(15119, 4, 10, 38, NULL),
(12149, 4, 10, 39, NULL),
(12153, 4, 10, 41, NULL),
(12150, 4, 10, 42, NULL),
(12151, 4, 10, 43, NULL),
(12148, 4, 10, 44, NULL),
(12152, 4, 10, 45, NULL),
(12154, 4, 10, 46, NULL),
(12170, 4, 10, 59, NULL),
(12166, 4, 10, 60, NULL),
(12174, 4, 10, 62, NULL),
(12175, 4, 10, 86, NULL),
(13539, 4, 10, 116, NULL),
(13549, 4, 10, 117, NULL),
(13589, 4, 10, 118, NULL),
(13579, 4, 10, 119, NULL),
(13559, 4, 10, 120, NULL),
(13609, 4, 10, 121, NULL),
(13599, 4, 10, 122, NULL),
(13619, 4, 10, 124, NULL),
(13768, 4, 10, 140, NULL),
(12190, 4, 10, 143, NULL),
(12171, 4, 10, 151, NULL),
(12168, 4, 10, 152, NULL),
(13569, 4, 10, 174, NULL),
(14225, 4, 10, 179, NULL),
(14945, 4, 10, 192, NULL),
(16787, 4, 10, 2052, NULL),
(16807, 4, 10, 2053, NULL),
(16797, 4, 10, 2054, NULL),
(9039, 7, 1, 2, NULL),
(9041, 7, 1, 3, '-mdp_client,mdp_absystech'),
(9040, 7, 1, 4, NULL),
(9042, 7, 1, 9, NULL),
(9029, 7, 1, 37, NULL),
(9031, 7, 1, 38, NULL),
(9033, 7, 1, 39, NULL),
(9037, 7, 1, 41, NULL),
(9034, 7, 1, 42, NULL),
(9035, 7, 1, 43, NULL),
(9032, 7, 1, 44, NULL),
(9036, 7, 1, 45, NULL),
(9038, 7, 1, 46, NULL),
(9044, 7, 1, 48, NULL),
(9045, 7, 1, 60, NULL),
(9051, 7, 1, 72, NULL),
(9043, 7, 1, 111, NULL),
(15133, 7, 1, 151, NULL),
(9047, 7, 1, 152, NULL),
(13642, 7, 1, 167, NULL),
(17054, 7, 1, 191, NULL),
(17041, 7, 1, 192, NULL),
(9392, 7, 2, 2, NULL),
(9394, 7, 2, 3, '-mdp_client,mdp_absystech'),
(9393, 7, 2, 4, NULL),
(9395, 7, 2, 9, NULL),
(9382, 7, 2, 37, NULL),
(9384, 7, 2, 38, NULL),
(9386, 7, 2, 39, NULL),
(9390, 7, 2, 41, NULL),
(9387, 7, 2, 42, NULL),
(9388, 7, 2, 43, NULL),
(9385, 7, 2, 44, NULL),
(9389, 7, 2, 45, NULL),
(9391, 7, 2, 46, NULL),
(9397, 7, 2, 48, NULL),
(9398, 7, 2, 60, NULL),
(9404, 7, 2, 72, NULL),
(9396, 7, 2, 111, NULL),
(15134, 7, 2, 151, NULL),
(9400, 7, 2, 152, NULL),
(17053, 7, 2, 191, NULL),
(9745, 7, 3, 2, NULL),
(9747, 7, 3, 3, '-mdp_client,mdp_absystech'),
(9746, 7, 3, 4, NULL),
(9748, 7, 3, 9, NULL),
(9735, 7, 3, 37, NULL),
(9737, 7, 3, 38, NULL),
(9739, 7, 3, 39, NULL),
(9743, 7, 3, 41, NULL),
(9740, 7, 3, 42, NULL),
(9741, 7, 3, 43, NULL),
(17382, 7, 3, 44, NULL),
(9742, 7, 3, 45, NULL),
(9744, 7, 3, 46, NULL),
(9750, 7, 3, 48, NULL),
(9751, 7, 3, 60, NULL),
(9757, 7, 3, 72, NULL),
(9749, 7, 3, 111, NULL),
(15135, 7, 3, 151, NULL),
(9753, 7, 3, 152, NULL),
(17052, 7, 3, 191, NULL),
(10098, 7, 4, 2, NULL),
(10100, 7, 4, 3, '-mdp_client,mdp_absystech'),
(10099, 7, 4, 4, NULL),
(10101, 7, 4, 9, NULL),
(10088, 7, 4, 37, NULL),
(10090, 7, 4, 38, NULL),
(10092, 7, 4, 39, NULL),
(10096, 7, 4, 41, NULL),
(10093, 7, 4, 42, NULL),
(10094, 7, 4, 43, NULL),
(10095, 7, 4, 45, NULL),
(10103, 7, 4, 48, NULL),
(10110, 7, 4, 72, NULL),
(10102, 7, 4, 111, NULL),
(15141, 7, 4, 151, NULL),
(10106, 7, 4, 152, NULL),
(17051, 7, 4, 191, NULL),
(10451, 7, 5, 2, NULL),
(10453, 7, 5, 3, '-mdp_client,mdp_absystech'),
(10452, 7, 5, 4, NULL),
(10454, 7, 5, 9, NULL),
(10441, 7, 5, 37, NULL),
(10443, 7, 5, 38, NULL),
(10445, 7, 5, 39, NULL),
(10449, 7, 5, 41, NULL),
(10446, 7, 5, 42, NULL),
(10447, 7, 5, 43, NULL),
(10444, 7, 5, 44, NULL),
(10448, 7, 5, 45, NULL),
(10450, 7, 5, 46, NULL),
(10456, 7, 5, 48, NULL),
(10457, 7, 5, 60, NULL),
(10463, 7, 5, 72, NULL),
(10455, 7, 5, 111, NULL),
(15140, 7, 5, 151, NULL),
(10459, 7, 5, 152, NULL),
(17050, 7, 5, 191, NULL),
(10804, 7, 6, 2, NULL),
(10806, 7, 6, 3, '-mdp_client,mdp_absystech'),
(10805, 7, 6, 4, NULL),
(10807, 7, 6, 9, NULL),
(10794, 7, 6, 37, NULL),
(10796, 7, 6, 38, NULL),
(10798, 7, 6, 39, NULL),
(10802, 7, 6, 41, NULL),
(10799, 7, 6, 42, NULL),
(10800, 7, 6, 43, NULL),
(10797, 7, 6, 44, NULL),
(10801, 7, 6, 45, NULL),
(10803, 7, 6, 46, NULL),
(10809, 7, 6, 48, NULL),
(10810, 7, 6, 60, NULL),
(10816, 7, 6, 72, NULL),
(10808, 7, 6, 111, NULL),
(15139, 7, 6, 151, NULL),
(10812, 7, 6, 152, NULL),
(17049, 7, 6, 191, NULL),
(11157, 7, 7, 2, NULL),
(11159, 7, 7, 3, '-mdp_client,mdp_absystech'),
(11158, 7, 7, 4, NULL),
(11160, 7, 7, 9, NULL),
(11147, 7, 7, 37, NULL),
(11149, 7, 7, 38, NULL),
(11151, 7, 7, 39, NULL),
(11155, 7, 7, 41, NULL),
(11152, 7, 7, 42, NULL),
(11153, 7, 7, 43, NULL),
(11150, 7, 7, 44, NULL),
(11154, 7, 7, 45, NULL),
(11156, 7, 7, 46, NULL),
(11162, 7, 7, 48, NULL),
(11163, 7, 7, 60, NULL),
(11169, 7, 7, 72, NULL),
(11161, 7, 7, 111, NULL),
(15138, 7, 7, 151, NULL),
(11165, 7, 7, 152, NULL),
(17048, 7, 7, 191, NULL),
(11510, 7, 8, 2, NULL),
(11512, 7, 8, 3, '-mdp_client,mdp_absystech'),
(11511, 7, 8, 4, NULL),
(11513, 7, 8, 9, NULL),
(11500, 7, 8, 37, NULL),
(11502, 7, 8, 38, NULL),
(11504, 7, 8, 39, NULL),
(11508, 7, 8, 41, NULL),
(11505, 7, 8, 42, NULL),
(11506, 7, 8, 43, NULL),
(11503, 7, 8, 44, NULL),
(11507, 7, 8, 45, NULL),
(11509, 7, 8, 46, NULL),
(11515, 7, 8, 48, NULL),
(11516, 7, 8, 60, NULL),
(11522, 7, 8, 72, NULL),
(11514, 7, 8, 111, NULL),
(15137, 7, 8, 151, NULL),
(11518, 7, 8, 152, NULL),
(17047, 7, 8, 191, NULL),
(11863, 7, 9, 2, NULL),
(11865, 7, 9, 3, '-mdp_client,mdp_absystech'),
(11864, 7, 9, 4, NULL),
(11866, 7, 9, 9, NULL),
(11853, 7, 9, 37, NULL),
(11855, 7, 9, 38, NULL),
(11857, 7, 9, 39, NULL),
(11861, 7, 9, 41, NULL),
(11858, 7, 9, 42, NULL),
(11859, 7, 9, 43, NULL),
(11856, 7, 9, 44, NULL),
(11860, 7, 9, 45, NULL),
(11862, 7, 9, 46, NULL),
(11868, 7, 9, 48, NULL),
(11869, 7, 9, 60, NULL),
(11875, 7, 9, 72, NULL),
(11867, 7, 9, 111, NULL),
(15136, 7, 9, 151, NULL),
(11871, 7, 9, 152, NULL),
(17046, 7, 9, 191, NULL),
(12216, 7, 10, 2, NULL),
(12218, 7, 10, 3, '-mdp_client,mdp_absystech'),
(12217, 7, 10, 4, NULL),
(12219, 7, 10, 9, NULL),
(12206, 7, 10, 37, NULL),
(12208, 7, 10, 38, NULL),
(12210, 7, 10, 39, NULL),
(12214, 7, 10, 41, NULL),
(12211, 7, 10, 42, NULL),
(12212, 7, 10, 43, NULL),
(12209, 7, 10, 44, NULL),
(12213, 7, 10, 45, NULL),
(12215, 7, 10, 46, NULL),
(12221, 7, 10, 48, NULL),
(12222, 7, 10, 60, NULL),
(12228, 7, 10, 72, NULL),
(12220, 7, 10, 111, NULL),
(12224, 7, 10, 152, NULL),
(17045, 7, 10, 191, NULL),
(9069, 9, 1, 2, NULL),
(9071, 9, 1, 3, '-mdp_client,mdp_absystech'),
(9070, 9, 1, 4, NULL),
(9075, 9, 1, 9, NULL),
(9059, 9, 1, 37, NULL),
(9061, 9, 1, 38, NULL),
(9063, 9, 1, 39, NULL),
(9067, 9, 1, 41, NULL),
(9064, 9, 1, 42, NULL),
(9065, 9, 1, 43, NULL),
(9062, 9, 1, 44, NULL),
(9066, 9, 1, 45, NULL),
(9068, 9, 1, 46, NULL),
(9077, 9, 1, 47, NULL),
(9091, 9, 1, 48, NULL),
(9114, 9, 1, 50, NULL),
(9086, 9, 1, 51, NULL),
(9078, 9, 1, 52, NULL),
(9081, 9, 1, 53, NULL),
(9082, 9, 1, 54, NULL),
(9084, 9, 1, 55, NULL),
(9098, 9, 1, 59, NULL),
(9094, 9, 1, 60, NULL),
(9103, 9, 1, 62, NULL),
(13528, 9, 1, 68, NULL),
(9089, 9, 1, 75, NULL),
(9087, 9, 1, 82, NULL),
(9124, 9, 1, 83, NULL),
(9126, 9, 1, 84, NULL),
(9105, 9, 1, 86, NULL),
(9104, 9, 1, 89, NULL),
(9107, 9, 1, 92, NULL),
(9106, 9, 1, 93, NULL),
(9088, 9, 1, 97, NULL),
(9090, 9, 1, 98, NULL),
(9076, 9, 1, 111, NULL),
(15011, 9, 1, 116, NULL),
(15021, 9, 1, 117, NULL),
(15071, 9, 1, 118, NULL),
(15061, 9, 1, 119, NULL),
(15041, 9, 1, 120, NULL),
(15102, 9, 1, 121, NULL),
(15081, 9, 1, 122, NULL),
(15082, 9, 1, 123, NULL),
(15092, 9, 1, 124, NULL),
(9072, 9, 1, 125, NULL),
(9073, 9, 1, 126, NULL),
(9118, 9, 1, 132, NULL),
(14352, 9, 1, 139, NULL),
(9074, 9, 1, 140, NULL),
(13351, 9, 1, 141, NULL),
(13361, 9, 1, 143, NULL),
(9092, 9, 1, 150, NULL),
(9099, 9, 1, 151, NULL),
(9096, 9, 1, 152, NULL),
(9085, 9, 1, 153, NULL),
(9122, 9, 1, 157, NULL),
(9110, 9, 1, 160, NULL),
(13643, 9, 1, 167, NULL),
(14616, 9, 1, 168, NULL),
(14614, 9, 1, 169, NULL),
(14615, 9, 1, 169, NULL),
(15051, 9, 1, 174, NULL),
(14982, 9, 1, 175, NULL),
(13516, 9, 1, 176, NULL),
(13517, 9, 1, 177, NULL),
(15031, 9, 1, 179, NULL),
(14277, 9, 1, 190, NULL),
(17031, 9, 1, 191, NULL),
(17030, 9, 1, 192, NULL),
(14617, 9, 1, 193, NULL),
(14620, 9, 1, 194, NULL),
(14563, 9, 1, 195, NULL),
(14619, 9, 1, 196, NULL),
(14873, 9, 1, 197, NULL),
(16275, 9, 1, 2047, NULL),
(16495, 9, 1, 2048, NULL),
(16917, 9, 1, 2052, NULL),
(16927, 9, 1, 2053, NULL),
(16937, 9, 1, 2054, NULL),
(16948, 9, 1, 2055, NULL),
(17009, 9, 1, 2056, NULL),
(9422, 9, 2, 2, NULL),
(9424, 9, 2, 3, '-mdp_client,mdp_absystech'),
(9423, 9, 2, 4, NULL),
(9428, 9, 2, 9, NULL),
(9412, 9, 2, 37, NULL),
(9414, 9, 2, 38, NULL),
(9416, 9, 2, 39, NULL),
(9420, 9, 2, 41, NULL),
(9417, 9, 2, 42, NULL),
(9418, 9, 2, 43, NULL),
(9415, 9, 2, 44, NULL),
(9419, 9, 2, 45, NULL),
(9421, 9, 2, 46, NULL),
(9430, 9, 2, 47, NULL),
(9444, 9, 2, 48, NULL),
(9467, 9, 2, 50, NULL),
(9439, 9, 2, 51, NULL),
(9431, 9, 2, 52, NULL),
(9434, 9, 2, 53, NULL),
(9435, 9, 2, 54, NULL),
(9437, 9, 2, 55, NULL),
(9451, 9, 2, 59, NULL),
(13486, 9, 2, 60, NULL),
(9456, 9, 2, 62, NULL),
(9442, 9, 2, 75, NULL),
(9440, 9, 2, 82, NULL),
(9458, 9, 2, 86, NULL),
(9457, 9, 2, 89, NULL),
(9460, 9, 2, 92, NULL),
(9459, 9, 2, 93, NULL),
(9441, 9, 2, 97, NULL),
(9443, 9, 2, 98, NULL),
(9429, 9, 2, 111, NULL),
(15010, 9, 2, 116, NULL),
(15020, 9, 2, 117, NULL),
(15070, 9, 2, 118, NULL),
(15060, 9, 2, 119, NULL),
(15040, 9, 2, 120, NULL),
(15101, 9, 2, 121, NULL),
(15080, 9, 2, 122, NULL),
(15091, 9, 2, 124, NULL),
(9425, 9, 2, 125, NULL),
(9426, 9, 2, 126, NULL),
(9471, 9, 2, 132, NULL),
(14351, 9, 2, 139, NULL),
(9427, 9, 2, 140, NULL),
(13350, 9, 2, 141, NULL),
(13360, 9, 2, 143, NULL),
(9445, 9, 2, 150, NULL),
(9452, 9, 2, 151, NULL),
(9449, 9, 2, 152, NULL),
(9438, 9, 2, 153, NULL),
(9475, 9, 2, 157, NULL),
(9463, 9, 2, 160, NULL),
(15050, 9, 2, 174, NULL),
(13518, 9, 2, 177, NULL),
(15030, 9, 2, 179, NULL),
(14276, 9, 2, 190, NULL),
(17032, 9, 2, 191, NULL),
(14562, 9, 2, 195, NULL),
(14987, 9, 2, 196, NULL),
(14913, 9, 2, 197, NULL),
(16494, 9, 2, 2048, NULL),
(16916, 9, 2, 2052, NULL),
(16926, 9, 2, 2053, NULL),
(16936, 9, 2, 2054, NULL),
(16947, 9, 2, 2055, NULL),
(17008, 9, 2, 2056, NULL),
(9775, 9, 3, 2, NULL),
(9777, 9, 3, 3, '-mdp_client,mdp_absystech'),
(9776, 9, 3, 4, NULL),
(9781, 9, 3, 9, NULL),
(9765, 9, 3, 37, NULL),
(9767, 9, 3, 38, NULL),
(9769, 9, 3, 39, NULL),
(9773, 9, 3, 41, NULL),
(9770, 9, 3, 42, NULL),
(9771, 9, 3, 43, NULL),
(14976, 9, 3, 44, NULL),
(9772, 9, 3, 45, NULL),
(9774, 9, 3, 46, NULL),
(9783, 9, 3, 47, NULL),
(9797, 9, 3, 48, NULL),
(9820, 9, 3, 50, NULL),
(9792, 9, 3, 51, NULL),
(9784, 9, 3, 52, NULL),
(9787, 9, 3, 53, NULL),
(9788, 9, 3, 54, NULL),
(15104, 9, 3, 55, NULL),
(9804, 9, 3, 59, NULL),
(15103, 9, 3, 60, NULL),
(9809, 9, 3, 62, NULL),
(9795, 9, 3, 75, NULL),
(9793, 9, 3, 82, NULL),
(9811, 9, 3, 86, NULL),
(9810, 9, 3, 89, NULL),
(9813, 9, 3, 92, NULL),
(9812, 9, 3, 93, NULL),
(9794, 9, 3, 97, NULL),
(9796, 9, 3, 98, NULL),
(9782, 9, 3, 111, NULL),
(15009, 9, 3, 116, NULL),
(15019, 9, 3, 117, NULL),
(15069, 9, 3, 118, NULL),
(15059, 9, 3, 119, NULL),
(15039, 9, 3, 120, NULL),
(15100, 9, 3, 121, NULL),
(15079, 9, 3, 122, NULL),
(15090, 9, 3, 124, NULL),
(9778, 9, 3, 125, NULL),
(9779, 9, 3, 126, NULL),
(9824, 9, 3, 132, NULL),
(14350, 9, 3, 139, NULL),
(9780, 9, 3, 140, NULL),
(13349, 9, 3, 141, NULL),
(13359, 9, 3, 143, NULL),
(9805, 9, 3, 151, NULL),
(9802, 9, 3, 152, NULL),
(13656, 9, 3, 153, NULL),
(9828, 9, 3, 157, NULL),
(9816, 9, 3, 160, NULL),
(15049, 9, 3, 174, NULL),
(13519, 9, 3, 177, NULL),
(15029, 9, 3, 179, NULL),
(14291, 9, 3, 190, NULL),
(17033, 9, 3, 191, NULL),
(14988, 9, 3, 195, NULL),
(16938, 9, 3, 2048, NULL),
(16915, 9, 3, 2052, NULL),
(16925, 9, 3, 2053, NULL),
(16935, 9, 3, 2054, NULL),
(16946, 9, 3, 2055, NULL),
(17007, 9, 3, 2056, NULL),
(10128, 9, 4, 2, NULL),
(10130, 9, 4, 3, '-mdp_client,mdp_absystech'),
(10129, 9, 4, 4, NULL),
(10134, 9, 4, 9, NULL),
(10118, 9, 4, 37, NULL),
(10120, 9, 4, 38, NULL),
(10122, 9, 4, 39, NULL),
(10126, 9, 4, 41, NULL),
(10123, 9, 4, 42, NULL),
(10124, 9, 4, 43, NULL),
(14975, 9, 4, 44, NULL),
(10125, 9, 4, 45, NULL),
(10136, 9, 4, 47, NULL),
(10150, 9, 4, 48, NULL),
(10173, 9, 4, 50, NULL),
(10145, 9, 4, 51, NULL),
(10137, 9, 4, 52, NULL),
(10140, 9, 4, 53, NULL),
(10141, 9, 4, 54, NULL),
(10143, 9, 4, 55, NULL),
(10157, 9, 4, 59, NULL),
(10162, 9, 4, 62, NULL),
(10148, 9, 4, 75, NULL),
(10146, 9, 4, 82, NULL),
(10164, 9, 4, 86, NULL),
(10163, 9, 4, 89, NULL),
(10166, 9, 4, 92, NULL),
(10165, 9, 4, 93, NULL),
(10147, 9, 4, 97, NULL),
(10149, 9, 4, 98, NULL),
(10135, 9, 4, 111, NULL),
(15008, 9, 4, 116, NULL),
(15018, 9, 4, 117, NULL),
(15068, 9, 4, 118, NULL),
(15058, 9, 4, 119, NULL),
(15038, 9, 4, 120, NULL),
(15078, 9, 4, 122, NULL),
(10131, 9, 4, 125, NULL),
(10132, 9, 4, 126, NULL),
(10177, 9, 4, 132, NULL),
(14349, 9, 4, 139, NULL),
(10133, 9, 4, 140, NULL),
(13348, 9, 4, 141, NULL),
(13358, 9, 4, 143, NULL),
(10158, 9, 4, 151, NULL),
(10155, 9, 4, 152, NULL),
(10144, 9, 4, 153, NULL),
(10181, 9, 4, 157, NULL),
(10169, 9, 4, 160, NULL),
(15048, 9, 4, 174, NULL),
(15028, 9, 4, 179, NULL),
(17034, 9, 4, 191, NULL),
(16274, 9, 4, 2047, NULL),
(16914, 9, 4, 2052, NULL),
(16924, 9, 4, 2053, NULL),
(16934, 9, 4, 2054, NULL),
(16945, 9, 4, 2055, NULL),
(17006, 9, 4, 2056, NULL),
(10481, 9, 5, 2, NULL),
(10483, 9, 5, 3, '-mdp_client,mdp_absystech'),
(10482, 9, 5, 4, NULL),
(10487, 9, 5, 9, NULL),
(10471, 9, 5, 37, NULL),
(10473, 9, 5, 38, NULL),
(10475, 9, 5, 39, NULL),
(10479, 9, 5, 41, NULL),
(10476, 9, 5, 42, NULL),
(10477, 9, 5, 43, NULL),
(10474, 9, 5, 44, NULL),
(10478, 9, 5, 45, NULL),
(10480, 9, 5, 46, NULL),
(10489, 9, 5, 47, NULL),
(10503, 9, 5, 48, NULL),
(10526, 9, 5, 50, NULL),
(10498, 9, 5, 51, NULL),
(10490, 9, 5, 52, NULL),
(10493, 9, 5, 53, NULL),
(10494, 9, 5, 54, NULL),
(10496, 9, 5, 55, NULL),
(10510, 9, 5, 59, NULL),
(10506, 9, 5, 60, NULL),
(10515, 9, 5, 62, NULL),
(10501, 9, 5, 75, NULL),
(10499, 9, 5, 82, NULL),
(10538, 9, 5, 84, NULL),
(10517, 9, 5, 86, NULL),
(10516, 9, 5, 89, NULL),
(10519, 9, 5, 92, NULL),
(10518, 9, 5, 93, NULL),
(10500, 9, 5, 97, NULL),
(10502, 9, 5, 98, NULL),
(10488, 9, 5, 111, NULL),
(15007, 9, 5, 116, NULL),
(15017, 9, 5, 117, NULL),
(15067, 9, 5, 118, NULL),
(15057, 9, 5, 119, NULL),
(15037, 9, 5, 120, NULL),
(15098, 9, 5, 121, NULL),
(15077, 9, 5, 122, NULL),
(15088, 9, 5, 124, NULL),
(10484, 9, 5, 125, NULL),
(10485, 9, 5, 126, NULL),
(10530, 9, 5, 132, NULL),
(14348, 9, 5, 139, NULL),
(10486, 9, 5, 140, NULL),
(13347, 9, 5, 141, NULL),
(13357, 9, 5, 143, NULL),
(10504, 9, 5, 150, NULL),
(10511, 9, 5, 151, NULL),
(10508, 9, 5, 152, NULL),
(10497, 9, 5, 153, NULL),
(10534, 9, 5, 157, NULL),
(10522, 9, 5, 160, NULL),
(15047, 9, 5, 174, NULL),
(15027, 9, 5, 179, NULL),
(14273, 9, 5, 190, NULL),
(17035, 9, 5, 191, NULL),
(14559, 9, 5, 195, NULL),
(14609, 9, 5, 196, NULL),
(14914, 9, 5, 197, NULL),
(16273, 9, 5, 2047, NULL),
(16491, 9, 5, 2048, NULL),
(16913, 9, 5, 2052, NULL),
(16923, 9, 5, 2053, NULL),
(16933, 9, 5, 2054, NULL),
(16944, 9, 5, 2055, NULL),
(17005, 9, 5, 2056, NULL),
(10834, 9, 6, 2, NULL),
(10836, 9, 6, 3, '-mdp_client,mdp_absystech'),
(10835, 9, 6, 4, NULL),
(10840, 9, 6, 9, NULL),
(10824, 9, 6, 37, NULL),
(10826, 9, 6, 38, NULL),
(10828, 9, 6, 39, NULL),
(10832, 9, 6, 41, NULL),
(10829, 9, 6, 42, NULL),
(10830, 9, 6, 43, NULL),
(10827, 9, 6, 44, NULL),
(10831, 9, 6, 45, NULL),
(10833, 9, 6, 46, NULL),
(10842, 9, 6, 47, NULL),
(10856, 9, 6, 48, NULL),
(10879, 9, 6, 50, NULL),
(10851, 9, 6, 51, NULL),
(10843, 9, 6, 52, NULL),
(10846, 9, 6, 53, NULL),
(10847, 9, 6, 54, NULL),
(10849, 9, 6, 55, NULL),
(10863, 9, 6, 59, NULL),
(13485, 9, 6, 60, NULL),
(10868, 9, 6, 62, NULL),
(10854, 9, 6, 75, NULL),
(10852, 9, 6, 82, NULL),
(10891, 9, 6, 84, NULL),
(10870, 9, 6, 86, NULL),
(10869, 9, 6, 89, NULL),
(10872, 9, 6, 92, NULL),
(10871, 9, 6, 93, NULL),
(10853, 9, 6, 97, NULL),
(10855, 9, 6, 98, NULL),
(10841, 9, 6, 111, NULL),
(15006, 9, 6, 116, NULL),
(15016, 9, 6, 117, NULL),
(15066, 9, 6, 118, NULL),
(15056, 9, 6, 119, NULL),
(15036, 9, 6, 120, NULL),
(15097, 9, 6, 121, NULL),
(15076, 9, 6, 122, NULL),
(15087, 9, 6, 124, NULL),
(10837, 9, 6, 125, NULL),
(10838, 9, 6, 126, NULL),
(10883, 9, 6, 132, NULL),
(14347, 9, 6, 139, NULL),
(10839, 9, 6, 140, NULL),
(10857, 9, 6, 150, NULL),
(10864, 9, 6, 151, NULL),
(10861, 9, 6, 152, NULL),
(10850, 9, 6, 153, NULL),
(10887, 9, 6, 157, NULL);
INSERT INTO `profil_privilege` (`id_profil_privilege`, `id_profil`, `id_privilege`, `id_module`, `field`) VALUES
(10875, 9, 6, 160, NULL),
(15046, 9, 6, 174, NULL),
(15026, 9, 6, 179, NULL),
(14272, 9, 6, 190, NULL),
(17036, 9, 6, 191, NULL),
(14558, 9, 6, 195, NULL),
(14608, 9, 6, 196, NULL),
(14915, 9, 6, 197, NULL),
(16490, 9, 6, 2048, NULL),
(16912, 9, 6, 2052, NULL),
(16922, 9, 6, 2053, NULL),
(16932, 9, 6, 2054, NULL),
(16943, 9, 6, 2055, NULL),
(17004, 9, 6, 2056, NULL),
(11187, 9, 7, 2, NULL),
(11189, 9, 7, 3, '-mdp_client,mdp_absystech'),
(11188, 9, 7, 4, NULL),
(11193, 9, 7, 9, NULL),
(11177, 9, 7, 37, NULL),
(11179, 9, 7, 38, NULL),
(11181, 9, 7, 39, NULL),
(11185, 9, 7, 41, NULL),
(11182, 9, 7, 42, NULL),
(11183, 9, 7, 43, NULL),
(11180, 9, 7, 44, NULL),
(11184, 9, 7, 45, NULL),
(11186, 9, 7, 46, NULL),
(11195, 9, 7, 47, NULL),
(11209, 9, 7, 48, NULL),
(11232, 9, 7, 50, NULL),
(11204, 9, 7, 51, NULL),
(11196, 9, 7, 52, NULL),
(11199, 9, 7, 53, NULL),
(11200, 9, 7, 54, NULL),
(11202, 9, 7, 55, NULL),
(11216, 9, 7, 59, NULL),
(13484, 9, 7, 60, NULL),
(11221, 9, 7, 62, NULL),
(11207, 9, 7, 75, NULL),
(11205, 9, 7, 82, NULL),
(11244, 9, 7, 84, NULL),
(11223, 9, 7, 86, NULL),
(11222, 9, 7, 89, NULL),
(11225, 9, 7, 92, NULL),
(11224, 9, 7, 93, NULL),
(11206, 9, 7, 97, NULL),
(11208, 9, 7, 98, NULL),
(11194, 9, 7, 111, NULL),
(15005, 9, 7, 116, NULL),
(15015, 9, 7, 117, NULL),
(15065, 9, 7, 118, NULL),
(15055, 9, 7, 119, NULL),
(15035, 9, 7, 120, NULL),
(15096, 9, 7, 121, NULL),
(15075, 9, 7, 122, NULL),
(15086, 9, 7, 124, NULL),
(11190, 9, 7, 125, NULL),
(11191, 9, 7, 126, NULL),
(11236, 9, 7, 132, NULL),
(14346, 9, 7, 139, NULL),
(11192, 9, 7, 140, NULL),
(13345, 9, 7, 141, NULL),
(13355, 9, 7, 143, NULL),
(11210, 9, 7, 150, NULL),
(11217, 9, 7, 151, NULL),
(11214, 9, 7, 152, NULL),
(11203, 9, 7, 153, NULL),
(11240, 9, 7, 157, NULL),
(11228, 9, 7, 160, NULL),
(15045, 9, 7, 174, NULL),
(15025, 9, 7, 179, NULL),
(14271, 9, 7, 190, NULL),
(17037, 9, 7, 191, NULL),
(14557, 9, 7, 195, NULL),
(14607, 9, 7, 196, NULL),
(14916, 9, 7, 197, NULL),
(16489, 9, 7, 2048, NULL),
(16911, 9, 7, 2052, NULL),
(16921, 9, 7, 2053, NULL),
(16931, 9, 7, 2054, NULL),
(16942, 9, 7, 2055, NULL),
(17003, 9, 7, 2056, NULL),
(11540, 9, 8, 2, NULL),
(11542, 9, 8, 3, '-mdp_client,mdp_absystech'),
(11541, 9, 8, 4, NULL),
(11546, 9, 8, 9, NULL),
(11530, 9, 8, 37, NULL),
(11532, 9, 8, 38, NULL),
(11534, 9, 8, 39, NULL),
(11538, 9, 8, 41, NULL),
(11535, 9, 8, 42, NULL),
(11536, 9, 8, 43, NULL),
(11533, 9, 8, 44, NULL),
(11537, 9, 8, 45, NULL),
(11539, 9, 8, 46, NULL),
(11548, 9, 8, 47, NULL),
(11562, 9, 8, 48, NULL),
(11585, 9, 8, 50, NULL),
(11557, 9, 8, 51, NULL),
(11549, 9, 8, 52, NULL),
(11552, 9, 8, 53, NULL),
(11553, 9, 8, 54, NULL),
(11555, 9, 8, 55, NULL),
(11569, 9, 8, 59, NULL),
(11574, 9, 8, 62, NULL),
(11560, 9, 8, 75, NULL),
(11558, 9, 8, 82, NULL),
(11576, 9, 8, 86, NULL),
(11575, 9, 8, 89, NULL),
(11578, 9, 8, 92, NULL),
(11577, 9, 8, 93, NULL),
(11559, 9, 8, 97, NULL),
(11561, 9, 8, 98, NULL),
(11547, 9, 8, 111, NULL),
(15004, 9, 8, 116, NULL),
(15014, 9, 8, 117, NULL),
(15064, 9, 8, 118, NULL),
(15054, 9, 8, 119, NULL),
(15034, 9, 8, 120, NULL),
(15095, 9, 8, 121, NULL),
(15074, 9, 8, 122, NULL),
(15085, 9, 8, 124, NULL),
(11543, 9, 8, 125, NULL),
(11544, 9, 8, 126, NULL),
(11589, 9, 8, 132, NULL),
(14345, 9, 8, 139, NULL),
(11545, 9, 8, 140, NULL),
(13344, 9, 8, 141, NULL),
(13354, 9, 8, 143, NULL),
(11563, 9, 8, 150, NULL),
(11570, 9, 8, 151, NULL),
(11567, 9, 8, 152, NULL),
(11556, 9, 8, 153, NULL),
(11593, 9, 8, 157, NULL),
(11581, 9, 8, 160, NULL),
(15044, 9, 8, 174, NULL),
(15024, 9, 8, 179, NULL),
(17038, 9, 8, 191, NULL),
(14556, 9, 8, 195, NULL),
(14606, 9, 8, 196, NULL),
(14917, 9, 8, 197, NULL),
(16488, 9, 8, 2048, NULL),
(16941, 9, 8, 2055, NULL),
(17002, 9, 8, 2056, NULL),
(11893, 9, 9, 2, NULL),
(11895, 9, 9, 3, '-mdp_client,mdp_absystech'),
(11894, 9, 9, 4, NULL),
(11899, 9, 9, 9, NULL),
(11883, 9, 9, 37, NULL),
(11885, 9, 9, 38, NULL),
(11887, 9, 9, 39, NULL),
(11891, 9, 9, 41, NULL),
(11888, 9, 9, 42, NULL),
(11889, 9, 9, 43, NULL),
(11886, 9, 9, 44, NULL),
(11890, 9, 9, 45, NULL),
(11892, 9, 9, 46, NULL),
(11901, 9, 9, 47, NULL),
(11915, 9, 9, 48, NULL),
(11938, 9, 9, 50, NULL),
(11910, 9, 9, 51, NULL),
(11902, 9, 9, 52, NULL),
(11905, 9, 9, 53, NULL),
(11906, 9, 9, 54, NULL),
(11908, 9, 9, 55, NULL),
(11922, 9, 9, 59, NULL),
(11927, 9, 9, 62, NULL),
(11913, 9, 9, 75, NULL),
(11911, 9, 9, 82, NULL),
(11929, 9, 9, 86, NULL),
(11928, 9, 9, 89, NULL),
(11931, 9, 9, 92, NULL),
(11930, 9, 9, 93, NULL),
(11912, 9, 9, 97, NULL),
(11914, 9, 9, 98, NULL),
(11900, 9, 9, 111, NULL),
(15003, 9, 9, 116, NULL),
(15013, 9, 9, 117, NULL),
(15063, 9, 9, 118, NULL),
(15053, 9, 9, 119, NULL),
(15033, 9, 9, 120, NULL),
(15094, 9, 9, 121, NULL),
(15073, 9, 9, 122, NULL),
(15084, 9, 9, 124, NULL),
(11896, 9, 9, 125, NULL),
(11897, 9, 9, 126, NULL),
(11942, 9, 9, 132, NULL),
(14344, 9, 9, 139, NULL),
(11898, 9, 9, 140, NULL),
(13343, 9, 9, 141, NULL),
(13353, 9, 9, 143, NULL),
(11916, 9, 9, 150, NULL),
(11923, 9, 9, 151, NULL),
(11920, 9, 9, 152, NULL),
(11909, 9, 9, 153, NULL),
(11946, 9, 9, 157, NULL),
(11934, 9, 9, 160, NULL),
(15043, 9, 9, 174, NULL),
(15023, 9, 9, 179, NULL),
(17039, 9, 9, 191, NULL),
(14555, 9, 9, 195, NULL),
(14605, 9, 9, 196, NULL),
(16487, 9, 9, 2048, NULL),
(16909, 9, 9, 2052, NULL),
(16919, 9, 9, 2053, NULL),
(16929, 9, 9, 2054, NULL),
(16940, 9, 9, 2055, NULL),
(17001, 9, 9, 2056, NULL),
(12246, 9, 10, 2, NULL),
(12248, 9, 10, 3, '-mdp_client,mdp_absystech'),
(12247, 9, 10, 4, NULL),
(12252, 9, 10, 9, NULL),
(12236, 9, 10, 37, NULL),
(12238, 9, 10, 38, NULL),
(12240, 9, 10, 39, NULL),
(12244, 9, 10, 41, NULL),
(12241, 9, 10, 42, NULL),
(12242, 9, 10, 43, NULL),
(12239, 9, 10, 44, NULL),
(12243, 9, 10, 45, NULL),
(12245, 9, 10, 46, NULL),
(12254, 9, 10, 47, NULL),
(12268, 9, 10, 48, NULL),
(12291, 9, 10, 50, NULL),
(12263, 9, 10, 51, NULL),
(12255, 9, 10, 52, NULL),
(12258, 9, 10, 53, NULL),
(12259, 9, 10, 54, NULL),
(12261, 9, 10, 55, NULL),
(12275, 9, 10, 59, NULL),
(12280, 9, 10, 62, NULL),
(12266, 9, 10, 75, NULL),
(12264, 9, 10, 82, NULL),
(12282, 9, 10, 86, NULL),
(12281, 9, 10, 89, NULL),
(12284, 9, 10, 92, NULL),
(12283, 9, 10, 93, NULL),
(12265, 9, 10, 97, NULL),
(12267, 9, 10, 98, NULL),
(12253, 9, 10, 111, NULL),
(15002, 9, 10, 116, NULL),
(15012, 9, 10, 117, NULL),
(15062, 9, 10, 118, NULL),
(15052, 9, 10, 119, NULL),
(15032, 9, 10, 120, NULL),
(15093, 9, 10, 121, NULL),
(15072, 9, 10, 122, NULL),
(15083, 9, 10, 124, NULL),
(12249, 9, 10, 125, NULL),
(12250, 9, 10, 126, NULL),
(12295, 9, 10, 132, NULL),
(14343, 9, 10, 139, NULL),
(12251, 9, 10, 140, NULL),
(13342, 9, 10, 141, NULL),
(13352, 9, 10, 143, NULL),
(12269, 9, 10, 150, NULL),
(12276, 9, 10, 151, NULL),
(12273, 9, 10, 152, NULL),
(12262, 9, 10, 153, NULL),
(12299, 9, 10, 157, NULL),
(12287, 9, 10, 160, NULL),
(15042, 9, 10, 174, NULL),
(15022, 9, 10, 179, NULL),
(17040, 9, 10, 191, NULL),
(16908, 9, 10, 2052, NULL),
(16918, 9, 10, 2053, NULL),
(16928, 9, 10, 2054, NULL),
(16939, 9, 10, 2055, NULL),
(17000, 9, 10, 2056, NULL),
(14175, 10, 1, 2, NULL),
(14173, 10, 1, 3, '-mdp_client,mdp_absystech'),
(14174, 10, 1, 4, NULL),
(14172, 10, 1, 9, NULL),
(14183, 10, 1, 37, NULL),
(14983, 10, 1, 38, NULL),
(14181, 10, 1, 39, NULL),
(14177, 10, 1, 41, NULL),
(14180, 10, 1, 42, NULL),
(14179, 10, 1, 43, NULL),
(14182, 10, 1, 44, NULL),
(14178, 10, 1, 45, NULL),
(14176, 10, 1, 46, NULL),
(13923, 10, 1, 47, NULL),
(14171, 10, 1, 48, NULL),
(13883, 10, 1, 50, NULL),
(13905, 10, 1, 52, NULL),
(13914, 10, 1, 53, NULL),
(16637, 10, 1, 54, NULL),
(16647, 10, 1, 55, NULL),
(14168, 10, 1, 59, NULL),
(14170, 10, 1, 60, NULL),
(14164, 10, 1, 62, NULL),
(13924, 10, 1, 68, NULL),
(14981, 10, 1, 69, NULL),
(13887, 10, 1, 75, NULL),
(16677, 10, 1, 82, NULL),
(14280, 10, 1, 83, NULL),
(14282, 10, 1, 84, NULL),
(14163, 10, 1, 86, NULL),
(15129, 10, 1, 87, NULL),
(13879, 10, 1, 97, NULL),
(13880, 10, 1, 97, NULL),
(13878, 10, 1, 98, NULL),
(16723, 10, 1, 107, NULL),
(13929, 10, 1, 111, NULL),
(13865, 10, 1, 116, NULL),
(13855, 10, 1, 117, NULL),
(13815, 10, 1, 118, NULL),
(13825, 10, 1, 119, NULL),
(13845, 10, 1, 120, NULL),
(13795, 10, 1, 121, NULL),
(13805, 10, 1, 122, NULL),
(13785, 10, 1, 124, NULL),
(14184, 10, 1, 125, NULL),
(14185, 10, 1, 126, NULL),
(14292, 10, 1, 132, NULL),
(14187, 10, 1, 134, NULL),
(14186, 10, 1, 135, NULL),
(16484, 10, 1, 139, NULL),
(13779, 10, 1, 140, NULL),
(14159, 10, 1, 141, NULL),
(14158, 10, 1, 143, NULL),
(16756, 10, 1, 150, NULL),
(14167, 10, 1, 151, NULL),
(14169, 10, 1, 152, NULL),
(16657, 10, 1, 153, NULL),
(13877, 10, 1, 157, NULL),
(13875, 10, 1, 160, NULL),
(13784, 10, 1, 167, NULL),
(13896, 10, 1, 168, NULL),
(13781, 10, 1, 169, NULL),
(16667, 10, 1, 170, NULL),
(13835, 10, 1, 174, NULL),
(13884, 10, 1, 176, NULL),
(13885, 10, 1, 177, NULL),
(14224, 10, 1, 179, NULL),
(14281, 10, 1, 190, NULL),
(14934, 10, 1, 192, NULL),
(14899, 10, 1, 195, NULL),
(14900, 10, 1, 196, NULL),
(16736, 10, 1, 197, NULL),
(16721, 10, 1, 2046, NULL),
(16272, 10, 1, 2047, NULL),
(16515, 10, 1, 2048, NULL),
(16826, 10, 1, 2052, NULL),
(16846, 10, 1, 2053, NULL),
(16836, 10, 1, 2054, NULL),
(16999, 10, 1, 2055, NULL),
(17019, 10, 1, 2056, NULL),
(14149, 10, 2, 2, NULL),
(14147, 10, 2, 3, '-mdp_client,mdp_absystech'),
(14148, 10, 2, 4, NULL),
(14146, 10, 2, 9, NULL),
(14157, 10, 2, 37, NULL),
(14984, 10, 2, 38, NULL),
(14155, 10, 2, 39, NULL),
(14151, 10, 2, 41, NULL),
(14154, 10, 2, 42, NULL),
(14153, 10, 2, 43, NULL),
(14156, 10, 2, 44, NULL),
(14152, 10, 2, 45, NULL),
(14150, 10, 2, 46, NULL),
(14145, 10, 2, 48, NULL),
(13906, 10, 2, 52, NULL),
(13915, 10, 2, 53, NULL),
(16636, 10, 2, 54, NULL),
(16646, 10, 2, 55, NULL),
(14142, 10, 2, 59, NULL),
(14144, 10, 2, 60, NULL),
(14138, 10, 2, 62, NULL),
(13925, 10, 2, 68, NULL),
(13888, 10, 2, 75, NULL),
(16676, 10, 2, 82, NULL),
(14137, 10, 2, 86, NULL),
(16724, 10, 2, 107, NULL),
(13866, 10, 2, 116, NULL),
(13856, 10, 2, 117, NULL),
(13816, 10, 2, 118, NULL),
(13826, 10, 2, 119, NULL),
(13846, 10, 2, 120, NULL),
(13796, 10, 2, 121, NULL),
(13806, 10, 2, 122, NULL),
(13786, 10, 2, 124, NULL),
(14979, 10, 2, 126, NULL),
(14293, 10, 2, 132, NULL),
(16483, 10, 2, 139, NULL),
(13778, 10, 2, 140, NULL),
(14133, 10, 2, 141, NULL),
(14132, 10, 2, 143, NULL),
(16755, 10, 2, 150, NULL),
(14141, 10, 2, 151, NULL),
(14143, 10, 2, 152, NULL),
(16656, 10, 2, 153, NULL),
(13783, 10, 2, 167, NULL),
(13897, 10, 2, 168, NULL),
(16666, 10, 2, 170, NULL),
(13836, 10, 2, 174, NULL),
(13886, 10, 2, 177, NULL),
(14223, 10, 2, 179, NULL),
(14283, 10, 2, 190, NULL),
(14901, 10, 2, 195, NULL),
(16722, 10, 2, 196, NULL),
(16735, 10, 2, 197, NULL),
(16720, 10, 2, 2046, NULL),
(16514, 10, 2, 2048, NULL),
(16825, 10, 2, 2052, NULL),
(16845, 10, 2, 2053, NULL),
(16835, 10, 2, 2054, NULL),
(16998, 10, 2, 2055, NULL),
(17018, 10, 2, 2056, NULL),
(14123, 10, 3, 2, NULL),
(14121, 10, 3, 3, '-mdp_client,mdp_absystech'),
(14122, 10, 3, 4, NULL),
(14120, 10, 3, 9, NULL),
(14131, 10, 3, 37, NULL),
(14985, 10, 3, 38, NULL),
(14129, 10, 3, 39, NULL),
(14125, 10, 3, 41, NULL),
(14128, 10, 3, 42, NULL),
(14127, 10, 3, 43, NULL),
(14973, 10, 3, 44, NULL),
(14126, 10, 3, 45, NULL),
(14124, 10, 3, 46, NULL),
(14119, 10, 3, 48, NULL),
(13907, 10, 3, 52, NULL),
(13916, 10, 3, 53, NULL),
(16635, 10, 3, 54, NULL),
(16645, 10, 3, 55, NULL),
(14116, 10, 3, 59, NULL),
(14118, 10, 3, 60, NULL),
(14112, 10, 3, 62, NULL),
(13882, 10, 3, 68, NULL),
(13889, 10, 3, 75, NULL),
(16675, 10, 3, 82, NULL),
(14111, 10, 3, 86, NULL),
(16725, 10, 3, 107, NULL),
(13867, 10, 3, 116, NULL),
(13857, 10, 3, 117, NULL),
(13817, 10, 3, 118, NULL),
(13827, 10, 3, 119, NULL),
(13847, 10, 3, 120, NULL),
(13797, 10, 3, 121, NULL),
(13807, 10, 3, 122, NULL),
(13787, 10, 3, 124, NULL),
(14980, 10, 3, 126, NULL),
(16482, 10, 3, 139, NULL),
(13777, 10, 3, 140, NULL),
(14107, 10, 3, 141, NULL),
(14106, 10, 3, 143, NULL),
(16754, 10, 3, 150, NULL),
(14115, 10, 3, 151, NULL),
(14117, 10, 3, 152, NULL),
(16655, 10, 3, 153, NULL),
(13876, 10, 3, 157, NULL),
(14294, 10, 3, 167, NULL),
(13898, 10, 3, 168, NULL),
(16665, 10, 3, 170, NULL),
(13837, 10, 3, 174, NULL),
(13881, 10, 3, 177, NULL),
(14222, 10, 3, 179, NULL),
(14284, 10, 3, 190, NULL),
(16734, 10, 3, 197, NULL),
(16719, 10, 3, 2046, NULL),
(16513, 10, 3, 2048, NULL),
(16824, 10, 3, 2052, NULL),
(16844, 10, 3, 2053, NULL),
(16834, 10, 3, 2054, NULL),
(16997, 10, 3, 2055, NULL),
(17017, 10, 3, 2056, NULL),
(14097, 10, 4, 2, NULL),
(14095, 10, 4, 3, '-mdp_client,mdp_absystech'),
(14096, 10, 4, 4, NULL),
(14094, 10, 4, 9, NULL),
(14105, 10, 4, 37, NULL),
(14986, 10, 4, 38, NULL),
(14103, 10, 4, 39, NULL),
(14099, 10, 4, 41, NULL),
(14102, 10, 4, 42, NULL),
(14101, 10, 4, 43, NULL),
(14974, 10, 4, 44, NULL),
(14100, 10, 4, 45, NULL),
(14093, 10, 4, 48, NULL),
(13908, 10, 4, 52, NULL),
(13917, 10, 4, 53, NULL),
(14090, 10, 4, 59, NULL),
(14086, 10, 4, 62, NULL),
(13890, 10, 4, 75, NULL),
(14085, 10, 4, 86, NULL),
(16726, 10, 4, 107, NULL),
(13868, 10, 4, 116, NULL),
(13858, 10, 4, 117, NULL),
(13818, 10, 4, 118, NULL),
(13828, 10, 4, 119, NULL),
(13848, 10, 4, 120, NULL),
(13798, 10, 4, 121, NULL),
(13808, 10, 4, 122, NULL),
(13788, 10, 4, 124, NULL),
(16485, 10, 4, 139, NULL),
(13776, 10, 4, 140, NULL),
(14081, 10, 4, 141, NULL),
(14080, 10, 4, 143, NULL),
(14089, 10, 4, 151, NULL),
(14091, 10, 4, 152, NULL),
(14295, 10, 4, 167, NULL),
(13899, 10, 4, 168, NULL),
(13838, 10, 4, 174, NULL),
(14221, 10, 4, 179, NULL),
(16271, 10, 4, 2047, NULL),
(16598, 10, 4, 2048, NULL),
(16823, 10, 4, 2052, NULL),
(16843, 10, 4, 2053, NULL),
(16833, 10, 4, 2054, NULL),
(16996, 10, 4, 2055, NULL),
(14071, 10, 5, 2, NULL),
(14069, 10, 5, 3, '-mdp_client,mdp_absystech'),
(14070, 10, 5, 4, NULL),
(14068, 10, 5, 9, NULL),
(14079, 10, 5, 37, NULL),
(14077, 10, 5, 39, NULL),
(14073, 10, 5, 41, NULL),
(14076, 10, 5, 42, NULL),
(14075, 10, 5, 43, NULL),
(14078, 10, 5, 44, NULL),
(14074, 10, 5, 45, NULL),
(14072, 10, 5, 46, NULL),
(14067, 10, 5, 48, NULL),
(13909, 10, 5, 52, NULL),
(13918, 10, 5, 53, NULL),
(16633, 10, 5, 54, NULL),
(16643, 10, 5, 55, NULL),
(14064, 10, 5, 59, NULL),
(14066, 10, 5, 60, NULL),
(14060, 10, 5, 62, NULL),
(13926, 10, 5, 68, NULL),
(13891, 10, 5, 75, NULL),
(16673, 10, 5, 82, NULL),
(14285, 10, 5, 84, NULL),
(14059, 10, 5, 86, NULL),
(13869, 10, 5, 116, NULL),
(13859, 10, 5, 117, NULL),
(13819, 10, 5, 118, NULL),
(13829, 10, 5, 119, NULL),
(13849, 10, 5, 120, NULL),
(13799, 10, 5, 121, NULL),
(13809, 10, 5, 122, NULL),
(13789, 10, 5, 124, NULL),
(16480, 10, 5, 139, NULL),
(13775, 10, 5, 140, NULL),
(14055, 10, 5, 141, NULL),
(14054, 10, 5, 143, NULL),
(16752, 10, 5, 150, NULL),
(14063, 10, 5, 151, NULL),
(14065, 10, 5, 152, NULL),
(16653, 10, 5, 153, NULL),
(13900, 10, 5, 168, NULL),
(16663, 10, 5, 170, NULL),
(13839, 10, 5, 174, NULL),
(14220, 10, 5, 179, NULL),
(14286, 10, 5, 190, NULL),
(14906, 10, 5, 195, NULL),
(14907, 10, 5, 196, NULL),
(16732, 10, 5, 197, NULL),
(16717, 10, 5, 2046, NULL),
(16270, 10, 5, 2047, NULL),
(16511, 10, 5, 2048, NULL),
(16822, 10, 5, 2052, NULL),
(16842, 10, 5, 2053, NULL),
(16832, 10, 5, 2054, NULL),
(16995, 10, 5, 2055, NULL),
(17015, 10, 5, 2056, NULL),
(14045, 10, 6, 2, NULL),
(14043, 10, 6, 3, '-mdp_client,mdp_absystech'),
(14044, 10, 6, 4, NULL),
(14042, 10, 6, 9, NULL),
(14053, 10, 6, 37, NULL),
(14051, 10, 6, 39, NULL),
(14047, 10, 6, 41, NULL),
(14050, 10, 6, 42, NULL),
(14049, 10, 6, 43, NULL),
(14052, 10, 6, 44, NULL),
(14048, 10, 6, 45, NULL),
(14046, 10, 6, 46, NULL),
(14041, 10, 6, 48, NULL),
(13910, 10, 6, 52, NULL),
(13919, 10, 6, 53, NULL),
(16632, 10, 6, 54, NULL),
(16642, 10, 6, 55, NULL),
(14038, 10, 6, 59, NULL),
(14040, 10, 6, 60, NULL),
(14034, 10, 6, 62, NULL),
(13927, 10, 6, 68, NULL),
(13892, 10, 6, 75, NULL),
(16672, 10, 6, 82, NULL),
(14288, 10, 6, 84, NULL),
(14033, 10, 6, 86, NULL),
(13870, 10, 6, 116, NULL),
(13860, 10, 6, 117, NULL),
(13820, 10, 6, 118, NULL),
(13830, 10, 6, 119, NULL),
(13850, 10, 6, 120, NULL),
(13800, 10, 6, 121, NULL),
(13810, 10, 6, 122, NULL),
(13790, 10, 6, 124, NULL),
(16479, 10, 6, 139, NULL),
(13774, 10, 6, 140, NULL),
(16751, 10, 6, 150, NULL),
(14037, 10, 6, 151, NULL),
(14039, 10, 6, 152, NULL),
(16652, 10, 6, 153, NULL),
(13901, 10, 6, 168, NULL),
(16662, 10, 6, 170, NULL),
(13840, 10, 6, 174, NULL),
(14219, 10, 6, 179, NULL),
(14287, 10, 6, 190, NULL),
(14905, 10, 6, 195, NULL),
(14908, 10, 6, 196, NULL),
(16731, 10, 6, 197, NULL),
(16716, 10, 6, 2046, NULL),
(16510, 10, 6, 2048, NULL),
(16821, 10, 6, 2052, NULL),
(16841, 10, 6, 2053, NULL),
(16831, 10, 6, 2054, NULL),
(16994, 10, 6, 2055, NULL),
(17014, 10, 6, 2056, NULL),
(14021, 10, 7, 2, NULL),
(14019, 10, 7, 3, '-mdp_client,mdp_absystech'),
(14020, 10, 7, 4, NULL),
(14018, 10, 7, 9, NULL),
(14029, 10, 7, 37, NULL),
(14027, 10, 7, 39, NULL),
(14023, 10, 7, 41, NULL),
(14026, 10, 7, 42, NULL),
(14025, 10, 7, 43, NULL),
(14028, 10, 7, 44, NULL),
(14024, 10, 7, 45, NULL),
(14022, 10, 7, 46, NULL),
(14017, 10, 7, 48, NULL),
(13911, 10, 7, 52, NULL),
(13920, 10, 7, 53, NULL),
(16631, 10, 7, 54, NULL),
(16641, 10, 7, 55, NULL),
(14014, 10, 7, 59, NULL),
(14016, 10, 7, 60, NULL),
(14010, 10, 7, 62, NULL),
(13928, 10, 7, 68, NULL),
(13893, 10, 7, 75, NULL),
(16671, 10, 7, 82, NULL),
(14289, 10, 7, 84, NULL),
(14009, 10, 7, 86, NULL),
(13871, 10, 7, 116, NULL),
(13861, 10, 7, 117, NULL),
(13821, 10, 7, 118, NULL),
(13831, 10, 7, 119, NULL),
(13851, 10, 7, 120, NULL),
(13801, 10, 7, 121, NULL),
(13811, 10, 7, 122, NULL),
(13791, 10, 7, 124, NULL),
(16478, 10, 7, 139, NULL),
(13773, 10, 7, 140, NULL),
(14005, 10, 7, 141, NULL),
(14004, 10, 7, 143, NULL),
(16750, 10, 7, 150, NULL),
(14013, 10, 7, 151, NULL),
(14015, 10, 7, 152, NULL),
(16651, 10, 7, 153, NULL),
(13902, 10, 7, 168, NULL),
(16661, 10, 7, 170, NULL),
(13841, 10, 7, 174, NULL),
(14218, 10, 7, 179, NULL),
(14290, 10, 7, 190, NULL),
(14904, 10, 7, 195, NULL),
(14909, 10, 7, 196, NULL),
(16730, 10, 7, 197, NULL),
(16715, 10, 7, 2046, NULL),
(16509, 10, 7, 2048, NULL),
(16820, 10, 7, 2052, NULL),
(16840, 10, 7, 2053, NULL),
(16830, 10, 7, 2054, NULL),
(16993, 10, 7, 2055, NULL),
(17013, 10, 7, 2056, NULL),
(13995, 10, 8, 2, NULL),
(13993, 10, 8, 3, '-mdp_client,mdp_absystech'),
(13994, 10, 8, 4, NULL),
(13992, 10, 8, 9, NULL),
(14003, 10, 8, 37, NULL),
(14001, 10, 8, 39, NULL),
(13997, 10, 8, 41, NULL),
(14000, 10, 8, 42, NULL),
(13999, 10, 8, 43, NULL),
(14002, 10, 8, 44, NULL),
(13998, 10, 8, 45, NULL),
(13996, 10, 8, 46, NULL),
(13991, 10, 8, 48, NULL),
(13912, 10, 8, 52, NULL),
(13921, 10, 8, 53, NULL),
(16630, 10, 8, 54, NULL),
(16640, 10, 8, 55, NULL),
(13988, 10, 8, 59, NULL),
(13990, 10, 8, 60, NULL),
(13984, 10, 8, 62, NULL),
(13894, 10, 8, 75, NULL),
(16670, 10, 8, 82, NULL),
(13983, 10, 8, 86, NULL),
(13872, 10, 8, 116, NULL),
(13862, 10, 8, 117, NULL),
(13822, 10, 8, 118, NULL),
(13832, 10, 8, 119, NULL),
(13852, 10, 8, 120, NULL),
(13802, 10, 8, 121, NULL),
(13812, 10, 8, 122, NULL),
(13792, 10, 8, 124, NULL),
(16477, 10, 8, 139, NULL),
(13772, 10, 8, 140, NULL),
(13979, 10, 8, 141, NULL),
(13978, 10, 8, 143, NULL),
(16749, 10, 8, 150, NULL),
(13987, 10, 8, 151, NULL),
(13989, 10, 8, 152, NULL),
(16650, 10, 8, 153, NULL),
(13903, 10, 8, 168, NULL),
(16660, 10, 8, 170, NULL),
(13842, 10, 8, 174, NULL),
(14217, 10, 8, 179, NULL),
(14903, 10, 8, 195, NULL),
(14910, 10, 8, 196, NULL),
(16729, 10, 8, 197, NULL),
(16714, 10, 8, 2046, NULL),
(16508, 10, 8, 2048, NULL),
(16992, 10, 8, 2055, NULL),
(17012, 10, 8, 2056, NULL),
(13969, 10, 9, 2, NULL),
(13967, 10, 9, 3, '-mdp_client,mdp_absystech'),
(13968, 10, 9, 4, NULL),
(13966, 10, 9, 9, NULL),
(13977, 10, 9, 37, NULL),
(13975, 10, 9, 39, NULL),
(13971, 10, 9, 41, NULL),
(13974, 10, 9, 42, NULL),
(13973, 10, 9, 43, NULL),
(13976, 10, 9, 44, NULL),
(13972, 10, 9, 45, NULL),
(13970, 10, 9, 46, NULL),
(13965, 10, 9, 48, NULL),
(13913, 10, 9, 52, NULL),
(13922, 10, 9, 53, NULL),
(16629, 10, 9, 54, NULL),
(16639, 10, 9, 55, NULL),
(13962, 10, 9, 59, NULL),
(13964, 10, 9, 60, NULL),
(13958, 10, 9, 62, NULL),
(13895, 10, 9, 75, NULL),
(16669, 10, 9, 82, NULL),
(13957, 10, 9, 86, NULL),
(13873, 10, 9, 116, NULL),
(13863, 10, 9, 117, NULL),
(13823, 10, 9, 118, NULL),
(13833, 10, 9, 119, NULL),
(13853, 10, 9, 120, NULL),
(13803, 10, 9, 121, NULL),
(13813, 10, 9, 122, NULL),
(13793, 10, 9, 124, NULL),
(16476, 10, 9, 139, NULL),
(13771, 10, 9, 140, NULL),
(13953, 10, 9, 141, NULL),
(13952, 10, 9, 143, NULL),
(16748, 10, 9, 150, NULL),
(13961, 10, 9, 151, NULL),
(13963, 10, 9, 152, NULL),
(16649, 10, 9, 153, NULL),
(13904, 10, 9, 168, NULL),
(16659, 10, 9, 170, NULL),
(13843, 10, 9, 174, NULL),
(14216, 10, 9, 179, NULL),
(14902, 10, 9, 195, NULL),
(14911, 10, 9, 196, NULL),
(16728, 10, 9, 197, NULL),
(16713, 10, 9, 2046, NULL),
(16507, 10, 9, 2048, NULL),
(16818, 10, 9, 2052, NULL),
(16838, 10, 9, 2053, NULL),
(16828, 10, 9, 2054, NULL),
(16991, 10, 9, 2055, NULL),
(17011, 10, 9, 2056, NULL),
(13943, 10, 10, 3, '-mdp_client,mdp_absystech'),
(13944, 10, 10, 4, NULL),
(13942, 10, 10, 9, NULL),
(13950, 10, 10, 39, NULL),
(13946, 10, 10, 41, NULL),
(13949, 10, 10, 42, NULL),
(13948, 10, 10, 43, NULL),
(13951, 10, 10, 44, NULL),
(13947, 10, 10, 45, NULL),
(13945, 10, 10, 46, NULL),
(13939, 10, 10, 59, NULL),
(13941, 10, 10, 60, NULL),
(13935, 10, 10, 62, NULL),
(13934, 10, 10, 86, NULL),
(13864, 10, 10, 117, NULL),
(13824, 10, 10, 118, NULL),
(13834, 10, 10, 119, NULL),
(13854, 10, 10, 120, NULL),
(13804, 10, 10, 121, NULL),
(13814, 10, 10, 122, NULL),
(13794, 10, 10, 124, NULL),
(13770, 10, 10, 140, NULL),
(13930, 10, 10, 143, NULL),
(16747, 10, 10, 150, NULL),
(13938, 10, 10, 151, NULL),
(13940, 10, 10, 152, NULL),
(13844, 10, 10, 174, NULL),
(14215, 10, 10, 179, NULL),
(16817, 10, 10, 2052, NULL),
(16837, 10, 10, 2053, NULL),
(16827, 10, 10, 2054, NULL),
(17010, 10, 10, 2056, NULL),
(14441, 11, 1, 2, NULL),
(14462, 11, 1, 3, NULL),
(14452, 11, 1, 4, NULL),
(14482, 11, 1, 9, NULL),
(14415, 11, 1, 37, NULL),
(14416, 11, 1, 45, NULL),
(14427, 11, 1, 46, NULL),
(14483, 11, 1, 47, NULL),
(14487, 11, 1, 51, NULL),
(14484, 11, 1, 52, NULL),
(14503, 11, 1, 53, NULL),
(14508, 11, 1, 54, NULL),
(14631, 11, 1, 68, NULL),
(14428, 11, 1, 72, NULL),
(14494, 11, 1, 75, NULL),
(14493, 11, 1, 97, NULL),
(14498, 11, 1, 98, NULL),
(14495, 11, 1, 101, NULL),
(14496, 11, 1, 102, NULL),
(14472, 11, 1, 140, NULL),
(14497, 11, 1, 145, NULL),
(14512, 11, 1, 157, NULL),
(14486, 11, 1, 160, NULL),
(14461, 11, 2, 3, NULL),
(14451, 11, 2, 4, NULL),
(14481, 11, 2, 9, NULL),
(14426, 11, 2, 46, NULL),
(14488, 11, 2, 51, NULL),
(14485, 11, 2, 52, NULL),
(14505, 11, 2, 53, NULL),
(14471, 11, 2, 140, NULL),
(14460, 11, 3, 3, NULL),
(14450, 11, 3, 4, NULL),
(14480, 11, 3, 9, NULL),
(14425, 11, 3, 46, NULL),
(14489, 11, 3, 51, NULL),
(14507, 11, 3, 53, NULL),
(14470, 11, 3, 140, NULL),
(14459, 11, 4, 3, NULL),
(14449, 11, 4, 4, NULL),
(14479, 11, 4, 9, NULL),
(14469, 11, 4, 140, NULL),
(14458, 11, 5, 3, NULL),
(14448, 11, 5, 4, NULL),
(14478, 11, 5, 9, NULL),
(14423, 11, 5, 46, NULL),
(14490, 11, 5, 51, NULL),
(14510, 11, 5, 53, NULL),
(14457, 11, 6, 3, NULL),
(14447, 11, 6, 4, NULL),
(14477, 11, 6, 9, NULL),
(14422, 11, 6, 46, NULL),
(14491, 11, 6, 51, NULL),
(14509, 11, 6, 53, NULL),
(14456, 11, 7, 3, NULL),
(14446, 11, 7, 4, NULL),
(14476, 11, 7, 9, NULL),
(14421, 11, 7, 46, NULL),
(14492, 11, 7, 51, NULL),
(14511, 11, 7, 53, NULL),
(14453, 11, 10, 3, NULL),
(14862, 13, 1, 2, NULL),
(14860, 13, 1, 3, '-mdp_client,mdp_absystech'),
(14861, 13, 1, 4, NULL),
(14859, 13, 1, 9, NULL),
(14871, 13, 1, 37, NULL),
(14870, 13, 1, 38, NULL),
(14868, 13, 1, 39, NULL),
(14864, 13, 1, 41, NULL),
(14867, 13, 1, 42, NULL),
(14866, 13, 1, 43, NULL),
(14869, 13, 1, 44, NULL),
(14865, 13, 1, 45, NULL),
(14863, 13, 1, 46, NULL),
(14857, 13, 1, 48, NULL),
(14851, 13, 1, 50, NULL),
(14856, 13, 1, 60, NULL),
(14642, 13, 1, 68, NULL),
(14852, 13, 1, 72, NULL),
(14858, 13, 1, 111, NULL),
(14850, 13, 1, 141, NULL),
(14849, 13, 1, 143, NULL),
(14855, 13, 1, 152, NULL),
(14643, 13, 1, 167, NULL),
(14633, 13, 1, 195, NULL),
(14632, 13, 1, 196, NULL),
(14839, 13, 2, 2, NULL),
(14837, 13, 2, 3, '-mdp_client,mdp_absystech'),
(14838, 13, 2, 4, NULL),
(14836, 13, 2, 9, NULL),
(14848, 13, 2, 37, NULL),
(14847, 13, 2, 38, NULL),
(14845, 13, 2, 39, NULL),
(14841, 13, 2, 41, NULL),
(14844, 13, 2, 42, NULL),
(14843, 13, 2, 43, NULL),
(14846, 13, 2, 44, NULL),
(14842, 13, 2, 45, NULL),
(14840, 13, 2, 46, NULL),
(14834, 13, 2, 48, NULL),
(14828, 13, 2, 50, NULL),
(14833, 13, 2, 60, NULL),
(14829, 13, 2, 72, NULL),
(14835, 13, 2, 111, NULL),
(14827, 13, 2, 141, NULL),
(14826, 13, 2, 143, NULL),
(14832, 13, 2, 152, NULL),
(14634, 13, 2, 195, NULL),
(14817, 13, 3, 2, NULL),
(14815, 13, 3, 3, '-mdp_client,mdp_absystech'),
(14816, 13, 3, 4, NULL),
(14814, 13, 3, 9, NULL),
(14825, 13, 3, 37, NULL),
(14824, 13, 3, 38, NULL),
(14823, 13, 3, 39, NULL),
(14819, 13, 3, 41, NULL),
(14822, 13, 3, 42, NULL),
(14821, 13, 3, 43, NULL),
(14820, 13, 3, 45, NULL),
(14818, 13, 3, 46, NULL),
(14812, 13, 3, 48, NULL),
(14806, 13, 3, 50, NULL),
(14811, 13, 3, 60, NULL),
(14807, 13, 3, 72, NULL),
(14813, 13, 3, 111, NULL),
(14805, 13, 3, 141, NULL),
(14804, 13, 3, 143, NULL),
(14810, 13, 3, 152, NULL),
(14635, 13, 3, 195, NULL),
(14795, 13, 4, 2, NULL),
(14793, 13, 4, 3, '-mdp_client,mdp_absystech'),
(14794, 13, 4, 4, NULL),
(14792, 13, 4, 9, NULL),
(14803, 13, 4, 37, NULL),
(14802, 13, 4, 38, NULL),
(14801, 13, 4, 39, NULL),
(14797, 13, 4, 41, NULL),
(14800, 13, 4, 42, NULL),
(14799, 13, 4, 43, NULL),
(14798, 13, 4, 45, NULL),
(14790, 13, 4, 48, NULL),
(14784, 13, 4, 50, NULL),
(14789, 13, 4, 60, NULL),
(14785, 13, 4, 72, NULL),
(14791, 13, 4, 111, NULL),
(14783, 13, 4, 141, NULL),
(14782, 13, 4, 143, NULL),
(14788, 13, 4, 152, NULL),
(14636, 13, 4, 195, NULL),
(14772, 13, 5, 2, NULL),
(14770, 13, 5, 3, '-mdp_client,mdp_absystech'),
(14771, 13, 5, 4, NULL),
(14769, 13, 5, 9, NULL),
(14781, 13, 5, 37, NULL),
(14780, 13, 5, 38, NULL),
(14778, 13, 5, 39, NULL),
(14774, 13, 5, 41, NULL),
(14777, 13, 5, 42, NULL),
(14776, 13, 5, 43, NULL),
(14779, 13, 5, 44, NULL),
(14775, 13, 5, 45, NULL),
(14773, 13, 5, 46, NULL),
(14767, 13, 5, 48, NULL),
(14761, 13, 5, 50, NULL),
(14766, 13, 5, 60, NULL),
(14762, 13, 5, 72, NULL),
(14768, 13, 5, 111, NULL),
(14760, 13, 5, 141, NULL),
(14759, 13, 5, 143, NULL),
(14765, 13, 5, 152, NULL),
(14637, 13, 5, 195, NULL),
(14749, 13, 6, 2, NULL),
(14747, 13, 6, 3, '-mdp_client,mdp_absystech'),
(14748, 13, 6, 4, NULL),
(14746, 13, 6, 9, NULL),
(14758, 13, 6, 37, NULL),
(14757, 13, 6, 38, NULL),
(14755, 13, 6, 39, NULL),
(14751, 13, 6, 41, NULL),
(14754, 13, 6, 42, NULL),
(14753, 13, 6, 43, NULL),
(14756, 13, 6, 44, NULL),
(14752, 13, 6, 45, NULL),
(14750, 13, 6, 46, NULL),
(14744, 13, 6, 48, NULL),
(14738, 13, 6, 50, NULL),
(14743, 13, 6, 60, NULL),
(14739, 13, 6, 72, NULL),
(14745, 13, 6, 111, NULL),
(14737, 13, 6, 141, NULL),
(14736, 13, 6, 143, NULL),
(14742, 13, 6, 152, NULL),
(14638, 13, 6, 195, NULL),
(14726, 13, 7, 2, NULL),
(14724, 13, 7, 3, '-mdp_client,mdp_absystech'),
(14725, 13, 7, 4, NULL),
(14723, 13, 7, 9, NULL),
(14735, 13, 7, 37, NULL),
(14734, 13, 7, 38, NULL),
(14732, 13, 7, 39, NULL),
(14728, 13, 7, 41, NULL),
(14731, 13, 7, 42, NULL),
(14730, 13, 7, 43, NULL),
(14733, 13, 7, 44, NULL),
(14729, 13, 7, 45, NULL),
(14727, 13, 7, 46, NULL),
(14721, 13, 7, 48, NULL),
(14715, 13, 7, 50, NULL),
(14720, 13, 7, 60, NULL),
(14716, 13, 7, 72, NULL),
(14722, 13, 7, 111, NULL),
(14714, 13, 7, 141, NULL),
(14713, 13, 7, 143, NULL),
(14719, 13, 7, 152, NULL),
(14639, 13, 7, 195, NULL),
(14703, 13, 8, 2, NULL),
(14701, 13, 8, 3, '-mdp_client,mdp_absystech'),
(14702, 13, 8, 4, NULL),
(14700, 13, 8, 9, NULL),
(14712, 13, 8, 37, NULL),
(14711, 13, 8, 38, NULL),
(14709, 13, 8, 39, NULL),
(14705, 13, 8, 41, NULL),
(14708, 13, 8, 42, NULL),
(14707, 13, 8, 43, NULL),
(14710, 13, 8, 44, NULL),
(14706, 13, 8, 45, NULL),
(14704, 13, 8, 46, NULL),
(14698, 13, 8, 48, NULL),
(14692, 13, 8, 50, NULL),
(14697, 13, 8, 60, NULL),
(14693, 13, 8, 72, NULL),
(14699, 13, 8, 111, NULL),
(14691, 13, 8, 141, NULL),
(14690, 13, 8, 143, NULL),
(14696, 13, 8, 152, NULL),
(14640, 13, 8, 195, NULL),
(14680, 13, 9, 2, NULL),
(14678, 13, 9, 3, '-mdp_client,mdp_absystech'),
(14679, 13, 9, 4, NULL),
(14677, 13, 9, 9, NULL),
(14689, 13, 9, 37, NULL),
(14688, 13, 9, 38, NULL),
(14686, 13, 9, 39, NULL),
(14682, 13, 9, 41, NULL),
(14685, 13, 9, 42, NULL),
(14684, 13, 9, 43, NULL),
(14687, 13, 9, 44, NULL),
(14683, 13, 9, 45, NULL),
(14681, 13, 9, 46, NULL),
(14675, 13, 9, 48, NULL),
(14669, 13, 9, 50, NULL),
(14674, 13, 9, 60, NULL),
(14670, 13, 9, 72, NULL),
(14676, 13, 9, 111, NULL),
(14668, 13, 9, 141, NULL),
(14667, 13, 9, 143, NULL),
(14673, 13, 9, 152, NULL),
(14641, 13, 9, 195, NULL),
(14657, 13, 10, 2, NULL),
(14655, 13, 10, 3, '-mdp_client,mdp_absystech'),
(14656, 13, 10, 4, NULL),
(14654, 13, 10, 9, NULL),
(14666, 13, 10, 37, NULL),
(14665, 13, 10, 38, NULL),
(14663, 13, 10, 39, NULL),
(14659, 13, 10, 41, NULL),
(14662, 13, 10, 42, NULL),
(14661, 13, 10, 43, NULL),
(14664, 13, 10, 44, NULL),
(14660, 13, 10, 45, NULL),
(14658, 13, 10, 46, NULL),
(14652, 13, 10, 48, NULL),
(14646, 13, 10, 50, NULL),
(14651, 13, 10, 60, NULL),
(14647, 13, 10, 72, NULL),
(14653, 13, 10, 111, NULL),
(14645, 13, 10, 141, NULL),
(14644, 13, 10, 143, NULL),
(14650, 13, 10, 152, NULL),
(15161, 14, 1, 2, NULL),
(15467, 14, 1, 3, NULL),
(15457, 14, 1, 4, NULL),
(15477, 14, 1, 9, NULL),
(15151, 14, 1, 37, NULL),
(15347, 14, 1, 38, NULL),
(15367, 14, 1, 39, NULL),
(15417, 14, 1, 41, NULL),
(15387, 14, 1, 42, NULL),
(15397, 14, 1, 43, NULL),
(15357, 14, 1, 44, NULL),
(15407, 14, 1, 45, NULL),
(15487, 14, 1, 46, NULL),
(15517, 14, 1, 47, NULL),
(15527, 14, 1, 48, NULL),
(15628, 14, 1, 49, NULL),
(15245, 14, 1, 50, NULL),
(15193, 14, 1, 51, NULL),
(15507, 14, 1, 52, NULL),
(15171, 14, 1, 53, NULL),
(15181, 14, 1, 54, NULL),
(15182, 14, 1, 55, NULL),
(15225, 14, 1, 59, NULL),
(15223, 14, 1, 60, NULL),
(15244, 14, 1, 62, NULL),
(15285, 14, 1, 68, NULL),
(15888, 14, 1, 69, NULL),
(15286, 14, 1, 70, NULL),
(15878, 14, 1, 71, NULL),
(15427, 14, 1, 72, NULL),
(15924, 14, 1, 75, NULL),
(15317, 14, 1, 83, NULL),
(15327, 14, 1, 84, NULL),
(15578, 14, 1, 86, NULL),
(15848, 14, 1, 87, NULL),
(15568, 14, 1, 89, NULL),
(15598, 14, 1, 92, NULL),
(15588, 14, 1, 93, NULL),
(15195, 14, 1, 97, NULL),
(15198, 14, 1, 98, NULL),
(15934, 14, 1, 101, NULL),
(15944, 14, 1, 102, NULL),
(15497, 14, 1, 111, NULL),
(15788, 14, 1, 116, NULL),
(15778, 14, 1, 117, NULL),
(15708, 14, 1, 118, NULL),
(15728, 14, 1, 119, NULL),
(15748, 14, 1, 120, NULL),
(15718, 14, 1, 121, NULL),
(15688, 14, 1, 122, NULL),
(15768, 14, 1, 123, NULL),
(15698, 14, 1, 124, NULL),
(17423, 14, 1, 125, NULL),
(17433, 14, 1, 126, NULL),
(15914, 14, 1, 132, NULL),
(17453, 14, 1, 134, NULL),
(17443, 14, 1, 135, NULL),
(17463, 14, 1, 140, NULL),
(15274, 14, 1, 141, NULL),
(15808, 14, 1, 142, NULL),
(15798, 14, 1, 143, NULL),
(15828, 14, 1, 144, NULL),
(15275, 14, 1, 147, NULL),
(15818, 14, 1, 148, NULL),
(15838, 14, 1, 149, NULL),
(15213, 14, 1, 150, NULL),
(15547, 14, 1, 151, NULL),
(15537, 14, 1, 152, NULL),
(15302, 14, 1, 157, NULL),
(15183, 14, 1, 160, NULL),
(15264, 14, 1, 161, NULL),
(15608, 14, 1, 162, NULL),
(15618, 14, 1, 163, NULL),
(15377, 14, 1, 167, NULL),
(15965, 14, 1, 169, NULL),
(15858, 14, 1, 171, NULL),
(15898, 14, 1, 172, NULL),
(15738, 14, 1, 174, NULL),
(17023, 14, 1, 175, NULL),
(15901, 14, 1, 176, NULL),
(15868, 14, 1, 177, NULL),
(15296, 14, 1, 178, NULL),
(15758, 14, 1, 179, NULL),
(15337, 14, 1, 190, NULL),
(15447, 14, 1, 191, NULL),
(15437, 14, 1, 192, NULL),
(15548, 14, 1, 194, NULL),
(15226, 14, 1, 195, NULL),
(15558, 14, 1, 196, NULL),
(15194, 14, 1, 197, NULL),
(15224, 14, 1, 2044, NULL),
(15549, 14, 1, 2045, NULL),
(15954, 14, 1, 2046, NULL),
(16268, 14, 1, 2047, NULL),
(16597, 14, 1, 2048, NULL),
(16856, 14, 1, 2052, NULL),
(16876, 14, 1, 2053, NULL),
(16866, 14, 1, 2054, NULL),
(15160, 14, 2, 2, NULL),
(15466, 14, 2, 3, NULL),
(15456, 14, 2, 4, NULL),
(15476, 14, 2, 9, NULL),
(15150, 14, 2, 37, NULL),
(15346, 14, 2, 38, NULL),
(15366, 14, 2, 39, NULL),
(15416, 14, 2, 41, NULL),
(15386, 14, 2, 42, NULL),
(15396, 14, 2, 43, NULL),
(15356, 14, 2, 44, NULL),
(15406, 14, 2, 45, NULL),
(15486, 14, 2, 46, NULL),
(15516, 14, 2, 47, NULL),
(15526, 14, 2, 48, NULL),
(15627, 14, 2, 49, NULL),
(15246, 14, 2, 50, NULL),
(15192, 14, 2, 51, NULL),
(15506, 14, 2, 52, NULL),
(15170, 14, 2, 53, NULL),
(15180, 14, 2, 54, NULL),
(15222, 14, 2, 60, NULL),
(15243, 14, 2, 62, NULL),
(15284, 14, 2, 68, NULL),
(15887, 14, 2, 69, NULL),
(15287, 14, 2, 70, NULL),
(15877, 14, 2, 71, NULL),
(15426, 14, 2, 72, NULL),
(15923, 14, 2, 75, NULL),
(15316, 14, 2, 83, NULL),
(15326, 14, 2, 84, NULL),
(15577, 14, 2, 86, NULL),
(15847, 14, 2, 87, NULL),
(15567, 14, 2, 89, NULL),
(15597, 14, 2, 92, NULL),
(15587, 14, 2, 93, NULL),
(15196, 14, 2, 97, NULL),
(15199, 14, 2, 98, NULL),
(15933, 14, 2, 101, NULL),
(15943, 14, 2, 102, NULL),
(15496, 14, 2, 111, NULL),
(15787, 14, 2, 116, NULL),
(15777, 14, 2, 117, NULL),
(15707, 14, 2, 118, NULL),
(15727, 14, 2, 119, NULL),
(15747, 14, 2, 120, NULL),
(15717, 14, 2, 121, NULL),
(15687, 14, 2, 122, NULL),
(15767, 14, 2, 123, NULL),
(15697, 14, 2, 124, NULL),
(17422, 14, 2, 125, NULL),
(17432, 14, 2, 126, NULL),
(15913, 14, 2, 132, NULL),
(17452, 14, 2, 134, NULL),
(17442, 14, 2, 135, NULL),
(17462, 14, 2, 140, NULL),
(15273, 14, 2, 141, NULL),
(15807, 14, 2, 142, NULL),
(15797, 14, 2, 143, NULL),
(15827, 14, 2, 144, NULL),
(15817, 14, 2, 148, NULL),
(15837, 14, 2, 149, NULL),
(15212, 14, 2, 150, NULL),
(15546, 14, 2, 151, NULL),
(15536, 14, 2, 152, NULL),
(15303, 14, 2, 157, NULL),
(15263, 14, 2, 161, NULL),
(15607, 14, 2, 162, NULL),
(15617, 14, 2, 163, NULL),
(15376, 14, 2, 167, NULL),
(15964, 14, 2, 169, NULL),
(15857, 14, 2, 171, NULL),
(15897, 14, 2, 172, NULL),
(15737, 14, 2, 174, NULL),
(15902, 14, 2, 176, NULL),
(15867, 14, 2, 177, NULL),
(15297, 14, 2, 178, NULL),
(15757, 14, 2, 179, NULL),
(15336, 14, 2, 190, NULL),
(15446, 14, 2, 191, NULL),
(15436, 14, 2, 192, NULL),
(15227, 14, 2, 195, NULL),
(15557, 14, 2, 196, NULL),
(15953, 14, 2, 2046, NULL),
(16596, 14, 2, 2048, NULL),
(16855, 14, 2, 2052, NULL),
(16875, 14, 2, 2053, NULL),
(16865, 14, 2, 2054, NULL),
(15159, 14, 3, 2, NULL),
(15465, 14, 3, 3, NULL),
(15455, 14, 3, 4, NULL),
(15475, 14, 3, 9, NULL),
(15149, 14, 3, 37, NULL),
(15345, 14, 3, 38, NULL),
(15365, 14, 3, 39, NULL),
(15415, 14, 3, 41, NULL),
(15385, 14, 3, 42, NULL),
(15395, 14, 3, 43, NULL),
(15355, 14, 3, 44, NULL),
(15405, 14, 3, 45, NULL),
(15485, 14, 3, 46, NULL),
(15515, 14, 3, 47, NULL),
(15525, 14, 3, 48, NULL),
(15626, 14, 3, 49, NULL),
(15247, 14, 3, 50, NULL),
(15191, 14, 3, 51, NULL),
(15505, 14, 3, 52, NULL),
(15169, 14, 3, 53, NULL),
(15179, 14, 3, 54, NULL),
(15221, 14, 3, 60, NULL),
(15242, 14, 3, 62, NULL),
(15283, 14, 3, 68, NULL),
(15886, 14, 3, 69, NULL),
(15288, 14, 3, 70, NULL),
(15876, 14, 3, 71, NULL),
(15425, 14, 3, 72, NULL),
(15922, 14, 3, 75, NULL),
(15315, 14, 3, 83, NULL),
(15325, 14, 3, 84, NULL),
(15576, 14, 3, 86, NULL),
(15846, 14, 3, 87, NULL),
(15566, 14, 3, 89, NULL),
(15596, 14, 3, 92, NULL),
(15586, 14, 3, 93, NULL),
(15197, 14, 3, 97, NULL),
(15200, 14, 3, 98, NULL),
(15932, 14, 3, 101, NULL),
(15942, 14, 3, 102, NULL),
(15495, 14, 3, 111, NULL),
(15786, 14, 3, 116, NULL),
(15776, 14, 3, 117, NULL),
(15706, 14, 3, 118, NULL),
(15726, 14, 3, 119, NULL),
(15746, 14, 3, 120, NULL),
(15716, 14, 3, 121, NULL),
(15686, 14, 3, 122, NULL),
(15766, 14, 3, 123, NULL),
(15696, 14, 3, 124, NULL),
(17421, 14, 3, 125, NULL),
(17431, 14, 3, 126, NULL),
(15912, 14, 3, 132, NULL),
(17451, 14, 3, 134, NULL),
(17441, 14, 3, 135, NULL),
(17461, 14, 3, 140, NULL),
(15272, 14, 3, 141, NULL),
(15806, 14, 3, 142, NULL),
(15796, 14, 3, 143, NULL),
(15826, 14, 3, 144, NULL),
(15816, 14, 3, 148, NULL),
(15836, 14, 3, 149, NULL),
(15211, 14, 3, 150, NULL),
(15545, 14, 3, 151, NULL),
(15535, 14, 3, 152, NULL),
(15304, 14, 3, 157, NULL),
(15262, 14, 3, 161, NULL),
(15606, 14, 3, 162, NULL),
(15616, 14, 3, 163, NULL),
(15375, 14, 3, 167, NULL),
(15963, 14, 3, 169, NULL),
(15856, 14, 3, 171, NULL),
(15896, 14, 3, 172, NULL),
(15736, 14, 3, 174, NULL),
(15903, 14, 3, 176, NULL),
(15866, 14, 3, 177, NULL),
(15298, 14, 3, 178, NULL),
(15756, 14, 3, 179, NULL),
(15335, 14, 3, 190, NULL),
(15445, 14, 3, 191, NULL),
(15435, 14, 3, 192, NULL),
(15228, 14, 3, 195, NULL),
(15556, 14, 3, 196, NULL),
(15952, 14, 3, 2046, NULL),
(16595, 14, 3, 2048, NULL),
(16854, 14, 3, 2052, NULL),
(16874, 14, 3, 2053, NULL),
(16864, 14, 3, 2054, NULL),
(15158, 14, 4, 2, NULL),
(15464, 14, 4, 3, NULL),
(15454, 14, 4, 4, NULL),
(17464, 14, 4, 9, NULL),
(15148, 14, 4, 37, NULL),
(15344, 14, 4, 38, NULL),
(15364, 14, 4, 39, NULL),
(15414, 14, 4, 41, NULL),
(15384, 14, 4, 42, NULL),
(15394, 14, 4, 43, NULL),
(15354, 14, 4, 44, NULL),
(15404, 14, 4, 45, NULL),
(15514, 14, 4, 47, NULL),
(15524, 14, 4, 48, NULL),
(15625, 14, 4, 49, NULL),
(15249, 14, 4, 50, NULL),
(15190, 14, 4, 51, NULL),
(15504, 14, 4, 52, NULL),
(15220, 14, 4, 60, NULL),
(15241, 14, 4, 62, NULL),
(15282, 14, 4, 68, NULL),
(15885, 14, 4, 69, NULL),
(15289, 14, 4, 70, NULL),
(15875, 14, 4, 71, NULL),
(15424, 14, 4, 72, NULL),
(15921, 14, 4, 75, NULL),
(15314, 14, 4, 83, NULL),
(15324, 14, 4, 84, NULL),
(15575, 14, 4, 86, NULL),
(15845, 14, 4, 87, NULL),
(15565, 14, 4, 89, NULL),
(15595, 14, 4, 92, NULL),
(15585, 14, 4, 93, NULL),
(15931, 14, 4, 101, NULL),
(15941, 14, 4, 102, NULL),
(15494, 14, 4, 111, NULL),
(15785, 14, 4, 116, NULL),
(15775, 14, 4, 117, NULL),
(15705, 14, 4, 118, NULL),
(15725, 14, 4, 119, NULL),
(15745, 14, 4, 120, NULL),
(15715, 14, 4, 121, NULL),
(15685, 14, 4, 122, NULL),
(15765, 14, 4, 123, NULL),
(15695, 14, 4, 124, NULL),
(17420, 14, 4, 125, NULL),
(17430, 14, 4, 126, NULL),
(15911, 14, 4, 132, NULL),
(17450, 14, 4, 134, NULL),
(17440, 14, 4, 135, NULL),
(17460, 14, 4, 140, NULL),
(15271, 14, 4, 141, NULL),
(15805, 14, 4, 142, NULL),
(15795, 14, 4, 143, NULL),
(15825, 14, 4, 144, NULL),
(15815, 14, 4, 148, NULL),
(15835, 14, 4, 149, NULL),
(15544, 14, 4, 151, NULL),
(15534, 14, 4, 152, NULL),
(15261, 14, 4, 161, NULL),
(15605, 14, 4, 162, NULL),
(15615, 14, 4, 163, NULL),
(15374, 14, 4, 167, NULL),
(15855, 14, 4, 171, NULL),
(15895, 14, 4, 172, NULL),
(15735, 14, 4, 174, NULL),
(15904, 14, 4, 176, NULL),
(15865, 14, 4, 177, NULL),
(15299, 14, 4, 178, NULL),
(15755, 14, 4, 179, NULL),
(15334, 14, 4, 190, NULL),
(15444, 14, 4, 191, NULL),
(15434, 14, 4, 192, NULL),
(15229, 14, 4, 195, NULL),
(15555, 14, 4, 196, NULL),
(15951, 14, 4, 2046, NULL),
(16265, 14, 4, 2047, NULL),
(16853, 14, 4, 2052, NULL),
(16873, 14, 4, 2053, NULL),
(16863, 14, 4, 2054, NULL),
(15157, 14, 5, 2, NULL),
(15463, 14, 5, 3, NULL),
(15453, 14, 5, 4, NULL),
(15473, 14, 5, 9, NULL),
(15147, 14, 5, 37, NULL),
(15343, 14, 5, 38, NULL),
(15363, 14, 5, 39, NULL),
(15413, 14, 5, 41, NULL),
(15383, 14, 5, 42, NULL),
(15393, 14, 5, 43, NULL),
(15353, 14, 5, 44, NULL),
(15403, 14, 5, 45, NULL),
(15483, 14, 5, 46, NULL),
(15513, 14, 5, 47, NULL),
(15523, 14, 5, 48, NULL),
(15624, 14, 5, 49, NULL),
(15248, 14, 5, 50, NULL),
(15189, 14, 5, 51, NULL),
(15503, 14, 5, 52, NULL),
(15167, 14, 5, 53, NULL),
(15177, 14, 5, 54, NULL),
(15219, 14, 5, 60, NULL),
(15240, 14, 5, 62, NULL),
(15281, 14, 5, 68, NULL),
(15884, 14, 5, 69, NULL),
(15290, 14, 5, 70, NULL),
(15874, 14, 5, 71, NULL),
(15423, 14, 5, 72, NULL),
(15920, 14, 5, 75, NULL),
(15313, 14, 5, 83, NULL),
(15323, 14, 5, 84, NULL),
(15574, 14, 5, 86, NULL),
(15844, 14, 5, 87, NULL),
(15564, 14, 5, 89, NULL),
(15594, 14, 5, 92, NULL),
(15584, 14, 5, 93, NULL),
(15930, 14, 5, 101, NULL),
(15940, 14, 5, 102, NULL),
(15493, 14, 5, 111, NULL),
(15784, 14, 5, 116, NULL),
(15774, 14, 5, 117, NULL),
(15704, 14, 5, 118, NULL),
(15724, 14, 5, 119, NULL),
(15744, 14, 5, 120, NULL),
(15714, 14, 5, 121, NULL),
(15684, 14, 5, 122, NULL),
(15764, 14, 5, 123, NULL),
(15694, 14, 5, 124, NULL),
(17419, 14, 5, 125, NULL),
(17429, 14, 5, 126, NULL),
(15910, 14, 5, 132, NULL),
(17449, 14, 5, 134, NULL),
(17439, 14, 5, 135, NULL),
(17459, 14, 5, 140, NULL),
(15270, 14, 5, 141, NULL),
(15804, 14, 5, 142, NULL),
(15794, 14, 5, 143, NULL),
(15824, 14, 5, 144, NULL),
(15814, 14, 5, 148, NULL),
(15834, 14, 5, 149, NULL),
(15209, 14, 5, 150, NULL),
(15543, 14, 5, 151, NULL),
(15533, 14, 5, 152, NULL),
(15307, 14, 5, 157, NULL),
(15260, 14, 5, 161, NULL),
(15604, 14, 5, 162, NULL),
(15614, 14, 5, 163, NULL),
(15373, 14, 5, 167, NULL),
(15961, 14, 5, 169, NULL),
(15854, 14, 5, 171, NULL),
(15894, 14, 5, 172, NULL),
(15734, 14, 5, 174, NULL),
(17024, 14, 5, 175, NULL),
(15864, 14, 5, 177, NULL),
(15754, 14, 5, 179, NULL),
(15333, 14, 5, 190, NULL),
(15443, 14, 5, 191, NULL),
(15433, 14, 5, 192, NULL),
(15230, 14, 5, 195, NULL),
(15554, 14, 5, 196, NULL),
(15950, 14, 5, 2046, NULL),
(16264, 14, 5, 2047, NULL),
(16593, 14, 5, 2048, NULL),
(16852, 14, 5, 2052, NULL),
(16872, 14, 5, 2053, NULL),
(16862, 14, 5, 2054, NULL),
(15156, 14, 6, 2, NULL),
(15462, 14, 6, 3, NULL),
(15452, 14, 6, 4, NULL),
(15472, 14, 6, 9, NULL),
(15146, 14, 6, 37, NULL),
(15342, 14, 6, 38, NULL),
(15362, 14, 6, 39, NULL),
(15412, 14, 6, 41, NULL),
(15382, 14, 6, 42, NULL),
(15392, 14, 6, 43, NULL),
(15352, 14, 6, 44, NULL),
(15402, 14, 6, 45, NULL),
(15482, 14, 6, 46, NULL),
(15512, 14, 6, 47, NULL),
(15522, 14, 6, 48, NULL),
(15623, 14, 6, 49, NULL),
(15250, 14, 6, 50, NULL),
(15188, 14, 6, 51, NULL),
(15502, 14, 6, 52, NULL),
(15166, 14, 6, 53, NULL),
(15176, 14, 6, 54, NULL),
(15218, 14, 6, 60, NULL),
(15239, 14, 6, 62, NULL),
(15280, 14, 6, 68, NULL),
(15883, 14, 6, 69, NULL),
(15291, 14, 6, 70, NULL),
(15873, 14, 6, 71, NULL),
(15422, 14, 6, 72, NULL),
(15919, 14, 6, 75, NULL),
(15312, 14, 6, 83, NULL),
(15322, 14, 6, 84, NULL),
(15573, 14, 6, 86, NULL),
(15843, 14, 6, 87, NULL),
(15563, 14, 6, 89, NULL),
(15593, 14, 6, 92, NULL),
(15583, 14, 6, 93, NULL),
(15929, 14, 6, 101, NULL),
(15939, 14, 6, 102, NULL),
(15492, 14, 6, 111, NULL),
(15783, 14, 6, 116, NULL),
(15773, 14, 6, 117, NULL),
(15703, 14, 6, 118, NULL),
(15723, 14, 6, 119, NULL),
(15743, 14, 6, 120, NULL),
(15713, 14, 6, 121, NULL),
(15683, 14, 6, 122, NULL),
(15763, 14, 6, 123, NULL),
(15693, 14, 6, 124, NULL),
(17418, 14, 6, 125, NULL),
(17428, 14, 6, 126, NULL),
(15909, 14, 6, 132, NULL),
(17448, 14, 6, 134, NULL),
(17438, 14, 6, 135, NULL),
(17458, 14, 6, 140, NULL),
(15269, 14, 6, 141, NULL),
(15803, 14, 6, 142, NULL),
(15793, 14, 6, 143, NULL),
(15823, 14, 6, 144, NULL),
(15813, 14, 6, 148, NULL),
(15833, 14, 6, 149, NULL),
(15208, 14, 6, 150, NULL),
(15542, 14, 6, 151, NULL),
(15532, 14, 6, 152, NULL),
(15305, 14, 6, 157, NULL),
(15259, 14, 6, 161, NULL),
(15603, 14, 6, 162, NULL),
(15613, 14, 6, 163, NULL),
(15372, 14, 6, 167, NULL),
(15960, 14, 6, 169, NULL),
(15853, 14, 6, 171, NULL),
(15893, 14, 6, 172, NULL),
(15733, 14, 6, 174, NULL),
(17025, 14, 6, 175, NULL),
(15863, 14, 6, 177, NULL),
(15301, 14, 6, 178, NULL),
(15753, 14, 6, 179, NULL),
(15332, 14, 6, 190, NULL),
(15442, 14, 6, 191, NULL),
(15432, 14, 6, 192, NULL),
(15231, 14, 6, 195, NULL),
(15553, 14, 6, 196, NULL),
(15949, 14, 6, 2046, NULL),
(16269, 14, 6, 2047, NULL),
(16592, 14, 6, 2048, NULL),
(16851, 14, 6, 2052, NULL),
(16871, 14, 6, 2053, NULL),
(16861, 14, 6, 2054, NULL),
(15155, 14, 7, 2, NULL),
(15461, 14, 7, 3, NULL),
(15451, 14, 7, 4, NULL),
(15471, 14, 7, 9, NULL),
(15145, 14, 7, 37, NULL),
(15341, 14, 7, 38, NULL),
(15361, 14, 7, 39, NULL),
(15411, 14, 7, 41, NULL),
(15381, 14, 7, 42, NULL),
(15391, 14, 7, 43, NULL),
(15351, 14, 7, 44, NULL),
(15401, 14, 7, 45, NULL),
(15481, 14, 7, 46, NULL),
(15511, 14, 7, 47, NULL),
(15521, 14, 7, 48, NULL),
(15622, 14, 7, 49, NULL),
(15251, 14, 7, 50, NULL),
(15187, 14, 7, 51, NULL),
(15501, 14, 7, 52, NULL),
(15165, 14, 7, 53, NULL),
(15175, 14, 7, 54, NULL),
(15217, 14, 7, 60, NULL),
(15238, 14, 7, 62, NULL),
(15279, 14, 7, 68, NULL),
(15882, 14, 7, 69, NULL),
(15292, 14, 7, 70, NULL),
(15872, 14, 7, 71, NULL),
(15421, 14, 7, 72, NULL),
(15918, 14, 7, 75, NULL),
(15311, 14, 7, 83, NULL),
(15321, 14, 7, 84, NULL),
(15572, 14, 7, 86, NULL),
(15842, 14, 7, 87, NULL),
(15562, 14, 7, 89, NULL),
(15592, 14, 7, 92, NULL),
(15582, 14, 7, 93, NULL),
(15928, 14, 7, 101, NULL),
(15938, 14, 7, 102, NULL),
(15491, 14, 7, 111, NULL),
(15782, 14, 7, 116, NULL),
(15772, 14, 7, 117, NULL),
(15702, 14, 7, 118, NULL),
(15722, 14, 7, 119, NULL),
(15742, 14, 7, 120, NULL),
(15712, 14, 7, 121, NULL),
(15682, 14, 7, 122, NULL),
(15762, 14, 7, 123, NULL),
(15692, 14, 7, 124, NULL),
(17417, 14, 7, 125, NULL),
(17427, 14, 7, 126, NULL),
(15908, 14, 7, 132, NULL),
(17447, 14, 7, 134, NULL),
(17437, 14, 7, 135, NULL),
(17457, 14, 7, 140, NULL),
(15268, 14, 7, 141, NULL),
(15802, 14, 7, 142, NULL),
(15792, 14, 7, 143, NULL),
(15822, 14, 7, 144, NULL),
(15812, 14, 7, 148, NULL),
(15832, 14, 7, 149, NULL),
(15207, 14, 7, 150, NULL),
(15541, 14, 7, 151, NULL),
(15531, 14, 7, 152, NULL),
(15306, 14, 7, 157, NULL),
(15258, 14, 7, 161, NULL),
(15602, 14, 7, 162, NULL),
(15612, 14, 7, 163, NULL),
(15371, 14, 7, 167, NULL),
(15959, 14, 7, 169, NULL),
(15852, 14, 7, 171, NULL),
(15892, 14, 7, 172, NULL),
(15732, 14, 7, 174, NULL),
(17026, 14, 7, 175, NULL),
(15862, 14, 7, 177, NULL),
(15300, 14, 7, 178, NULL),
(15752, 14, 7, 179, NULL),
(15331, 14, 7, 190, NULL),
(15441, 14, 7, 191, NULL),
(15431, 14, 7, 192, NULL),
(15232, 14, 7, 195, NULL),
(15552, 14, 7, 196, NULL),
(15948, 14, 7, 2046, NULL),
(16262, 14, 7, 2047, NULL),
(16591, 14, 7, 2048, NULL),
(16850, 14, 7, 2052, NULL),
(16870, 14, 7, 2053, NULL),
(16860, 14, 7, 2054, NULL),
(15154, 14, 8, 2, NULL),
(15460, 14, 8, 3, NULL),
(15450, 14, 8, 4, NULL),
(15470, 14, 8, 9, NULL),
(15144, 14, 8, 37, NULL),
(15340, 14, 8, 38, NULL),
(15360, 14, 8, 39, NULL),
(15410, 14, 8, 41, NULL),
(15380, 14, 8, 42, NULL),
(15390, 14, 8, 43, NULL),
(15350, 14, 8, 44, NULL),
(15400, 14, 8, 45, NULL),
(15480, 14, 8, 46, NULL),
(15510, 14, 8, 47, NULL),
(15520, 14, 8, 48, NULL),
(15621, 14, 8, 49, NULL),
(15252, 14, 8, 50, NULL),
(15186, 14, 8, 51, NULL),
(15500, 14, 8, 52, NULL),
(15164, 14, 8, 53, NULL),
(15174, 14, 8, 54, NULL),
(15216, 14, 8, 60, NULL),
(15237, 14, 8, 62, NULL),
(15278, 14, 8, 68, NULL),
(15881, 14, 8, 69, NULL),
(15293, 14, 8, 70, NULL),
(15871, 14, 8, 71, NULL),
(15420, 14, 8, 72, NULL),
(15917, 14, 8, 75, NULL),
(15310, 14, 8, 83, NULL),
(15320, 14, 8, 84, NULL),
(15571, 14, 8, 86, NULL),
(15841, 14, 8, 87, NULL),
(15561, 14, 8, 89, NULL),
(15591, 14, 8, 92, NULL),
(15581, 14, 8, 93, NULL),
(15927, 14, 8, 101, NULL),
(15937, 14, 8, 102, NULL),
(15490, 14, 8, 111, NULL),
(15781, 14, 8, 116, NULL),
(15771, 14, 8, 117, NULL),
(15701, 14, 8, 118, NULL),
(15721, 14, 8, 119, NULL),
(15741, 14, 8, 120, NULL),
(15711, 14, 8, 121, NULL),
(15681, 14, 8, 122, NULL),
(15761, 14, 8, 123, NULL),
(15691, 14, 8, 124, NULL),
(17416, 14, 8, 125, NULL),
(17426, 14, 8, 126, NULL),
(15907, 14, 8, 132, NULL),
(17446, 14, 8, 134, NULL),
(17436, 14, 8, 135, NULL),
(17456, 14, 8, 140, NULL),
(15267, 14, 8, 141, NULL),
(15801, 14, 8, 142, NULL),
(15791, 14, 8, 143, NULL),
(15821, 14, 8, 144, NULL),
(15811, 14, 8, 148, NULL),
(15831, 14, 8, 149, NULL),
(15206, 14, 8, 150, NULL),
(15540, 14, 8, 151, NULL),
(15530, 14, 8, 152, NULL),
(15257, 14, 8, 161, NULL),
(15601, 14, 8, 162, NULL),
(15611, 14, 8, 163, NULL),
(15370, 14, 8, 167, NULL),
(15958, 14, 8, 169, NULL),
(15851, 14, 8, 171, NULL),
(15891, 14, 8, 172, NULL),
(15731, 14, 8, 174, NULL),
(17027, 14, 8, 175, NULL),
(15861, 14, 8, 177, NULL),
(15751, 14, 8, 179, NULL),
(15330, 14, 8, 190, NULL),
(15440, 14, 8, 191, NULL),
(15430, 14, 8, 192, NULL),
(15233, 14, 8, 195, NULL),
(15551, 14, 8, 196, NULL),
(15947, 14, 8, 2046, NULL),
(16590, 14, 8, 2048, NULL),
(15153, 14, 9, 2, NULL),
(15459, 14, 9, 3, NULL),
(15449, 14, 9, 4, NULL),
(15469, 14, 9, 9, NULL),
(15143, 14, 9, 37, NULL),
(15339, 14, 9, 38, NULL),
(15359, 14, 9, 39, NULL),
(15409, 14, 9, 41, NULL),
(15379, 14, 9, 42, NULL),
(15389, 14, 9, 43, NULL),
(15349, 14, 9, 44, NULL),
(15399, 14, 9, 45, NULL),
(15479, 14, 9, 46, NULL),
(15509, 14, 9, 47, NULL),
(15519, 14, 9, 48, NULL),
(15620, 14, 9, 49, NULL),
(15253, 14, 9, 50, NULL),
(15185, 14, 9, 51, NULL),
(15499, 14, 9, 52, NULL),
(15163, 14, 9, 53, NULL),
(15173, 14, 9, 54, NULL),
(15215, 14, 9, 60, NULL),
(15236, 14, 9, 62, NULL),
(15277, 14, 9, 68, NULL),
(15880, 14, 9, 69, NULL),
(15294, 14, 9, 70, NULL),
(15870, 14, 9, 71, NULL),
(15419, 14, 9, 72, NULL),
(15916, 14, 9, 75, NULL),
(15309, 14, 9, 83, NULL),
(15319, 14, 9, 84, NULL),
(15570, 14, 9, 86, NULL),
(15840, 14, 9, 87, NULL),
(15560, 14, 9, 89, NULL),
(15590, 14, 9, 92, NULL),
(15580, 14, 9, 93, NULL),
(15926, 14, 9, 101, NULL),
(15936, 14, 9, 102, NULL),
(15489, 14, 9, 111, NULL),
(15780, 14, 9, 116, NULL),
(15770, 14, 9, 117, NULL),
(15700, 14, 9, 118, NULL),
(15720, 14, 9, 119, NULL),
(15740, 14, 9, 120, NULL),
(15710, 14, 9, 121, NULL),
(15680, 14, 9, 122, NULL),
(15760, 14, 9, 123, NULL),
(15690, 14, 9, 124, NULL),
(17415, 14, 9, 125, NULL),
(17425, 14, 9, 126, NULL),
(15906, 14, 9, 132, NULL),
(17445, 14, 9, 134, NULL),
(17435, 14, 9, 135, NULL),
(17455, 14, 9, 140, NULL),
(15266, 14, 9, 141, NULL),
(15800, 14, 9, 142, NULL),
(15790, 14, 9, 143, NULL),
(15820, 14, 9, 144, NULL),
(15810, 14, 9, 148, NULL),
(15830, 14, 9, 149, NULL),
(15205, 14, 9, 150, NULL),
(15539, 14, 9, 151, NULL),
(15529, 14, 9, 152, NULL),
(15256, 14, 9, 161, NULL),
(15600, 14, 9, 162, NULL),
(15610, 14, 9, 163, NULL),
(15369, 14, 9, 167, NULL),
(15957, 14, 9, 169, NULL),
(15850, 14, 9, 171, NULL),
(15890, 14, 9, 172, NULL),
(15730, 14, 9, 174, NULL),
(17028, 14, 9, 175, NULL),
(15860, 14, 9, 177, NULL),
(15750, 14, 9, 179, NULL),
(15329, 14, 9, 190, NULL),
(15439, 14, 9, 191, NULL),
(15429, 14, 9, 192, NULL),
(15234, 14, 9, 195, NULL),
(15550, 14, 9, 196, NULL),
(15946, 14, 9, 2046, NULL),
(16589, 14, 9, 2048, NULL),
(16848, 14, 9, 2052, NULL),
(16868, 14, 9, 2053, NULL),
(16858, 14, 9, 2054, NULL),
(15152, 14, 10, 2, NULL),
(15458, 14, 10, 3, NULL),
(15448, 14, 10, 4, NULL),
(15468, 14, 10, 9, NULL),
(15142, 14, 10, 37, NULL),
(15338, 14, 10, 38, NULL),
(15358, 14, 10, 39, NULL),
(15408, 14, 10, 41, NULL),
(15378, 14, 10, 42, NULL),
(15388, 14, 10, 43, NULL),
(15348, 14, 10, 44, NULL),
(15398, 14, 10, 45, NULL),
(15478, 14, 10, 46, NULL),
(15508, 14, 10, 47, NULL),
(15518, 14, 10, 48, NULL),
(15619, 14, 10, 49, NULL),
(15254, 14, 10, 50, NULL),
(15184, 14, 10, 51, NULL),
(15498, 14, 10, 52, NULL),
(15162, 14, 10, 53, NULL),
(15172, 14, 10, 54, NULL),
(15214, 14, 10, 60, NULL),
(15235, 14, 10, 62, NULL),
(15276, 14, 10, 68, NULL),
(15879, 14, 10, 69, NULL),
(15295, 14, 10, 70, NULL),
(15869, 14, 10, 71, NULL),
(15418, 14, 10, 72, NULL),
(15915, 14, 10, 75, NULL),
(15308, 14, 10, 83, NULL),
(15318, 14, 10, 84, NULL),
(15569, 14, 10, 86, NULL),
(15839, 14, 10, 87, NULL),
(15559, 14, 10, 89, NULL),
(15589, 14, 10, 92, NULL),
(15579, 14, 10, 93, NULL),
(15925, 14, 10, 101, NULL),
(15935, 14, 10, 102, NULL),
(15488, 14, 10, 111, NULL),
(15779, 14, 10, 116, NULL),
(15769, 14, 10, 117, NULL),
(15699, 14, 10, 118, NULL),
(15719, 14, 10, 119, NULL),
(15739, 14, 10, 120, NULL),
(15709, 14, 10, 121, NULL),
(15679, 14, 10, 122, NULL),
(15759, 14, 10, 123, NULL),
(15689, 14, 10, 124, NULL),
(17414, 14, 10, 125, NULL),
(17424, 14, 10, 126, NULL),
(15905, 14, 10, 132, NULL),
(17444, 14, 10, 134, NULL),
(17434, 14, 10, 135, NULL),
(17454, 14, 10, 140, NULL),
(15265, 14, 10, 141, NULL),
(15799, 14, 10, 142, NULL),
(15789, 14, 10, 143, NULL),
(15819, 14, 10, 144, NULL),
(15809, 14, 10, 148, NULL),
(15829, 14, 10, 149, NULL),
(15204, 14, 10, 150, NULL),
(15538, 14, 10, 151, NULL),
(15528, 14, 10, 152, NULL),
(15255, 14, 10, 161, NULL),
(15599, 14, 10, 162, NULL),
(15609, 14, 10, 163, NULL),
(15368, 14, 10, 167, NULL),
(15956, 14, 10, 169, NULL),
(15849, 14, 10, 171, NULL),
(15889, 14, 10, 172, NULL),
(15729, 14, 10, 174, NULL),
(15859, 14, 10, 177, NULL),
(15749, 14, 10, 179, NULL),
(15328, 14, 10, 190, NULL),
(15438, 14, 10, 191, NULL),
(15428, 14, 10, 192, NULL),
(15945, 14, 10, 2046, NULL),
(16588, 14, 10, 2048, NULL),
(16847, 14, 10, 2052, NULL),
(16867, 14, 10, 2053, NULL),
(16857, 14, 10, 2054, NULL),
(16085, 15, 1, 2, NULL),
(16105, 15, 1, 3, NULL),
(16095, 15, 1, 4, NULL),
(16115, 15, 1, 9, NULL),
(15975, 15, 1, 37, NULL),
(15985, 15, 1, 38, NULL),
(16005, 15, 1, 39, NULL),
(16065, 15, 1, 41, NULL),
(16025, 15, 1, 42, NULL),
(16045, 15, 1, 43, NULL),
(15995, 15, 1, 44, NULL),
(16055, 15, 1, 45, NULL),
(16125, 15, 1, 46, NULL),
(16145, 15, 1, 47, NULL),
(16186, 15, 1, 48, NULL),
(17189, 15, 1, 49, NULL),
(16245, 15, 1, 50, NULL),
(16292, 15, 1, 51, NULL),
(16155, 15, 1, 52, NULL),
(16165, 15, 1, 53, NULL),
(16175, 15, 1, 54, NULL),
(16327, 15, 1, 55, NULL),
(16226, 15, 1, 59, NULL),
(16196, 15, 1, 60, NULL),
(16258, 15, 1, 68, NULL),
(16075, 15, 1, 72, NULL),
(16303, 15, 1, 75, NULL),
(16365, 15, 1, 82, NULL),
(16302, 15, 1, 97, NULL),
(16312, 15, 1, 98, NULL),
(16306, 15, 1, 101, NULL),
(16308, 15, 1, 102, NULL),
(16135, 15, 1, 111, NULL),
(17251, 15, 1, 116, NULL),
(17261, 15, 1, 117, NULL),
(17321, 15, 1, 118, NULL),
(17311, 15, 1, 119, NULL),
(17291, 15, 1, 120, NULL),
(17351, 15, 1, 121, NULL),
(17331, 15, 1, 122, NULL),
(17341, 15, 1, 124, NULL),
(17204, 15, 1, 125, NULL),
(17214, 15, 1, 126, NULL),
(17368, 15, 1, 132, NULL),
(17234, 15, 1, 134, NULL),
(17224, 15, 1, 135, NULL),
(16185, 15, 1, 139, NULL),
(17170, 15, 1, 140, NULL),
(17147, 15, 1, 141, NULL),
(17136, 15, 1, 143, NULL),
(16315, 15, 1, 145, NULL),
(17159, 15, 1, 150, NULL),
(16216, 15, 1, 151, NULL),
(16206, 15, 1, 152, NULL),
(16345, 15, 1, 153, NULL),
(17186, 15, 1, 154, NULL),
(17185, 15, 1, 155, NULL),
(17180, 15, 1, 156, NULL),
(16256, 15, 1, 157, NULL),
(16324, 15, 1, 160, NULL),
(16015, 15, 1, 167, NULL);
INSERT INTO `profil_privilege` (`id_profil_privilege`, `id_profil`, `id_privilege`, `id_module`, `field`) VALUES
(16404, 15, 1, 168, NULL),
(16394, 15, 1, 169, NULL),
(16355, 15, 1, 170, NULL),
(17301, 15, 1, 174, NULL),
(17169, 15, 1, 175, NULL),
(16454, 15, 1, 176, NULL),
(16255, 15, 1, 177, NULL),
(17281, 15, 1, 179, NULL),
(16035, 15, 1, 191, NULL),
(17055, 15, 1, 192, NULL),
(16384, 15, 1, 193, NULL),
(16235, 15, 1, 195, NULL),
(16244, 15, 1, 196, NULL),
(16321, 15, 1, 197, NULL),
(16320, 15, 1, 2046, NULL),
(16886, 15, 1, 2052, NULL),
(16906, 15, 1, 2053, NULL),
(16896, 15, 1, 2054, NULL),
(17020, 15, 1, 2056, NULL),
(16084, 15, 2, 2, NULL),
(16104, 15, 2, 3, NULL),
(16094, 15, 2, 4, NULL),
(16114, 15, 2, 9, NULL),
(15974, 15, 2, 37, NULL),
(15984, 15, 2, 38, NULL),
(16004, 15, 2, 39, NULL),
(16064, 15, 2, 41, NULL),
(16024, 15, 2, 42, NULL),
(16044, 15, 2, 43, NULL),
(15994, 15, 2, 44, NULL),
(16054, 15, 2, 45, NULL),
(16124, 15, 2, 46, NULL),
(16144, 15, 2, 47, NULL),
(16291, 15, 2, 51, NULL),
(16154, 15, 2, 52, NULL),
(16164, 15, 2, 53, NULL),
(16174, 15, 2, 54, NULL),
(16328, 15, 2, 55, NULL),
(16225, 15, 2, 59, NULL),
(16195, 15, 2, 60, NULL),
(16074, 15, 2, 72, NULL),
(16304, 15, 2, 75, NULL),
(16364, 15, 2, 82, NULL),
(16301, 15, 2, 97, NULL),
(16313, 15, 2, 98, NULL),
(16311, 15, 2, 101, NULL),
(16309, 15, 2, 102, NULL),
(16134, 15, 2, 111, NULL),
(17250, 15, 2, 116, NULL),
(17260, 15, 2, 117, NULL),
(17320, 15, 2, 118, NULL),
(17310, 15, 2, 119, NULL),
(17290, 15, 2, 120, NULL),
(17350, 15, 2, 121, NULL),
(17330, 15, 2, 122, NULL),
(17340, 15, 2, 124, NULL),
(17203, 15, 2, 125, NULL),
(17213, 15, 2, 126, NULL),
(17367, 15, 2, 132, NULL),
(17233, 15, 2, 134, NULL),
(17223, 15, 2, 135, NULL),
(16184, 15, 2, 139, NULL),
(17171, 15, 2, 140, NULL),
(17146, 15, 2, 141, NULL),
(17135, 15, 2, 143, NULL),
(16316, 15, 2, 145, NULL),
(16215, 15, 2, 151, NULL),
(16205, 15, 2, 152, NULL),
(16344, 15, 2, 153, NULL),
(17187, 15, 2, 154, NULL),
(17184, 15, 2, 155, NULL),
(17181, 15, 2, 156, NULL),
(16325, 15, 2, 160, NULL),
(16014, 15, 2, 167, NULL),
(16403, 15, 2, 168, NULL),
(16393, 15, 2, 169, NULL),
(16354, 15, 2, 170, NULL),
(17300, 15, 2, 174, NULL),
(17168, 15, 2, 175, NULL),
(16254, 15, 2, 177, NULL),
(17280, 15, 2, 179, NULL),
(16034, 15, 2, 191, NULL),
(16383, 15, 2, 193, NULL),
(16234, 15, 2, 195, NULL),
(16243, 15, 2, 196, NULL),
(16322, 15, 2, 197, NULL),
(16318, 15, 2, 2046, NULL),
(16885, 15, 2, 2052, NULL),
(16905, 15, 2, 2053, NULL),
(16895, 15, 2, 2054, NULL),
(17021, 15, 2, 2056, NULL),
(16083, 15, 3, 2, NULL),
(16103, 15, 3, 3, NULL),
(16093, 15, 3, 4, NULL),
(16113, 15, 3, 9, NULL),
(15973, 15, 3, 37, NULL),
(15983, 15, 3, 38, NULL),
(16003, 15, 3, 39, NULL),
(16063, 15, 3, 41, NULL),
(16023, 15, 3, 42, NULL),
(16043, 15, 3, 43, NULL),
(15993, 15, 3, 44, NULL),
(16053, 15, 3, 45, NULL),
(16123, 15, 3, 46, NULL),
(16143, 15, 3, 47, NULL),
(16290, 15, 3, 51, NULL),
(16153, 15, 3, 52, NULL),
(16163, 15, 3, 53, NULL),
(16173, 15, 3, 54, NULL),
(16329, 15, 3, 55, NULL),
(16224, 15, 3, 59, NULL),
(16194, 15, 3, 60, NULL),
(16073, 15, 3, 72, NULL),
(16305, 15, 3, 75, NULL),
(16363, 15, 3, 82, NULL),
(16300, 15, 3, 97, NULL),
(16314, 15, 3, 98, NULL),
(16307, 15, 3, 101, NULL),
(16310, 15, 3, 102, NULL),
(16133, 15, 3, 111, NULL),
(17249, 15, 3, 116, NULL),
(17259, 15, 3, 117, NULL),
(17319, 15, 3, 118, NULL),
(17309, 15, 3, 119, NULL),
(17289, 15, 3, 120, NULL),
(17349, 15, 3, 121, NULL),
(17329, 15, 3, 122, NULL),
(17339, 15, 3, 124, NULL),
(17202, 15, 3, 125, NULL),
(17212, 15, 3, 126, NULL),
(17366, 15, 3, 132, NULL),
(17232, 15, 3, 134, NULL),
(17222, 15, 3, 135, NULL),
(16183, 15, 3, 139, NULL),
(17172, 15, 3, 140, NULL),
(17145, 15, 3, 141, NULL),
(17134, 15, 3, 143, NULL),
(16317, 15, 3, 145, NULL),
(16214, 15, 3, 151, NULL),
(16204, 15, 3, 152, NULL),
(16343, 15, 3, 153, NULL),
(17188, 15, 3, 154, NULL),
(17183, 15, 3, 155, NULL),
(17182, 15, 3, 156, NULL),
(16257, 15, 3, 157, NULL),
(16326, 15, 3, 160, NULL),
(16013, 15, 3, 167, NULL),
(16402, 15, 3, 168, NULL),
(16392, 15, 3, 169, NULL),
(16353, 15, 3, 170, NULL),
(17299, 15, 3, 174, NULL),
(17167, 15, 3, 175, NULL),
(16253, 15, 3, 177, NULL),
(17279, 15, 3, 179, NULL),
(16033, 15, 3, 191, NULL),
(16382, 15, 3, 193, NULL),
(16233, 15, 3, 195, NULL),
(16242, 15, 3, 196, NULL),
(16323, 15, 3, 197, NULL),
(16319, 15, 3, 2046, NULL),
(16884, 15, 3, 2052, NULL),
(16904, 15, 3, 2053, NULL),
(16894, 15, 3, 2054, NULL),
(17022, 15, 3, 2056, NULL),
(16082, 15, 4, 2, NULL),
(16102, 15, 4, 3, NULL),
(16092, 15, 4, 4, NULL),
(15972, 15, 4, 37, NULL),
(15982, 15, 4, 38, NULL),
(16002, 15, 4, 39, NULL),
(16062, 15, 4, 41, NULL),
(16022, 15, 4, 42, NULL),
(16042, 15, 4, 43, NULL),
(15992, 15, 4, 44, NULL),
(16052, 15, 4, 45, NULL),
(16142, 15, 4, 47, NULL),
(16289, 15, 4, 51, NULL),
(16152, 15, 4, 52, NULL),
(16162, 15, 4, 53, NULL),
(16172, 15, 4, 54, NULL),
(16223, 15, 4, 59, NULL),
(16193, 15, 4, 60, NULL),
(16072, 15, 4, 72, NULL),
(17241, 15, 4, 75, NULL),
(16132, 15, 4, 111, NULL),
(17248, 15, 4, 116, NULL),
(17258, 15, 4, 117, NULL),
(17308, 15, 4, 119, NULL),
(17288, 15, 4, 120, NULL),
(17348, 15, 4, 121, NULL),
(17201, 15, 4, 125, NULL),
(17211, 15, 4, 126, NULL),
(17231, 15, 4, 134, NULL),
(17221, 15, 4, 135, NULL),
(16182, 15, 4, 139, NULL),
(17173, 15, 4, 140, NULL),
(17144, 15, 4, 141, NULL),
(17133, 15, 4, 143, NULL),
(16213, 15, 4, 151, NULL),
(16203, 15, 4, 152, NULL),
(17191, 15, 4, 155, NULL),
(17190, 15, 4, 156, NULL),
(16012, 15, 4, 167, NULL),
(17298, 15, 4, 174, NULL),
(17166, 15, 4, 175, NULL),
(17278, 15, 4, 179, NULL),
(16032, 15, 4, 191, NULL),
(16232, 15, 4, 195, NULL),
(16241, 15, 4, 196, NULL),
(16883, 15, 4, 2052, NULL),
(16903, 15, 4, 2053, NULL),
(16893, 15, 4, 2054, NULL),
(16081, 15, 5, 2, NULL),
(16453, 15, 5, 3, NULL),
(16091, 15, 5, 4, NULL),
(16111, 15, 5, 9, NULL),
(15971, 15, 5, 37, NULL),
(15981, 15, 5, 38, NULL),
(16001, 15, 5, 39, NULL),
(16061, 15, 5, 41, NULL),
(16021, 15, 5, 42, NULL),
(16041, 15, 5, 43, NULL),
(15991, 15, 5, 44, NULL),
(16051, 15, 5, 45, NULL),
(16121, 15, 5, 46, NULL),
(16141, 15, 5, 47, NULL),
(16288, 15, 5, 51, NULL),
(16151, 15, 5, 52, NULL),
(16161, 15, 5, 53, NULL),
(16171, 15, 5, 54, NULL),
(16330, 15, 5, 55, NULL),
(16222, 15, 5, 59, NULL),
(16192, 15, 5, 60, NULL),
(16071, 15, 5, 72, NULL),
(17240, 15, 5, 75, NULL),
(16361, 15, 5, 82, NULL),
(16298, 15, 5, 97, NULL),
(16131, 15, 5, 111, NULL),
(17247, 15, 5, 116, NULL),
(17257, 15, 5, 117, NULL),
(17317, 15, 5, 118, NULL),
(17307, 15, 5, 119, NULL),
(17287, 15, 5, 120, NULL),
(17347, 15, 5, 121, NULL),
(17327, 15, 5, 122, NULL),
(17337, 15, 5, 124, NULL),
(17200, 15, 5, 125, NULL),
(17210, 15, 5, 126, NULL),
(17364, 15, 5, 132, NULL),
(17230, 15, 5, 134, NULL),
(17220, 15, 5, 135, NULL),
(16181, 15, 5, 139, NULL),
(17174, 15, 5, 140, NULL),
(17143, 15, 5, 141, NULL),
(17132, 15, 5, 143, NULL),
(17154, 15, 5, 150, NULL),
(16212, 15, 5, 151, NULL),
(16202, 15, 5, 152, NULL),
(16341, 15, 5, 153, NULL),
(16011, 15, 5, 167, NULL),
(16400, 15, 5, 168, NULL),
(16390, 15, 5, 169, NULL),
(16351, 15, 5, 170, NULL),
(17297, 15, 5, 174, NULL),
(17165, 15, 5, 175, NULL),
(17277, 15, 5, 179, NULL),
(16031, 15, 5, 191, NULL),
(16380, 15, 5, 193, NULL),
(16231, 15, 5, 195, NULL),
(16240, 15, 5, 196, NULL),
(16882, 15, 5, 2052, NULL),
(16902, 15, 5, 2053, NULL),
(16892, 15, 5, 2054, NULL),
(16080, 15, 6, 2, NULL),
(16100, 15, 6, 3, NULL),
(16090, 15, 6, 4, NULL),
(16110, 15, 6, 9, NULL),
(15970, 15, 6, 37, NULL),
(15980, 15, 6, 38, NULL),
(16000, 15, 6, 39, NULL),
(16060, 15, 6, 41, NULL),
(16020, 15, 6, 42, NULL),
(16040, 15, 6, 43, NULL),
(15990, 15, 6, 44, NULL),
(16050, 15, 6, 45, NULL),
(16120, 15, 6, 46, NULL),
(16140, 15, 6, 47, NULL),
(16287, 15, 6, 51, NULL),
(16150, 15, 6, 52, NULL),
(16160, 15, 6, 53, NULL),
(16170, 15, 6, 54, NULL),
(16331, 15, 6, 55, NULL),
(16221, 15, 6, 59, NULL),
(16191, 15, 6, 60, NULL),
(16070, 15, 6, 72, NULL),
(17239, 15, 6, 75, NULL),
(16360, 15, 6, 82, NULL),
(16297, 15, 6, 97, NULL),
(16130, 15, 6, 111, NULL),
(17246, 15, 6, 116, NULL),
(17256, 15, 6, 117, NULL),
(17316, 15, 6, 118, NULL),
(17306, 15, 6, 119, NULL),
(17286, 15, 6, 120, NULL),
(17346, 15, 6, 121, NULL),
(17326, 15, 6, 122, NULL),
(17336, 15, 6, 124, NULL),
(17199, 15, 6, 125, NULL),
(17209, 15, 6, 126, NULL),
(17363, 15, 6, 132, NULL),
(17229, 15, 6, 134, NULL),
(17219, 15, 6, 135, NULL),
(16180, 15, 6, 139, NULL),
(17142, 15, 6, 141, NULL),
(17131, 15, 6, 143, NULL),
(17153, 15, 6, 150, NULL),
(16211, 15, 6, 151, NULL),
(16201, 15, 6, 152, NULL),
(16340, 15, 6, 153, NULL),
(16010, 15, 6, 167, NULL),
(16399, 15, 6, 168, NULL),
(16389, 15, 6, 169, NULL),
(16350, 15, 6, 170, NULL),
(17296, 15, 6, 174, NULL),
(17164, 15, 6, 175, NULL),
(17276, 15, 6, 179, NULL),
(16030, 15, 6, 191, NULL),
(16379, 15, 6, 193, NULL),
(16230, 15, 6, 195, NULL),
(16239, 15, 6, 196, NULL),
(16881, 15, 6, 2052, NULL),
(16901, 15, 6, 2053, NULL),
(16891, 15, 6, 2054, NULL),
(16079, 15, 7, 2, NULL),
(16099, 15, 7, 3, NULL),
(16089, 15, 7, 4, NULL),
(16109, 15, 7, 9, NULL),
(15969, 15, 7, 37, NULL),
(15979, 15, 7, 38, NULL),
(15999, 15, 7, 39, NULL),
(16059, 15, 7, 41, NULL),
(16019, 15, 7, 42, NULL),
(16039, 15, 7, 43, NULL),
(15989, 15, 7, 44, NULL),
(16049, 15, 7, 45, NULL),
(16119, 15, 7, 46, NULL),
(16139, 15, 7, 47, NULL),
(16286, 15, 7, 51, NULL),
(16149, 15, 7, 52, NULL),
(16159, 15, 7, 53, NULL),
(16169, 15, 7, 54, NULL),
(16332, 15, 7, 55, NULL),
(16220, 15, 7, 59, NULL),
(16190, 15, 7, 60, NULL),
(16069, 15, 7, 72, NULL),
(17238, 15, 7, 75, NULL),
(16359, 15, 7, 82, NULL),
(16296, 15, 7, 97, NULL),
(16129, 15, 7, 111, NULL),
(17245, 15, 7, 116, NULL),
(17255, 15, 7, 117, NULL),
(17315, 15, 7, 118, NULL),
(17305, 15, 7, 119, NULL),
(17285, 15, 7, 120, NULL),
(17345, 15, 7, 121, NULL),
(17325, 15, 7, 122, NULL),
(17335, 15, 7, 124, NULL),
(17198, 15, 7, 125, NULL),
(17208, 15, 7, 126, NULL),
(17362, 15, 7, 132, NULL),
(17228, 15, 7, 134, NULL),
(17218, 15, 7, 135, NULL),
(16179, 15, 7, 139, NULL),
(17141, 15, 7, 141, NULL),
(17130, 15, 7, 143, NULL),
(17152, 15, 7, 150, NULL),
(16210, 15, 7, 151, NULL),
(16200, 15, 7, 152, NULL),
(16339, 15, 7, 153, NULL),
(16009, 15, 7, 167, NULL),
(16398, 15, 7, 168, NULL),
(16388, 15, 7, 169, NULL),
(16349, 15, 7, 170, NULL),
(17295, 15, 7, 174, NULL),
(17163, 15, 7, 175, NULL),
(17275, 15, 7, 179, NULL),
(16029, 15, 7, 191, NULL),
(16378, 15, 7, 193, NULL),
(16229, 15, 7, 195, NULL),
(16238, 15, 7, 196, NULL),
(16880, 15, 7, 2052, NULL),
(16900, 15, 7, 2053, NULL),
(16890, 15, 7, 2054, NULL),
(16078, 15, 8, 2, NULL),
(16098, 15, 8, 3, NULL),
(16088, 15, 8, 4, NULL),
(16108, 15, 8, 9, NULL),
(15968, 15, 8, 37, NULL),
(15978, 15, 8, 38, NULL),
(15998, 15, 8, 39, NULL),
(16058, 15, 8, 41, NULL),
(16018, 15, 8, 42, NULL),
(16038, 15, 8, 43, NULL),
(15988, 15, 8, 44, NULL),
(16048, 15, 8, 45, NULL),
(16118, 15, 8, 46, NULL),
(16138, 15, 8, 47, NULL),
(16285, 15, 8, 51, NULL),
(16148, 15, 8, 52, NULL),
(16158, 15, 8, 53, NULL),
(16168, 15, 8, 54, NULL),
(16333, 15, 8, 55, NULL),
(16219, 15, 8, 59, NULL),
(16189, 15, 8, 60, NULL),
(16068, 15, 8, 72, NULL),
(17237, 15, 8, 75, NULL),
(16358, 15, 8, 82, NULL),
(16295, 15, 8, 97, NULL),
(16128, 15, 8, 111, NULL),
(17244, 15, 8, 116, NULL),
(17254, 15, 8, 117, NULL),
(17314, 15, 8, 118, NULL),
(17304, 15, 8, 119, NULL),
(17284, 15, 8, 120, NULL),
(17344, 15, 8, 121, NULL),
(17324, 15, 8, 122, NULL),
(17334, 15, 8, 124, NULL),
(17197, 15, 8, 125, NULL),
(17207, 15, 8, 126, NULL),
(17361, 15, 8, 132, NULL),
(17227, 15, 8, 134, NULL),
(17217, 15, 8, 135, NULL),
(16178, 15, 8, 139, NULL),
(17140, 15, 8, 141, NULL),
(17129, 15, 8, 143, NULL),
(17151, 15, 8, 150, NULL),
(16209, 15, 8, 151, NULL),
(16199, 15, 8, 152, NULL),
(16338, 15, 8, 153, NULL),
(16008, 15, 8, 167, NULL),
(16397, 15, 8, 168, NULL),
(16387, 15, 8, 169, NULL),
(16348, 15, 8, 170, NULL),
(17294, 15, 8, 174, NULL),
(17162, 15, 8, 175, NULL),
(17274, 15, 8, 179, NULL),
(16028, 15, 8, 191, NULL),
(16377, 15, 8, 193, NULL),
(16228, 15, 8, 195, NULL),
(16237, 15, 8, 196, NULL),
(16077, 15, 9, 2, NULL),
(16097, 15, 9, 3, NULL),
(16087, 15, 9, 4, NULL),
(16107, 15, 9, 9, NULL),
(15967, 15, 9, 37, NULL),
(15977, 15, 9, 38, NULL),
(15997, 15, 9, 39, NULL),
(16057, 15, 9, 41, NULL),
(16017, 15, 9, 42, NULL),
(16037, 15, 9, 43, NULL),
(15987, 15, 9, 44, NULL),
(16047, 15, 9, 45, NULL),
(16117, 15, 9, 46, NULL),
(16137, 15, 9, 47, NULL),
(16284, 15, 9, 51, NULL),
(16147, 15, 9, 52, NULL),
(16157, 15, 9, 53, NULL),
(16167, 15, 9, 54, NULL),
(16334, 15, 9, 55, NULL),
(16218, 15, 9, 59, NULL),
(16188, 15, 9, 60, NULL),
(16067, 15, 9, 72, NULL),
(17236, 15, 9, 75, NULL),
(16357, 15, 9, 82, NULL),
(16294, 15, 9, 97, NULL),
(16127, 15, 9, 111, NULL),
(17243, 15, 9, 116, NULL),
(17253, 15, 9, 117, NULL),
(17313, 15, 9, 118, NULL),
(17303, 15, 9, 119, NULL),
(17283, 15, 9, 120, NULL),
(17343, 15, 9, 121, NULL),
(17323, 15, 9, 122, NULL),
(17333, 15, 9, 124, NULL),
(17196, 15, 9, 125, NULL),
(17206, 15, 9, 126, NULL),
(17360, 15, 9, 132, NULL),
(17226, 15, 9, 134, NULL),
(17216, 15, 9, 135, NULL),
(16177, 15, 9, 139, NULL),
(17139, 15, 9, 141, NULL),
(17128, 15, 9, 143, NULL),
(17150, 15, 9, 150, NULL),
(16208, 15, 9, 151, NULL),
(16198, 15, 9, 152, NULL),
(16337, 15, 9, 153, NULL),
(16007, 15, 9, 167, NULL),
(16396, 15, 9, 168, NULL),
(16386, 15, 9, 169, NULL),
(16347, 15, 9, 170, NULL),
(17293, 15, 9, 174, NULL),
(17161, 15, 9, 175, NULL),
(17273, 15, 9, 179, NULL),
(16027, 15, 9, 191, NULL),
(16376, 15, 9, 193, NULL),
(16227, 15, 9, 195, NULL),
(16236, 15, 9, 196, NULL),
(16878, 15, 9, 2052, NULL),
(16898, 15, 9, 2053, NULL),
(16888, 15, 9, 2054, NULL),
(16076, 15, 10, 2, NULL),
(16096, 15, 10, 3, NULL),
(16086, 15, 10, 4, NULL),
(16106, 15, 10, 9, NULL),
(15966, 15, 10, 37, NULL),
(15976, 15, 10, 38, NULL),
(15996, 15, 10, 39, NULL),
(16056, 15, 10, 41, NULL),
(16016, 15, 10, 42, NULL),
(16036, 15, 10, 43, NULL),
(15986, 15, 10, 44, NULL),
(16046, 15, 10, 45, NULL),
(16116, 15, 10, 46, NULL),
(16136, 15, 10, 47, NULL),
(16283, 15, 10, 51, NULL),
(16146, 15, 10, 52, NULL),
(16156, 15, 10, 53, NULL),
(16166, 15, 10, 54, NULL),
(16335, 15, 10, 55, NULL),
(16217, 15, 10, 59, NULL),
(16187, 15, 10, 60, NULL),
(16066, 15, 10, 72, NULL),
(17235, 15, 10, 75, NULL),
(16356, 15, 10, 82, NULL),
(16293, 15, 10, 97, NULL),
(16126, 15, 10, 111, NULL),
(17242, 15, 10, 116, NULL),
(17252, 15, 10, 117, NULL),
(17312, 15, 10, 118, NULL),
(17302, 15, 10, 119, NULL),
(17282, 15, 10, 120, NULL),
(17342, 15, 10, 121, NULL),
(17322, 15, 10, 122, NULL),
(17332, 15, 10, 124, NULL),
(17195, 15, 10, 125, NULL),
(17205, 15, 10, 126, NULL),
(17359, 15, 10, 132, NULL),
(17225, 15, 10, 134, NULL),
(17215, 15, 10, 135, NULL),
(16176, 15, 10, 139, NULL),
(17138, 15, 10, 141, NULL),
(17127, 15, 10, 143, NULL),
(17149, 15, 10, 150, NULL),
(16207, 15, 10, 151, NULL),
(16197, 15, 10, 152, NULL),
(16336, 15, 10, 153, NULL),
(16006, 15, 10, 167, NULL),
(16395, 15, 10, 168, NULL),
(16385, 15, 10, 169, NULL),
(16346, 15, 10, 170, NULL),
(17292, 15, 10, 174, NULL),
(17160, 15, 10, 175, NULL),
(17272, 15, 10, 179, NULL),
(16026, 15, 10, 191, NULL),
(16877, 15, 10, 2052, NULL),
(16897, 15, 10, 2053, NULL),
(16887, 15, 10, 2054, NULL),
(17393, 16, 1, 48, NULL),
(17392, 16, 1, 60, NULL),
(17403, 16, 1, 152, NULL),
(17391, 16, 2, 60, NULL),
(17402, 16, 2, 152, NULL),
(17390, 16, 3, 60, NULL),
(17401, 16, 3, 152, NULL),
(17400, 16, 4, 152, NULL),
(17388, 16, 5, 60, NULL),
(17399, 16, 5, 152, NULL),
(17387, 16, 6, 60, NULL),
(17398, 16, 6, 152, NULL),
(17386, 16, 7, 60, NULL),
(17397, 16, 7, 152, NULL),
(17385, 16, 8, 60, NULL),
(17396, 16, 8, 152, NULL),
(17384, 16, 9, 60, NULL),
(17395, 16, 9, 152, NULL),
(17383, 16, 10, 60, NULL),
(17394, 16, 10, 152, NULL),
(17495, 17, 1, 2, NULL),
(17506, 17, 1, 3, NULL),
(17505, 17, 1, 4, NULL),
(17494, 17, 2, 2, NULL),
(17493, 17, 3, 2, NULL),
(17492, 17, 4, 2, NULL),
(17491, 17, 5, 2, NULL),
(17507, 17, 5, 3, NULL),
(17501, 17, 5, 4, NULL),
(17490, 17, 6, 2, NULL),
(17508, 17, 6, 3, NULL),
(17500, 17, 6, 4, NULL),
(17489, 17, 7, 2, NULL),
(17509, 17, 7, 3, NULL),
(17499, 17, 7, 4, NULL),
(17488, 17, 8, 2, NULL),
(17498, 17, 8, 4, NULL),
(17487, 17, 9, 2, NULL),
(17497, 17, 9, 4, NULL),
(17486, 17, 10, 2, NULL),
(17512, 17, 10, 3, NULL),
(17496, 17, 10, 4, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `region`
--

DROP TABLE IF EXISTS `region`;
CREATE TABLE IF NOT EXISTS `region` (
  `id_region` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `region` varchar(64) NOT NULL,
  PRIMARY KEY (`id_region`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `region`
--

INSERT INTO `region` (`id_region`, `region`) VALUES
(1, 'Rhône-Alpes'),
(2, 'Picardie'),
(3, 'Auvergne'),
(4, 'Provence-Alpes-Côte d\'Azur'),
(5, 'Champagne-Ardenne'),
(6, 'Midi-Pyrénées'),
(7, 'Languedoc-Roussillon'),
(8, 'Basse-Normandie'),
(9, 'Poitou-Charentes'),
(10, 'Centre'),
(11, 'Limousin'),
(12, 'Corse'),
(13, 'Bourgogne'),
(14, 'Bretagne'),
(15, 'Aquitaine'),
(16, 'Franche-Comté'),
(17, 'Haute-Normandie'),
(18, 'Pays de la Loire'),
(19, 'Lorraine'),
(20, 'Nord-Pas-de-Calais'),
(21, 'Alsace'),
(22, 'Ile-de-France');

-- --------------------------------------------------------

--
-- Structure de la table `registrar`
--

DROP TABLE IF EXISTS `registrar`;
CREATE TABLE IF NOT EXISTS `registrar` (
  `id_registrar` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `registrar` varchar(255) NOT NULL,
  PRIMARY KEY (`id_registrar`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `reglement`
--

DROP TABLE IF EXISTS `reglement`;
CREATE TABLE IF NOT EXISTS `reglement` (
  `id_reglement` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reglement` text NOT NULL,
  PRIMARY KEY (`id_reglement`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `relance`
--

DROP TABLE IF EXISTS `relance`;
CREATE TABLE IF NOT EXISTS `relance` (
  `id_relance` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `date_1` date DEFAULT NULL,
  `date_2` date DEFAULT NULL,
  `date_demeurre` date DEFAULT NULL,
  `date_injonction` date DEFAULT NULL,
  PRIMARY KEY (`id_relance`),
  KEY `id_facture` (`id_facture`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `scanner`
--

DROP TABLE IF EXISTS `scanner`;
CREATE TABLE IF NOT EXISTS `scanner` (
  `id_scanner` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `nbpages` int(11) DEFAULT NULL,
  `provenance` varchar(255) NOT NULL,
  `transfert` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_scanner`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `secteur_commercial`
--

DROP TABLE IF EXISTS `secteur_commercial`;
CREATE TABLE IF NOT EXISTS `secteur_commercial` (
  `id_secteur_commercial` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `secteur_commercial` varchar(64) NOT NULL,
  PRIMARY KEY (`id_secteur_commercial`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `secteur_commercial`
--

INSERT INTO `secteur_commercial` (`id_secteur_commercial`, `secteur_commercial`) VALUES
(1, 'Commerce');

-- --------------------------------------------------------

--
-- Structure de la table `secteur_geographique`
--

DROP TABLE IF EXISTS `secteur_geographique`;
CREATE TABLE IF NOT EXISTS `secteur_geographique` (
  `id_secteur_geographique` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `secteur_geographique` varchar(64) NOT NULL,
  PRIMARY KEY (`id_secteur_geographique`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `secteur_geographique`
--

INSERT INTO `secteur_geographique` (`id_secteur_geographique`, `secteur_geographique`) VALUES
(1, 'Europe');

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id_settings` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module` varchar(100) NOT NULL,
  `id` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `mail_to` varchar(255) DEFAULT NULL,
  `mail_content` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_settings`),
  KEY `id_module` (`module`),
  KEY `id` (`id`),
  KEY `id_societe` (`id_societe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `societe`
--

DROP TABLE IF EXISTS `societe`;
CREATE TABLE IF NOT EXISTS `societe` (
  `id_societe` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `id_commercial` mediumint(8) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_societe`),
  UNIQUE KEY `societe` (`societe`),
  UNIQUE KEY `ref` (`ref`),
  UNIQUE KEY `cle_externe` (`cle_externe`),
  UNIQUE KEY `siret` (`siret`),
  UNIQUE KEY `ref_comptable_2` (`ref_comptable`),
  KEY `id_famille` (`id_famille`),
  KEY `id_owner` (`id_owner`),
  KEY `id_filiale` (`id_filiale`),
  KEY `id_secteur_geographique` (`id_secteur_geographique`),
  KEY `id_secteur_commercial` (`id_secteur_commercial`),
  KEY `effectif` (`effectif`),
  KEY `id_termes` (`id_termes`),
  KEY `id_contact_facturation` (`id_contact_facturation`),
  KEY `tel` (`tel`),
  KEY `recallCounter` (`recallCounter`),
  KEY `id_apporteur` (`id_apporteur_affaire`),
  KEY `id_commercial` (`id_commercial`),
  KEY `ref_comptable` (`ref_comptable`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `societe_CA`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `societe_CA`;
CREATE TABLE IF NOT EXISTS `societe_CA` (
`CA` decimal(32,2)
,`dateMin` date
,`dateMax` date
,`nbAnnees` int(6)
,`moyenneCAParAn` decimal(36,6)
,`anneesActives` mediumtext
,`nbAnneesActives` bigint(21)
,`moyenneCAParAnActives` decimal(36,6)
,`societe` varchar(128)
,`id_societe` mediumint(8) unsigned
);

-- --------------------------------------------------------

--
-- Structure de la table `societe_domaine`
--

DROP TABLE IF EXISTS `societe_domaine`;
CREATE TABLE IF NOT EXISTS `societe_domaine` (
  `id_societe_domaine` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_domaine` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_societe_domaine`),
  UNIQUE KEY `UNIQUE` (`id_societe`,`id_domaine`),
  KEY `id_domaine` (`id_domaine`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `societe_frais_port`
--

DROP TABLE IF EXISTS `societe_frais_port`;
CREATE TABLE IF NOT EXISTS `societe_frais_port` (
  `id_societe_frais_port` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `borne1` decimal(8,2) DEFAULT NULL,
  `borne2` decimal(8,2) DEFAULT NULL,
  `prix` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id_societe_frais_port`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `sous_categorie`
--

DROP TABLE IF EXISTS `sous_categorie`;
CREATE TABLE IF NOT EXISTS `sous_categorie` (
  `id_sous_categorie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_categorie` mediumint(8) UNSIGNED NOT NULL,
  `sous_categorie` varchar(64) NOT NULL,
  PRIMARY KEY (`id_sous_categorie`),
  KEY `id_categorie` (`id_categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `sous_categorie`
--

INSERT INTO `sous_categorie` (`id_sous_categorie`, `id_categorie`, `sous_categorie`) VALUES
(5, 9, '17 pouces'),
(6, 10, 'Bureautique'),
(7, 11, 'Maintenance & Support'),
(8, 12, 'Hébergement'),
(9, 12, 'SpamAway'),
(10, 12, 'e-Backup'),
(11, 11, 'Développement');

-- --------------------------------------------------------

--
-- Structure de la table `stat_snap`
--

DROP TABLE IF EXISTS `stat_snap`;
CREATE TABLE IF NOT EXISTS `stat_snap` (
  `id_stat_snap` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `code` varchar(128) NOT NULL COMMENT 'Nom de la méthode appelée',
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `valeur` float DEFAULT NULL,
  `data` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`id_stat_snap`),
  KEY `code` (`code`),
  KEY `id_module` (`id_module`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id_stock` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_bon_de_commande_ligne` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `libelle` varchar(1000) NOT NULL,
  `date_achat` timestamp NULL DEFAULT current_timestamp(),
  `prix` decimal(10,2) DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `serial` varchar(32) DEFAULT NULL,
  `adresse_mac` varchar(20) DEFAULT NULL,
  `date_fin_immo` timestamp NULL DEFAULT NULL,
  `affectation` varchar(255) DEFAULT NULL,
  `serialAT` varchar(24) DEFAULT NULL,
  `marque` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `poids` text DEFAULT NULL,
  `categories_magento` enum('imprimantes','uc','ecrans','portables','serveurs','reseau','photocopieurs','video_projecteurs','divers') DEFAULT NULL,
  `to_magento` enum('oui','non') NOT NULL DEFAULT 'non',
  `inventaire2013` enum('oui','non') NOT NULL DEFAULT 'non',
  `commentaire` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id_stock`),
  UNIQUE KEY `adresse_mac` (`adresse_mac`),
  UNIQUE KEY `serialAT` (`serialAT`),
  UNIQUE KEY `serial` (`serial`),
  KEY `id_bon_de_commande_ligne` (`id_bon_de_commande_ligne`),
  KEY `id_affaire` (`id_affaire`),
  KEY `inventaire2013` (`inventaire2013`),
  KEY `ref` (`ref`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `stock_etat`
--

DROP TABLE IF EXISTS `stock_etat`;
CREATE TABLE IF NOT EXISTS `stock_etat` (
  `id_stock_etat` mediumint(8) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `etat` enum('reception','stock','livraison','livr','immo','sinistr','pret','annule','defectueux') NOT NULL DEFAULT 'stock',
  `commentaire` text DEFAULT NULL,
  `id_stock` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'id_stock->stock',
  PRIMARY KEY (`id_stock_etat`),
  UNIQUE KEY `date` (`date`,`etat`,`id_stock`),
  KEY `id_stock` (`id_stock`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `suivi`
--

DROP TABLE IF EXISTS `suivi`;
CREATE TABLE IF NOT EXISTS `suivi` (
  `id_suivi` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `type` enum('tel','email','reunion','note') CHARACTER SET utf8 NOT NULL DEFAULT 'tel',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `texte` text CHARACTER SET utf8 NOT NULL,
  `id_opportunite` mediumint(8) UNSIGNED DEFAULT NULL,
  `temps_passe` time NOT NULL DEFAULT '00:00:00',
  `ponderation` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_suivi`),
  KEY `id_user` (`id_user`),
  KEY `id_societe` (`id_societe`),
  KEY `id_affaire` (`id_affaire`),
  KEY `id_opportunite` (`id_opportunite`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `suivi_contact`
--

DROP TABLE IF EXISTS `suivi_contact`;
CREATE TABLE IF NOT EXISTS `suivi_contact` (
  `id_suivi_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_suivi_contact`),
  UNIQUE KEY `id_contact` (`id_contact`,`id_suivi`),
  KEY `id_suivi` (`id_suivi`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre contact et suivi';

-- --------------------------------------------------------

--
-- Structure de la table `suivi_notifie`
--

DROP TABLE IF EXISTS `suivi_notifie`;
CREATE TABLE IF NOT EXISTS `suivi_notifie` (
  `id_suivi_notifie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_suivi_notifie`),
  UNIQUE KEY `id_user` (`id_user`,`id_suivi`),
  KEY `id_suivi` (`id_suivi`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `suivi_societe`
--

DROP TABLE IF EXISTS `suivi_societe`;
CREATE TABLE IF NOT EXISTS `suivi_societe` (
  `id_suivi_societe` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_suivi_societe`),
  UNIQUE KEY `id_user` (`id_user`,`id_suivi`),
  KEY `id_suivi` (`id_suivi`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre user et suivi';

-- --------------------------------------------------------

--
-- Structure de la table `tache`
--

DROP TABLE IF EXISTS `tache`;
CREATE TABLE IF NOT EXISTS `tache` (
  `id_tache` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `periodique` enum('hebdomadaire','mensuel','trimestriel','annuel') DEFAULT NULL,
  PRIMARY KEY (`id_tache`),
  KEY `id_user` (`id_user`),
  KEY `id_societe` (`id_societe`),
  KEY `id_aboutisseur` (`id_aboutisseur`),
  KEY `id_suivi` (`id_suivi`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='id_user = créateur !!!';

-- --------------------------------------------------------

--
-- Structure de la table `tache_user`
--

DROP TABLE IF EXISTS `tache_user`;
CREATE TABLE IF NOT EXISTS `tache_user` (
  `id_tache_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_tache` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_tache_user`),
  KEY `id_user` (`id_user`),
  KEY `id_tache` (`id_tache`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre tache et user, permet d''assigner une';

-- --------------------------------------------------------

--
-- Structure de la table `telescope`
--

DROP TABLE IF EXISTS `telescope`;
CREATE TABLE IF NOT EXISTS `telescope` (
  `id_telescope` int(11) NOT NULL AUTO_INCREMENT,
  `telescope` varchar(45) NOT NULL DEFAULT 'Nom du telescope',
  `url` varchar(255) DEFAULT 'URL du telescope',
  `codename` varchar(255) DEFAULT 'Codename à utiliser',
  `theme` varchar(255) DEFAULT 'nom du theme à utiliser',
  `actif` enum('oui','non') DEFAULT 'oui',
  `home_events` enum('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Afficher le calendrier sur la page d''accueil',
  PRIMARY KEY (`id_telescope`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `telescope`
--

INSERT INTO `telescope` (`id_telescope`, `telescope`, `url`, `codename`, `theme`, `actif`, `home_events`) VALUES
(1, 'Absystech', 'https://telescope.absystech.net', 'absystech', 'null', 'oui', 'oui'),
(2, 'Absystech Telecom', 'https://telescope-att.absystech.net', 'att', 'type-b/theme-purple', 'oui', 'non'),
(3, 'AtoutComs', 'https://telescope-atc.absystech.net', 'atoutcoms', 'type-b/theme-dust', 'oui', 'non'),
(4, 'NCO Services', 'https://telescope-nco.absystech.net', 'nco', 'type-b/theme-coffee', 'oui', 'non'),
(5, 'I2M', 'https://telescope-i5m.absystech.net', 'i2m', 'type-b/theme-coffee', 'oui', 'non');

-- --------------------------------------------------------

--
-- Structure de la table `termes`
--

DROP TABLE IF EXISTS `termes`;
CREATE TABLE IF NOT EXISTS `termes` (
  `id_termes` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `termes` varchar(255) NOT NULL,
  PRIMARY KEY (`id_termes`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='Termes de paiements pour les devis smart proposal' PACK_KEYS=0;

--
-- Déchargement des données de la table `termes`
--

INSERT INTO `termes` (`id_termes`, `termes`) VALUES
(1, 'A réception de facture'),
(2, '30% à la commande, le solde à réception de facture'),
(3, 'A la livraison'),
(4, '30% à la commande, le solde à la livraison'),
(5, 'A 30 jours, date de facture'),
(6, '30% à la commande, le solde à 30 jours'),
(7, 'A 30 jours le 10'),
(8, '30% à la commande, le solde à 30 jours le 10'),
(9, 'A 30 jours le 15'),
(10, '30% à la commande, le solde à 30 jours le 15'),
(11, 'A 30 jours fin de mois'),
(12, '30% à la commande, le solde à 30 jours fin de mois'),
(13, 'A 60 jours le 10'),
(14, '30% à la commande, le solde à 60 jours le 10'),
(15, 'A 60 jours le 15'),
(16, '30% à la commande, le solde à 60 jours le 15'),
(17, 'A 60 jours fin de mois'),
(18, '30% à la commande, le solde à 60 jours fin de mois'),
(19, 'Financement sur 36 mois'),
(20, 'Paiement en 3 fois'),
(21, 'Payable d\'avance'),
(22, 'A échoir'),
(23, 'Financement spécifique'),
(24, 'Par prélèvement automatique mensuel le 15'),
(25, 'Par prélèvement automatique trimestriel le 15'),
(26, 'Selon les termes établis par nos accords'),
(27, 'A la commande'),
(28, 'A 30 jours, fin de mois, le 10'),
(29, 'A 60 jours, date de facture'),
(30, 'A 15 jours, date de facture'),
(31, 'Par prélèvement bancaire'),
(32, '30% à la commande, 25% à fin janv 2013, 25% à fin fév 2013, 20% à fin mars 2013.'),
(33, 'A 30 jours, fin de mois, le 15'),
(34, '30% à la commande, le solde en traite à 60 jours'),
(35, 'Traite à 45 jours'),
(36, '50% à la commande, le solde à 30 jours'),
(37, 'Traite à 30 jours'),
(38, 'Par prélèvement automatique annuel'),
(39, 'Paiement en 3 fois réparti sur 3 mois successifs, à réception de facture'),
(40, '30% à la commande, 30% à la livraison, le solde à l\'installation'),
(41, 'Par prélèvement automatique mensuel'),
(42, 'Par prélèvement automatique lorsque le solde de crédits hotline est inférieur ou égal à zéro'),
(43, '30% à la commande, 50% à la livraison, Solde à la recette, 30 jours date de facture'),
(44, 'Par prélèvement automatique mensuel pour l\'ensemble des services, l\'investissement est réglable en 2 fois réparties sur 2 mois successifs à réception de facture'),
(45, 'Par prélèvement automatique trimestriel pour l\'ensemble des services, l\'investissement est réglable à réception de facture'),
(46, 'Par prélèvement automatique mensuel pour l\'ensemble des services, l\'investissement est réglable à réception de facture'),
(47, '50% à la commande, le solde à réception de facture.'),
(48, 'Par prélèvement automatique annuel pour l\'ensemble des services, l\'investissement est réglable à hauteur de 50% à la commande, le solde à réception de facture.');

-- --------------------------------------------------------

--
-- Structure de la table `tmp`
--

DROP TABLE IF EXISTS `tmp`;
CREATE TABLE IF NOT EXISTS `tmp` (
  `a` int(11) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tracabilite`
--

DROP TABLE IF EXISTS `tracabilite`;
CREATE TABLE IF NOT EXISTS `tracabilite` (
  `id_tracabilite` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tracabilite` enum('insert','update','delete') NOT NULL DEFAULT 'insert',
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_module` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_element` mediumint(8) UNSIGNED DEFAULT NULL,
  `nom_element` varchar(256) DEFAULT NULL,
  `avant_modification` longtext DEFAULT NULL,
  `modification` longtext DEFAULT NULL COMMENT 'Enregistrement de ce qui a été modifié',
  `id_tracabilite_parent` mediumint(8) UNSIGNED DEFAULT NULL,
  `rollback` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'détermine si l''on a fait un rollback sur cette trace',
  PRIMARY KEY (`id_tracabilite`),
  KEY `id_module` (`id_module`),
  KEY `id_user` (`id_user`),
  KEY `id_tracabilite_parent` (`id_tracabilite_parent`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `transporteur`
--

DROP TABLE IF EXISTS `transporteur`;
CREATE TABLE IF NOT EXISTS `transporteur` (
  `id_transporteur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transporteur` varchar(200) NOT NULL,
  PRIMARY KEY (`id_transporteur`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `transporteur`
--

INSERT INTO `transporteur` (`id_transporteur`, `transporteur`) VALUES
(1, 'DHL'),
(2, 'TAT'),
(3, 'Absystech'),
(4, 'La poste'),
(5, 'TNT'),
(6, 'UPS'),
(7, 'Chronopost');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `password_mail` varchar(255) DEFAULT NULL,
  `login_mattermost` varchar(150) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `login` (`login`),
  KEY `id_societe` (`id_societe`),
  KEY `id_pays` (`id_pays`),
  KEY `id_agence` (`id_agence`),
  KEY `id_superieur` (`id_superieur`),
  KEY `id_profil` (`id_profil`),
  KEY `password` (`password`,`login`),
  KEY `id_localisation_langue` (`id_localisation_langue`),
  KEY `apikey` (`api_key`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vhost`
--

DROP TABLE IF EXISTS `vhost`;
CREATE TABLE IF NOT EXISTS `vhost` (
  `id_vhost` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(128) DEFAULT NULL,
  `id_nom_de_domaine` mediumint(8) UNSIGNED NOT NULL,
  `date_creation` date NOT NULL,
  `date_expiration` date NOT NULL,
  `id_hebergement` mediumint(8) UNSIGNED NOT NULL,
  `repertoire` varchar(128) NOT NULL,
  PRIMARY KEY (`id_vhost`),
  KEY `id_hebergement` (`id_hebergement`),
  KEY `id_nom_de_domaine` (`id_nom_de_domaine`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `vue`
--

DROP TABLE IF EXISTS `vue`;
CREATE TABLE IF NOT EXISTS `vue` (
  `id_vue` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `vue` varchar(256) NOT NULL,
  `ordre_colonne` text DEFAULT NULL,
  `tronque` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_vue`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la vue `affaire_client`
--
DROP TABLE IF EXISTS `affaire_client`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `affaire_client`  AS  select `affaire`.`id_societe` AS `id_societe`,`affaire`.`id_affaire` AS `id_affaire`,'' AS `ref`,'' AS `ref_externe`,`affaire`.`date` AS `date`,`affaire`.`affaire` AS `affaire`,'' AS `id_parent`,'' AS `id_fille`,`affaire`.`nature` AS `nature`,'' AS `date_garantie`,'' AS `site_associe`,'' AS `mail_signature`,'' AS `date_signature`,'' AS `signataire`,'' AS `langue`,'' AS `adresse_livraison`,'' AS `adresse_livraison_2`,'' AS `adresse_livraison_3`,'' AS `adresse_livraison_cp`,'' AS `adresse_livraison_ville`,'' AS `adresse_facturation`,'' AS `adresse_facturation_2`,'' AS `adresse_facturation_3`,'' AS `adresse_facturation_cp`,'' AS `adresse_facturation_ville`,'' AS `id_partenaire`,'' AS `id_magasin`,'' AS `vendeur`,'' AS `magasin`,'' AS `partenaire`,'' AS `id_apporteur` from (`affaire` left join `societe` `client` on(`client`.`id_societe` = `affaire`.`id_societe`)) where `affaire`.`id_affaire` is not null ;

-- --------------------------------------------------------

--
-- Structure de la vue `contact_client`
--
DROP TABLE IF EXISTS `contact_client`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `contact_client`  AS  select `contact`.`id_contact` AS `id_contact`,`contact`.`id_societe` AS `id_societe`,`contact`.`email` AS `email`,`contact`.`anniversaire` AS `anniversaire`,`contact`.`civilite` AS `civilite`,'' AS `situation_maritale`,'' AS `situation_perso`,`contact`.`fonction` AS `fonction`,'' AS `situation_pro` from `contact` where `contact`.`etat` = 'actif' ;

-- --------------------------------------------------------

--
-- Structure de la vue `coordonnees_client`
--
DROP TABLE IF EXISTS `coordonnees_client`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `coordonnees_client`  AS  select `societe`.`id_societe` AS `id_societe`,`societe`.`ref` AS `ref`,`societe`.`societe` AS `societe`,`famille`.`famille` AS `type_client`,`societe`.`id_famille` AS `id_famille`,`societe`.`nom_commercial` AS `nom_commercial`,`societe`.`adresse` AS `adresse`,`societe`.`adresse_2` AS `adresse_2`,`societe`.`adresse_3` AS `adresse_3`,`societe`.`cp` AS `cp`,`societe`.`ville` AS `ville`,`societe`.`facturation_adresse` AS `facturation_adresse`,`societe`.`facturation_adresse_2` AS `facturation_adresse_2`,`societe`.`facturation_adresse_3` AS `facturation_adresse_3`,`societe`.`facturation_cp` AS `facturation_cp`,`societe`.`facturation_ville` AS `facturation_ville`,'' AS `livraison_adresse`,'' AS `livraison_adresse_2`,'' AS `livraison_adresse_3`,'' AS `livraison_cp`,'' AS `livraison_ville`,`societe`.`email` AS `email`,`societe`.`tel` AS `tel`,'' AS `particulier_civilite`,'' AS `particulier_nom`,'' AS `particulier_prenom`,'' AS `particulier_portable`,'' AS `num_carte_fidelite`,'' AS `particulier_fixe`,'' AS `particulier_email`,'' AS `code_client`,'' AS `id_apporteur`,'' AS `langue`,`societe`.`siret` AS `siret` from (`societe` join `famille` on(`famille`.`id_famille` = `societe`.`id_famille`)) ;

-- --------------------------------------------------------

--
-- Structure de la vue `espace_client_inscription`
--
DROP TABLE IF EXISTS `espace_client_inscription`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `espace_client_inscription`  AS  select `affaire`.`affaire` AS `dossier`,`societe`.`societe` AS `societe`,`societe`.`ref` AS `code_client`,case when `societe`.`id_famille` = 9 then `societe`.`nom_commercial` else `contact`.`nom` end AS `nom`,case when `societe`.`id_famille` = 9 then `societe`.`nom_commercial` else `contact`.`prenom` end AS `prenom`,case when `societe`.`id_famille` = 9 then `societe`.`tel` else `contact`.`gsm` end AS `gsm`,case when `societe`.`id_famille` = 9 then `societe`.`email` else `contact`.`email` end AS `email`,`societe`.`id_societe` AS `id_societe`,`contact`.`id_contact` AS `id_contact`,`societe`.`id_famille` AS `id_famille`,`famille`.`famille` AS `famille` from (((`affaire` left join `contact` on(`contact`.`id_societe` = `affaire`.`id_societe`)) left join `societe` on(`societe`.`id_societe` = `contact`.`id_societe`)) left join `famille` on(`famille`.`id_famille` = `societe`.`id_famille`)) where `contact`.`etat` = 'actif' ;

-- --------------------------------------------------------

--
-- Structure de la vue `factures_client`
--
DROP TABLE IF EXISTS `factures_client`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `factures_client`  AS  select `facture`.`id_facture` AS `id_facture`,`facture`.`ref` AS `ref`,'' AS `ref_externe`,`facture`.`id_societe` AS `id_societe`,`facture`.`prix` AS `prix`,`facture`.`etat` AS `etat`,`facture`.`date` AS `date`,'' AS `date_paiement`,`facture`.`type_facture` AS `type_facture`,`facture`.`date_debut_periode` AS `date_periode_debut`,`facture`.`date_fin_periode` AS `date_periode_fin`,`facture`.`tva` AS `tva`,`facture`.`id_affaire` AS `id_affaire`,'' AS `mode_paiement`,'' AS `nature`,'' AS `rejet`,'' AS `date_rejet`,'' AS `date_regularisation` from `facture` where `facture`.`id_affaire` is not null ;

-- --------------------------------------------------------

--
-- Structure de la vue `gie_contact`
--
DROP TABLE IF EXISTS `gie_contact`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `gie_contact`  AS  (select 1000000 + `contact`.`id_contact` AS `id_gie_contact`,1000000 + `contact`.`id_societe` AS `id_gie_societe`,'absystech' AS `codename`,`contact`.`id_societe` AS `id_societe`,`contact`.`id_contact` AS `id_contact`,`contact`.`date` AS `date`,`contact`.`civilite` AS `civilite`,`contact`.`nom` AS `nom`,`contact`.`prenom` AS `prenom`,`contact`.`etat` AS `etat`,`contact`.`id_owner` AS `id_owner`,`contact`.`private` AS `private`,`contact`.`adresse` AS `adresse`,`contact`.`adresse_2` AS `adresse_2`,`contact`.`adresse_3` AS `adresse_3`,`contact`.`cp` AS `cp`,`contact`.`ville` AS `ville`,`contact`.`id_pays` AS `id_pays`,`contact`.`tel` AS `tel`,`contact`.`gsm` AS `gsm`,`contact`.`fax` AS `fax`,`contact`.`email` AS `email`,`contact`.`fonction` AS `fonction`,`contact`.`departement` AS `departement`,`contact`.`anniversaire` AS `anniversaire`,`contact`.`loisir` AS `loisir`,`contact`.`langue` AS `langue`,`contact`.`assistant` AS `assistant`,`contact`.`assistant_tel` AS `assistant_tel`,`contact`.`tel_autres` AS `tel_autres`,`contact`.`adresse_autres` AS `adresse_autres`,`contact`.`forecast` AS `forecast`,`contact`.`description` AS `description`,`contact`.`disponibilite` AS `disponibilite` from `contact`) union all (select 2000000 + `optima_att`.`contact`.`id_contact` AS `id_gie_contact`,2000000 + `optima_att`.`contact`.`id_societe` AS `id_gie_societe`,'att' AS `codename`,`optima_att`.`contact`.`id_societe` AS `id_societe`,`optima_att`.`contact`.`id_contact` AS `id_contact`,`optima_att`.`contact`.`date` AS `date`,`optima_att`.`contact`.`civilite` AS `civilite`,`optima_att`.`contact`.`nom` AS `nom`,`optima_att`.`contact`.`prenom` AS `prenom`,`optima_att`.`contact`.`etat` AS `etat`,`optima_att`.`contact`.`id_owner` AS `id_owner`,`optima_att`.`contact`.`private` AS `private`,`optima_att`.`contact`.`adresse` AS `adresse`,`optima_att`.`contact`.`adresse_2` AS `adresse_2`,`optima_att`.`contact`.`adresse_3` AS `adresse_3`,`optima_att`.`contact`.`cp` AS `cp`,`optima_att`.`contact`.`ville` AS `ville`,`optima_att`.`contact`.`id_pays` AS `id_pays`,`optima_att`.`contact`.`tel` AS `tel`,`optima_att`.`contact`.`gsm` AS `gsm`,`optima_att`.`contact`.`fax` AS `fax`,`optima_att`.`contact`.`email` AS `email`,`optima_att`.`contact`.`fonction` AS `fonction`,`optima_att`.`contact`.`departement` AS `departement`,`optima_att`.`contact`.`anniversaire` AS `anniversaire`,`optima_att`.`contact`.`loisir` AS `loisir`,`optima_att`.`contact`.`langue` AS `langue`,`optima_att`.`contact`.`assistant` AS `assistant`,`optima_att`.`contact`.`assistant_tel` AS `assistant_tel`,`optima_att`.`contact`.`tel_autres` AS `tel_autres`,`optima_att`.`contact`.`adresse_autres` AS `adresse_autres`,`optima_att`.`contact`.`forecast` AS `forecast`,`optima_att`.`contact`.`description` AS `description`,`optima_att`.`contact`.`disponibilite` AS `disponibilite` from `optima_att`.`contact`) union all (select 4000000 + `optima_nco`.`contact`.`id_contact` AS `id_gie_contact`,4000000 + `optima_nco`.`contact`.`id_societe` AS `id_gie_societe`,'nco' AS `codename`,`optima_nco`.`contact`.`id_societe` AS `id_societe`,`optima_nco`.`contact`.`id_contact` AS `id_contact`,`optima_nco`.`contact`.`date` AS `date`,`optima_nco`.`contact`.`civilite` AS `civilite`,`optima_nco`.`contact`.`nom` AS `nom`,`optima_nco`.`contact`.`prenom` AS `prenom`,`optima_nco`.`contact`.`etat` AS `etat`,`optima_nco`.`contact`.`id_owner` AS `id_owner`,`optima_nco`.`contact`.`private` AS `private`,`optima_nco`.`contact`.`adresse` AS `adresse`,`optima_nco`.`contact`.`adresse_2` AS `adresse_2`,`optima_nco`.`contact`.`adresse_3` AS `adresse_3`,`optima_nco`.`contact`.`cp` AS `cp`,`optima_nco`.`contact`.`ville` AS `ville`,`optima_nco`.`contact`.`id_pays` AS `id_pays`,`optima_nco`.`contact`.`tel` AS `tel`,`optima_nco`.`contact`.`gsm` AS `gsm`,`optima_nco`.`contact`.`fax` AS `fax`,`optima_nco`.`contact`.`email` AS `email`,`optima_nco`.`contact`.`fonction` AS `fonction`,`optima_nco`.`contact`.`departement` AS `departement`,`optima_nco`.`contact`.`anniversaire` AS `anniversaire`,`optima_nco`.`contact`.`loisir` AS `loisir`,`optima_nco`.`contact`.`langue` AS `langue`,`optima_nco`.`contact`.`assistant` AS `assistant`,`optima_nco`.`contact`.`assistant_tel` AS `assistant_tel`,`optima_nco`.`contact`.`tel_autres` AS `tel_autres`,`optima_nco`.`contact`.`adresse_autres` AS `adresse_autres`,`optima_nco`.`contact`.`forecast` AS `forecast`,`optima_nco`.`contact`.`description` AS `description`,`optima_nco`.`contact`.`disponibilite` AS `disponibilite` from `optima_nco`.`contact`) ;

-- --------------------------------------------------------

--
-- Structure de la vue `gie_societe`
--
DROP TABLE IF EXISTS `gie_societe`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `gie_societe`  AS  (select 1000000 + `societe`.`id_societe` AS `id_gie_societe`,'absystech' AS `codename`,`societe`.`id_societe` AS `id_societe`,`societe`.`societe` AS `societe`,`societe`.`nom_commercial` AS `nom_commercial`,`societe`.`activite` AS `activite`,`societe`.`etat` AS `etat`,`societe`.`relation` AS `relation`,`societe`.`latitude` AS `latitude`,`societe`.`longitude` AS `longitude`,`societe`.`siren` AS `siren`,`societe`.`siret` AS `siret`,`societe`.`naf` AS `naf`,`societe`.`adresse` AS `adresse`,`societe`.`adresse_2` AS `adresse_2`,`societe`.`adresse_3` AS `adresse_3`,`societe`.`cp` AS `cp`,`societe`.`ville` AS `ville`,`societe`.`id_pays` AS `id_pays`,`societe`.`tel` AS `tel`,`societe`.`fax` AS `fax`,`societe`.`email` AS `email`,`societe`.`web` AS `web`,`societe`.`ca` AS `ca`,`societe`.`structure` AS `structure`,`societe`.`capital` AS `capital`,`societe`.`nb_employe` AS `nb_employe`,`societe`.`effectif` AS `effectif`,`societe`.`fournisseur` AS `fournisseur`,`societe`.`partenaire` AS `partenaire` from `societe`) union all (select 2000000 + `optima_att`.`societe`.`id_societe` AS `id_gie_societe`,'att' AS `codename`,`optima_att`.`societe`.`id_societe` AS `id_societe`,`optima_att`.`societe`.`societe` AS `societe`,`optima_att`.`societe`.`nom_commercial` AS `nom_commercial`,`optima_att`.`societe`.`activite` AS `activite`,`optima_att`.`societe`.`etat` AS `etat`,`optima_att`.`societe`.`relation` AS `relation`,`optima_att`.`societe`.`latitude` AS `latitude`,`optima_att`.`societe`.`longitude` AS `longitude`,`optima_att`.`societe`.`siren` AS `siren`,`optima_att`.`societe`.`siret` AS `siret`,`optima_att`.`societe`.`naf` AS `naf`,`optima_att`.`societe`.`adresse` AS `adresse`,`optima_att`.`societe`.`adresse_2` AS `adresse_2`,`optima_att`.`societe`.`adresse_3` AS `adresse_3`,`optima_att`.`societe`.`cp` AS `cp`,`optima_att`.`societe`.`ville` AS `ville`,`optima_att`.`societe`.`id_pays` AS `id_pays`,`optima_att`.`societe`.`tel` AS `tel`,`optima_att`.`societe`.`fax` AS `fax`,`optima_att`.`societe`.`email` AS `email`,`optima_att`.`societe`.`web` AS `web`,`optima_att`.`societe`.`ca` AS `ca`,`optima_att`.`societe`.`structure` AS `structure`,`optima_att`.`societe`.`capital` AS `capital`,`optima_att`.`societe`.`nb_employe` AS `nb_employe`,`optima_att`.`societe`.`effectif` AS `effectif`,`optima_att`.`societe`.`fournisseur` AS `fournisseur`,`optima_att`.`societe`.`partenaire` AS `partenaire` from `optima_att`.`societe`) union all (select 4000000 + `optima_nco`.`societe`.`id_societe` AS `id_gie_societe`,'nco' AS `codename`,`optima_nco`.`societe`.`id_societe` AS `id_societe`,`optima_nco`.`societe`.`societe` AS `societe`,`optima_nco`.`societe`.`nom_commercial` AS `nom_commercial`,`optima_nco`.`societe`.`activite` AS `activite`,`optima_nco`.`societe`.`etat` AS `etat`,`optima_nco`.`societe`.`relation` AS `relation`,`optima_nco`.`societe`.`latitude` AS `latitude`,`optima_nco`.`societe`.`longitude` AS `longitude`,`optima_nco`.`societe`.`siren` AS `siren`,`optima_nco`.`societe`.`siret` AS `siret`,`optima_nco`.`societe`.`naf` AS `naf`,`optima_nco`.`societe`.`adresse` AS `adresse`,`optima_nco`.`societe`.`adresse_2` AS `adresse_2`,`optima_nco`.`societe`.`adresse_3` AS `adresse_3`,`optima_nco`.`societe`.`cp` AS `cp`,`optima_nco`.`societe`.`ville` AS `ville`,`optima_nco`.`societe`.`id_pays` AS `id_pays`,`optima_nco`.`societe`.`tel` AS `tel`,`optima_nco`.`societe`.`fax` AS `fax`,`optima_nco`.`societe`.`email` AS `email`,`optima_nco`.`societe`.`web` AS `web`,`optima_nco`.`societe`.`ca` AS `ca`,`optima_nco`.`societe`.`structure` AS `structure`,`optima_nco`.`societe`.`capital` AS `capital`,`optima_nco`.`societe`.`nb_employe` AS `nb_employe`,`optima_nco`.`societe`.`effectif` AS `effectif`,`optima_nco`.`societe`.`fournisseur` AS `fournisseur`,`optima_nco`.`societe`.`partenaire` AS `partenaire` from `optima_nco`.`societe`) ;

-- --------------------------------------------------------

--
-- Structure de la vue `historique_affaire`
--
DROP TABLE IF EXISTS `historique_affaire`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `historique_affaire`  AS  select `affaire_etat`.`id_affaire` AS `id_affaire`,`affaire_etat`.`date` AS `date`,'' AS `etat`,`affaire_etat`.`comment` AS `commentaire` from `affaire_etat` order by `affaire_etat`.`id_affaire_etat` ;

-- --------------------------------------------------------

--
-- Structure de la vue `licence_affaire`
--
DROP TABLE IF EXISTS `licence_affaire`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `licence_affaire`  AS  select `affaire`.`id_affaire` AS `id_affaire`,`affaire`.`id_societe` AS `id_societe`,'' AS `ref`,'' AS `ref_externe`,'' AS `licence_part1`,'' AS `licence_part2`,'' AS `date_envoi`,'' AS `id_licence`,'' AS `licence_type`,'' AS `url_telechargement` from `affaire` ;

-- --------------------------------------------------------

--
-- Structure de la vue `loyer_affaire`
--
DROP TABLE IF EXISTS `loyer_affaire`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `loyer_affaire`  AS  select `affaire`.`id_affaire` AS `id_affaire`,'' AS `type`,'' AS `loyer`,'' AS `frequence`,'' AS `duree`,`devis`.`tva` AS `tva`,'' AS `id_loyer` from (`affaire` left join `devis` on(`devis`.`id_affaire` = `affaire`.`id_affaire`)) ;

-- --------------------------------------------------------

--
-- Structure de la vue `parc_client`
--
DROP TABLE IF EXISTS `parc_client`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `parc_client`  AS  select `affaire`.`id_societe` AS `id_societe`,`affaire`.`id_affaire` AS `id_affaire`,'' AS `ref_affaire`,'' AS `ref`,'' AS `libelle`,'' AS `divers`,'' AS `serial`,'' AS `code`,`affaire`.`date` AS `date`,'' AS `date_inactif`,'' AS `date_garantie`,'' AS `date_achat`,'' AS `existence`,`affaire`.`etat` AS `etat` from `affaire` ;

-- --------------------------------------------------------

--
-- Structure de la vue `societe_CA`
--
DROP TABLE IF EXISTS `societe_CA`;

CREATE ALGORITHM=UNDEFINED DEFINER=`afachaux`@`%` SQL SECURITY DEFINER VIEW `societe_CA`  AS  select sum(`facture`.`prix`) AS `CA`,min(`facture`.`date`) AS `dateMin`,max(`facture`.`date`) AS `dateMax`,1 + year(current_timestamp()) - year(min(`facture`.`date`)) AS `nbAnnees`,sum(`facture`.`prix`) / (1 + year(current_timestamp()) - year(min(`facture`.`date`))) AS `moyenneCAParAn`,group_concat(distinct year(`facture`.`date`) separator ',') AS `anneesActives`,count(distinct year(`facture`.`date`)) AS `nbAnneesActives`,sum(`facture`.`prix`) / count(distinct year(`facture`.`date`)) AS `moyenneCAParAnActives`,`societe`.`societe` AS `societe`,`facture`.`id_societe` AS `id_societe` from (`facture` join `societe` on(`societe`.`id_societe` = `facture`.`id_societe`)) where year(`facture`.`date`) < year(current_timestamp()) group by `facture`.`id_societe` ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affaire`
--
ALTER TABLE `affaire`
  ADD CONSTRAINT `affaire_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_2` FOREIGN KEY (`id_termes`) REFERENCES `termes` (`id_termes`) ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_3` FOREIGN KEY (`id_commercial`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  ADD CONSTRAINT `affaire_etat_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_etat_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_etat_ibfk_3` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_etat_ibfk_4` FOREIGN KEY (`id_jalon`) REFERENCES `jalon` (`id_jalon`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `affaire_ged_dossier`
--
ALTER TABLE `affaire_ged_dossier`
  ADD CONSTRAINT `affaire_ged_dossier_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE,
  ADD CONSTRAINT `affaire_ged_dossier_ibfk_4` FOREIGN KEY (`id_ged_dossier`) REFERENCES `ged_dossier` (`id_ged_dossier`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `affaire_ged_fichier`
--
ALTER TABLE `affaire_ged_fichier`
  ADD CONSTRAINT `affaire_ged_fichier_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE,
  ADD CONSTRAINT `affaire_ged_fichier_ibfk_2` FOREIGN KEY (`id_ged_fichier`) REFERENCES `ged_fichier` (`id_ged_fichier`) ON DELETE CASCADE;

--
-- Contraintes pour la table `alerte`
--
ALTER TABLE `alerte`
  ADD CONSTRAINT `alerte_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alerte_ibfk_2` FOREIGN KEY (`id_hotline`) REFERENCES `hotline` (`id_hotline`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alerte_ibfk_3` FOREIGN KEY (`id_hotline_interaction`) REFERENCES `hotline_interaction` (`id_hotline_interaction`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `alerte_materiel`
--
ALTER TABLE `alerte_materiel`
  ADD CONSTRAINT `alerte_materiel_ibfk_1` FOREIGN KEY (`id_hotline_interaction`) REFERENCES `hotline_interaction` (`id_hotline_interaction`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alerte_materiel_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `base_de_connaissance`
--
ALTER TABLE `base_de_connaissance`
  ADD CONSTRAINT `base_de_connaissance_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Contraintes pour la table `bon_de_commande`
--
ALTER TABLE `bon_de_commande`
  ADD CONSTRAINT `bon_de_commande_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_2` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_3` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_4` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_5` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bon_de_commande_ligne`
--
ALTER TABLE `bon_de_commande_ligne`
  ADD CONSTRAINT `bon_de_commande_ligne_ibfk_1` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bon_de_pret`
--
ALTER TABLE `bon_de_pret`
  ADD CONSTRAINT `bon_de_pret_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_pret_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_pret_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_pret_ibfk_4` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bon_de_pret_ligne`
--
ALTER TABLE `bon_de_pret_ligne`
  ADD CONSTRAINT `bon_de_pret_ligne_ibfk_1` FOREIGN KEY (`id_bon_de_pret`) REFERENCES `bon_de_pret` (`id_bon_de_pret`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_pret_ligne_ibfk_2` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `cgv`
--
ALTER TABLE `cgv`
  ADD CONSTRAINT `cgv_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `cgv_article`
--
ALTER TABLE `cgv_article`
  ADD CONSTRAINT `cgv_article_ibfk_1` FOREIGN KEY (`id_cgv`) REFERENCES `cgv` (`id_cgv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `cgv_article_second`
--
ALTER TABLE `cgv_article_second`
  ADD CONSTRAINT `cgv_article_second_ibfk_1` FOREIGN KEY (`id_cgv_article`) REFERENCES `cgv_article` (`id_cgv_article`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `colonne`
--
ALTER TABLE `colonne`
  ADD CONSTRAINT `colonne_ibfk_1` FOREIGN KEY (`id_vue`) REFERENCES `vue` (`id_vue`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_4` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_facture`
--
ALTER TABLE `commande_facture`
  ADD CONSTRAINT `commande_facture_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_facture_ibfk_2` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_ligne`
--
ALTER TABLE `commande_ligne`
  ADD CONSTRAINT `commande_ligne_ibfk_3` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ligne_ibfk_4` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ligne_ibfk_5` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ligne_ibfk_6` FOREIGN KEY (`id_compte_absystech`) REFERENCES `compte_absystech` (`id_compte_absystech`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `conge`
--
ALTER TABLE `conge`
  ADD CONSTRAINT `conge_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `departement`
--
ALTER TABLE `departement`
  ADD CONSTRAINT `departement_ibfk_1` FOREIGN KEY (`id_region`) REFERENCES `region` (`id_region`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `devis_ibfk_11` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_12` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_13` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_16` FOREIGN KEY (`id_remplacant`) REFERENCES `devis` (`id_devis`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_17` FOREIGN KEY (`id_user_technique`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_18` FOREIGN KEY (`id_user_admin`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_19` FOREIGN KEY (`id_delai_de_realisation`) REFERENCES `delai_de_realisation` (`id_delai_de_realisation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_4` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_8` FOREIGN KEY (`id_opportunite`) REFERENCES `opportunite` (`id_opportunite`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `devis_ligne`
--
ALTER TABLE `devis_ligne`
  ADD CONSTRAINT `devis_ligne_ibfk_1` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ligne_ibfk_10` FOREIGN KEY (`id_compte_absystech`) REFERENCES `compte_absystech` (`id_compte_absystech`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ligne_ibfk_8` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ligne_ibfk_9` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `echeancier`
--
ALTER TABLE `echeancier`
  ADD CONSTRAINT `echeancier_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `echeancier_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `echeancier_ibfk_3` FOREIGN KEY (`id_termes`) REFERENCES `termes` (`id_termes`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `echeancier_ligne_periodique`
--
ALTER TABLE `echeancier_ligne_periodique`
  ADD CONSTRAINT `compte_absystech2` FOREIGN KEY (`id_compte_absystech`) REFERENCES `compte_absystech` (`id_compte_absystech`),
  ADD CONSTRAINT `echeancier_ligne_periodique_ibfk_1` FOREIGN KEY (`id_echeancier`) REFERENCES `echeancier` (`id_echeancier`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `echeancier_ligne_ponctuelle`
--
ALTER TABLE `echeancier_ligne_ponctuelle`
  ADD CONSTRAINT `echeancier_ligne_ponctuelle_ibfk_1` FOREIGN KEY (`id_echeancier`) REFERENCES `echeancier` (`id_echeancier`) ON UPDATE CASCADE,
  ADD CONSTRAINT `echeancier_ligne_ponctuelle_ibfk_2` FOREIGN KEY (`id_compte_absystech`) REFERENCES `compte_absystech` (`id_compte_absystech`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_contact`
--
ALTER TABLE `emailing_contact`
  ADD CONSTRAINT `FK_emailing_contact_1` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `emailing_contact_ibfk_1` FOREIGN KEY (`id_emailing_source`) REFERENCES `emailing_source` (`id_emailing_source`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_forms`
--
ALTER TABLE `emailing_forms`
  ADD CONSTRAINT `emailing_forms_ibfk_1` FOREIGN KEY (`id_emailing_projet`) REFERENCES `emailing_projet` (`id_emailing_projet`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_job`
--
ALTER TABLE `emailing_job`
  ADD CONSTRAINT `FK_emailing_job_1` FOREIGN KEY (`id_emailing_projet`) REFERENCES `emailing_projet` (`id_emailing_projet`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_emailing_job_2` FOREIGN KEY (`id_emailing_liste`) REFERENCES `emailing_liste` (`id_emailing_liste`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_job_email`
--
ALTER TABLE `emailing_job_email`
  ADD CONSTRAINT `FK_emailing_job_email_1` FOREIGN KEY (`id_emailing_job`) REFERENCES `emailing_job` (`id_emailing_job`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_emailing_job_email_2` FOREIGN KEY (`id_emailing_liste_contact`) REFERENCES `emailing_liste_contact` (`id_emailing_liste_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_liste_contact`
--
ALTER TABLE `emailing_liste_contact`
  ADD CONSTRAINT `FK_emailing_liste_contact_1` FOREIGN KEY (`id_emailing_liste`) REFERENCES `emailing_liste` (`id_emailing_liste`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_emailing_liste_contact_2` FOREIGN KEY (`id_emailing_contact`) REFERENCES `emailing_contact` (`id_emailing_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_source`
--
ALTER TABLE `emailing_source`
  ADD CONSTRAINT `emailing_source_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_tracking`
--
ALTER TABLE `emailing_tracking`
  ADD CONSTRAINT `FK_emailing_tracking_1` FOREIGN KEY (`id_emailing_job_email`) REFERENCES `emailing_job_email` (`id_emailing_job_email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `emailing_tracking_ibfk_1` FOREIGN KEY (`id_emailing_lien`) REFERENCES `emailing_lien` (`id_emailing_lien`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `export_comptable`
--
ALTER TABLE `export_comptable`
  ADD CONSTRAINT `export_comptable_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `echeancier` FOREIGN KEY (`id_echeancier`) REFERENCES `echeancier` (`id_echeancier`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ibfk_5` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ibfk_6` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ibfk_7` FOREIGN KEY (`id_termes`) REFERENCES `termes` (`id_termes`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ibfk_8` FOREIGN KEY (`id_facture_parente`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ibfk_9` FOREIGN KEY (`id_export_comptable`) REFERENCES `export_comptable` (`id_export_comptable`);

--
-- Contraintes pour la table `facture_fournisseur`
--
ALTER TABLE `facture_fournisseur`
  ADD CONSTRAINT `facture_fournisseur_ibfk_1` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_fournisseur_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_fournisseur_affaire`
--
ALTER TABLE `facture_fournisseur_affaire`
  ADD CONSTRAINT `facture_fournisseur_affaire_ibfk_1` FOREIGN KEY (`id_facture_fournisseur`) REFERENCES `facture_fournisseur` (`id_facture_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_fournisseur_affaire_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_ligne`
--
ALTER TABLE `facture_ligne`
  ADD CONSTRAINT `facture_ligne_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ligne_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ligne_ibfk_3` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ligne_ibfk_4` FOREIGN KEY (`id_compte_absystech`) REFERENCES `compte_absystech` (`id_compte_absystech`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_paiement`
--
ALTER TABLE `facture_paiement`
  ADD CONSTRAINT `facture_paiement_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_paiement_ibfk_3` FOREIGN KEY (`id_facture_avoir`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `filtre_defaut`
--
ALTER TABLE `filtre_defaut`
  ADD CONSTRAINT `filtre_defaut_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `filtre_optima`
--
ALTER TABLE `filtre_optima`
  ADD CONSTRAINT `filtre_optima_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `filtre_optima_ibfk_2` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `filtre_user`
--
ALTER TABLE `filtre_user`
  ADD CONSTRAINT `filtre_user_ibfk_1` FOREIGN KEY (`id_filtre_optima`) REFERENCES `filtre_optima` (`id_filtre_optima`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `filtre_user_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `filtre_user_ibfk_3` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `ged`
--
ALTER TABLE `ged`
  ADD CONSTRAINT `FK_ged_1` FOREIGN KEY (`id_parent`) REFERENCES `ged` (`id_ged`),
  ADD CONSTRAINT `ged_ibfk_1` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ged_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `ged_dossier`
--
ALTER TABLE `ged_dossier`
  ADD CONSTRAINT `ged_dossier_ibfk_1` FOREIGN KEY (`id_parent`) REFERENCES `ged_dossier` (`id_ged_dossier`) ON DELETE SET NULL;

--
-- Contraintes pour la table `ged_fichier`
--
ALTER TABLE `ged_fichier`
  ADD CONSTRAINT `ged_fichier_ibfk_1` FOREIGN KEY (`id_ged_dossier`) REFERENCES `ged_dossier` (`id_ged_dossier`) ON DELETE SET NULL,
  ADD CONSTRAINT `ged_fichier_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Contraintes pour la table `ged_user`
--
ALTER TABLE `ged_user`
  ADD CONSTRAINT `ged_user_ibfk_2` FOREIGN KEY (`id_ged_dossier`) REFERENCES `ged_dossier` (`id_ged_dossier`),
  ADD CONSTRAINT `ged_user_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Contraintes pour la table `gep_equipe`
--
ALTER TABLE `gep_equipe`
  ADD CONSTRAINT `FK_gep_equipe_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gep_equipe_2` FOREIGN KEY (`id_gep_projet`) REFERENCES `gep_projet` (`id_gep_projet`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `gep_processus`
--
ALTER TABLE `gep_processus`
  ADD CONSTRAINT `FK_gep_processus_1` FOREIGN KEY (`id_gep_projet`) REFERENCES `gep_projet` (`id_gep_projet`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `gep_projet`
--
ALTER TABLE `gep_projet`
  ADD CONSTRAINT `FK_gep_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gep_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gep_3` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `gep_projet_ibfk_1` FOREIGN KEY (`id_contact_facturation`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `gep_projet_ibfk_2` FOREIGN KEY (`id_projet_parent`) REFERENCES `gep_projet` (`id_gep_projet`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `gep_tache`
--
ALTER TABLE `gep_tache`
  ADD CONSTRAINT `FK_gep_tache_1` FOREIGN KEY (`id_gep_equipe`) REFERENCES `gep_equipe` (`id_gep_equipe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gep_tache_2` FOREIGN KEY (`id_gep_processus`) REFERENCES `gep_processus` (`id_gep_processus`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `gep_tache_dependance`
--
ALTER TABLE `gep_tache_dependance`
  ADD CONSTRAINT `FK_gep_tache_dependance_1` FOREIGN KEY (`id_tache_dependante`) REFERENCES `gep_tache` (`id_gep_tache`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gep_tache_dependance_2` FOREIGN KEY (`id_tache_liee`) REFERENCES `gep_tache` (`id_gep_tache`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `gestion_ticket`
--
ALTER TABLE `gestion_ticket`
  ADD CONSTRAINT `gestion_ticket_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gestion_ticket_ibfk_2` FOREIGN KEY (`id_hotline`) REFERENCES `hotline` (`id_hotline`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gestion_ticket_ibfk_3` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `hebergement`
--
ALTER TABLE `hebergement`
  ADD CONSTRAINT `hebergement_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `hotline`
--
ALTER TABLE `hotline`
  ADD CONSTRAINT `hotline_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hotline_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hotline_ibfk_3` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `hotline_ibfk_4` FOREIGN KEY (`id_gep_projet`) REFERENCES `gep_projet` (`id_gep_projet`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hotline_ibfk_5` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `hotline_interaction`
--
ALTER TABLE `hotline_interaction`
  ADD CONSTRAINT `hotline_interaction_ibfk_1` FOREIGN KEY (`id_hotline`) REFERENCES `hotline` (`id_hotline`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hotline_interaction_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hotline_interaction_ibfk_3` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hotline_interaction_ibfk_4` FOREIGN KEY (`id_ordre_de_mission`) REFERENCES `ordre_de_mission` (`id_ordre_de_mission`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `im`
--
ALTER TABLE `im`
  ADD CONSTRAINT `im_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `im_ibfk_2` FOREIGN KEY (`id_user_recipient`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `importer`
--
ALTER TABLE `importer`
  ADD CONSTRAINT `module` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `importer_ligne`
--
ALTER TABLE `importer_ligne`
  ADD CONSTRAINT `importer` FOREIGN KEY (`id_importer`) REFERENCES `importer` (`id_importer`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `livraison`
--
ALTER TABLE `livraison`
  ADD CONSTRAINT `livraison_ibfk_2` FOREIGN KEY (`id_transporteur`) REFERENCES `transporteur` (`id_transporteur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `livraison_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `livraison_ibfk_4` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `livraison_ibfk_5` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id_devis`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `livraison_ibfk_6` FOREIGN KEY (`id_expediteur`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `livraison_ligne`
--
ALTER TABLE `livraison_ligne`
  ADD CONSTRAINT `livraison_ligne_ibfk_1` FOREIGN KEY (`id_livraison`) REFERENCES `livraison` (`id_livraison`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `localisation_langue`
--
ALTER TABLE `localisation_langue`
  ADD CONSTRAINT `localisation_langue_ibfk_1` FOREIGN KEY (`id_pays`) REFERENCES `pays` (`id_pays`);

--
-- Contraintes pour la table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `module_ibfk_1` FOREIGN KEY (`id_parent`) REFERENCES `module` (`id_module`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `module_privilege`
--
ALTER TABLE `module_privilege`
  ADD CONSTRAINT `module_privilege_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `module_privilege_ibfk_2` FOREIGN KEY (`id_privilege`) REFERENCES `privilege` (`id_privilege`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `nom_de_domaine`
--
ALTER TABLE `nom_de_domaine`
  ADD CONSTRAINT `nom_de_domaine_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `nom_de_domaine_ibfk_2` FOREIGN KEY (`id_registrar`) REFERENCES `registrar` (`id_registrar`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `note_de_frais`
--
ALTER TABLE `note_de_frais`
  ADD CONSTRAINT `note_de_frais_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `note_de_frais_ligne`
--
ALTER TABLE `note_de_frais_ligne`
  ADD CONSTRAINT `note_de_frais_ligne_ibfk_1` FOREIGN KEY (`id_note_de_frais`) REFERENCES `note_de_frais` (`id_note_de_frais`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `note_de_frais_ligne_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `opportunite`
--
ALTER TABLE `opportunite`
  ADD CONSTRAINT `opportunite_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunite_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunite_ibfk_3` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `opportunite_ibfk_4` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `ordre_de_mission`
--
ALTER TABLE `ordre_de_mission`
  ADD CONSTRAINT `ordre_de_mission_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`),
  ADD CONSTRAINT `ordre_de_mission_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `ordre_de_mission_ibfk_4` FOREIGN KEY (`id_hotline`) REFERENCES `hotline` (`id_hotline`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ordre_de_mission_ibfk_5` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE;

--
-- Contraintes pour la table `phone`
--
ALTER TABLE `phone`
  ADD CONSTRAINT `phone_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `phone_ibfk_2` FOREIGN KEY (`id_asterisk`) REFERENCES `asterisk` (`id_asterisk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `pointage`
--
ALTER TABLE `pointage`
  ADD CONSTRAINT `FK_pointage_1` FOREIGN KEY (`id_conge`) REFERENCES `conge` (`id_conge`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pointage_ibfk_1` FOREIGN KEY (`id_hotline_interaction`) REFERENCES `hotline_interaction` (`id_hotline_interaction`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `print_alerte`
--
ALTER TABLE `print_alerte`
  ADD CONSTRAINT `print_alerte_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `print_consommable`
--
ALTER TABLE `print_consommable`
  ADD CONSTRAINT `ref_stock` FOREIGN KEY (`ref_stock`) REFERENCES `stock` (`ref`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `print_etat`
--
ALTER TABLE `print_etat`
  ADD CONSTRAINT `print_etat_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `print_etat_consommable`
--
ALTER TABLE `print_etat_consommable`
  ADD CONSTRAINT `print_etat_consommable_ibfk_1` FOREIGN KEY (`id_print_consommable`) REFERENCES `print_consommable` (`id_print_consommable`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`id_sous_categorie`) REFERENCES `sous_categorie` (`id_sous_categorie`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_2` FOREIGN KEY (`id_fabriquant`) REFERENCES `fabriquant` (`id_fabriquant`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_3` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_4` FOREIGN KEY (`id_compte_absystech`) REFERENCES `compte_absystech` (`id_compte_absystech`);

--
-- Contraintes pour la table `profil_privilege`
--
ALTER TABLE `profil_privilege`
  ADD CONSTRAINT `profil_privilege_ibfk_1` FOREIGN KEY (`id_profil`) REFERENCES `profil` (`id_profil`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profil_privilege_ibfk_2` FOREIGN KEY (`id_privilege`) REFERENCES `privilege` (`id_privilege`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profil_privilege_ibfk_3` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `relance`
--
ALTER TABLE `relance`
  ADD CONSTRAINT `relance_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `societe`
--
ALTER TABLE `societe`
  ADD CONSTRAINT `FK_societe_4` FOREIGN KEY (`id_secteur_commercial`) REFERENCES `secteur_commercial` (`id_secteur_commercial`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_societe_5` FOREIGN KEY (`id_secteur_geographique`) REFERENCES `secteur_geographique` (`id_secteur_geographique`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_4` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_5` FOREIGN KEY (`id_filiale`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_6` FOREIGN KEY (`id_termes`) REFERENCES `termes` (`id_termes`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_7` FOREIGN KEY (`id_contact_facturation`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_8` FOREIGN KEY (`id_famille`) REFERENCES `famille` (`id_famille`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_9` FOREIGN KEY (`id_apporteur_affaire`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `societe_domaine`
--
ALTER TABLE `societe_domaine`
  ADD CONSTRAINT `societe_domaine_ibfk_1` FOREIGN KEY (`id_domaine`) REFERENCES `domaine` (`id_domaine`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_domaine_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sous_categorie`
--
ALTER TABLE `sous_categorie`
  ADD CONSTRAINT `sous_categorie_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `stat_snap`
--
ALTER TABLE `stat_snap`
  ADD CONSTRAINT `stat_snap_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`id_bon_de_commande_ligne`) REFERENCES `bon_de_commande_ligne` (`id_bon_de_commande_ligne`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `stock_etat`
--
ALTER TABLE `stock_etat`
  ADD CONSTRAINT `stock_etat_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `suivi`
--
ALTER TABLE `suivi`
  ADD CONSTRAINT `suivi_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_ibfk_4` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_ibfk_5` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `suivi_contact`
--
ALTER TABLE `suivi_contact`
  ADD CONSTRAINT `suivi_contact_ibfk_3` FOREIGN KEY (`id_suivi`) REFERENCES `suivi` (`id_suivi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_contact_ibfk_4` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `suivi_notifie`
--
ALTER TABLE `suivi_notifie`
  ADD CONSTRAINT `suivi_notifie_ibfk_1` FOREIGN KEY (`id_suivi`) REFERENCES `suivi` (`id_suivi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_notifie_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `suivi_societe`
--
ALTER TABLE `suivi_societe`
  ADD CONSTRAINT `suivi_societe_ibfk_1` FOREIGN KEY (`id_suivi`) REFERENCES `suivi` (`id_suivi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_societe_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tache`
--
ALTER TABLE `tache`
  ADD CONSTRAINT `tache_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_ibfk_3` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_ibfk_4` FOREIGN KEY (`id_aboutisseur`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_ibfk_5` FOREIGN KEY (`id_suivi`) REFERENCES `suivi` (`id_suivi`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `tache_user`
--
ALTER TABLE `tache_user`
  ADD CONSTRAINT `tache_user_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_user_ibfk_2` FOREIGN KEY (`id_tache`) REFERENCES `tache` (`id_tache`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tracabilite`
--
ALTER TABLE `tracabilite`
  ADD CONSTRAINT `tracabilite_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tracabilite_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tracabilite_ibfk_3` FOREIGN KEY (`id_tracabilite_parent`) REFERENCES `tracabilite` (`id_tracabilite`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id_agence`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_3` FOREIGN KEY (`id_superieur`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `user_ibfk_4` FOREIGN KEY (`id_profil`) REFERENCES `profil` (`id_profil`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_7` FOREIGN KEY (`id_localisation_langue`) REFERENCES `localisation_langue` (`id_localisation_langue`);

--
-- Contraintes pour la table `vhost`
--
ALTER TABLE `vhost`
  ADD CONSTRAINT `vhost_ibfk_2` FOREIGN KEY (`id_hebergement`) REFERENCES `hebergement` (`id_hebergement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vhost_ibfk_3` FOREIGN KEY (`id_nom_de_domaine`) REFERENCES `nom_de_domaine` (`id_nom_de_domaine`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `vue`
--
ALTER TABLE `vue`
  ADD CONSTRAINT `vue_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;


INSERT INTO `user`
(`id_user`, `login`, `password`, `id_societe`, `date`, `date_connection`, `date_activity`, `etat`, `id_profil`, `civilite`, `prenom`, `nom`, `adresse`, `adresse_2`, `adresse_3`, `cp`, `ville`, `gsm`, `email`, `id_pays`, `id_agence`, `custom`, `id_superieur`, `pole`, `id_phone`, `last_news`, `newsletter`, `id_localisation_langue`, `zid`, `temps_partiel`, `api_key`, `password_mail`, `login_mattermost`)
VALUES


INSERT INTO `user` (`id_user`, `login`, `password`, `id_societe`, `date`, `date_connection`, `date_activity`, `etat`, `id_profil`, `civilite`, `prenom`, `nom`, `adresse`, `adresse_2`, `adresse_3`, `cp`, `ville`, `gsm`, `email`, `id_pays`, `id_agence`, `custom`, `id_superieur`, `pole`, `id_phone`, `last_news`, `newsletter`, `id_localisation_langue`, `zid`, `temps_partiel`, `api_key`, `password_mail`, `login_mattermost`) VALUES
(NULL, 'absystech', 'secured', NULL, NULL, '2022-07-13 15:06:25', '2012-08-02 12:22:51', 'normal', NULL, 'M', 'Absys', 'Tech', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', 'a:1:{s:7:\"toolbar\";s:5:\"ferme\";}', NULL, NULL, NULL, '2017-05-22 16:17:27', 'oui', '1', NULL, '1.00', NULL, NULL, ''),
(NULL, 'smortier', 'e28ea5b1936cfb5ba86f72e19fadecc1e969527e82bda97e15a7a82506674eb0', 1, NULL, '2022-08-18 09:56:39', '2012-09-03 14:14:02', 'normal', 1, 'M', 'Sébastien', 'MORTIER', '10, rue Marie Curie', 'Appt. 134', NULL, '59470', 'Wormhout', '0620792429', 'smortier@absystech.fr', 'FR', 1, NULL, NULL, 'system', NULL, '2017-04-13 19:02:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'sol-r'),
(NULL, 'gdegraeve', '440eabbe0075f505387eabfab499cc4d2752ec0ee7f1524138509a4baaa468e2', 1, NULL, '2022-08-16 15:57:27', '2012-08-23 09:38:23', 'normal', 4, 'M', 'Guirec', 'DEGRAEVE', '5 place Philippe de Girard', NULL, NULL, '59800', 'LILLE', '0688681778', 'gdegraeve@absystech-telecom.fr', 'FR', 1, NULL, NULL, 'telecom', NULL, '2017-04-14 12:09:00', 'oui', 1, NULL, '0.80', NULL, NULL, 'guirec'),
(NULL, 'mfleurquin', 'e7285d2e2de8a0a2745760771bab43e75f4d0c8bae173035bc0b5aabd076e0d8', 1, '2012-11-19 08:27:46', '2022-08-18 10:20:09', NULL, 'normal', 10, 'M', 'Morgan', 'FLEURQUIN', '15 rue Louise de Vilmorin', NULL, NULL, '62119', 'Dourges', '0625303642', 'mfleurquin@absystech.fr', 'FR', 1, NULL, NULL, 'dev', NULL, '2017-04-14 08:43:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'morgan'),
(NULL, 'aduquesne', 'aa8c7cf54d3aa4c918dab0330822a26b878f29ba34264c79c9581f3bb395853a', 1, '2014-04-10 12:04:57', '2022-08-18 08:58:34', NULL, 'normal', 4, 'M', 'Alexandre', 'DUQUESNE', NULL, NULL, NULL, NULL, NULL, NULL, 'aduquesne@absystech.fr', 'FR', 1, NULL, NULL, 'system', NULL, '2017-04-14 08:32:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'alex'),
(NULL, 'tpruvost', '6df549b465a1c2a3b7355bd7c06a2eae963f910a582ae31038e4b0ee88f0d3ac', 1, '2015-12-03 09:43:54', '2022-08-17 19:08:23', NULL, 'normal', 1, 'M', 'Thibaut', 'PRUVOST', NULL, NULL, NULL, NULL, NULL, NULL, 'tpruvost@absystech.fr', 'FR', 1, NULL, NULL, 'dev', NULL, '2017-04-14 07:21:00', 'oui', 1, NULL, '1.00', NULL, 'U2FsdGVkX1/LnhTCnfEk4mDeGbV34i38l0Pxi3C+EQ4=', 'thibaut'),
(NULL, 'adefever', 'a75bf34fe8122969087dc4f45ecb63f1338982f07c8f50179ffbbe6df58e0985', 1, '2017-03-01 10:31:18', '2022-08-11 17:17:50', NULL, 'normal', 4, 'M', 'Adrien', 'DEFEVER', NULL, NULL, NULL, NULL, NULL, NULL, 'adefever@absystech-telecom.fr', 'FR', 1, NULL, NULL, 'telecom', NULL, '2017-04-14 09:34:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'adrien'),
(NULL, 'ckupiec', '02a8b6b0a8d67d7c38bd8df4ae6548f66b4d5c42d29f7c52209a7c1fc30461f3', 1, '2018-04-03 08:54:00', '2022-08-18 09:42:20', '2019-04-01 10:08:00', 'normal', 1, 'M', 'Christophe', 'KUPIEC', NULL, NULL, NULL, NULL, NULL, '0610772513', 'ckupiec@absystech.fr', 'FR', 1, NULL, NULL, 'system,telecom', NULL, '2018-04-03 08:53:00', 'non', 1, NULL, '1.00', NULL, NULL, 'kingkuku'),
(NULL, 'opladys', '35243bf246bd7299e43d633726b28267c7a69943f4bcf3b3567a8ee5195ef00c', 1, '2019-06-05 16:32:46', '2022-08-18 09:58:03', NULL, 'normal', 4, 'M', 'Olivier', 'PLADYS', NULL, NULL, NULL, NULL, NULL, NULL, 'opladys@absystech.fr', 'FR', 1, NULL, NULL, 'system', NULL, '2019-06-05 16:31:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'olivier'),
(NULL, 'afachaux', '3526067dd2a52631e25ff2e9f35f004ec8a80bd77e8e561d91c92475ffb38d8c', 1, '2020-01-27 13:23:54', '2022-08-13 11:51:02', NULL, 'normal', 14, 'M', 'Aurélien', 'FACHAUX', NULL, NULL, NULL, NULL, NULL, '0750598581', 'afachaux@absystech.fr', 'FR', 1, NULL, NULL, 'dev', NULL, '2020-01-27 13:20:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'aurelien'),
(NULL, 'lsawicki', '9d7ba28fab35aad0a4fc369c2fc354ffbd02b78e54451ead24acbb5cb2bb692b', 1, '2020-12-29 13:22:07', '2022-08-03 15:05:15', NULL, 'normal', 4, 'M', 'Ludwig', 'SAWICKI', NULL, NULL, NULL, NULL, NULL, NULL, 'lsawicki@absystech-telecom.fr', 'FR', 1, NULL, NULL, 'system,telecom', NULL, '2020-12-29 13:19:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'ludwig'),
(NULL, 'mbourchouk', '4f918d9d9e2cf4ea6aca22d5fff9d4307aa3e19602aff59bfe2295cbbaa5b8c7', 1, '2021-01-18 09:22:17', '2022-08-18 10:15:37', NULL, 'normal', 4, 'M', 'Mohamed', 'BOURCHOUK', NULL, NULL, NULL, NULL, NULL, NULL, 'mbourchouk@absystech.fr', 'FR', 1, NULL, NULL, 'system,telecom', NULL, '2021-01-18 09:19:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'mbourchouk'),
(NULL, 'dbaetens', '4396aebc76387740fb9c0280a726aedf30c38623d8ede9814681be2cc13ea4c7', 1, '2021-05-12 16:36:15', '2022-08-17 14:34:24', NULL, 'normal', 4, 'M', 'Damien', 'BAETENS', NULL, NULL, NULL, NULL, NULL, NULL, 'dbaetens@absystech.fr', 'FR', 1, NULL, NULL, 'system,telecom', NULL, '2021-05-12 16:34:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'damien'),
(NULL, 'fmartin', '2128c2b3300741e64c9009e7a839e7af7dbd8d01304d0c408eed26f5e4006ff3', 1, '2021-10-15 17:09:40', '2022-08-18 09:35:45', NULL, 'normal', 4, 'M', 'Frédéric', 'MARTIN', NULL, NULL, NULL, NULL, NULL, '0676860818', 'fmartin@absystech.fr', 'FR', 1, NULL , NULL, 'system,telecom', NULL, '2021-10-15 17:08:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'frederic'),
(NULL, 'rsantos', '7c1748a7e5419e9f39719b781f95a80939b58bc8d2241e4955d00a0b0f613d3e', 1, '2022-02-14 12:46:18', '2022-08-18 09:50:23', NULL, 'normal', 4, 'M', 'Romain', 'SANTOS', NULL, NULL, NULL, NULL, NULL, '0755593469', 'rsantos@absystech.fr', 'FR', 1, NULL, NULL, 'system,telecom', NULL, '2022-02-14 12:45:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'romain'),
(NULL, 'gskowron', 'c00cbe2d0860dc65a46586b278075fbe480ade1e50bd203cfda2783780e3171b', 1, '2022-04-28 09:37:24', '2022-08-18 09:39:40', NULL, 'normal', 4, 'M', 'Gabriel', 'SKOWRON', NULL, NULL, NULL, NULL, NULL, NULL, 'gskowron@absystech.fr', 'FR', 1, NULL, NULL, 'system,telecom', NULL, '2022-04-28 09:34:00', 'oui', 1, NULL, '1.00', NULL, NULL, 'gabriel');


INSERT INTO societe (`date`,code_groupe,`ref`,ref_comptable,id_owner,id_pays,id_famille,siren,siret,naf,societe,nom_commercial,adresse,adresse_2,adresse_3,cp,ville,id_contact_facturation,facturation_id_pays,facturation_adresse,facturation_adresse_2,facturation_adresse_3,facturation_cp,facturation_ville,reference_tva,iban,id_termes,latitude,longitude,tel,fax,email,web,activite,etat,nb_employe,effectif,id_secteur_geographique,id_contact_commercial,id_secteur_commercial,liens,ca,id_devise,solde,`structure`,capital,date_creation,id_filiale,facturer_le_siege,notes,fournisseur,partenaire,delai_relance,code_fournisseur,cle_externe,divers_1,divers_5,relation,banque,rib,bic,swift,meteo,meteo_calcul,rib_affacturage,iban_affacturage,bic_affacturage,mdp_client,mdp_absystech,recallCounter,dematerialisation,id_apporteur_affaire,derogation_credit_negatif,forfait_dep,est_sous_contrat_maintenance,commentaire_contrat_maintenance,option_contrat_maintenance,date_fin_contrat_maintenance,date_fin_option,id_commercial) VALUES
	 ('2022-08-17 10:50:34',NULL,'ILI22080001',NULL,2,'FR',1,'804970101','80497010100031','6190Z','I2M NETWORK','-','31 RÉSIDENCE PARC CLUB DU GOLF',NULL,NULL,'13100','AIX-EN-PROVENCE',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR61804970101',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Autres activités de télécommunication','actif',NULL,NULL,NULL,NULL,NULL,NULL,'250210',1,0.00,'SARL',10500,'2014-10-08',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'4f5d','prospect',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'w6R3161Lp','LPMmnryMQ',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,2),
	 ('2022-08-19 06:43:18',NULL,'IOI22080001',NULL,3,'FR',1,'339955759','33995575900024','7112B','ACIER BETON ARME CONSEILS','-','27 RUE  PAUL DUBRULLE',NULL,NULL,'59810','Lesquin',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR74339955759',NULL,NULL,NULL,NULL,'0321440113','0321442606','so@abaclievin.fr',NULL,'Ingénierie, études techniques','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'SAS',94600,'1987-02-23',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'8b59','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'afXJfs6cv','Xxwr9KPUq',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:44:30',NULL,'IOI22080002',NULL,115,'FR',1,'326831567','32683156700010','4332B','ALTOMARE ALTALU','ALTOMARE ALTALU','41 CITÉ DES ATELIERS',NULL,NULL,'62820','LIBERCOURT',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR89326831567',NULL,NULL,NULL,NULL,'0321371864','0321740527','admin@altomare.fr',NULL,'Travaux de menuiserie métallique et serrurerie','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'(Autre) SA à directoire',94500,'1983-04-12',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'a1c2','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'JAj9bpj7h','NfVCPvHRN',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:46:22',NULL,'IOI22080003',NULL,115,'FR',1,'784027377','78402737700018','8531Z','ASS AMIS DE L INSTITUT STE IDE','COLLEGE PRIVE STE IDE','75 RUE EMILE ZOLA',NULL,NULL,'62300','LENS',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR50784027377',NULL,NULL,NULL,NULL,'0321147247','0321786295','herve.leplus@sainteide.org',NULL,'Enseignement secondaire général','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'Association déclarée',NULL,'1970-01-01',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'dd96','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'r5qMF74wr','pZL43c7Qv',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:47:00',NULL,'IOI22080004',NULL,115,'FR',1,'453206831','45320683100050','4662Z','ATEC  - DUNKERQUE','-','ZI PETITE SYNTHE, 21 RUE ARMAND CARREL',NULL,NULL,'59640','DUNKERQUE',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR23453206831',NULL,NULL,NULL,NULL,'0328250500',NULL,NULL,NULL,'Commerce de gros (commerce interentreprises) de machines-outils','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'SAS',500000,'2004-05-06',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'9ed0','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'uKv5YyRja','9cLvWrr2n',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:47:55',NULL,'IOI22080005',NULL,115,'FR',1,'443879994','44387999400020','3320D','ATRIS COMMUNICATION','-','28 RUE EDGARD SELLIER',NULL,NULL,'62800','LIEVIN',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR35443879994',NULL,NULL,NULL,NULL,'0321442757','0321447235','eric@atris.fr',NULL,'Installation d''équipements électriques, de matériels électroniques et optiques ou d''autres matériels','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'SAS',7600,'2002-11-05',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'6618','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pDHfJAFNC','WSjP91Z2n',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:48:22',NULL,'IOI22080006',NULL,115,'FR',1,'215905506','21590550600014','8411Z','COMMUNE DE SALOME','MAIRIE','7 RUE PASTEUR',NULL,NULL,'59496','SALOME',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR00215905506',NULL,NULL,NULL,NULL,'0320502793','0320290548',NULL,NULL,'Administration publique générale','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'Commune et commune nouvelle',NULL,'1970-01-01',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'be8a','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'9m5BAwjYa','N1pcpqCct',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:48:56',NULL,'IOI22080007',NULL,115,'FR',1,'893233999','89323399900018','4711D','POIXAMAG','INTERMARCHE','2 RUE DE MENESVILLERS',NULL,NULL,'80290','POIX-DE-PICARDIE',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR88893233999',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Supermarchés','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'SAS',390000,'2021-01-22',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'5748','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'tK8SRMuhx','v9CdYRJ2f',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:50:07',NULL,'IOI22080008',NULL,115,'FR',1,'369200761','36920076100020','2562B','REALISATION MECANIQUE ET RECTIFICATION INDUSTRIES','RMR INDUSTRIES','PARC DES INDUSTRIES ARTOIS FLANDRES, 315 BOULEVARD SUD',NULL,NULL,'62138','BILLY-BERCLAU',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR35369200761',NULL,NULL,NULL,NULL,'0321799897','0321088301','comptabilite@rmr-industries.com',NULL,'Mécanique industrielle','actif',NULL,NULL,NULL,NULL,NULL,NULL,'7014964',1,0.00,'SAS',150000,'1969-11-20',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'ed2e','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ZxpMPvheN','9hrd7sbCK',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:50:43',NULL,'IOI22080009',NULL,115,'FR',1,'420205130','42020513000032','7112B','ROBOTIQUE MAINTENANCE SERVICES','-','PARC ACTIVITES LES OISEAUX','RUE DES COLIBRIS',NULL,'62300','LENS',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR07420205130',NULL,NULL,NULL,NULL,'0391847120','0321294496','f.dusautois@rmsfrance.fr',NULL,'Ingénierie, études techniques','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'SARL',450000,'1998-09-24',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'7ca0','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'qsrUBTmYr','9LqAhBfy1',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
   ('2022-08-19 06:51:35',NULL,'IOI22080010',NULL,115,'FR',1,'325634665','32563466500021','4332A','DANIEL GARCON','-','3 ZA LES ALOUETTES',NULL,NULL,'62223','SAINT-NICOLAS',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR32325634665',NULL,NULL,NULL,NULL,'0321503934','0321503923','sandrine.laine@menuiseriegarcon.fr',NULL,'Travaux de menuiserie bois et PVC','actif',NULL,NULL,NULL,NULL,NULL,NULL,'1945572',1,0.00,'SARL',76000,'1982-10-27',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'0e35','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'f8vnnh59c','SQKE9uNev',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:52:25',NULL,'IOI22080011',NULL,115,'FR',1,'438606352','43860635200021','4399C','GROUPEMENT DES PROFESSIONNELS DU BATIMENT','SAS GPBAT','20 RUE FREYCINET',NULL,NULL,'62300','LENS',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR03438606352',NULL,NULL,NULL,NULL,'0391842510','0321422573','gpbat@servicesbtp.com',NULL,'Travaux de maçonnerie générale et gros œuvre de bâtiment','actif',NULL,NULL,NULL,NULL,NULL,NULL,'7538468',1,0.00,'SAS',44800,'2001-07-20',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'318a','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'QqCRCCtRH','VhmNGfJs8',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:52:57',NULL,'IOI22080012',NULL,115,'FR',1,'418955027','41895502700028','0111Z','SCEA DE LA CHAPELLE','-','13 RUE DE LA CHAPELLE',NULL,NULL,'62161','DUISANS',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR09418955027',NULL,NULL,NULL,NULL,'0321504545','0321485403','comptabilite@bouttemy.immo',NULL,'Culture de céréales (à l''exception du riz), de légumineuses et de graines oléagineuses','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'Société civile d''exploitation agricole',218100,'1998-06-03',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'11cc','client',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'F5ThyNRNQ','b8GBeR8BB',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115),
	 ('2022-08-19 06:53:26',NULL,'IOI22080013',NULL,115,'FR',1,'309585594','30958559400042','4939A','SA VOYAGES MULLIE','-','QUADRAPARC, 246 RUE DE CONDE',NULL,NULL,'62160','GRENAY',NULL,'FR',NULL,NULL,NULL,NULL,NULL,'FR30309585594',NULL,NULL,NULL,NULL,'0321290236','0321729427','loic.chevalier@voyagemullie.com',NULL,'Transports routiers réguliers de voyageurs','actif',NULL,NULL,NULL,NULL,NULL,NULL,'0',1,0.00,'SAS',37022,'1977-03-07',NULL,'non',NULL,'non','non',NULL,NULL,NULL,NULL,'bd33','prospect',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'4X6mpTXmB','bUqvTA6Fy',0,'non',NULL,'non',1.00,NULL,NULL,'aucune',NULL,NULL,115);