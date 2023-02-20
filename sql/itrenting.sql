
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `accompagnateur` (
  `id_accompagnateur` mediumint(8) UNSIGNED NOT NULL,
  `accompagnateur` varchar(255) NOT NULL,
  `portail_associe` enum('Midas','Optic_2000','Norauto') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `affaire` (
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('devis','commande','facture','terminee','perdue','demande_refi','facture_refi','terminee_contentieux') NOT NULL DEFAULT 'devis',
  `ref` varchar(12) NOT NULL,
  `ref_externe` varchar(50) DEFAULT NULL,
  `etat_cmd_externe` enum('attente','valide') DEFAULT NULL,
  `reference_refinanceur` varchar(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_filiale` mediumint(8) UNSIGNED NOT NULL DEFAULT 4225,
  `affaire` varchar(255) NOT NULL,
  `taux_refi` decimal(6,3) UNSIGNED DEFAULT NULL,
  `taux_refi_reel` decimal(6,3) UNSIGNED DEFAULT NULL,
  `id_apporteur` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_commercial` mediumint(8) UNSIGNED DEFAULT NULL,
  `assurance_fixe` decimal(5,2) UNSIGNED DEFAULT NULL,
  `assurance_portable` decimal(5,2) UNSIGNED DEFAULT NULL,
  `total_depense` decimal(8,2) UNSIGNED DEFAULT NULL,
  `total_recette` decimal(8,2) UNSIGNED DEFAULT NULL,
  `valeur_residuelle` decimal(10,2) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `forecast` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `id_parent` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Remplacé',
  `id_fille` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Lien vers l''affaire fille qui reprend l''actif de cette affaire.',
  `date_installation_prevu` date DEFAULT NULL,
  `date_installation_reel` date DEFAULT NULL,
  `date_livraison_prevu` date DEFAULT NULL COMMENT 'date_installation_prevu + 3 semaines',
  `date_garantie` date DEFAULT NULL,
  `nature` enum('affaire','AR','avenant','vente') NOT NULL DEFAULT 'affaire' COMMENT 'Nature de cette affaire',
  `mail_signature` enum('oui','non') NOT NULL DEFAULT 'non',
  `date_signature` datetime DEFAULT NULL,
  `signataire` varchar(150) DEFAULT NULL,
  `mail_document` enum('oui','non') NOT NULL DEFAULT 'non',
  `RIB` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `IBAN` varchar(35) CHARACTER SET utf8 DEFAULT NULL,
  `BIC` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `RUM` varchar(32) DEFAULT NULL,
  `nom_banque` varchar(24) DEFAULT NULL,
  `ville_banque` varchar(35) DEFAULT NULL,
  `date_previsionnelle` int(3) NOT NULL DEFAULT 0,
  `date_recettage_cablage` date DEFAULT NULL,
  `date_ouverture` date DEFAULT NULL,
  `type_affaire` enum('normal','2SI') NOT NULL DEFAULT 'normal',
  `langue` enum('FR','ES') NOT NULL DEFAULT 'ES',
  `id_contract_sellandsign` int(10) UNSIGNED DEFAULT NULL,
  `site_associe` enum('it_renting') DEFAULT NULL,
  `etat_comite` enum('accepte','refuse','attente','accord_non utilise','favorable_cession') NOT NULL DEFAULT 'attente',
  `provenance` enum('it_renting','vendeur','partenaire') DEFAULT NULL,
  `pieces` enum('NOK','OK') DEFAULT NULL,
  `date_verification` date DEFAULT NULL,
  `id_partenaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `commentaire_facture` varchar(500) DEFAULT NULL,
  `commentaire_facture2` varchar(500) DEFAULT NULL,
  `commentaire_facture3` varchar(500) DEFAULT NULL,
  `tel_signature` varchar(20) DEFAULT NULL,
  `mail_signataire` varchar(100) DEFAULT NULL,
  `adresse_livraison` text DEFAULT NULL,
  `adresse_livraison_2` text DEFAULT NULL,
  `adresse_livraison_3` text DEFAULT NULL,
  `cp_adresse_livraison` varchar(5) DEFAULT NULL,
  `ville_adresse_livraison` varchar(100) DEFAULT NULL,
  `pays_livraison` char(2) NOT NULL DEFAULT 'ES',
  `adresse_facturation` text DEFAULT NULL,
  `adresse_facturation_2` text DEFAULT NULL,
  `adresse_facturation_3` text DEFAULT NULL,
  `cp_adresse_facturation` varchar(5) DEFAULT NULL,
  `ville_adresse_facturation` varchar(100) DEFAULT NULL,
  `pays_facturation` char(2) DEFAULT 'FR',
  `id_magasin` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_panier` mediumint(8) UNSIGNED DEFAULT NULL,
  `hash_panier` varchar(32) DEFAULT NULL,
  `snapshot_pack_produit` text DEFAULT NULL,
  `ref_sign` varchar(50) DEFAULT NULL COMMENT 'Reference de signature chez le prestataire de signature',
  `ref_mandate` varchar(50) DEFAULT NULL COMMENT 'Reference du mandat chez le prestataire de signature',
  `subscriber_reference` varchar(80) DEFAULT NULL COMMENT 'Reference du client chez le prestataire de signature',
  `prestataire_signature` enum('sellandsign','slimpay') DEFAULT NULL COMMENT 'Permet d''identifier quel prestataire de signature est utilisé pour cette affaire',
  `prestataire_paiement` enum('slimpay','mercanetv1','mercanetv2','adyen') DEFAULT NULL COMMENT 'Permet d''identifier quel prestataire de paiement est utilisé pour cette affaire',
  `vendeur` varchar(255) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `id_type_affaire` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `affaire_etat`
--

CREATE TABLE `affaire_etat` (
  `id_affaire_etat` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `etat` enum('reception_demande','reception_pj','preparation_commande','refus_dossier','expedition_en_cours','colis_recu','valide_administratif','comite_cleodis_valide','comite_cleodis_refuse','refus_administratif','autre','envoi_mail_relance','signature_document','signature_document_ok','finalisation_souscription','paiement_init','paiement_ok') NOT NULL,
  `commentaire` varchar(500) DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `agence`
--

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
  `fax` varchar(20) DEFAULT NULL,
  `objectif_devis_reseaux` int(11) NOT NULL DEFAULT 0,
  `objectif_devis_autre` int(11) NOT NULL DEFAULT 0,
  `objectif_mep_reseaux` int(11) NOT NULL DEFAULT 0,
  `objectif_mep_autre` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `assurance`
--

CREATE TABLE `assurance` (
  `id_assurance` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(16) NOT NULL,
  `date` date DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `montant` decimal(8,2) UNSIGNED DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `asterisk`
--

CREATE TABLE `asterisk` (
  `id_asterisk` mediumint(8) UNSIGNED NOT NULL,
  `asterisk` varchar(32) NOT NULL,
  `host` varchar(32) NOT NULL,
  `url_webservice` varchar(255) NOT NULL,
  `login` varchar(16) NOT NULL,
  `password` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Gestion des serveurs asterisk';

--
-- Déchargement des données de la table `asterisk`
--

INSERT INTO `asterisk` (`id_asterisk`, `asterisk`, `host`, `url_webservice`, `login`, `password`) VALUES
(3, 'Cleodis ATT Appliance', 'cleodis-sdsl.absystech.net', 'https://cleodis-sdsl.absystech.net/apps/asteriskadmin/webservices/', 'cleodis', 'ffe311f4b9c010ce5f8c0d9d7eef6614');

-- --------------------------------------------------------

--
-- Structure de la table `base_de_connaissance`
--

CREATE TABLE `base_de_connaissance` (
  `id_base_de_connaissance` mediumint(8) UNSIGNED NOT NULL,
  `base_de_connaissance` varchar(255) NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `last_seen` datetime DEFAULT NULL,
  `texte` text NOT NULL,
  `frequentation` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `batch_facture`
--

CREATE TABLE `batch_facture` (
  `date` varchar(10) DEFAULT NULL,
  `ref` varchar(35) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bon_de_commande`
--

CREATE TABLE `bon_de_commande` (
  `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `date_reception_fournisseur` date DEFAULT NULL,
  `date_reception_prevision` tinyint(4) DEFAULT 0,
  `date_livraison` date DEFAULT NULL COMMENT 'date de livraison chez le client',
  `date_livraison_prevision` tinyint(4) DEFAULT 0,
  `date_installation` date DEFAULT NULL COMMENT 'date d''installation pour le client',
  `date_installation_prevision` tinyint(4) DEFAULT 0,
  `date_pv_install` date DEFAULT NULL,
  `livraison_partielle` tinyint(1) DEFAULT NULL,
  `bon_de_commande` varchar(256) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('envoyee','terminee','fnp','stock','a_regler','fae') NOT NULL DEFAULT 'envoyee',
  `date` date NOT NULL,
  `payee` enum('oui','non') NOT NULL DEFAULT 'non',
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_commande` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `destinataire` varchar(64) NOT NULL,
  `adresse` varchar(64) NOT NULL DEFAULT '',
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(5) NOT NULL DEFAULT '',
  `ville` varchar(32) NOT NULL DEFAULT '',
  `id_pays` varchar(2) NOT NULL DEFAULT 'BE',
  `livraison_destinataire` varchar(250) DEFAULT NULL,
  `livraison_adresse` varchar(500) DEFAULT NULL,
  `livraison_cp` int(6) DEFAULT NULL,
  `livraison_ville` varchar(150) DEFAULT NULL,
  `tva` decimal(4,3) UNSIGNED NOT NULL,
  `commentaire` text DEFAULT NULL,
  `date_livraison_demande` date DEFAULT NULL,
  `date_installation_demande` date DEFAULT NULL,
  `date_livraison_estime` date DEFAULT NULL,
  `date_livraison_prevue` date DEFAULT NULL,
  `date_livraison_reelle` date DEFAULT NULL,
  `date_installation_prevue` date DEFAULT NULL,
  `date_installation_reele` date DEFAULT NULL COMMENT 'reele : se nomme comme ça pour ne pas entrer en conflit avec la même date d''install mais de la table affaire',
  `date_limite_rav` date DEFAULT NULL,
  `export_cegid` datetime DEFAULT NULL,
  `export_servantissimmo` datetime DEFAULT NULL,
  `envoye_par_mail` date DEFAULT NULL COMMENT 'Permet de savoir si le BDC a été envoyé par mail et à quelle date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bon_de_commande_ligne`
--

CREATE TABLE `bon_de_commande_ligne` (
  `id_bon_de_commande_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `produit` varchar(500) NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `prix` decimal(10,2) UNSIGNED DEFAULT NULL,
  `id_commande_ligne` mediumint(8) UNSIGNED NOT NULL,
  `caracteristique` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `bon_de_commande_non_envoyes`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `bon_de_commande_non_envoyes` (
`id_bon_de_commande` mediumint(8) unsigned
,`ref` varchar(32)
,`prix` decimal(10,2)
,`client` varchar(128)
,`ref_affaire` varchar(12)
,`affaire` varchar(255)
,`envoye_par_mail` date
,`id_fournisseur` mediumint(8) unsigned
,`fournisseur` varchar(128)
);

-- --------------------------------------------------------

--
-- Structure de la table `campagne`
--

CREATE TABLE `campagne` (
  `id_campagne` smallint(6) NOT NULL,
  `campagne` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` mediumint(8) UNSIGNED NOT NULL,
  `categorie` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `id_client` mediumint(8) UNSIGNED NOT NULL,
  `client` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `etat` enum('actif','inactif') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'inactif',
  `client_id` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Clé sur 32 carac non crypté',
  `client_secret` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Clé sur 32 caractères bcrypté'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collaborateur`
--

CREATE TABLE `collaborateur` (
  `id_collaborateur` int(10) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `enable` tinyint(1) NOT NULL,
  `id_magasin` mediumint(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `colonne`
--

CREATE TABLE `colonne` (
  `id_colonne` mediumint(8) UNSIGNED NOT NULL,
  `id_vue` mediumint(8) UNSIGNED NOT NULL,
  `champs` varchar(256) DEFAULT NULL,
  `taille` smallint(4) UNSIGNED DEFAULT NULL,
  `tri` enum('asc','desc') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `comite`
--

CREATE TABLE `comite` (
  `id_comite` mediumint(8) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `id_refinanceur` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `activite` varchar(250) DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valeur_residuelle` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pourcentage_materiel` decimal(5,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `pourcentage_logiciel` decimal(5,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `description` varchar(255) NOT NULL,
  `marque_materiel` varchar(255) DEFAULT NULL,
  `reponse` date DEFAULT NULL,
  `etat` enum('refuse','favorable_cession','accord_portage_recherche_cession','accord_portage_recherche_cession_groupee','accord_non utilise','accepte','en_attente') NOT NULL DEFAULT 'en_attente',
  `taux` decimal(5,2) DEFAULT NULL,
  `coefficient` varchar(16) DEFAULT NULL,
  `encours` decimal(10,2) DEFAULT NULL,
  `frais_de_gestion` decimal(10,2) DEFAULT NULL,
  `validite_accord` date DEFAULT NULL,
  `observations` varchar(255) DEFAULT NULL,
  `loyer_actualise` decimal(10,2) DEFAULT NULL,
  `date_cession` date DEFAULT NULL,
  `duree_refinancement` varchar(20) DEFAULT NULL,
  `note` varchar(11) DEFAULT NULL,
  `limite` varchar(11) DEFAULT NULL,
  `ca` varchar(15) DEFAULT NULL,
  `resultat_exploitation` varchar(15) DEFAULT NULL,
  `capital_social` varchar(15) DEFAULT NULL,
  `capitaux_propres` varchar(15) DEFAULT NULL,
  `dettes_financieres` varchar(15) DEFAULT NULL,
  `maison_mere1` varchar(150) DEFAULT NULL,
  `maison_mere2` varchar(150) DEFAULT NULL,
  `maison_mere3` varchar(150) DEFAULT NULL,
  `maison_mere4` varchar(150) DEFAULT NULL,
  `date_compte` varchar(15) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `date_creation` varchar(12) DEFAULT NULL,
  `decisionComite` varchar(100) DEFAULT NULL,
  `notifie_utilisateur` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

CREATE TABLE `commande` (
  `id_commande` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(16) CHARACTER SET utf8 NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `commande` varchar(200) DEFAULT NULL,
  `prix_achat` decimal(10,2) UNSIGNED DEFAULT NULL,
  `prix` decimal(10,2) DEFAULT 0.00,
  `prix_sans_tva` decimal(8,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `id_devis` mediumint(8) UNSIGNED DEFAULT NULL,
  `etat` enum('non_loyer','mis_loyer','prolongation','AR','arreter','vente','restitution','mis_loyer_contentieux','prolongation_contentieux','restitution_contentieux','arreter_contentieux','non_loyer_contentieux') CHARACTER SET utf8 NOT NULL DEFAULT 'non_loyer',
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL,
  `clause_logicielle` enum('oui','non') DEFAULT 'non',
  `date_debut` date DEFAULT NULL,
  `type` enum('prelevement','mandat','virement') NOT NULL DEFAULT 'prelevement',
  `retour_prel` date DEFAULT NULL COMMENT 'date de retour du document "autorisation de prélevement"',
  `mise_en_place` date DEFAULT NULL COMMENT 'date de mise en place',
  `retour_pv` date DEFAULT NULL COMMENT 'date de retour du pv',
  `retour_contrat` date DEFAULT NULL,
  `date_evolution` date DEFAULT NULL COMMENT 'Date de début + durée totale de l''affaire',
  `date_arret` date DEFAULT NULL,
  `date_demande_resiliation` date DEFAULT NULL,
  `date_prevision_restitution` date DEFAULT NULL,
  `date_restitution_effective` date DEFAULT NULL,
  `date_demande_reprise_broker` date DEFAULT NULL COMMENT 'date de demande reprise au broker'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `commande_ligne`
--

CREATE TABLE `commande_ligne` (
  `id_commande_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
  `produit` varchar(500) COLLATE utf8_swedish_ci NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix_achat` decimal(10,2) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_affaire_provenance` mediumint(8) UNSIGNED DEFAULT NULL,
  `serial` varchar(2500) COLLATE utf8_swedish_ci DEFAULT NULL,
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui' COMMENT 'Flag qui permet',
  `neuf` enum('oui','non') COLLATE utf8_swedish_ci DEFAULT 'oui',
  `date_achat` date DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `duree` int(11) DEFAULT NULL,
  `loyer` float(6,3) DEFAULT NULL,
  `ean` varchar(14) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_pack_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_sous_categorie` mediumint(8) UNSIGNED DEFAULT NULL,
  `pack_produit` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `sous_categorie` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_categorie` mediumint(8) UNSIGNED DEFAULT NULL,
  `categorie` varchar(64) COLLATE utf8_swedish_ci DEFAULT NULL,
  `commentaire_produit` varchar(512) COLLATE utf8_swedish_ci DEFAULT NULL,
  `visible_sur_site` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non',
  `visible_pdf` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui',
  `ordre` int(11) NOT NULL DEFAULT 1,
  `frequence_fournisseur` enum('sans','mois','bimestre','trimestre','quadrimestre','semestre','an') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'sans' COMMENT 'Frequence des commandes fournisseurs',
  `caracteristique` text COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `conge`
--

CREATE TABLE `conge` (
  `id_conge` mediumint(8) UNSIGNED NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `conge` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `type` enum('paye','sans_solde','maladie','autre') CHARACTER SET utf8 NOT NULL DEFAULT 'paye',
  `periode` enum('am','pm','jour','autre') CHARACTER SET utf8 NOT NULL DEFAULT 'autre',
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('nok','ok','en_cours','annule','attente_jerome','attente_christophe') CHARACTER SET utf8 NOT NULL DEFAULT 'en_cours',
  `raison` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `commentaire` varchar(255) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `constante`
--

CREATE TABLE `constante` (
  `id_constante` smallint(5) UNSIGNED NOT NULL,
  `constante` varchar(128) NOT NULL,
  `valeur` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `constante`
--

INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES
(1, '__SOCIETE__', 'IT Renting'),
(2, '__RECORD_BY_PAGE__', '30'),
(3, '__CLEODIS_ASSURANCE_FIXE__', '0.33'),
(4, '__CLEODIS_ASSURANCE_PORTABLE__', '0.66'),
(6, '__WEBDAV_PATH__', '/var/dav/'),
(7, '__TVA__', '1.21'),
(8, '__GMAP_KEY__', 'ABQIAAAAC8ZV9rQjYy1QRp1TdgdOQRQ--2Ve0yp6hOXFbJIJqHnuUTSunRSLJkPYUGwP_0lCufTnIBJAUtMUxA'),
(9, '__ADRESSE_FACTURATION__', 'RENTING INFORMATICO Y TECNOLOGICO SA, CALLE DE LA GRANJA 82, 2108 AMCOBENDAS'),
(10, '__WEBSITE_CODENAME__', 'IT RENTING'),
(11, '__DEFAULT_EMAIL__', 'info@itrenting.com'),
(12, '__MAIL_SOCIETE__', 'contact@cleodis.fr'),
(13, '__SEUIL_AUTOCOMPLETION__', '30'),
(14, '__RECORD_BY_PAGE_MORE__', '100'),
(16, '__ICS__', 'BE31 ZZZ 0811464584'),
(17, '__CREDIT_SAFE_LOGIN__', 'cleodisLive'),
(18, '__CREDIT_SAFE_PWD__', 'cleodis0236523'),
(21, '__CLEOSCOPE_WEB_PATH__', 'https://cleoscope.cleodis.com/'),
(22, '__API_CREDIT_SAFE_USERNAME__', 'jerome.loison@cleodis.com'),
(23, '__API_CREDIT_SAFE_PASSWORD__', 'RtIZULINHO;G0@5IKRFP'),
(24, '__API_CREDIT_SAFE_BASEURL__', 'https://connect.creditsafe.com/v1'),
(25, '__API_CREDIT_PAYS_RECHERCHE__', 'SP'),
(26, '__URL_ESPACE_CLIENT_BACK__', 'https://boulanger-back-espaceclient.cleodis.com'),
(27, '__USER_VALIDATEUR_COMITE__', 'jloison,cloison,pcaminel,tdelattre,jraposo,eperez'),
(28, '__DESTINATAIRE_NOTIFIE_TACHE_AFFAIRE_PARTENAIRE__', 'lmoussy,amussard'),
(29, '__DESTINATAIRE_NOTIFIE_TACHE_AFFAIRE_PARTENAIRE__', 'lmoussy,amussard'),
(30, '__NOTIFIE_PASSAGE_ARRETEE_AFFAIRE__', 'lmoussy,amussard'),
(31, '__NOTIFIE_DATE_RESTITUTION_DEPASSEE__', 'lmoussy,amussard'),
(32, '__NOTIFIE_STOP_CONTRAT__', 'lmoussy,amussard'),
(33, '__NOTIFIE_AVIS_CREDIT_SOCIETE_UPDATE__', 'lmoussy,amussard'),
(34, '__NOTIFIE_CREATE_TACHE_PARTENAIRE__', 'lmoussy,amussard'),
(35, '__NOTIFIE_VALIDATION_COMITE__', 'lmoussy,amussard'),
(36, '__NOTIFIE_COMMENTAIRE_AFFAIRE_PARTENAIRE__', 'tdelattre,lmoussy,amussard'),
(37, '__SUIVI_NOTIFIE_MEP__', 'compta'),
(38, '__SUIVI_NOTIFIE_UPLOAD_FACTURE_PARTENAIRE__', 'compta'),
(39, '__NOTIFIE_UPLOAD_PJ_PARTENAIRE__', 'lmoussy,amussard'),
(40, '__EMAIL_NOTIFIE_UPLOAD_FILE_PARTENAIRE__', 'adv@cleodis.com'),
(41, '__REGLE_MDP__', '/(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\\W)(.{8,})/'),
(42, '__REGLE_MDP_ERROR_MSG__', 'Le mot de passe doit être d\'au moins 8 caratères avec 1 lettre en majuscule, 1 lettre en minuscule, 1 chiffre et 1 caractere spécial'),
(43, '__MAIL_STATUT_CONTRAT_CHANGE__', 'jl@itrenting.com'),
(44, '__NOTIFIE_PRELEVEMENT_IMPAYEE__', '1'),
(45, '__USER_NOTIFIE_SUIVI_CONTENTIEUX__', 'compta');

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE `contact` (
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `civilite` enum('M','Mme','Mlle') CHARACTER SET utf8 DEFAULT 'M',
  `nom` varchar(100) CHARACTER SET utf8 NOT NULL,
  `prenom` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `langue` enum('FR','NL') NOT NULL DEFAULT 'FR',
  `private` enum('oui','non') NOT NULL DEFAULT 'non',
  `adresse` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_2` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_3` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cp` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `ville` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `id_pays` varchar(2) NOT NULL DEFAULT 'BE',
  `tel` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `gsm` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `fax` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `tel_perso` varchar(20) DEFAULT NULL,
  `email_perso` varchar(255) DEFAULT NULL,
  `gsm_perso` varchar(20) DEFAULT NULL,
  `fonction` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `departement` varchar(255) DEFAULT NULL,
  `anniversaire` date DEFAULT NULL,
  `loisir` varchar(255) DEFAULT NULL,
  `assistant` varchar(128) DEFAULT NULL,
  `assistant_tel` varchar(32) DEFAULT NULL,
  `tel_autres` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_autres` varchar(255) DEFAULT NULL,
  `forecast` enum('0','20','40','60','80','100') NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `cle_externe` varchar(32) DEFAULT NULL,
  `divers_1` varchar(255) DEFAULT NULL,
  `divers_2` varchar(255) NOT NULL DEFAULT '0',
  `divers_3` enum('Utilisateur','Superviseur','Fournisseur','Administrateur','Dieu') DEFAULT NULL,
  `divers_4` varchar(255) DEFAULT NULL COMMENT 'login pour portail Midas',
  `divers_5` varchar(255) DEFAULT NULL COMMENT 'pwd pour portail Midas',
  `disponibilite` set('LunAM','LunPM','MarAM','MarPM','MerAM','MerPM','JeuAM','JeuPM','VenAM','VenPM','SamAM','SamPM') CHARACTER SET utf8 DEFAULT NULL,
  `code_adherent` varchar(15) DEFAULT NULL,
  `num_secu` varchar(25) DEFAULT NULL COMMENT 'Numéro de Sécurité sociale',
  `num_siret_perso` varchar(50) DEFAULT NULL COMMENT 'Numéro de siret personnel',
  `est_dirigeant` enum('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Est un dirigeant de la société (récupéré de l''interogation CreditSafe)',
  `login` varchar(50) DEFAULT NULL,
  `pwd` varchar(64) DEFAULT NULL,
  `pwd_client` varchar(64) DEFAULT NULL,
  `compte` enum('administrateur','utilisateur') DEFAULT NULL,
  `situation_maritale` varchar(255) DEFAULT NULL,
  `situation_pro` varchar(255) DEFAULT NULL,
  `situation_perso` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `demande_refi`
--

CREATE TABLE `demande_refi` (
  `id_demande_refi` mediumint(8) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `id_refinanceur` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `valeur_residuelle` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pourcentage_materiel` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `pourcentage_logiciel` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `description` varchar(255) NOT NULL,
  `marque_materiel` varchar(255) DEFAULT NULL,
  `reponse` date DEFAULT NULL,
  `etat` enum('en_attente','en_attente_etls','accepte','refuse','valide','accord_non utilise','etude','passage_comite') NOT NULL DEFAULT 'en_attente',
  `taux` decimal(5,2) DEFAULT NULL,
  `coefficient` varchar(16) DEFAULT NULL,
  `encours` decimal(10,2) DEFAULT NULL,
  `frais_de_gestion` decimal(10,2) DEFAULT NULL,
  `validite_accord` date DEFAULT NULL,
  `observations` varchar(255) DEFAULT NULL,
  `loyer_actualise` decimal(10,2) DEFAULT NULL,
  `date_cession` date DEFAULT NULL,
  `duree_refinancement` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `departement`
--

CREATE TABLE `departement` (
  `id_departement` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(2) NOT NULL,
  `id_region` tinyint(3) UNSIGNED NOT NULL,
  `departement` varchar(64) NOT NULL,
  `chef_lieu` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

CREATE TABLE `devis` (
  `id_devis` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(16) CHARACTER SET utf8 NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_filiale` mediumint(8) UNSIGNED NOT NULL DEFAULT 4225,
  `prix` decimal(8,2) DEFAULT 0.00,
  `prix_sans_tva` decimal(8,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `devis` varchar(255) CHARACTER SET utf8 NOT NULL,
  `type_contrat` enum('lld','lrp','presta','loa','logiciel','speciaux','vente','mat_clt','cout_copie') NOT NULL,
  `type_devis` enum('normal') NOT NULL DEFAULT 'normal',
  `first_date_accord` date DEFAULT NULL,
  `date_accord` date DEFAULT NULL,
  `etat` enum('gagne','attente','perdu') CHARACTER SET utf8 DEFAULT 'attente',
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_opportunite` mediumint(8) UNSIGNED DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL,
  `validite` date NOT NULL,
  `loyer_unique` enum('oui','non') NOT NULL DEFAULT 'non',
  `prix_achat` decimal(8,2) UNSIGNED DEFAULT 0.00,
  `raison_refus` text DEFAULT NULL,
  `offre_partenaire` varchar(80) DEFAULT NULL,
  `commentaire_offre_partenaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `devise`
--

CREATE TABLE `devise` (
  `id_devise` mediumint(8) UNSIGNED NOT NULL,
  `devise` varchar(32) DEFAULT NULL,
  `symbole` varchar(8) DEFAULT NULL,
  `ratio_eur` float UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `devise`
--

INSERT INTO `devise` (`id_devise`, `devise`, `symbole`, `ratio_eur`) VALUES
(1, 'Euro', '€', 1);

-- --------------------------------------------------------

--
-- Structure de la table `devis_ligne`
--

CREATE TABLE `devis_ligne` (
  `id_devis_ligne` mediumint(8) UNSIGNED NOT NULL,
  `type` enum('fixe','portable','sans_objet','immateriel') CHARACTER SET latin1 NOT NULL DEFAULT 'sans_objet',
  `id_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
  `produit` varchar(512) COLLATE utf8_swedish_ci NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix_achat_ttc` decimal(10,2) NOT NULL DEFAULT 0.00,
  `prix_achat` decimal(8,2) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_affaire_provenance` mediumint(8) UNSIGNED DEFAULT NULL,
  `serial` varchar(512) COLLATE utf8_swedish_ci DEFAULT NULL,
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui' COMMENT 'Flag qui permet',
  `visibilite_prix` enum('visible','invisible') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'visible' COMMENT 'Visibilité',
  `neuf` enum('oui','non') COLLATE utf8_swedish_ci DEFAULT 'oui',
  `date_achat` date DEFAULT NULL,
  `ref_simag` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `options` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non',
  `duree` int(11) DEFAULT NULL,
  `loyer` float(6,3) DEFAULT NULL,
  `ean` varchar(14) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_pack_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_sous_categorie` mediumint(8) UNSIGNED DEFAULT NULL,
  `pack_produit` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `sous_categorie` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_categorie` mediumint(8) UNSIGNED DEFAULT NULL,
  `categorie` varchar(64) COLLATE utf8_swedish_ci DEFAULT NULL,
  `commentaire_produit` varchar(512) COLLATE utf8_swedish_ci DEFAULT NULL,
  `visible_sur_site` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non',
  `visible_pdf` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui',
  `ordre` int(11) NOT NULL DEFAULT 1,
  `frequence_fournisseur` enum('sans','mois','bimestre','trimestre','quadrimestre','semestre','an') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT 'sans' COMMENT 'Récurrence des commande fournisseur pour ce produit',
  `caracteristique` text COLLATE utf8_swedish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `document`
--

CREATE TABLE `document` (
  `id_document` mediumint(8) UNSIGNED NOT NULL,
  `document` varchar(200) NOT NULL,
  `filename` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `document_contrat`
--

CREATE TABLE `document_contrat` (
  `id_document_contrat` mediumint(8) UNSIGNED NOT NULL,
  `document_contrat` varchar(150) NOT NULL,
  `type_signature` enum('commune_avec_contrat','hors_contrat') NOT NULL DEFAULT 'commune_avec_contrat',
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `document_revendeur`
--

CREATE TABLE `document_revendeur` (
  `id_document_revendeur` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `site_associe` enum('toshiba') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `domaine`
--

CREATE TABLE `domaine` (
  `id_domaine` smallint(5) UNSIGNED NOT NULL,
  `domaine` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `domaine`
--

INSERT INTO `domaine` (`id_domaine`, `domaine`) VALUES
(1, 'Informatique'),
(2, 'Bureautique'),
(3, 'Téléphonie'),
(4, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `emailing_contact`
--

CREATE TABLE `emailing_contact` (
  `id_emailing_contact` mediumint(8) UNSIGNED NOT NULL,
  `date` datetime DEFAULT NULL,
  `sollicitation` mediumint(8) UNSIGNED DEFAULT 0,
  `tracking` mediumint(8) UNSIGNED DEFAULT 0,
  `last_tracking` datetime DEFAULT NULL,
  `erreur` mediumint(8) UNSIGNED DEFAULT 0,
  `civilite` enum('M','Mme','Mlle') CHARACTER SET utf8 DEFAULT NULL,
  `nom` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `prenom` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `opt_in` enum('oui','non') DEFAULT 'oui',
  `societe` varchar(64) DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `adresse` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_2` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `adresse_3` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cp` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `ville` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `id_pays` varchar(2) DEFAULT 'BE',
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
  `forecast` enum('0','20','40','60','80','100') DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `divers_1` varchar(255) DEFAULT NULL,
  `divers_2` varchar(255) DEFAULT NULL,
  `divers_3` varchar(255) DEFAULT NULL,
  `divers_4` varchar(255) DEFAULT NULL,
  `divers_5` varchar(255) DEFAULT NULL,
  `id_emailing_source` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_source` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_erreur`
--

CREATE TABLE `emailing_erreur` (
  `id_erreur` mediumint(8) UNSIGNED NOT NULL,
  `code` varchar(5) NOT NULL DEFAULT '',
  `groupe` varchar(255) NOT NULL DEFAULT '',
  `titre` varchar(255) NOT NULL DEFAULT '',
  `definition` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_job`
--

CREATE TABLE `emailing_job` (
  `id_emailing_job` mediumint(8) UNSIGNED NOT NULL,
  `emailing_job` varchar(64) NOT NULL,
  `id_emailing_projet` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_liste` mediumint(8) UNSIGNED DEFAULT NULL,
  `depart` datetime NOT NULL,
  `fin` datetime DEFAULT NULL,
  `etat` enum('wait','sending','sent','cancelled') DEFAULT 'wait',
  `nbMailToSend` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_job_email`
--

CREATE TABLE `emailing_job_email` (
  `id_emailing_job_email` mediumint(8) UNSIGNED NOT NULL,
  `id_emailing_job` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_liste_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `tracking` mediumint(8) UNSIGNED NOT NULL DEFAULT 0,
  `last_tracking` datetime DEFAULT NULL,
  `retour` enum('oui','non') NOT NULL DEFAULT 'non',
  `permanent_failure` set('5.0.0','5.1.0','5.1.1','5.1.2','5.1.3','5.1.4','5.1.5','5.1.6','5.1.7','5.1.8','5.2.0','5.2.1','5.2.2','5.2.3','5.2.4','5.3.0','5.3.1','5.3.2','5.3.3','5.3.4','5.3.5','5.4.0','5.4.1','5.4.2','5.4.3','5.4.4','5.4.5','5.4.6','5.4.7','5.5.0','5.5.1','5.5.2','5.5.3','5.5.4','5.5.5','5.6.0','5.6.1','5.6.2','5.6.3','5.6.4','5.6.5','5.7.0','5.7.1','5.7.2','5.7.3','5.7.4','5.7.5','5.7.6','5.7.7') DEFAULT NULL,
  `persistent_failure` set('4.0.0','4.1.0','4.1.1','4.1.2','4.1.3','4.1.4','4.1.5','4.1.6','4.1.7','4.1.8','4.2.0','4.2.1','4.2.2','4.2.3','4.2.4','4.3.0','4.3.1','4.3.2','4.3.3','4.3.4','4.3.5','4.4.0','4.4.1','4.4.2','4.4.3','4.4.4','4.4.5','4.4.6','4.4.7','4.5.0','4.5.1','4.5.2','4.5.3','4.5.4','4.5.5','4.6.0','4.6.1','4.6.2','4.6.3','4.6.4','4.6.5','4.7.0','4.7.1','4.7.2','4.7.3','4.7.4','4.7.5','4.7.6','4.7.7') DEFAULT NULL,
  `success` set('2.0.0','2.1.0','2.1.1','2.1.2','2.1.3','2.1.4','2.1.5','2.1.6','2.1.7','2.1.8','2.2.0','2.2.1','2.2.2','2.2.3','2.2.4','2.3.0','2.3.1','2.3.2','2.3.3','2.3.4','2.3.5','2.4.0','2.4.1','2.4.2','2.4.3','2.4.4','2.4.5','2.4.6','2.4.7','2.5.0','2.5.1','2.5.2','2.5.3','2.5.4','2.5.5','2.6.0','2.6.1','2.6.2','2.6.3','2.6.4','2.6.5','2.7.0','2.7.1','2.7.2','2.7.3','2.7.4','2.7.5','2.7.6','2.7.7') DEFAULT NULL,
  `erreur_brute` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_lien`
--

CREATE TABLE `emailing_lien` (
  `id_emailing_lien` mediumint(8) UNSIGNED NOT NULL,
  `emailing_lien` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_liste`
--

CREATE TABLE `emailing_liste` (
  `id_emailing_liste` mediumint(8) UNSIGNED NOT NULL,
  `emailing_liste` varchar(128) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `etat` enum('open','close') NOT NULL DEFAULT 'open',
  `sollicitation` mediumint(8) UNSIGNED DEFAULT 0,
  `tracking` mediumint(8) UNSIGNED DEFAULT 0,
  `last_tracking` datetime DEFAULT NULL,
  `erreur` mediumint(8) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_liste_contact`
--

CREATE TABLE `emailing_liste_contact` (
  `id_emailing_liste_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_emailing_liste` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `sollicitation` mediumint(8) UNSIGNED DEFAULT 0,
  `tracking` mediumint(8) UNSIGNED DEFAULT 0,
  `last_tracking` date DEFAULT NULL,
  `erreur` mediumint(8) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_projet`
--

CREATE TABLE `emailing_projet` (
  `id_emailing_projet` mediumint(8) UNSIGNED NOT NULL,
  `emailing_projet` varchar(128) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `mail_from` varchar(128) NOT NULL,
  `nom_expediteur` varchar(64) NOT NULL,
  `couleur_fond` varchar(6) DEFAULT NULL,
  `couleur_footer` varchar(6) DEFAULT NULL,
  `couleur_link` varchar(6) DEFAULT NULL,
  `corps` text DEFAULT NULL,
  `afficher_infos_societe` enum('oui','non') NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_projet_lien`
--

CREATE TABLE `emailing_projet_lien` (
  `id_emailing_projet_lien` mediumint(8) UNSIGNED NOT NULL,
  `id_emailing_projet` mediumint(8) UNSIGNED DEFAULT NULL,
  `emailing_projet_lien` varchar(64) NOT NULL,
  `url` varchar(255) NOT NULL,
  `tracking` mediumint(8) UNSIGNED DEFAULT 0,
  `last_tracking` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_source`
--

CREATE TABLE `emailing_source` (
  `id_emailing_source` mediumint(8) UNSIGNED NOT NULL,
  `emailing_source` varchar(200) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_user` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `emailing_tracking`
--

CREATE TABLE `emailing_tracking` (
  `id_emailing_tracking` mediumint(8) UNSIGNED NOT NULL,
  `id_emailing_job_email` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_emailing_projet_lien` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `exporter`
--

CREATE TABLE `exporter` (
  `id_exporter` mediumint(8) UNSIGNED NOT NULL,
  `exporter` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_module` mediumint(8) UNSIGNED DEFAULT NULL,
  `filtre` varchar(1024) DEFAULT NULL,
  `filtre_fields` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `export_facture`
--

CREATE TABLE `export_facture` (
  `id_export_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `date_export` timestamp NOT NULL DEFAULT current_timestamp(),
  `fichier_export` enum('flux_vente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `fabriquant`
--

CREATE TABLE `fabriquant` (
  `id_fabriquant` mediumint(8) UNSIGNED NOT NULL,
  `fabriquant` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `facturation`
--

CREATE TABLE `facturation` (
  `id_facturation` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED DEFAULT NULL,
  `montant` float(8,2) NOT NULL,
  `frais_de_gestion` float(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `serenite` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `maintenance` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `hotline` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `supervision` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `support` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `assurance` float(6,2) UNSIGNED DEFAULT 0.00,
  `date_periode_debut` date NOT NULL,
  `type` enum('contrat','prolongation','liberatoire') CHARACTER SET latin1 NOT NULL DEFAULT 'contrat',
  `envoye` enum('oui','non') CHARACTER SET latin1 NOT NULL DEFAULT 'non',
  `date_periode_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facturation_attente`
--

CREATE TABLE `facturation_attente` (
  `id_facturation_attente` mediumint(8) UNSIGNED NOT NULL,
  `mail` text NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `nom_table` varchar(50) NOT NULL,
  `path` text NOT NULL,
  `envoye` enum('oui','non','erreur') NOT NULL DEFAULT 'non',
  `id_facturation` mediumint(9) NOT NULL,
  `erreur` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facturation_fournisseur`
--

CREATE TABLE `facturation_fournisseur` (
  `id_facturation_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_bon_de_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `montant` float(8,2) NOT NULL,
  `date_debut_periode` date NOT NULL,
  `date_fin_periode` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `facturation_fournisseur_ligne`
--

CREATE TABLE `facturation_fournisseur_ligne` (
  `id_facturation_fournisseur_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_facturation_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `produit` varchar(150) NOT NULL,
  `quantite` int(11) NOT NULL,
  `montant` float(8,2) NOT NULL,
  `ref` varchar(32) NOT NULL,
  `id_commande_ligne` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

CREATE TABLE `facture` (
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `type_facture` enum('facture','ap','refi','libre','midas') NOT NULL DEFAULT 'facture' COMMENT 'Facture normale, Avis de prélèvement',
  `type_libre` enum('normale','retard','contentieux','prorata','liberatoire','cout_copie') DEFAULT NULL,
  `ref` varchar(32) NOT NULL,
  `ref_externe` varchar(11) DEFAULT NULL,
  `ref_magasin` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `designation` text DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `prix_sans_tva` decimal(8,2) DEFAULT NULL,
  `etat` enum('payee','impayee') NOT NULL DEFAULT 'impayee',
  `date` date NOT NULL,
  `date_previsionnelle` date DEFAULT NULL,
  `date_paiement` date DEFAULT NULL,
  `date_relance` date DEFAULT NULL,
  `date_periode_debut` date DEFAULT NULL,
  `date_periode_fin` date DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `mode_paiement` enum('prelevement','mandat','virement','remboursement','compensation','cheque','cb','pre-paiement') DEFAULT NULL,
  `id_fournisseur_prepaiement` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_demande_refi` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_refinanceur` mediumint(8) UNSIGNED DEFAULT NULL,
  `envoye_mail` varchar(128) DEFAULT NULL,
  `date_envoi` date DEFAULT NULL,
  `envoye` enum('oui','non','erreur') DEFAULT 'non',
  `rejet` enum('non_rejet','contestation_debiteur','provision_insuffisante','opposition_compte','decision_judiciaire','compte_cloture','coor_banc_inexploitable','pas_dordre_de_payer','non_preleve','solde','non_preleve_mandat') NOT NULL DEFAULT 'non_rejet',
  `commentaire` text DEFAULT NULL,
  `redevance` enum('oui','non') DEFAULT 'oui',
  `date_rejet` date DEFAULT NULL,
  `date_regularisation` date DEFAULT NULL,
  `nature` enum('prorata','engagement','prolongation','contrat','liberatoire') DEFAULT NULL,
  `exporte` enum('oui','non') DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facture_fournisseur`
--

CREATE TABLE `facture_fournisseur` (
  `id_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(50) NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `prix` decimal(10,2) UNSIGNED NOT NULL COMMENT 'Prix toutes taxes',
  `tva` decimal(4,3) UNSIGNED NOT NULL,
  `etat` enum('payee','impayee') NOT NULL DEFAULT 'impayee',
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `date_paiement` date DEFAULT NULL,
  `date_echeance` date NOT NULL,
  `type` enum('achat','maintenance','cout_copie') NOT NULL DEFAULT 'achat',
  `periodicite` varchar(100) DEFAULT NULL,
  `deja_exporte_immo` enum('oui','non') NOT NULL DEFAULT 'non',
  `deja_exporte_achat` enum('oui','non') NOT NULL DEFAULT 'non',
  `deja_exporte_cegid` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facture_fournisseur_ligne`
--

CREATE TABLE `facture_fournisseur_ligne` (
  `id_facture_fournisseur_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `produit` varchar(256) NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `prix` decimal(10,2) UNSIGNED DEFAULT NULL,
  `id_bon_de_commande_ligne` mediumint(8) UNSIGNED DEFAULT NULL,
  `serial` varchar(64) DEFAULT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `caracteristique` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `facture_ligne`
--

CREATE TABLE `facture_ligne` (
  `id_facture_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
  `produit` varchar(512) COLLATE utf8_swedish_ci NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix_achat` decimal(10,2) UNSIGNED DEFAULT NULL,
  `serial` varchar(2500) COLLATE utf8_swedish_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `id_affaire_provenance` mediumint(8) UNSIGNED DEFAULT NULL,
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui' COMMENT 'Flag qui permet',
  `afficher` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facture_magasin`
--

CREATE TABLE `facture_magasin` (
  `id_facture_magasin` mediumint(9) UNSIGNED NOT NULL,
  `ref_facture` varchar(25) NOT NULL,
  `id_affaire` mediumint(9) UNSIGNED NOT NULL,
  `etat` enum('non_recu','paye') NOT NULL DEFAULT 'non_recu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `facture_magasin_recu`
--

CREATE TABLE `facture_magasin_recu` (
  `id_facture_magasin_recu` mediumint(8) UNSIGNED NOT NULL,
  `fin_ref_facture` varchar(7) NOT NULL,
  `deb_ref_facture` varchar(4) NOT NULL,
  `nom_client` varchar(250) NOT NULL,
  `prenom_client` varchar(250) NOT NULL,
  `cp_client` varchar(5) DEFAULT NULL,
  `ville_client` varchar(150) DEFAULT NULL,
  `fixe_client` varchar(15) DEFAULT NULL,
  `mobile_client` varchar(15) DEFAULT NULL,
  `email_client` varchar(150) DEFAULT NULL,
  `code_produit` varchar(10) NOT NULL,
  `nom_produit` varchar(150) NOT NULL,
  `montantTTC` float(6,2) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_achat` varchar(8) NOT NULL,
  `matricule_vendeur` varchar(6) DEFAULT NULL,
  `nom_vendeur` varchar(150) DEFAULT NULL,
  `prenom_vendeur` varchar(150) DEFAULT NULL,
  `matricule_vendeur_conc` varchar(6) DEFAULT NULL,
  `nom_vendeur_conc` varchar(150) DEFAULT NULL,
  `prenom_vendeur_conc` varchar(150) DEFAULT NULL,
  `num_rue_client` varchar(6) DEFAULT NULL,
  `complete_num_rue_client` varchar(10) DEFAULT NULL,
  `type_rue_client` varchar(20) DEFAULT NULL,
  `rue_client` varchar(200) DEFAULT NULL,
  `rue2_client` varchar(200) DEFAULT NULL,
  `rue3_client` varchar(200) DEFAULT NULL,
  `code_client` varchar(8) DEFAULT NULL,
  `nom_magasin` varchar(250) DEFAULT NULL,
  `num_ligne_facture` varchar(2) DEFAULT NULL,
  `pays` varchar(3) DEFAULT NULL,
  `statut` enum('en_attente_traitement','traitee') NOT NULL DEFAULT 'en_attente_traitement'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `facture_non_parvenue`
--

CREATE TABLE `facture_non_parvenue` (
  `id_facture_non_parvenue` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) NOT NULL,
  `id_facture_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL COMMENT 'Prix toutes taxes',
  `tva` decimal(4,3) UNSIGNED NOT NULL,
  `etat` enum('payee','impayee') NOT NULL DEFAULT 'impayee',
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_bon_de_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `facturation_terminee` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `facture_transaction`
--

CREATE TABLE `facture_transaction` (
  `id_facture_transaction` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_transaction_externe` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  `statut` enum('processing','rejected','processed','notprocessed','transformed','contested','toreplay','togenerate','toprocess') NOT NULL,
  `prestataire` enum('slimpay') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `famille`
--

CREATE TABLE `famille` (
  `id_famille` tinyint(3) UNSIGNED NOT NULL,
  `famille` varchar(32) NOT NULL,
  `cle_externe` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `famille`
--

INSERT INTO `famille` (`id_famille`, `famille`, `cle_externe`) VALUES
(1, 'Association', NULL),
(2, 'Société', NULL),
(3, 'Administration', NULL),
(4, 'Autre', NULL),
(5, 'Cabinet', NULL),
(6, 'Artisan', NULL),
(7, 'Commercant', NULL),
(8, 'Affaire personnelle', NULL),
(9, 'Foyer', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `filtre_defaut`
--

CREATE TABLE `filtre_defaut` (
  `id_filtre_defaut` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `div` varchar(128) NOT NULL,
  `filter_key` varchar(32) DEFAULT NULL,
  `order` varchar(256) DEFAULT NULL,
  `page` tinyint(3) UNSIGNED DEFAULT NULL,
  `limit` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Structure de la table `filtre_optima`
--

CREATE TABLE `filtre_optima` (
  `id_filtre_optima` mediumint(8) UNSIGNED NOT NULL,
  `filtre_optima` varchar(32) NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `options` text NOT NULL,
  `type` enum('public','prive') NOT NULL DEFAULT 'public'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `filtre_user`
--

CREATE TABLE `filtre_user` (
  `id_filtre_user` mediumint(8) UNSIGNED NOT NULL,
  `id_filtre_optima` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_attestation_presence`
--

CREATE TABLE `formation_attestation_presence` (
  `id_formation_attestation_presence` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_commande` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_bon_de_commande_fournisseur`
--

CREATE TABLE `formation_bon_de_commande_fournisseur` (
  `id_formation_bon_de_commande_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `thematique` varchar(100) NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `commentaire` text DEFAULT NULL,
  `montant` float(10,2) NOT NULL,
  `ref` varchar(20) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_commande`
--

CREATE TABLE `formation_commande` (
  `id_formation_commande` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(11) NOT NULL,
  `date` date NOT NULL,
  `date_envoi` date DEFAULT NULL,
  `date_retour` date DEFAULT NULL,
  `objectif` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_commande_fournisseur`
--

CREATE TABLE `formation_commande_fournisseur` (
  `id_formation_commande_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_commande` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `objectif` text NOT NULL,
  `date_envoi` date DEFAULT NULL,
  `date_retour` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_devis`
--

CREATE TABLE `formation_devis` (
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `numero_dossier` varchar(20) DEFAULT NULL,
  `montantHT` float(10,2) DEFAULT NULL,
  `etat` enum('gagne','attente','perdu','') NOT NULL DEFAULT 'attente',
  `thematique` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `nb_heure` int(11) NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_owner` mediumint(8) UNSIGNED NOT NULL,
  `id_formateur` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_lieu_formation` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_apporteur_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix` float(10,2) NOT NULL COMMENT 'Prix Horaire pour 1 personne',
  `acompte` float(10,2) DEFAULT NULL,
  `date_retour` date DEFAULT NULL,
  `date_validite` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_devis_fournisseur`
--

CREATE TABLE `formation_devis_fournisseur` (
  `id_formation_devis_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `type` enum('apporteur_affaire','lieu_formation','formateur','autre') NOT NULL,
  `montant` float(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_devis_ligne`
--

CREATE TABLE `formation_devis_ligne` (
  `id_formation_devis_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `date_deb_matin` varchar(5) DEFAULT NULL,
  `date_fin_matin` varchar(5) DEFAULT NULL,
  `date_deb_am` varchar(5) DEFAULT NULL,
  `date_fin_am` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_facture`
--

CREATE TABLE `formation_facture` (
  `id_formation_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `num_dossier` varchar(20) DEFAULT NULL,
  `prix` float(10,2) NOT NULL,
  `date_regularisation` date DEFAULT NULL,
  `type` enum('normale','acompte') NOT NULL DEFAULT 'normale',
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_facture_fournisseur`
--

CREATE TABLE `formation_facture_fournisseur` (
  `id_formation_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_bon_de_commande_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(50) NOT NULL,
  `prix` decimal(10,2) UNSIGNED NOT NULL COMMENT 'Prix toutes taxes',
  `tva` decimal(4,3) UNSIGNED NOT NULL,
  `etat` enum('payee','impayee') NOT NULL DEFAULT 'impayee',
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `numero_dossier` varchar(25) NOT NULL,
  `date` date NOT NULL,
  `date_paiement` date DEFAULT NULL,
  `date_echeance` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_facture_fournisseur_ligne`
--

CREATE TABLE `formation_facture_fournisseur_ligne` (
  `id_formation_facture_fournisseur_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `produit` varchar(128) NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `prix` decimal(10,2) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `formation_participant`
--

CREATE TABLE `formation_participant` (
  `id_formation_participant` mediumint(8) UNSIGNED NOT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `formation_priseEnCharge`
--

CREATE TABLE `formation_priseEnCharge` (
  `id_formation_priseEnCharge` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(20) DEFAULT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED NOT NULL,
  `opca` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('accepte','refus','attente_element') NOT NULL DEFAULT 'accepte',
  `montant_demande` float(10,2) NOT NULL,
  `montant_accorde` float(10,2) DEFAULT NULL,
  `date_envoi` date NOT NULL,
  `date_retour` date DEFAULT NULL,
  `subro_client` enum('oui','non') NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ged`
--

CREATE TABLE `ged` (
  `id_ged` mediumint(8) UNSIGNED NOT NULL,
  `date` datetime DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `ged` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Nom du fichier',
  `format` varchar(8) DEFAULT NULL,
  `version` varchar(16) CHARACTER SET utf8 DEFAULT '1',
  `weight` decimal(5,2) UNSIGNED DEFAULT NULL,
  `commentaires` varchar(1024) CHARACTER SET utf8 DEFAULT NULL,
  `dossier` tinyint(1) NOT NULL DEFAULT 0,
  `id_parent` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `importer`
--

CREATE TABLE `importer` (
  `id_importer` mediumint(8) UNSIGNED NOT NULL,
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
  `lignes_inserer` int(10) UNSIGNED DEFAULT NULL,
  `lignes_ignore` int(10) UNSIGNED DEFAULT NULL,
  `lignes_update` int(10) UNSIGNED DEFAULT NULL,
  `options` enum('ignore','update') NOT NULL DEFAULT 'ignore'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `importer_ligne`
--

CREATE TABLE `importer_ligne` (
  `id_importer_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_importer` mediumint(8) UNSIGNED DEFAULT NULL,
  `id` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `import_facture_fournisseur`
--

CREATE TABLE `import_facture_fournisseur` (
  `id_import_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `importer` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `mapping` text CHARACTER SET utf8 NOT NULL,
  `etat` enum('en_attente','annule','fini','probleme') CHARACTER SET utf8 NOT NULL DEFAULT 'en_attente',
  `date_import` datetime DEFAULT NULL,
  `separateur` enum(',',';') NOT NULL DEFAULT ',',
  `complement` text CHARACTER SET utf8 DEFAULT NULL,
  `lignes_inserer` int(10) UNSIGNED DEFAULT NULL,
  `lignes_ignore` int(10) UNSIGNED DEFAULT NULL,
  `lignes_update` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `licence`
--

CREATE TABLE `licence` (
  `id_licence` mediumint(9) NOT NULL,
  `part_1` varchar(60) NOT NULL,
  `part_2` varchar(4) NOT NULL,
  `id_licence_type` mediumint(8) UNSIGNED NOT NULL,
  `id_commande_ligne` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Permet de savoir si la licence est déja utilisée et pour quelle affaire',
  `date_envoi` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `licence_type`
--

CREATE TABLE `licence_type` (
  `id_licence_type` mediumint(8) UNSIGNED NOT NULL,
  `licence_type` varchar(100) NOT NULL,
  `url_telechargement` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `localisation_langue`
--

CREATE TABLE `localisation_langue` (
  `id_localisation_langue` tinyint(3) UNSIGNED NOT NULL,
  `localisation_langue` varchar(2) NOT NULL,
  `libelle` varchar(64) NOT NULL,
  `id_pays` char(2) NOT NULL DEFAULT 'FR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `localisation_langue`
--

INSERT INTO `localisation_langue` (`id_localisation_langue`, `localisation_langue`, `libelle`, `id_pays`) VALUES
(1, 'fr', 'Français', 'FR'),
(2, 'en', 'English', 'UK'),
(3, 'es', 'Español', 'ES'),
(4, 'it', 'Italiano', 'IT'),
(5, 'de', 'Deutsch', 'DE'),
(121, 'zh', '国语', 'CN'),
(122, 'hu', 'Magyar', 'HU'),
(123, 'hi', 'हिंदी', 'IN'),
(124, 'nl', 'Nederlands', 'NL'),
(125, 'pl', 'Polski', 'PL'),
(126, 'pt', 'Português', 'PT'),
(127, 'ro', 'Român', 'RO'),
(128, 'ru', 'русский', 'RU'),
(129, 'tr', 'Türk', 'TR'),
(130, 'br', 'Brasileiro', 'BR'),
(131, 'cz', 'český', 'CZ'),
(132, 'sw', 'Svenska', 'SE'),
(133, 'jp', '日本', 'JP');

-- --------------------------------------------------------

--
-- Structure de la table `loyer`
--

CREATE TABLE `loyer` (
  `id_loyer` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `loyer` float(8,2) NOT NULL,
  `duree` tinyint(3) UNSIGNED NOT NULL,
  `type` enum('engagement','liberatoire','prolongation') NOT NULL DEFAULT 'engagement',
  `assurance` decimal(6,2) UNSIGNED DEFAULT NULL,
  `frais_de_gestion` decimal(6,2) UNSIGNED DEFAULT NULL,
  `serenite` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `maintenance` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `hotline` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `supervision` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `support` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `frequence_loyer` enum('jour','mois','trimestre','semestre','an') NOT NULL DEFAULT 'mois',
  `avec_option` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `loyer_prolongation`
--

CREATE TABLE `loyer_prolongation` (
  `id_loyer_prolongation` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_prolongation` mediumint(8) UNSIGNED NOT NULL,
  `loyer` float(8,2) DEFAULT NULL,
  `duree` tinyint(3) UNSIGNED DEFAULT NULL,
  `assurance` decimal(6,2) UNSIGNED DEFAULT NULL,
  `frais_de_gestion` decimal(6,2) UNSIGNED DEFAULT NULL,
  `serenite` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `maintenance` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `hotline` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `supervision` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `support` decimal(6,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `frequence_loyer` enum('mois','trimestre','semestre','an') NOT NULL DEFAULT 'mois',
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `magasin`
--

CREATE TABLE `magasin` (
  `id_magasin` mediumint(8) UNSIGNED NOT NULL,
  `magasin` varchar(100) NOT NULL,
  `code` varchar(6) DEFAULT NULL,
  `site_associe` enum('toshiba','btwin','bdomplus','boulanger-cafe') NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `adresse` varchar(64) DEFAULT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(64) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `statut` enum('ouvert','ferme') NOT NULL DEFAULT 'ouvert'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `magasin_vendeur`
--

CREATE TABLE `magasin_vendeur` (
  `id_magasin_vendeur` mediumint(8) UNSIGNED NOT NULL,
  `login` varchar(100) NOT NULL,
  `password` varchar(256) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id_magasin` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `messagerie`
--

CREATE TABLE `messagerie` (
  `id_messagerie` mediumint(8) UNSIGNED NOT NULL,
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
  `attachmentsRealName` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `module`
--

CREATE TABLE `module` (
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_parent` mediumint(8) UNSIGNED DEFAULT NULL,
  `module` varchar(40) NOT NULL,
  `abstrait` tinyint(1) DEFAULT 0,
  `priorite` tinyint(3) UNSIGNED DEFAULT 0,
  `visible` tinyint(3) UNSIGNED DEFAULT 1,
  `import` tinyint(1) UNSIGNED DEFAULT 0 COMMENT 'on peut ou non importer des éléments pour ce module',
  `couleur_fond` char(6) DEFAULT 'FFFFFF',
  `couleur_texte` char(6) DEFAULT '000000',
  `couleur` enum('red','green','brown','yellow','blue','purple') NOT NULL DEFAULT 'green',
  `description` varchar(512) DEFAULT NULL,
  `construct` text CHARACTER SET utf8 DEFAULT NULL COMMENT 'Sert de constructeur par défaut si aucune classe n''existe pour ce module'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `module`
--

INSERT INTO `module` (`id_module`, `id_parent`, `module`, `abstrait`, `priorite`, `visible`, `import`, `couleur_fond`, `couleur_texte`, `couleur`, `description`, `construct`) VALUES
(2, NULL, 'crm', 1, 5, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(3, 2, 'societe', 0, 0, 1, 1, 'D31C0B', 'FFFFFF', 'yellow', NULL, NULL),
(4, 2, 'contact', 0, 0, 1, 1, '000086', 'FFFFFF', 'yellow', NULL, NULL),
(9, 2, 'suivi', 0, 0, 1, 0, '2A9B34', 'FFFFFF', 'yellow', NULL, NULL),
(37, NULL, 'my', 1, 6, 1, 0, 'ffc000', '000000', 'yellow', NULL, NULL),
(39, 37, 'messagerie', 0, 0, 1, 0, '418662', 'FFFFFF', 'yellow', NULL, NULL),
(41, 37, 'stats', 0, 0, 1, 0, 'D65E92', 'FFFFFF', 'yellow', NULL, NULL),
(45, 37, 'preference', 0, 0, 0, 0, 'F0E35B', '000000', 'yellow', NULL, NULL),
(46, 2, 'tache', 0, 0, 1, 0, '018F19', 'FFFFFF', 'yellow', NULL, NULL),
(47, NULL, 'commerce', 1, 4, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(48, NULL, 'technique', 1, 3, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(49, NULL, 'qualite', 1, 2, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(50, NULL, 'administration', 1, 1, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(51, 47, 'opportunite', 0, 0, 1, 0, 'DD9999', '000000', 'yellow', NULL, NULL),
(52, 47, 'affaire', 0, 0, 1, 0, 'D8AEAE', '000000', 'yellow', NULL, NULL),
(53, 52, 'devis', 0, 1, 1, 0, 'FF0000', '000000', 'yellow', NULL, NULL),
(54, 52, 'commande', 0, 10, 1, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(55, 52, 'facture', 0, 50, 1, 0, '26E71F', '000000', 'yellow', NULL, NULL),
(61, 48, 'parc', 0, 0, 1, 0, '996C53', 'FFFFFF', 'yellow', NULL, NULL),
(64, 61, 'mdp', 0, 0, 1, 0, '996C53', 'FFFFFF', 'yellow', NULL, NULL),
(65, 49, 'document', 0, 0, 1, 0, 'DAF0FF', '000000', 'yellow', NULL, NULL),
(66, 49, 'reglement', 0, 0, 1, 0, 'ABC766', 'FFFFFF', 'yellow', NULL, NULL),
(68, 50, 'user', 0, 0, 1, 0, '00D4FF', '0022A8', 'yellow', NULL, NULL),
(69, 50, 'agence', 0, 0, 1, 0, '6E83A3', 'FFFFFF', 'yellow', NULL, NULL),
(70, 50, 'module', 0, 0, 1, 0, 'D10000', 'FFFFFF', 'yellow', NULL, NULL),
(71, 50, 'constante', 0, 0, 1, 0, '858585', 'FFFFFF', 'yellow', NULL, NULL),
(72, 37, 'accueil', 1, 1, 0, 0, '018F19', 'FFFFFF', 'yellow', NULL, NULL),
(75, 47, 'produit', 0, 10, 1, 0, 'E3D6B3', '000000', 'yellow', NULL, NULL),
(78, NULL, 'error', 1, NULL, 0, 0, 'FF0000', '000000', 'yellow', NULL, NULL),
(79, 49, 'base_de_connaissance', 0, NULL, 1, 0, '4EFF8F', '000000', 'yellow', NULL, NULL),
(87, 68, 'profil', 0, NULL, 1, 0, 'E1FF00', '000000', 'yellow', NULL, NULL),
(97, 47, 'politesse', 0, 10, 1, 0, '2C56FF', 'F7FF00', 'yellow', NULL, NULL),
(98, 47, 'termes', 0, 10, 1, 0, 'FF6F6F', 'CCCCCC', 'yellow', NULL, NULL),
(101, 75, 'categorie', 0, 1, 1, 0, '0C5856', 'FFFFFF', 'yellow', NULL, NULL),
(102, 101, 'sous_categorie', 0, 2, 1, 0, '137A3C', 'FFFFFF', 'yellow', NULL, NULL),
(104, 61, 'processeur', 0, NULL, 1, 0, '996C53', 'FFFFFF', 'yellow', NULL, NULL),
(105, 47, 'refinanceur', 0, 20, 1, 0, 'D37E00', 'FFFFFF', 'yellow', NULL, NULL),
(106, 52, 'demande_refi', 0, 12, 1, 0, 'FFFF00', '000000', 'yellow', NULL, NULL),
(111, 2, 'geolocalisation', 0, 10, 1, 0, '62DB8E', 'FFFFFF', 'yellow', NULL, NULL),
(114, 50, 'exporter', 0, NULL, NULL, 0, '250B4D', 'FFFFFF', 'yellow', NULL, NULL),
(116, NULL, 'emailing', 1, NULL, 1, 0, 'F78383', '008330', 'yellow', NULL, NULL),
(117, 116, 'emailing_projet', 0, 1, 1, 0, '3A50F3', 'FFFFFF', 'yellow', NULL, NULL),
(118, 116, 'emailing_job', 0, 20, 1, 0, 'F75454', 'FFFFFF', 'yellow', NULL, NULL),
(119, 116, 'emailing_liste', 0, 10, 1, 0, 'E2E2E2', '000000', 'yellow', NULL, NULL),
(120, 116, 'emailing_contact', 0, 2, 1, 0, '8B79B8', 'FFFFFF', 'yellow', NULL, NULL),
(121, 119, 'emailing_liste_contact', NULL, NULL, 0, 0, '505DE7', 'ADFF99', 'yellow', NULL, NULL),
(122, 116, 'emailing_tracking', NULL, 30, 1, 0, '791FEB', 'E1EBD2', 'yellow', NULL, NULL),
(123, 117, 'emailing_projet_lien', NULL, NULL, 1, 0, '00FF51', 'A0CEFF', 'yellow', NULL, NULL),
(124, 118, 'emailing_job_email', NULL, NULL, 1, 0, '00FF47', 'FFFFFF', 'yellow', NULL, NULL),
(125, 3, 'domaine', 0, NULL, 1, 0, 'CFE5E6', '2800AC', 'yellow', NULL, NULL),
(126, 3, 'famille', NULL, NULL, 1, 0, '0006A8', 'FFFDC4', 'yellow', NULL, NULL),
(132, 50, 'importer', NULL, NULL, 1, 0, '3F2D85', 'AAFFC5', 'yellow', NULL, NULL),
(134, 3, 'secteur_geographique', NULL, NULL, 1, 0, 'D4BABA', 'FFFFFF', 'yellow', NULL, NULL),
(135, 3, 'secteur_commercial', NULL, NULL, 1, 0, 'F8BDBD', '09006F', 'yellow', NULL, NULL),
(136, 50, 'devise', NULL, NULL, 1, 0, '0015FF', 'FFFFFF', 'yellow', NULL, NULL),
(139, 52, 'bon_de_commande', 0, 15, 1, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(140, 3, 'ged', 0, 10, 0, 0, 'D4A7FF', '110057', 'yellow', NULL, NULL),
(145, 75, 'fabriquant', 0, 2, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(151, 54, 'commande_ligne', 0, 15, 0, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(152, 2, 'suivi_portail', 0, 0, 1, 0, '2A9B34', 'FFFFFF', 'yellow', NULL, NULL),
(155, 47, 'assurance', 0, 60, 1, 0, '000000', 'FFFFFF', 'yellow', NULL, NULL),
(156, 50, 'tracabilite', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(157, 87, 'profil_privilege', 0, NULL, 0, 0, '9FFF5F', '000000', 'yellow', NULL, NULL),
(158, 52, 'facturation', 0, 55, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(159, 52, 'prolongation', 0, 30, 1, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(161, 53, 'loyer', 0, 0, 0, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(162, 139, 'bon_de_commande_ligne', 0, 15, 0, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(163, 139, 'facture_fournisseur', 0, 15, 1, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(164, 139, 'facture_non_parvenue', 0, 15, 1, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(171, 2, 'accompagnateur', 0, 0, 1, 1, '000086', 'FFFFFF', 'yellow', NULL, NULL),
(172, 159, 'loyer_prolongation', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(173, 163, 'facture_fournisseur_ligne', 0, 15, 1, 0, 'E6951D', '000000', 'yellow', NULL, NULL),
(174, 55, 'facture_ligne', 0, 15, 1, 0, '26E71F', '000000', 'yellow', NULL, NULL),
(175, 70, 'module_privilege', 0, 0, 0, 0, 'D10000', 'FFFFFF', 'yellow', 'Attribution des privl?ges sp?cifique au module', NULL),
(176, 50, 'asterisk', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(177, 50, 'phone', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(178, 116, 'emailing_source', 0, 1, 1, 0, '', '', 'yellow', NULL, NULL),
(179, 37, 'conge', 0, 0, 1, 0, '2752FF', 'FFFFFF', 'yellow', NULL, NULL),
(180, 48, 'scanner', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(181, 53, 'devis_ligne', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(182, NULL, 'gie', 1, 5, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(183, 182, 'gie_societe', 0, 0, 1, 1, 'D31C0B', 'FFFFFF', 'yellow', NULL, NULL),
(184, 182, 'gie_contact', 0, 0, 0, 1, 'D31C0B', 'FFFFFF', 'yellow', NULL, NULL),
(186, NULL, 'formation', 1, 2, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(187, 186, 'formation_devis', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(188, 186, 'formation_commande', 0, 10, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(189, 188, 'formation_attestation_presence', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(190, 188, 'formation_commande_fournisseur', 0, 10, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(191, 188, 'formation_facture', 0, 20, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(192, 187, 'formation_participant', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(193, 188, 'formation_priseEnCharge', 0, 30, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(195, 187, 'formation_devis_ligne', 0, 0, 0, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(196, 187, 'formation_devis_fournisseur', 0, 0, 0, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(197, 188, 'formation_bon_de_commande_fournisseur', 0, 30, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(198, 188, 'formation_facture_fournisseur', 0, 40, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(199, NULL, 'site_web', 1, 1, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(200, 199, 'site_menu', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(201, 199, 'site_article', 0, 10, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(202, 199, 'site_en_tete', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(203, 52, 'comite', 0, 10, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(204, 3, 'campagne', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(205, 52, 'pdf_affaire', 0, 0, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(206, 3, 'pdf_societe', 0, 200, 1, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(207, 75, 'produit_fournisseur_loyer', 0, 0, 0, 0, 'FFFFFF', '000000', 'yellow', NULL, NULL),
(208, 47, 'document_revendeur', 0, 60, 1, 0, 'FFFFFF', '000000', 'green', NULL, NULL),
(209, 47, 'courrier_information_pack', 0, 30, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(210, 75, 'compte_produit', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(211, 212, 'facturation_fournisseur_detail', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(212, 52, 'facturation_fournisseur', 0, 0, 0, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(213, 2, 'collaborateur', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(214, 2, 'magasin', 0, 255, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(215, 47, 'document_contrat', 0, 40, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(216, 50, 'rayon', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(217, 218, 'cgl_texte', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(218, 47, 'cgl_article', 0, 100, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(219, 75, 'produit_fournisseur', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(220, 75, 'produit_loyer', 0, 0, 1, 0, 'FFFFFF', '000000', 'blue', NULL, NULL),
(221, NULL, 'creditsafe', 1, 0, 0, 0, 'FFFFFF', '000000', 'green', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `module_privilege`
--

CREATE TABLE `module_privilege` (
  `id_module_privilege` mediumint(8) UNSIGNED NOT NULL,
  `id_module` mediumint(8) UNSIGNED NOT NULL,
  `id_privilege` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `module_privilege`
--

INSERT INTO `module_privilege` (`id_module_privilege`, `id_module`, `id_privilege`) VALUES
(0, 176, 9),
(0, 176, 8),
(0, 176, 7),
(0, 176, 6),
(0, 176, 5),
(0, 176, 4),
(0, 176, 3),
(0, 176, 2),
(0, 176, 1),
(0, 177, 9),
(0, 177, 8),
(0, 177, 7),
(0, 177, 6),
(0, 177, 5),
(0, 177, 4),
(0, 177, 3),
(0, 177, 2),
(0, 177, 1),
(0, 180, 10),
(0, 180, 9),
(0, 180, 8),
(0, 180, 7),
(0, 180, 6),
(0, 180, 5),
(0, 180, 4),
(0, 180, 3),
(0, 180, 2),
(0, 180, 1),
(0, 181, 10),
(0, 181, 9),
(0, 181, 8),
(0, 181, 7),
(0, 181, 6),
(0, 181, 5),
(0, 181, 4),
(0, 181, 3),
(0, 181, 2),
(0, 181, 1),
(0, 186, 10),
(0, 186, 9),
(0, 186, 8),
(0, 186, 7),
(0, 186, 6),
(0, 186, 5),
(0, 186, 4),
(0, 186, 3),
(0, 186, 2),
(0, 186, 1),
(0, 187, 10),
(0, 187, 9),
(0, 187, 8),
(0, 187, 7),
(0, 187, 6),
(0, 187, 5),
(0, 187, 4),
(0, 187, 3),
(0, 187, 2),
(0, 187, 1),
(0, 188, 10),
(0, 188, 9),
(0, 188, 8),
(0, 188, 7),
(0, 188, 6),
(0, 188, 5),
(0, 188, 4),
(0, 188, 3),
(0, 188, 2),
(0, 188, 1),
(0, 189, 10),
(0, 189, 9),
(0, 189, 8),
(0, 189, 7),
(0, 189, 6),
(0, 189, 5),
(0, 189, 4),
(0, 189, 3),
(0, 189, 2),
(0, 189, 1),
(0, 190, 10),
(0, 190, 9),
(0, 190, 8),
(0, 190, 7),
(0, 190, 6),
(0, 190, 5),
(0, 190, 4),
(0, 190, 3),
(0, 190, 2),
(0, 190, 1),
(0, 191, 10),
(0, 191, 9),
(0, 191, 8),
(0, 191, 7),
(0, 191, 6),
(0, 191, 5),
(0, 191, 4),
(0, 191, 3),
(0, 191, 2),
(0, 191, 1),
(0, 192, 10),
(0, 192, 9),
(0, 192, 8),
(0, 192, 7),
(0, 192, 6),
(0, 192, 5),
(0, 192, 4),
(0, 192, 3),
(0, 192, 2),
(0, 192, 1),
(0, 193, 10),
(0, 193, 9),
(0, 193, 8),
(0, 193, 7),
(0, 193, 6),
(0, 193, 5),
(0, 193, 4),
(0, 193, 3),
(0, 193, 2),
(0, 193, 1),
(0, 195, 10),
(0, 195, 9),
(0, 195, 8),
(0, 195, 7),
(0, 195, 6),
(0, 195, 5),
(0, 195, 4),
(0, 195, 3),
(0, 195, 2),
(0, 195, 1),
(0, 196, 10),
(0, 196, 9),
(0, 196, 8),
(0, 196, 7),
(0, 196, 6),
(0, 196, 5),
(0, 196, 4),
(0, 196, 3),
(0, 196, 2),
(0, 196, 1),
(0, 197, 10),
(0, 197, 9),
(0, 197, 8),
(0, 197, 7),
(0, 197, 6),
(0, 197, 5),
(0, 197, 4),
(0, 197, 3),
(0, 197, 2),
(0, 197, 1),
(0, 198, 10),
(0, 198, 9),
(0, 198, 8),
(0, 198, 7),
(0, 198, 6),
(0, 198, 5),
(0, 198, 4),
(0, 198, 3),
(0, 198, 2),
(0, 198, 1),
(0, 199, 10),
(0, 199, 9),
(0, 199, 8),
(0, 199, 7),
(0, 199, 6),
(0, 199, 5),
(0, 199, 4),
(0, 199, 3),
(0, 199, 2),
(0, 199, 1),
(0, 200, 10),
(0, 200, 9),
(0, 200, 8),
(0, 200, 7),
(0, 200, 6),
(0, 200, 5),
(0, 200, 4),
(0, 200, 3),
(0, 200, 2),
(0, 200, 1),
(0, 201, 10),
(0, 201, 9),
(0, 201, 8),
(0, 201, 7),
(0, 201, 6),
(0, 201, 5),
(0, 201, 4),
(0, 201, 3),
(0, 201, 2),
(0, 201, 1),
(0, 202, 10),
(0, 202, 9),
(0, 202, 8),
(0, 202, 7),
(0, 202, 6),
(0, 202, 5),
(0, 202, 4),
(0, 202, 3),
(0, 202, 2),
(0, 202, 1),
(0, 203, 10),
(0, 203, 9),
(0, 203, 8),
(0, 203, 7),
(0, 203, 6),
(0, 203, 5),
(0, 203, 4),
(0, 203, 3),
(0, 203, 2),
(0, 203, 1),
(0, 204, 10),
(0, 204, 9),
(0, 204, 8),
(0, 204, 7),
(0, 204, 6),
(0, 204, 5),
(0, 204, 4),
(0, 204, 3),
(0, 204, 2),
(0, 204, 1),
(0, 205, 10),
(0, 205, 9),
(0, 205, 8),
(0, 205, 7),
(0, 205, 6),
(0, 205, 5),
(0, 205, 4),
(0, 205, 3),
(0, 205, 2),
(0, 205, 1),
(0, 206, 10),
(0, 206, 9),
(0, 206, 8),
(0, 206, 7),
(0, 206, 6),
(0, 206, 5),
(0, 206, 4),
(0, 206, 3),
(0, 206, 2),
(0, 206, 1),
(0, 207, 10),
(0, 207, 9),
(0, 207, 8),
(0, 207, 7),
(0, 207, 6),
(0, 207, 5),
(0, 207, 4),
(0, 207, 3),
(0, 207, 2),
(0, 207, 1),
(0, 208, 10),
(0, 208, 9),
(0, 208, 8),
(0, 208, 7),
(0, 208, 6),
(0, 208, 5),
(0, 208, 4),
(0, 208, 3),
(0, 208, 2),
(0, 208, 1);

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE `news` (
  `id_news` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `news` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `opca`
--

CREATE TABLE `opca` (
  `id_opca` mediumint(8) UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(100) NOT NULL,
  `adresse2` varchar(100) DEFAULT NULL,
  `adresse3` varchar(100) DEFAULT NULL,
  `cp` varchar(6) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `siren` varchar(15) NOT NULL,
  `numero` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `opportunite`
--

CREATE TABLE `opportunite` (
  `id_opportunite` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `opportunite` varchar(128) NOT NULL,
  `etat` enum('en_cours','fini','annule') NOT NULL DEFAULT 'en_cours',
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_target` mediumint(8) UNSIGNED NOT NULL COMMENT 'User concerné par l''opportunité',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_installation_prevu` timestamp NULL DEFAULT NULL,
  `date_installation_reel` timestamp NULL DEFAULT NULL,
  `date_livraison_prevu` timestamp NULL DEFAULT NULL COMMENT 'date_installation_prevu + 3semaines',
  `date_vpi` timestamp NULL DEFAULT NULL,
  `source` enum('appel','email','reseau','prive','autre','campagne') DEFAULT NULL,
  `source_detail` varchar(128) DEFAULT NULL,
  `ca` mediumint(9) DEFAULT NULL,
  `marge` mediumint(9) DEFAULT NULL,
  `description` varchar(2048) DEFAULT NULL,
  `echeance` date DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `code_societe` varchar(20) DEFAULT NULL COMMENT 'Champs caché, histoire de pouvoir faire un tri du cote du portail associé'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `pack_produit`
--

CREATE TABLE `pack_produit` (
  `id_pack_produit` mediumint(8) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `site_associe` enum('cleodis','location_evolutive','toshiba','btwin','boulangerpro','hexamed','top office','burger king','flunch','sans') CHARACTER SET latin1 DEFAULT NULL,
  `type_offre` enum('multimedia','atol','midas','bv','moa','domino','dafy','gifar','heytens','glastint','osilog-axa','atol-table-vente','atol-impression','atol-digital','haplus','midas-informatique','midas-impression','midas-actia','midas-autre-equipement','orange-bleue','flunch','hippopotamus','divers') DEFAULT NULL,
  `loyer` float(8,2) DEFAULT NULL,
  `duree` int(8) DEFAULT NULL,
  `frequence` enum('jour','mois','trimestre','semestre','an','bimestre') NOT NULL DEFAULT 'mois',
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `specifique_partenaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `visible_sur_site` enum('oui','non') NOT NULL DEFAULT 'non',
  `id_pack_produit_besoin` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_pack_produit_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `avis_expert` text DEFAULT NULL,
  `popup` text DEFAULT NULL,
  `id_document_contrat` mediumint(8) UNSIGNED DEFAULT NULL,
  `prolongation` enum('oui','non') NOT NULL DEFAULT 'non',
  `val_plancher` int(10) NOT NULL DEFAULT 0 COMMENT 'Total de point minimum possible pour ce pack',
  `val_plafond` int(10) NOT NULL DEFAULT 0 COMMENT 'Total de point maximum possible pour ce pack',
  `max_qte` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pack_produit_besoin`
--

CREATE TABLE `pack_produit_besoin` (
  `id_pack_produit_besoin` mediumint(8) UNSIGNED NOT NULL,
  `pack_produit_besoin` varchar(200) NOT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pack_produit_fournisseur`
--

CREATE TABLE `pack_produit_fournisseur` (
  `id_pack_produit_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_pack_produit` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `produit` varchar(500) NOT NULL,
  `ref` varchar(50) NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `frequence` enum('mois','trimestre','semestre','an') CHARACTER SET utf8 NOT NULL DEFAULT 'mois',
  `montant` float(8,2) NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `pack_produit_ligne`
--

CREATE TABLE `pack_produit_ligne` (
  `id_pack_produit_ligne` mediumint(8) UNSIGNED NOT NULL,
  `type` enum('fixe','portable','sans_objet','immateriel') CHARACTER SET latin1 NOT NULL DEFAULT 'sans_objet',
  `id_pack_produit` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) COLLATE utf8_swedish_ci DEFAULT NULL,
  `produit` varchar(512) COLLATE utf8_swedish_ci NOT NULL,
  `quantite` int(10) UNSIGNED NOT NULL,
  `min` int(11) NOT NULL DEFAULT 0,
  `max` int(11) NOT NULL DEFAULT 0,
  `option_incluse` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non',
  `option_incluse_obligatoire` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui',
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `frequence_fournisseur` enum('mois','bimestre','trimestre','quadrimestre','semestre','an','sans') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'sans' COMMENT 'Fréquence',
  `id_partenaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `prix_achat` decimal(8,2) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `serial` varchar(512) COLLATE utf8_swedish_ci DEFAULT NULL,
  `visible` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui' COMMENT 'Flag qui permet',
  `visible_sur_pdf` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'oui',
  `visibilite_prix` enum('visible','invisible') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'visible' COMMENT 'Visibilité',
  `neuf` enum('oui','non') COLLATE utf8_swedish_ci DEFAULT 'oui',
  `date_achat` date DEFAULT NULL,
  `ref_simag` varchar(50) COLLATE utf8_swedish_ci DEFAULT NULL,
  `commentaire` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  `principal` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non' COMMENT 'Défini le produit comme étant le produit principal du pack.',
  `val_modifiable` enum('oui','non') COLLATE utf8_swedish_ci NOT NULL DEFAULT 'non' COMMENT 'Ce produit est il modifiable au mois le mois',
  `valeur` int(10) UNSIGNED DEFAULT NULL COMMENT 'Nombre de point pour ce produit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pack_produit_produit`
--

CREATE TABLE `pack_produit_produit` (
  `id_pack_produit_produit` mediumint(8) UNSIGNED NOT NULL,
  `pack_produit_produit` varchar(200) NOT NULL,
  `ordre` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id_panier` mediumint(8) UNSIGNED NOT NULL,
  `panier` varchar(32) NOT NULL COMMENT 'Hash du panier',
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_client` mediumint(8) UNSIGNED NOT NULL,
  `num_client` varchar(32) DEFAULT NULL,
  `content` text NOT NULL,
  `url_retour_success` varchar(255) NOT NULL,
  `url_retour_error` varchar(255) NOT NULL,
  `livraison` text NOT NULL COMMENT 'Adresse de livraison en format JSON',
  `facturation` text DEFAULT NULL COMMENT 'Adresse de facturatuin en format JSON',
  `statut` enum('en_cours','valide','annule','affaire','signe','paye','score_refuse') NOT NULL DEFAULT 'en_cours',
  `permalien` varchar(32) DEFAULT NULL COMMENT 'permalien permettant la reprise de la souscription',
  `expire_permalien` datetime DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `siret` varchar(32) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `meelo_record_id` varchar(50) DEFAULT NULL,
  `meelo_record_url` varchar(255) DEFAULT NULL,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `parc`
--

CREATE TABLE `parc` (
  `id_parc` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_produit` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `ref` varchar(32) DEFAULT NULL,
  `libelle` varchar(128) NOT NULL,
  `divers` text DEFAULT NULL,
  `serial` varchar(64) NOT NULL,
  `etat` enum('broke','loue','reloue','vole','vendu') NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Date de création',
  `date_inactif` date DEFAULT NULL COMMENT 'Date de passage en inactif',
  `date_garantie` date DEFAULT NULL COMMENT 'Date de fin de garantie',
  `date_achat` date DEFAULT NULL,
  `provenance` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'affaire de provenance',
  `existence` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `id_bon_de_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `caracteristique` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset`
--

CREATE TABLE `password_reset` (
  `id_password_reset` int(10) UNSIGNED NOT NULL,
  `code` varchar(62) NOT NULL,
  `expire` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `id_magasin_vendeur` int(10) UNSIGNED NOT NULL,
  `enable` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pays`
--

CREATE TABLE `pays` (
  `id_pays` char(2) CHARACTER SET utf8 NOT NULL,
  `pays` varchar(128) CHARACTER SET utf8 NOT NULL
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
-- Structure de la table `pdf_affaire`
--

CREATE TABLE `pdf_affaire` (
  `id_pdf_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `provenance` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pdf_societe`
--

CREATE TABLE `pdf_societe` (
  `id_pdf_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `nom_document` varchar(150) NOT NULL,
  `date_expiration` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `phone`
--

CREATE TABLE `phone` (
  `id_phone` mediumint(8) UNSIGNED NOT NULL,
  `phone` varchar(64) NOT NULL,
  `sip` varchar(32) NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_asterisk` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `politesse`
--

CREATE TABLE `politesse` (
  `id_politesse` tinyint(3) UNSIGNED NOT NULL,
  `type` enum('prefixee','postfixee') NOT NULL DEFAULT 'prefixee',
  `politesse` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Formules de politesse' PACK_KEYS=0;

--
-- Déchargement des données de la table `politesse`
--

INSERT INTO `politesse` (`id_politesse`, `type`, `politesse`) VALUES
(8, 'prefixee', 'Suite à notre conversation téléphonique, veuillez trouver ci-dessous nos meilleurs tarifs pour l\'affaire nous concernant. Les delais à titre indicatif sont de 4 semaines environ.'),
(2, 'prefixee', 'Veuillez trouver ci-dessous notre proposition concernant la fourniture des matériels demandés, ainsi que de leur installation.'),
(3, 'prefixee', 'Veuillez trouver ci-dessous notre proposition concernant l\'affaire évoquée récemment.'),
(4, 'postfixee', 'Nous restons à votre entière disposition pour tous compléments d\'information. Veuillez agréer, Madame, l\'expression de nos sentiments les meilleurs.'),
(5, 'postfixee', 'Nous restons à votre entière disposition pour tous compléments d\'information. Veuillez agréer, Monsieur, l\'expression de nos sentiments les meilleurs.'),
(6, 'postfixee', 'Veuillez agréer, Monsieur, l\'expression de nos sentiments les meilleurs.'),
(7, 'postfixee', 'Veuillez agréer, Madame, l\'expression de nos sentiments les meilleurs.'),
(9, 'prefixee', 'Suite à notre conversation téléphonique, veuillez trouver ci-dessous nos meilleurs tarifs pour l\'affaire nous concernant.'),
(10, 'prefixee', 'Votre domaine arrive à échéance dans 30 jours. Si vous le désirez, vous pouvez nous renvoyer le bon pour accord ci-joint afin de renouveller votre domaine pour une durée d\'un an, sans quoi celui-ci redeviendra disponible à l\'enregistrement au public.'),
(11, 'prefixee', 'Votre domaine arrive à échéance dans 15 jours. Si vous le désirez, vous pouvez nous renvoyer le bon pour accord ci-joint afin de renouveller votre domaine pour une durée d\'un an, sans quoi celui-ci redeviendra disponible à l\'enregistrement au public.'),
(12, 'postfixee', 'Veuillez agréer, Mademoiselle, l\'expression de nos sentiments les meilleurs.'),
(14, 'prefixee', 'Votre domaine arrive à échéance dans moins d\'une semaine. Si vous le désirez, vous pouvez nous renvoyer le bon pour accord ci-joint afin de renouveller votre domaine pour une durée d\'un an, sans quoi celui-ci redeviendra disponible au public.');

-- --------------------------------------------------------

--
-- Structure de la table `privilege`
--

CREATE TABLE `privilege` (
  `id_privilege` smallint(5) UNSIGNED NOT NULL,
  `privilege` varchar(32) NOT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Privileges pour les droits des profils';

--
-- Déchargement des données de la table `privilege`
--

INSERT INTO `privilege` (`id_privilege`, `privilege`, `note`) VALUES
(1, 'select', ''),
(2, 'insert', 'Insertion, Duplication'),
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

CREATE TABLE `processeur` (
  `id_processeur` mediumint(8) UNSIGNED NOT NULL,
  `processeur` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `processeur`
--

INSERT INTO `processeur` (`id_processeur`, `processeur`) VALUES
(1, 'Intel Pentium 4'),
(2, 'AMD Opteron'),
(3, 'Intel Pentium III'),
(4, 'AMD Athlon'),
(5, 'Intel Pentium II'),
(6, 'AMD Sempron'),
(7, 'Intel Pentium'),
(8, 'AMD Duron'),
(9, 'Intel Xeon'),
(10, 'Intel Pentium D945'),
(11, 'Intel CELERON'),
(12, 'Centrino Duo'),
(13, 'Dual Core'),
(14, 'AMD Turion'),
(15, 'Intel Core 2 Duo'),
(16, 'Intel Xscale'),
(17, 'Centrino M750'),
(18, 'Centrino'),
(19, 'Core Duo D930'),
(20, 'Intel Core Duo'),
(21, 'Intel Core Solo'),
(22, 'Intel Centrino Core Duo'),
(23, 'Intel Pentium D925'),
(24, 'Sempron 3600'),
(25, 'AMD 64'),
(26, 'Intel Xeon Quad Core'),
(27, 'Pentium Dual Core E2160'),
(28, 'INTEL 2 QUADCORE'),
(29, 'Turion'),
(30, 'AMD X2 5200'),
(31, 'Core 2 Quad Q9550'),
(32, 'Pentium D820'),
(33, 'Pentium Dual Core E5200'),
(34, 'Core 2 Duo E6405'),
(35, 'Quad Core Xeon'),
(36, 'Intel Xéon E3110'),
(37, 'Core 2 Duo E7300'),
(38, 'Core 2 Duo T6400'),
(39, 'Core 2 Duo SL9400'),
(40, 'Core 2 Duo T6570'),
(41, 'Core 2 Duo T6670'),
(42, 'Pentium Dual Core E5300'),
(43, 'Intel Xéon E5504'),
(44, 'Intel Xéon E5502'),
(45, 'Intel pentium G6950'),
(46, 'Intel Pentium i3 540'),
(47, 'Intel Core 2 Duo E7500'),
(48, 'Intel Xeon E5645'),
(49, 'Intel Xeon E5646'),
(50, 'Intel Core i5'),
(51, 'Core 2 Duo'),
(52, 'Core 2 Duo'),
(53, 'Intel Pentium E5800'),
(54, 'TURION II'),
(55, 'CORE i3'),
(56, 'Intel Dual Core i5'),
(57, 'core i7'),
(58, 'Dual Core E5500'),
(59, 'E5800'),
(60, 'Core i5'),
(61, 'Xeon E5603'),
(62, 'Dual Core E5700'),
(63, 'Intel Xeon E5620'),
(64, 'Quad Core'),
(65, 'Intel DB65AL'),
(66, 'AMD Geode'),
(67, 'Intel Atom'),
(68, 'Intel Pentium G630'),
(69, 'Intel Xeon X3430'),
(70, 'Intel Xeon E5506'),
(71, 'Intel Pentium G850'),
(72, 'Intel Xeon E3-1220'),
(73, 'Intel B840'),
(74, 'Intel Core I3 2100'),
(75, 'Core i3 2120'),
(76, 'XEON QUAD CORE E5620'),
(77, 'Xeon E5606'),
(78, '806 Mhz'),
(79, 'I3-2120'),
(80, 'XEON E5-1603'),
(81, 'CORE I3 550'),
(82, 'core i3 2350M'),
(83, 'AMD Turion Dual Core'),
(84, 'core i3 2370M'),
(85, 'Intel Pentium G640'),
(86, 'Intel Core i5 3470'),
(87, 'Intel G870'),
(88, 'G2120'),
(89, 'AMD Fusion E2-3200'),
(90, 'Intel Core i5 3570'),
(91, 'core i5 3427U'),
(92, 'Intel Xeon E5-2640'),
(93, 'Intel Core i5 3470S'),
(94, 'CORE i3-2120'),
(95, 'Intel Xeon E5-2620'),
(96, 'AMD A6 5400K'),
(97, 'AMD Athlon ll'),
(98, 'Intel Core i7'),
(99, 'Intel Xeon E3-1225V2'),
(100, 'Intel Core i7 2640M'),
(101, 'Intel Pentium G645'),
(102, 'Intel Xeon E3-1220V2'),
(103, 'Intel Core i3'),
(104, 'Intel Core i7 3770'),
(105, 'Intel Xeon E5-2403'),
(106, 'Core i3-3220'),
(107, 'Core i3-3110M'),
(108, 'Core i5-3210M'),
(109, 'Core i5 3230M'),
(110, 'Core i5 3380M'),
(111, 'Intel Core i7 3632QM'),
(112, 'Core i3-3240'),
(113, 'Intel Core i3 3120'),
(114, 'INTEL Xeon E5-2420'),
(115, 'Intel Core i3 3120M'),
(116, 'Quadricoeur Intel Core i7'),
(117, 'Intel Core i5 3230M'),
(118, 'AMD Turion II'),
(119, 'Intel Xeon E3'),
(120, 'Intel Core i7-4770'),
(121, 'Intel Core i5 3470T'),
(122, 'Intel Core i3-2130'),
(123, 'Intel Core i3 3220'),
(124, 'Intel Core i5-4300Y'),
(125, 'Intel Core i5 4200U'),
(126, 'Intel Xeon E5-2690'),
(127, 'Intel Xeon E5-2609'),
(128, '2* Intel Xeon E5-2690'),
(129, 'Core i7 4700MQ'),
(130, 'XEON E5-2603'),
(131, 'Intel Core I3-4130'),
(132, 'Intel Core i7 4550U'),
(133, 'Intel Core i5 4570'),
(134, 'Intel E5-2450'),
(135, 'Intel IVB Socket'),
(136, 'Intel Xeon E5'),
(137, 'Intel I7 4770T'),
(138, 'Intel Pentium'),
(139, 'Core i3 4000M'),
(140, 'Intel Xeon E5-2450'),
(141, 'Intel Core i7 4500U'),
(142, 'Bi-Processeur Xeon E5-2637'),
(143, 'Intel Core i5 4200M'),
(144, 'Bi-Processeurs Intel E5-2660'),
(145, 'Intel Core i5-4210U'),
(146, 'Intel Core i5 4130'),
(147, 'Intel Xeon E3-1220V3'),
(148, 'QuadCore ci5'),
(149, 'Intel Xeon E3 -1270V3'),
(150, 'Core i5 4590'),
(151, 'Core i5 4210M'),
(152, 'Core i5 4202Y'),
(153, 'Core i3 4030U'),
(154, 'Core i3 4150'),
(155, 'INTEL E5 2420 V2'),
(156, 'Tower Xeon'),
(157, 'Core i3 4030U'),
(158, 'Intel Core i5-4570S'),
(159, 'Xeon E3 1226 v3'),
(160, 'Core i7 bicoeur'),
(161, 'E3 1231V3'),
(162, 'Xeon E5-2603'),
(163, 'E3 1230V3'),
(164, 'Intel Pentium G3240'),
(165, 'Core i5 4310M'),
(166, 'Core i5 4310U'),
(167, 'Core i3 4550'),
(168, 'i3-4030U'),
(169, 'i3-2100'),
(170, 'i5-3470S'),
(171, 'i5-2520M'),
(172, 'E5-2670v3'),
(173, 'T9550'),
(174, 'P8400'),
(175, 'i3-2350M'),
(176, 'i5-3330S'),
(177, 'i3-4160T'),
(178, 'i5-4210M'),
(179, 'i5-4590T'),
(180, 'i5-4590S'),
(181, 'i7-4790'),
(182, 'intel E5-2620'),
(183, 'i3-4000M'),
(184, 'E5-1620V3'),
(185, 'bi-processeurs Xeon E5-2620v3'),
(186, 'Hexa-core E5-2420 v2'),
(187, 'Quadricoeur i5'),
(188, 'i3-4005U'),
(189, 'E5-2407V2'),
(190, 'E5-2650V2'),
(191, 'A6 5350M'),
(192, 'E5-1620V2'),
(193, 'Core i3 4160'),
(194, 'Intel Celeron Processor N2820'),
(195, 'Z2560'),
(196, 'Core i5 quadricoeur'),
(197, 'i7-4770S'),
(198, 'i7-4600U'),
(199, 'i5-4460S'),
(200, 'i7-4790S'),
(201, 'i3-4160'),
(202, 'i7 4710MQ'),
(203, 'Quadricoeur Xeon E3-1246V3'),
(204, 'Core i7 4710MQ'),
(205, 'Intel E3-1230V2'),
(206, 'Xeon E5-2620V3'),
(207, 'A4 PRO-7300B'),
(208, 'i5-5200U'),
(211, 'E5-2609V3'),
(212, 'Intel Xeon E5-2640 v3'),
(215, 'Celeron N2940'),
(216, 'Xeon E5-2609V3'),
(217, 'Core i5-4440'),
(218, 'Armada 370'),
(219, 'Core i5-5200U'),
(220, 'AMD A6-6400K'),
(221, 'Core i5 2400'),
(222, 'i5-3427U'),
(223, 'i7-5500U'),
(224, 'AMD Fusion'),
(225, 'Core i5 4300M'),
(226, 'E5-1603V3'),
(227, 'Core i3 4170'),
(228, 'I3-5010U'),
(229, 'Core I5-4590'),
(230, 'Core I3'),
(231, 'Core I3'),
(232, 'Intel Core i5-4200M'),
(233, 'Intel Core i3-4030U'),
(234, 'Intel Core i5-5300U'),
(235, 'Intel Core i7-4710MQ'),
(236, 'Core i5 4590S'),
(237, 'Core i7 4790'),
(238, 'Xéon E5-2603 V3'),
(239, 'i7-6700T'),
(240, 'i5-6500'),
(241, 'Intel Core i5 4460'),
(242, 'i7-4712MQ'),
(243, 'Core i3 5010U'),
(244, 'Intel Core i5-6440'),
(245, 'Intel Core i5-6440HQ'),
(246, 'Xeon e5-2630v3'),
(247, 'Intel Core i3-5005U'),
(248, 'Core I3-505U'),
(249, 'Intel Celeron N3050'),
(250, 'Core i5-6500'),
(251, 'XEON E5-2430Lv2'),
(252, 'Intel Core i3-6100T'),
(253, 'Pentium G3260'),
(254, 'i5-6400'),
(255, 'Intel Core i7-6700'),
(256, 'Intel Core i7-4790'),
(257, 'Core i3 6100'),
(258, 'Core i5 6200U'),
(259, 'A9'),
(260, 'E3-1220V5'),
(261, 'Xeon E3-1220V5'),
(262, 'Intel Core i5-6300U'),
(263, 'Core i5-6300U'),
(264, 'Intel Core m3'),
(265, 'Core i5-6200U'),
(266, 'E5-2620v4'),
(267, 'Xeon E5-2603 v4'),
(268, 'Intel Xeon E5-2640 v4'),
(269, 'Intel Core i5-7200U'),
(270, 'Intel Xeon E5-2620v4'),
(271, '2 x Intel Xeon E5-2620v4'),
(272, 'i7-6500U'),
(273, 'Intel Core i7-700HQ'),
(274, 'Intel Celeron Quad Core'),
(275, 'GX-420GI'),
(276, 'Core i5-6500T'),
(277, 'Core i3'),
(278, 'J3160'),
(279, 'i7-7820HQ'),
(280, 'Core i5-7500T'),
(281, 'i5-8250U'),
(282, 'Intel Core i3-7100'),
(283, 'Intel Core i7-7700K'),
(284, 'Intel Xeon Silver 4110'),
(285, 'Core i5-7500'),
(286, 'Intel Xeon W-2123'),
(287, 'Intel Core i7-7500U'),
(288, 'Xeon Silver 4114'),
(289, '2 x Intel Xeon 4110'),
(290, 'Intel Celeron J1900'),
(291, 'Core i3-7100U'),
(292, 'i5-8400T'),
(293, 'Core i3-8100T'),
(294, 'Xeon Bronze 3106'),
(295, 'Core i5-8500'),
(296, 'Xeon E-2174G');

-- --------------------------------------------------------

--
-- Structure de la table `produit`
--

CREATE TABLE `produit` (
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(32) NOT NULL,
  `produit` varchar(500) NOT NULL,
  `prix_achat` decimal(10,2) UNSIGNED NOT NULL,
  `taxe_ecotaxe` decimal(10,2) UNSIGNED DEFAULT NULL,
  `taxe_ecomob` decimal(10,2) UNSIGNED DEFAULT NULL,
  `url_produit` decimal(10,2) UNSIGNED DEFAULT NULL,
  `id_fabriquant` mediumint(8) UNSIGNED NOT NULL,
  `id_sous_categorie` mediumint(8) UNSIGNED NOT NULL,
  `type` enum('fixe','portable','sans_objet','immateriel') NOT NULL DEFAULT 'fixe',
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `obsolete` enum('oui','non') NOT NULL DEFAULT 'non',
  `id_produit_dd` smallint(3) UNSIGNED DEFAULT NULL,
  `id_produit_dotpitch` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_format` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_garantie_uc` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_garantie_ecran` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_garantie_imprimante` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_lan` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_lecteur` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_OS` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_puissance` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_ram` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_technique` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_type` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_typeecran` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_viewable` smallint(6) UNSIGNED DEFAULT NULL,
  `id_processeur` mediumint(8) UNSIGNED DEFAULT NULL,
  `etat` enum('actif','inactif') NOT NULL DEFAULT 'actif',
  `commentaire` varchar(512) DEFAULT NULL,
  `type_offre` enum('bureautique','informatique','telephonie','multimedia','atol') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `site_associe` enum('cleodis','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe') DEFAULT NULL,
  `loyer` float(6,3) DEFAULT NULL,
  `assurance` float(6,3) DEFAULT NULL,
  `frais_de_gestion` float(6,3) DEFAULT NULL,
  `serenite` float(6,3) DEFAULT NULL,
  `maintenance` float(6,3) DEFAULT NULL,
  `hotline` float(6,3) DEFAULT NULL,
  `supervision` float(6,3) DEFAULT NULL,
  `support` float(6,3) DEFAULT NULL,
  `duree` int(11) DEFAULT NULL,
  `loyer1` float(6,3) DEFAULT NULL,
  `duree2` int(11) DEFAULT NULL,
  `duree1` int(11) DEFAULT NULL,
  `loyer2` float(6,3) DEFAULT NULL,
  `visible_sur_site` enum('oui','non') NOT NULL DEFAULT 'non',
  `avis_expert` text DEFAULT NULL,
  `services` set('installation_sur_site','evolutivite_offre','garantie_maintenance','intervention_site','support_utilisateur','reprise_recyclage') DEFAULT NULL,
  `id_produit_env` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_besoins` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_tel_produit` smallint(6) UNSIGNED DEFAULT NULL,
  `id_produit_tel_type` smallint(6) UNSIGNED DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `ean` varchar(14) DEFAULT NULL,
  `id_document_contrat` mediumint(8) UNSIGNED DEFAULT NULL,
  `url_image` varchar(500) DEFAULT NULL,
  `livreur` mediumint(8) UNSIGNED DEFAULT NULL,
  `frais_livraison` float(6,3) DEFAULT NULL,
  `ref_garantie` varchar(15) DEFAULT NULL,
  `id_licence_type` mediumint(8) UNSIGNED DEFAULT NULL,
  `increment` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `produit_besoins`
--

CREATE TABLE `produit_besoins` (
  `id_produit_besoins` smallint(6) UNSIGNED NOT NULL,
  `produit_besoins` varchar(50) DEFAULT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_besoins`
--

INSERT INTO `produit_besoins` (`id_produit_besoins`, `produit_besoins`, `ordre`) VALUES
(1, 'Bureautique', 0),
(2, 'Calcul', 0),
(3, 'Infographie', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_dd`
--

CREATE TABLE `produit_dd` (
  `id_produit_dd` smallint(3) UNSIGNED NOT NULL,
  `produit_dd` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_dd`
--

INSERT INTO `produit_dd` (`id_produit_dd`, `produit_dd`) VALUES
(1, '1 x 20 Go'),
(2, '1 x 30 Go'),
(3, '1 x 40 Go'),
(4, '1 x 50 Go'),
(5, '1 x 60 Go'),
(6, '1 x 70 Go'),
(7, '1 x 80 Go'),
(8, '1 x 90 Go'),
(9, '1 x 100 Go'),
(10, '1 x 110 Go'),
(11, '1 x 120 Go'),
(12, '1 x 130 Go'),
(13, '1 x 140 Go'),
(14, '1 x 150 Go'),
(15, '1 x 160 Go'),
(16, '1 x 170 Go'),
(17, '1 x 180 Go'),
(18, '1 x 190 Go'),
(19, '1 x 200 Go'),
(20, '1 x 210 Go'),
(21, '1 x 220 Go'),
(22, '1 x 230 Go'),
(23, '1 x 240 Go'),
(24, '1 x 250 Go'),
(25, '1 x 260 Go'),
(26, '1 x 270 Go'),
(27, '1 x 280 Go'),
(28, '1 x 290 Go'),
(29, '1 x 300 Go'),
(30, '1 x 310 Go'),
(31, '1 x 320 Go'),
(32, '1 x 330 Go'),
(33, '1 x 340 Go'),
(34, '1 x 350 Go'),
(35, '1 x 360 Go'),
(36, '1 x 370 Go'),
(37, '1 x 380 Go'),
(38, '1 x 390 Go'),
(39, '1 x 400 Go'),
(40, '1 x 410 Go'),
(41, '1 x 420 Go'),
(42, '1 x 430 Go'),
(43, '1 x 440 Go'),
(44, '1 x 450 Go'),
(45, '1 x 460 Go'),
(46, '1 x 470 Go'),
(47, '1 x 480 Go'),
(48, '1 x 490 Go'),
(49, '1 x 500 Go'),
(50, '1 x 510 Go'),
(51, '1 x 520 Go'),
(52, '1 x 530 Go'),
(53, '1 x 540 Go'),
(54, '1 x 550 Go'),
(55, '1 x 560 Go'),
(56, '1 x 570 Go'),
(57, '1 x 580 Go'),
(58, '1 x 590 Go'),
(59, '1 x 600 Go'),
(60, '1 x 610 Go'),
(61, '1 x 620 Go'),
(62, '1 x 630 Go'),
(63, '1 x 640 Go'),
(64, '1 x 650 Go'),
(65, '1 x 660 Go'),
(66, '1 x 670 Go'),
(67, '1 x 680 Go'),
(68, '1 x 690 Go'),
(69, '1 x 700 Go'),
(70, '2 x 20 Go'),
(71, '2 x 30 Go'),
(72, '2 x 40 Go'),
(73, '2 x 50 Go'),
(74, '2 x 60 Go'),
(75, '2 x 70 Go'),
(76, '2 x 80 Go'),
(77, '2 x 90 Go'),
(78, '2 x 100 Go'),
(79, '2 x 110 Go'),
(80, '2 x 120 Go'),
(81, '2 x 130 Go'),
(82, '2 x 140 Go'),
(83, '2 x 150 Go'),
(84, '2 x 160 Go'),
(85, '2 x 170 Go'),
(86, '2 x 180 Go'),
(87, '2 x 190 Go'),
(88, '2 x 200 Go'),
(89, '2 x 210 Go'),
(90, '2 x 220 Go'),
(91, '2 x 230 Go'),
(92, '2 x 240 Go'),
(93, '2 x 250 Go'),
(94, '2 x 260 Go'),
(95, '2 x 270 Go'),
(96, '2 x 280 Go'),
(97, '2 x 290 Go'),
(98, '2 x 300 Go'),
(99, '2 x 310 Go'),
(100, '2 x 320 Go'),
(101, '2 x 330 Go'),
(102, '2 x 340 Go'),
(103, '2 x 350 Go'),
(104, '2 x 360 Go'),
(105, '2 x 370 Go'),
(106, '2 x 380 Go'),
(107, '2 x 390 Go'),
(108, '2 x 400 Go'),
(109, '2 x 410 Go'),
(110, '2 x 420 Go'),
(111, '2 x 430 Go'),
(112, '2 x 440 Go'),
(113, '2 x 450 Go'),
(114, '2 x 460 Go'),
(115, '2 x 470 Go'),
(116, '2 x 480 Go'),
(117, '2 x 490 Go'),
(118, '2 x 500 Go'),
(119, '2 x 510 Go'),
(120, '2 x 520 Go'),
(121, '2 x 530 Go'),
(122, '2 x 540 Go'),
(123, '2 x 550 Go'),
(124, '2 x 560 Go'),
(125, '2 x 570 Go'),
(126, '2 x 580 Go'),
(127, '2 x 590 Go'),
(128, '2 x 600 Go'),
(129, '2 x 610 Go'),
(130, '2 x 620 Go'),
(131, '2 x 630 Go'),
(132, '2 x 640 Go'),
(133, '2 x 650 Go'),
(134, '2 x 660 Go'),
(135, '2 x 670 Go'),
(136, '2 x 680 Go'),
(137, '2 x 690 Go'),
(138, '2 x 700 Go'),
(139, '3 x 20 Go'),
(140, '3 x 30 Go'),
(141, '3 x 40 Go'),
(142, '3 x 50 Go'),
(143, '3 x 60 Go'),
(144, '3 x 70 Go'),
(145, '3 x 80 Go'),
(146, '3 x 90 Go'),
(147, '3 x 100 Go'),
(148, '3 x 110 Go'),
(149, '3 x 120 Go'),
(150, '3 x 130 Go'),
(151, '3 x 140 Go'),
(152, '3 x 150 Go'),
(153, '3 x 160 Go'),
(154, '3 x 170 Go'),
(155, '3 x 180 Go'),
(156, '3 x 190 Go'),
(157, '3 x 200 Go'),
(158, '3 x 210 Go'),
(159, '3 x 220 Go'),
(160, '3 x 230 Go'),
(161, '3 x 240 Go'),
(162, '3 x 250 Go'),
(163, '3 x 260 Go'),
(164, '3 x 270 Go'),
(165, '3 x 280 Go'),
(166, '3 x 290 Go'),
(167, '3 x 300 Go'),
(168, '3 x 310 Go'),
(169, '3 x 320 Go'),
(170, '3 x 330 Go'),
(171, '3 x 340 Go'),
(172, '3 x 350 Go'),
(173, '3 x 360 Go'),
(174, '3 x 370 Go'),
(175, '3 x 380 Go'),
(176, '3 x 390 Go'),
(177, '3 x 400 Go'),
(178, '3 x 410 Go'),
(179, '3 x 420 Go'),
(180, '3 x 430 Go'),
(181, '3 x 440 Go'),
(182, '3 x 450 Go'),
(183, '3 x 460 Go'),
(184, '3 x 470 Go'),
(185, '3 x 480 Go'),
(186, '3 x 490 Go'),
(187, '3 x 500 Go'),
(188, '3 x 510 Go'),
(189, '3 x 520 Go'),
(190, '3 x 530 Go'),
(191, '3 x 540 Go'),
(192, '3 x 550 Go'),
(193, '3 x 560 Go'),
(194, '3 x 570 Go'),
(195, '3 x 580 Go'),
(196, '3 x 590 Go'),
(197, '3 x 600 Go'),
(198, '3 x 610 Go'),
(199, '3 x 620 Go'),
(200, '3 x 630 Go'),
(201, '3 x 640 Go'),
(202, '3 x 650 Go'),
(203, '3 x 660 Go'),
(204, '3 x 670 Go'),
(205, '3 x 680 Go'),
(206, '3 x 690 Go'),
(207, '3 x 700 Go'),
(208, '4 x 20 Go'),
(209, '4 x 30 Go'),
(210, '4 x 40 Go'),
(211, '4 x 50 Go'),
(212, '4 x 60 Go'),
(213, '4 x 70 Go'),
(214, '4 x 80 Go'),
(215, '4 x 90 Go'),
(216, '4 x 100 Go'),
(217, '4 x 110 Go'),
(218, '4 x 120 Go'),
(219, '4 x 130 Go'),
(220, '4 x 140 Go'),
(221, '4 x 150 Go'),
(222, '4 x 160 Go'),
(223, '4 x 170 Go'),
(224, '4 x 180 Go'),
(225, '4 x 190 Go'),
(226, '4 x 200 Go'),
(227, '4 x 210 Go'),
(228, '4 x 220 Go'),
(229, '4 x 230 Go'),
(230, '4 x 240 Go'),
(231, '4 x 250 Go'),
(232, '4 x 260 Go'),
(233, '4 x 270 Go'),
(234, '4 x 280 Go'),
(235, '4 x 290 Go'),
(236, '4 x 300 Go'),
(237, '4 x 310 Go'),
(238, '4 x 320 Go'),
(239, '4 x 330 Go'),
(240, '4 x 340 Go'),
(241, '4 x 350 Go'),
(242, '4 x 360 Go'),
(243, '4 x 370 Go'),
(244, '4 x 380 Go'),
(245, '4 x 390 Go'),
(246, '4 x 400 Go'),
(247, '4 x 410 Go'),
(248, '4 x 420 Go'),
(249, '4 x 430 Go'),
(250, '4 x 440 Go'),
(251, '4 x 450 Go'),
(252, '4 x 460 Go'),
(253, '4 x 470 Go'),
(254, '4 x 480 Go'),
(255, '4 x 490 Go'),
(256, '4 x 500 Go'),
(257, '4 x 510 Go'),
(258, '4 x 520 Go'),
(259, '4 x 530 Go'),
(260, '4 x 540 Go'),
(261, '4 x 550 Go'),
(262, '4 x 560 Go'),
(263, '4 x 570 Go'),
(264, '4 x 580 Go'),
(265, '4 x 590 Go'),
(266, '4 x 600 Go'),
(267, '4 x 610 Go'),
(268, '4 x 620 Go'),
(269, '4 x 630 Go'),
(270, '4 x 640 Go'),
(271, '4 x 650 Go'),
(272, '4 x 660 Go'),
(273, '4 x 670 Go'),
(274, '4 x 680 Go'),
(275, '4 x 690 Go'),
(276, '4 x 700 Go'),
(277, '5 x 20 Go'),
(278, '5 x 30 Go'),
(279, '5 x 40 Go'),
(280, '5 x 50 Go'),
(281, '5 x 60 Go'),
(282, '5 x 70 Go'),
(283, '5 x 80 Go'),
(284, '5 x 90 Go'),
(285, '5 x 100 Go'),
(286, '5 x 110 Go'),
(287, '5 x 120 Go'),
(288, '5 x 130 Go'),
(289, '5 x 140 Go'),
(290, '5 x 150 Go'),
(291, '5 x 160 Go'),
(292, '5 x 170 Go'),
(293, '5 x 180 Go'),
(294, '5 x 190 Go'),
(295, '5 x 200 Go'),
(296, '5 x 210 Go'),
(297, '5 x 220 Go'),
(298, '5 x 230 Go'),
(299, '5 x 240 Go'),
(300, '5 x 250 Go'),
(301, '5 x 260 Go'),
(302, '5 x 270 Go'),
(303, '5 x 280 Go'),
(304, '5 x 290 Go'),
(305, '5 x 300 Go'),
(306, '5 x 310 Go'),
(307, '5 x 320 Go'),
(308, '5 x 330 Go'),
(309, '5 x 340 Go'),
(310, '5 x 350 Go'),
(311, '5 x 360 Go'),
(312, '5 x 370 Go'),
(313, '5 x 380 Go'),
(314, '5 x 390 Go'),
(315, '5 x 400 Go'),
(316, '5 x 410 Go'),
(317, '5 x 420 Go'),
(318, '5 x 430 Go'),
(319, '5 x 440 Go'),
(320, '5 x 450 Go'),
(321, '5 x 460 Go'),
(322, '5 x 470 Go'),
(323, '5 x 480 Go'),
(324, '5 x 490 Go'),
(325, '5 x 500 Go'),
(326, '5 x 510 Go'),
(327, '5 x 520 Go'),
(328, '5 x 530 Go'),
(329, '5 x 540 Go'),
(330, '5 x 550 Go'),
(331, '5 x 560 Go'),
(332, '5 x 570 Go'),
(333, '5 x 580 Go'),
(334, '5 x 590 Go'),
(335, '5 x 600 Go'),
(336, '5 x 610 Go'),
(337, '5 x 620 Go'),
(338, '5 x 630 Go'),
(339, '5 x 640 Go'),
(340, '5 x 650 Go'),
(341, '5 x 660 Go'),
(342, '5 x 670 Go'),
(343, '5 x 680 Go'),
(344, '5 x 690 Go'),
(345, '5 x 700 Go'),
(346, '6 x 20 Go'),
(347, '6 x 30 Go'),
(348, '6 x 40 Go'),
(349, '6 x 50 Go'),
(350, '6 x 60 Go'),
(351, '6 x 70 Go'),
(352, '6 x 80 Go'),
(353, '6 x 90 Go'),
(354, '6 x 100 Go'),
(355, '6 x 110 Go'),
(356, '6 x 120 Go'),
(357, '6 x 130 Go'),
(358, '6 x 140 Go'),
(359, '6 x 150 Go'),
(360, '6 x 160 Go'),
(361, '6 x 170 Go'),
(362, '6 x 180 Go'),
(363, '6 x 190 Go'),
(364, '6 x 200 Go'),
(365, '6 x 210 Go'),
(366, '6 x 220 Go'),
(367, '6 x 230 Go'),
(368, '6 x 240 Go'),
(369, '6 x 250 Go'),
(370, '6 x 260 Go'),
(371, '6 x 270 Go'),
(372, '6 x 280 Go'),
(373, '6 x 290 Go'),
(374, '6 x 300 Go'),
(375, '6 x 310 Go'),
(376, '6 x 320 Go'),
(377, '6 x 330 Go'),
(378, '6 x 340 Go'),
(379, '6 x 350 Go'),
(380, '6 x 360 Go'),
(381, '6 x 370 Go'),
(382, '6 x 380 Go'),
(383, '6 x 390 Go'),
(384, '6 x 400 Go'),
(385, '6 x 410 Go'),
(386, '6 x 420 Go'),
(387, '6 x 430 Go'),
(388, '6 x 440 Go'),
(389, '6 x 450 Go'),
(390, '6 x 460 Go'),
(391, '6 x 470 Go'),
(392, '6 x 480 Go'),
(393, '6 x 490 Go'),
(394, '6 x 500 Go'),
(395, '6 x 510 Go'),
(396, '6 x 520 Go'),
(397, '6 x 530 Go'),
(398, '6 x 540 Go'),
(399, '6 x 550 Go'),
(400, '6 x 560 Go'),
(401, '6 x 570 Go'),
(402, '6 x 580 Go'),
(403, '6 x 590 Go'),
(404, '6 x 600 Go'),
(405, '6 x 610 Go'),
(406, '6 x 620 Go'),
(407, '6 x 630 Go'),
(408, '6 x 640 Go'),
(409, '6 x 650 Go'),
(410, '6 x 660 Go'),
(411, '6 x 670 Go'),
(412, '6 x 680 Go'),
(413, '6 x 690 Go'),
(414, '6 x 700 Go'),
(415, '7 x 20 Go'),
(416, '7 x 30 Go'),
(417, '7 x 40 Go'),
(418, '7 x 50 Go'),
(419, '7 x 60 Go'),
(420, '7 x 70 Go'),
(421, '7 x 80 Go'),
(422, '7 x 90 Go'),
(423, '7 x 100 Go'),
(424, '7 x 110 Go'),
(425, '7 x 120 Go'),
(426, '7 x 130 Go'),
(427, '7 x 140 Go'),
(428, '7 x 150 Go'),
(429, '7 x 160 Go'),
(430, '7 x 170 Go'),
(431, '7 x 180 Go'),
(432, '7 x 190 Go'),
(433, '7 x 200 Go'),
(434, '7 x 210 Go'),
(435, '7 x 220 Go'),
(436, '7 x 230 Go'),
(437, '7 x 240 Go'),
(438, '7 x 250 Go'),
(439, '7 x 260 Go'),
(440, '7 x 270 Go'),
(441, '7 x 280 Go'),
(442, '7 x 290 Go'),
(443, '7 x 300 Go'),
(444, '7 x 310 Go'),
(445, '7 x 320 Go'),
(446, '7 x 330 Go'),
(447, '7 x 340 Go'),
(448, '7 x 350 Go'),
(449, '7 x 360 Go'),
(450, '7 x 370 Go'),
(451, '7 x 380 Go'),
(452, '7 x 390 Go'),
(453, '7 x 400 Go'),
(454, '7 x 410 Go'),
(455, '7 x 420 Go'),
(456, '7 x 430 Go'),
(457, '7 x 440 Go'),
(458, '7 x 450 Go'),
(459, '7 x 460 Go'),
(460, '7 x 470 Go'),
(461, '7 x 480 Go'),
(462, '7 x 490 Go'),
(463, '7 x 500 Go'),
(464, '7 x 510 Go'),
(465, '7 x 520 Go'),
(466, '7 x 530 Go'),
(467, '7 x 540 Go'),
(468, '7 x 550 Go'),
(469, '7 x 560 Go'),
(470, '7 x 570 Go'),
(471, '7 x 580 Go'),
(472, '7 x 590 Go'),
(473, '7 x 600 Go'),
(474, '7 x 610 Go'),
(475, '7 x 620 Go'),
(476, '7 x 630 Go'),
(477, '7 x 640 Go'),
(478, '7 x 650 Go'),
(479, '7 x 660 Go'),
(480, '7 x 670 Go'),
(481, '7 x 680 Go'),
(482, '7 x 690 Go'),
(483, '7 x 700 Go'),
(484, '4Go'),
(485, '128Go'),
(486, '64Go'),
(487, '6Go'),
(488, '2x300GB'),
(489, '5x146Go'),
(490, '1* 1To'),
(491, '750Go'),
(492, '1To'),
(493, '3 X 300 GB'),
(494, '2 x 1 To'),
(495, '1 TB'),
(496, 'Western Digital 250G'),
(497, '2 x 4Go'),
(498, '2To'),
(499, '4 x 450 Go'),
(500, '1*256Go'),
(501, '16Go'),
(502, '250Gb'),
(503, '1Tb'),
(504, '120 Gb'),
(505, 'Samsung SSD 120GB'),
(506, '160 Gb'),
(507, '2 x 3 To'),
(508, '1 To'),
(509, '512 Go'),
(510, '2 x 4 Tb'),
(511, '2 x 2 To'),
(512, '2 x 4 To'),
(513, '4 To'),
(514, '1 x 1000 Go'),
(515, '3x146Go'),
(516, '512 Go Flash'),
(517, '21.6 To'),
(518, '24 x 900 Go'),
(519, '1 x 2000 Go'),
(520, '3 To'),
(521, '32 GB'),
(522, '1To'),
(523, '256Go'),
(524, '256Go'),
(525, '1To'),
(526, '2 TB'),
(527, '500GB'),
(528, '256GB'),
(529, '128GB'),
(530, '2 x 8Gb'),
(531, '3 x 100Gb SSD'),
(532, '3 x 600Gb HDD'),
(533, '6 x 600GB SAS'),
(534, '128Gb SSD'),
(535, '1Tb HDD'),
(536, '512GB'),
(537, '1TB'),
(538, '6 x 16GB'),
(539, '192 Go'),
(540, '2 x 300 Go'),
(541, '2 x 300 GB'),
(542, '256 MB SSD'),
(543, '1TB'),
(544, 'Flash 32GB'),
(545, '12GB SAS'),
(546, '256GB SSD'),
(547, '8Go'),
(548, '512 Go SSD'),
(549, '1GB Flash'),
(550, '3 x 600GB SAS HDD'),
(551, '256Go SSD'),
(552, '16Gb'),
(553, '3 x 16Gb DDR4'),
(554, '32GB SSD'),
(555, 'SSD 128Go'),
(556, '6 x 600GB SAS HDD');

-- --------------------------------------------------------

--
-- Structure de la table `produit_dotpitch`
--

CREATE TABLE `produit_dotpitch` (
  `id_produit_dotpitch` smallint(6) UNSIGNED NOT NULL,
  `produit_dotpitch` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_dotpitch`
--

INSERT INTO `produit_dotpitch` (`id_produit_dotpitch`, `produit_dotpitch`) VALUES
(1, '0.2'),
(2, '0.2'),
(3, '0.2'),
(4, '0.2'),
(5, '0.2'),
(6, '0.3'),
(7, '0.3'),
(8, '0.3'),
(9, '0.3'),
(10, '0.3');

-- --------------------------------------------------------

--
-- Structure de la table `produit_env`
--

CREATE TABLE `produit_env` (
  `id_produit_env` smallint(6) UNSIGNED NOT NULL,
  `produit_env` varchar(50) DEFAULT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_env`
--

INSERT INTO `produit_env` (`id_produit_env`, `produit_env`, `ordre`) VALUES
(1, 'Volume d\'impression mensuel < 1500', 0),
(2, '1500 < volume d\'impression < 3000', 0),
(3, 'NULL000 < volume d\'impression < 5000', 0),
(4, 'Volume d\'impression mensuel > 5000', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_format`
--

CREATE TABLE `produit_format` (
  `id_produit_format` smallint(6) UNSIGNED NOT NULL,
  `produit_format` varchar(32) NOT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_format`
--

INSERT INTO `produit_format` (`id_produit_format`, `produit_format`, `ordre`) VALUES
(1, 'A0', 0),
(2, 'A1', 0),
(3, 'A2', 0),
(4, 'A3', 0),
(5, 'A4', 0),
(6, 'A5', 0),
(7, 'A6', 0),
(8, 'A4, A5, A6', 0),
(9, 'A3, A4, A5', 0),
(10, 'A0, A3, A4', 0),
(11, 'A3, A4', 0),
(12, 'A4', 0),
(13, 'Tickets', 0),
(14, 'A4', 0),
(15, 'A4', 0),
(16, 'A4', 0),
(17, 'A4', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_fournisseur`
--

CREATE TABLE `produit_fournisseur` (
  `id_produit_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `prix_ttc` decimal(8,2) NOT NULL DEFAULT 0.00,
  `prix_prestation` decimal(10,4) NOT NULL COMMENT 'Montant HT',
  `recurrence` enum('mensuel','ponctuel','a_la_demande','achat') NOT NULL DEFAULT 'mensuel',
  `departement` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `produit_fournisseur_loyer`
--

CREATE TABLE `produit_fournisseur_loyer` (
  `id_produit_fournisseur_loyer` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `loyer` decimal(6,2) NOT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED NOT NULL,
  `frequence_loyer` enum('mois','trimestre','semestre','an') NOT NULL DEFAULT 'mois'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `produit_garantie`
--

CREATE TABLE `produit_garantie` (
  `id_produit_garantie` smallint(6) UNSIGNED NOT NULL,
  `produit_garantie` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_garantie`
--

INSERT INTO `produit_garantie` (`id_produit_garantie`, `produit_garantie`) VALUES
(1, 'Garantie 1 an'),
(2, 'Garantie 2 ans'),
(3, 'Garantie 3 ans'),
(4, 'Garantie 5 ans'),
(5, 'garantie 4 ans'),
(6, 'Garantie 2 ans'),
(7, 'Garantie 2 ans'),
(8, 'Garantie 3 ans sur site'),
(9, 'Garantie 5 ans sur site J+1'),
(10, 'Garantie 3 ans sur site J+1'),
(11, 'Garantie 5 ans sur site');

-- --------------------------------------------------------

--
-- Structure de la table `produit_lan`
--

CREATE TABLE `produit_lan` (
  `id_produit_lan` smallint(6) UNSIGNED NOT NULL,
  `produit_lan` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_lan`
--

INSERT INTO `produit_lan` (`id_produit_lan`, `produit_lan`) VALUES
(1, '10 Mbit'),
(2, '100 Mbit'),
(3, '1000 Mbit'),
(4, '10000 Mbit'),
(5, '32 Bits'),
(6, '64 bits'),
(7, '10, 100, 1000 Mbit/s'),
(8, '10, 100 Mbit/s'),
(9, '10,100,1000 Mbit/s'),
(10, '10/100/1000'),
(11, '64Bit'),
(12, '64 Bit'),
(13, '64 bit'),
(14, '64 bits'),
(15, '64 bits'),
(16, '10,100 Mbit/s'),
(17, '64 bits'),
(18, '64 bits'),
(19, '64 bits'),
(20, '64 bits');

-- --------------------------------------------------------

--
-- Structure de la table `produit_lecteur`
--

CREATE TABLE `produit_lecteur` (
  `id_produit_lecteur` smallint(6) UNSIGNED NOT NULL,
  `produit_lecteur` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_lecteur`
--

INSERT INTO `produit_lecteur` (`id_produit_lecteur`, `produit_lecteur`) VALUES
(1, 'Lecteur de CD'),
(2, 'Lecteur de DVD'),
(3, 'Graveur de CD'),
(4, 'Graveur de DVD'),
(5, 'Combo'),
(6, 'DVDRW'),
(7, 'Lecteur Graveur DVD'),
(8, 'DVD SuperMulti'),
(9, 'Lecteur DVD ROM SATA'),
(10, 'Lecteur Graveur DVD LG'),
(11, 'Lecteur blu ray'),
(12, 'DVD Super Multi DL'),
(13, 'DVD-RW'),
(14, 'DVD±RW'),
(15, 'DVD-ROM Drive');

-- --------------------------------------------------------

--
-- Structure de la table `produit_links`
--

CREATE TABLE `produit_links` (
  `id_produit_links` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `id_produit_cible` mediumint(8) UNSIGNED NOT NULL,
  `etat` enum('dependant','exclude') NOT NULL COMMENT 'produit cible dépendant du produit / produit cible exclu du produit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `produit_loyer`
--

CREATE TABLE `produit_loyer` (
  `id_produit_loyer` mediumint(8) UNSIGNED NOT NULL,
  `id_produit` mediumint(8) UNSIGNED NOT NULL,
  `duree` int(11) NOT NULL,
  `loyer` decimal(10,4) NOT NULL,
  `ordre` tinyint(3) UNSIGNED NOT NULL,
  `nature` enum('promo','majoration','engagement','prolongation','prolongation_probable') NOT NULL,
  `periodicite` enum('mois','trimestre','semestre','an') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `produit_OS`
--

CREATE TABLE `produit_OS` (
  `id_produit_OS` smallint(6) UNSIGNED NOT NULL,
  `produit_OS` varchar(32) NOT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_OS`
--

INSERT INTO `produit_OS` (`id_produit_OS`, `produit_OS`, `ordre`) VALUES
(1, 'Windows Vista Basic', 0),
(2, 'Windows Vista Enterprise', 0),
(3, 'Windows Vista Premium', 0),
(4, 'Windows Vista Ultimate', 0),
(5, 'Windows XP', 0),
(6, 'Windows 2000', 0),
(7, 'Windows XP PRO', 0),
(8, 'Windows XP Family', 0),
(9, 'Windows 2003 Server', 0),
(10, 'MacOS X', 0),
(11, 'Linux', 0),
(12, 'Windos 7 Pro', 0),
(13, 'Mac OS 10.7 Lion', 0),
(14, 'Windows 7 Pro', 0),
(15, 'Windows 7 Home', 0),
(16, 'WINDOWS SERVER 2008 STANDARD', 0),
(17, 'WINDOWS 2008 R2 OEM', 0),
(18, 'Windows 2008 R2', 0),
(19, 'Seven Pro', 0),
(20, 'Windows Serveur 2008', 0),
(21, 'Windows 8 Pro', 0),
(22, 'Windows 7 Premium', 0),
(23, 'Mac OS 10.8 Lion', 0),
(24, 'Mac Office', 0),
(25, 'Mac OS', 0),
(26, 'Windows 8.1 Pro', 0),
(27, 'Windows Vista Business', 0),
(28, 'Windows 7/8', 0),
(29, 'Windows 8', 0),
(30, 'Windows 8.1', 0),
(31, 'Windows 2012', 0),
(32, 'Windows 2012 R2 Foundation', 0),
(33, 'Windows 7', 0),
(34, 'Windows Server 2012', 0),
(35, 'Windows 7 Professional', 0),
(36, 'DOS gratuit', 0),
(37, 'Windows 7 Home Basic', 0),
(38, 'Non', 0),
(39, 'Android', 0),
(40, 'Mac OS 10.9 Maverick', 0),
(41, 'OS X Yosemite', 0),
(42, 'Android 5.0', 0),
(43, 'Android 6.0', 0),
(44, 'Windows 10 Pro', 0),
(45, 'Windows 10 Pro', 0),
(46, 'Windows 10', 0),
(47, 'Windows 10', 0),
(48, 'Windows 10 Home', 0),
(49, 'Windows CE 6.0', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_puissance`
--

CREATE TABLE `produit_puissance` (
  `id_produit_puissance` smallint(6) UNSIGNED NOT NULL,
  `produit_puissance` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_puissance`
--

INSERT INTO `produit_puissance` (`id_produit_puissance`, `produit_puissance`) VALUES
(1, '1 x 1.5 gHz'),
(2, '1 x 1.6 gHz'),
(3, '1 x 1.7 gHz'),
(4, '1 x 1.8 gHz'),
(5, '1 x 1.9 gHz'),
(6, '1 x 2.0 gHz'),
(7, '1 x 2.1 gHz'),
(8, '1 x 2.2 gHz'),
(9, '1 x 2.3 gHz'),
(10, '1 x 2.4 gHz'),
(11, '1 x 2.5 gHz'),
(12, '1 x 2.6 gHz'),
(13, '1 x 2.7 gHz'),
(14, '1 x 2.8 gHz'),
(15, '1 x 2.9 gHz'),
(16, '1 x 3.0 gHz'),
(17, '1 x 3.1 gHz'),
(18, '1 x 3.2 gHz'),
(19, '1 x 3.3 gHz'),
(20, '1 x 3.4 gHz'),
(21, '1 x 3.5 gHz'),
(22, '1 x 3.6 gHz'),
(23, '1 x 3.7 gHz'),
(24, '1 x 3.8 gHz'),
(25, '1 x 3.9 gHz'),
(26, '1 x 4.0 gHz'),
(27, '1 x 4.1 gHz'),
(28, '1 x 4.2 gHz'),
(29, '1 x 4.3 gHz'),
(30, '1 x 4.4 gHz'),
(31, '1 x 4.5 gHz'),
(32, '1 x 4.6 gHz'),
(33, '1 x 4.7 gHz'),
(34, '1 x 4.8 gHz'),
(35, '1 x 4.9 gHz'),
(36, '1 x 5.0 gHz'),
(37, '2 x 1.5 gHz'),
(38, '2 x 1.6 gHz'),
(39, '2 x 1.7 gHz'),
(40, '2 x 1.8 gHz'),
(41, '2 x 1.9 gHz'),
(42, '2 x 2.0 gHz'),
(43, '2 x 2.1 gHz'),
(44, '2 x 2.2 gHz'),
(45, '2 x 2.3 gHz'),
(46, '2 x 2.4 gHz'),
(47, '2 x 2.5 gHz'),
(48, '2 x 2.6 gHz'),
(49, '2 x 2.7 gHz'),
(50, '2 x 2.8 gHz'),
(51, '2 x 2.9 gHz'),
(52, '2 x 3.0 gHz'),
(53, '2 x 3.1 gHz'),
(54, '2 x 3.2 gHz'),
(55, '2 x 3.3 gHz'),
(56, '2 x 3.4 gHz'),
(57, '2 x 3.5 gHz'),
(58, '2 x 3.6 gHz'),
(59, '2 x 3.7 gHz'),
(60, '2 x 3.8 gHz'),
(61, '2 x 3.9 gHz'),
(62, '2 x 4.0 gHz'),
(63, '2 x 4.1 gHz'),
(64, '2 x 4.2 gHz'),
(65, '2 x 4.3 gHz'),
(66, '2 x 4.4 gHz'),
(67, '2 x 4.5 gHz'),
(68, '2 x 4.6 gHz'),
(69, '2 x 4.7 gHz'),
(70, '2 x 4.8 gHz'),
(71, '2 x 4.9 gHz'),
(72, '2 x 5.0 gHz'),
(73, '4 x 1.5 gHz'),
(74, '4 x 1.6 gHz'),
(75, '4 x 1.7 gHz'),
(76, '4 x 1.8 gHz'),
(77, '4 x 1.9 gHz'),
(78, '4 x 2.0 gHz'),
(79, '4 x 2.1 gHz'),
(80, '4 x 2.2 gHz'),
(81, '4 x 2.3 gHz'),
(82, '4 x 2.4 gHz'),
(83, '4 x 2.5 gHz'),
(84, '4 x 2.6 gHz'),
(85, '4 x 2.7 gHz'),
(86, '4 x 2.8 gHz'),
(87, '4 x 2.9 gHz'),
(88, '4 x 3.0 gHz'),
(89, '4 x 3.1 gHz'),
(90, '4 x 3.2 gHz'),
(91, '4 x 3.3 gHz'),
(92, '4 x 3.4 gHz'),
(93, '4 x 3.5 gHz'),
(94, '4 x 3.6 gHz'),
(95, '4 x 3.7 gHz'),
(96, '4 x 3.8 gHz'),
(97, '4 x 3.9 gHz'),
(98, '4 x 4.0 gHz'),
(99, '4 x 4.1 gHz'),
(100, '4 x 4.2 gHz'),
(101, '4 x 4.3 gHz'),
(102, '4 x 4.4 gHz'),
(103, '4 x 4.5 gHz'),
(104, '4 x 4.6 gHz'),
(105, '4 x 4.7 gHz'),
(106, '4 x 4.8 gHz'),
(107, '4 x 4.9 gHz'),
(108, '4 x 5.0 gHz'),
(109, '8 x 1.5 gHz'),
(110, '8 x 1.6 gHz'),
(111, '8 x 1.7 gHz'),
(112, '8 x 1.8 gHz'),
(113, '8 x 1.9 gHz'),
(114, '8 x 2.0 gHz'),
(115, '8 x 2.1 gHz'),
(116, '8 x 2.2 gHz'),
(117, '8 x 2.3 gHz'),
(118, '8 x 2.4 gHz'),
(119, '8 x 2.5 gHz'),
(120, '8 x 2.6 gHz'),
(121, '8 x 2.7 gHz'),
(122, '8 x 2.8 gHz'),
(123, '8 x 2.9 gHz'),
(124, '8 x 3.0 gHz'),
(125, '8 x 3.1 gHz'),
(126, '8 x 3.2 gHz'),
(127, '8 x 3.3 gHz'),
(128, '8 x 3.4 gHz'),
(129, '8 x 3.5 gHz'),
(130, '8 x 3.6 gHz'),
(131, '8 x 3.7 gHz'),
(132, '8 x 3.8 gHz'),
(133, '8 x 3.9 gHz'),
(134, '8 x 4.0 gHz'),
(135, '8 x 4.1 gHz'),
(136, '8 x 4.2 gHz'),
(137, '8 x 4.3 gHz'),
(138, '8 x 4.4 gHz'),
(139, '8 x 4.5 gHz'),
(140, '8 x 4.6 gHz'),
(141, '8 x 4.7 gHz'),
(142, '8 x 4.8 gHz'),
(143, '8 x 4.9 gHz'),
(144, '8 x 5.0 gHz'),
(145, '1 x 2.93Ghz'),
(146, '1.86Ghz'),
(147, '3.06Ghz'),
(148, '2.66GHz'),
(149, '2.26GHz'),
(150, '2.13Ghz'),
(151, '2 x 2.26Ghz'),
(152, '5 x 2.7 ghz'),
(153, '1.3 GHz'),
(154, '3 gHz'),
(155, '1 x 1.4 gHz'),
(156, '2Ghz'),
(157, '1 x 2 Ghz'),
(158, '2.39 Ghz'),
(159, '2 x 1.20 GHz'),
(160, '2.2 GHz'),
(161, '2.7GHz'),
(162, '3.2GHz'),
(163, '2.6GHz'),
(164, '1.4 GHz'),
(165, '1 x 1.1 GHz'),
(166, '2 x 1.1 GHz'),
(167, '2.3GHz'),
(168, '3.10GHz'),
(169, '2.1GHz'),
(170, '3GHz'),
(171, '2.5GHz'),
(172, '2.9 GHz'),
(173, '3.4GHz'),
(174, '2.0 GHz'),
(175, '3.6GHz'),
(176, '3.8 GHz'),
(177, '2.1 GHz');

-- --------------------------------------------------------

--
-- Structure de la table `produit_ram`
--

CREATE TABLE `produit_ram` (
  `id_produit_ram` smallint(6) UNSIGNED NOT NULL,
  `produit_ram` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_ram`
--

INSERT INTO `produit_ram` (`id_produit_ram`, `produit_ram`) VALUES
(1, '64 Mo'),
(2, '128 Mo'),
(3, '192 Mo'),
(4, '256 Mo'),
(5, '384 Mo'),
(6, '512 Mo'),
(7, '768 Mo'),
(8, '1024 Mo'),
(9, '1536 Mo'),
(10, '2048 Mo'),
(11, '3072 Mo'),
(12, '4096 Mo'),
(13, '5Go'),
(14, '4Go'),
(15, '1To'),
(16, '2Go'),
(17, '6Go'),
(18, '48Go'),
(19, '48GB'),
(20, '4Mo'),
(21, '12Go'),
(22, '8Go'),
(23, '1Go'),
(24, '3Go'),
(25, '2 GHZ'),
(26, '4GB'),
(27, '16 Go'),
(28, '320 Go'),
(29, '6GB'),
(30, '500GB'),
(31, '96Go'),
(32, '256 Go'),
(34, '64Go'),
(35, '4*8Go'),
(36, '2*4GB'),
(37, '6 x 8Go'),
(38, '8Gb'),
(39, '8 Mo'),
(40, '32 Go'),
(41, '4 Go'),
(42, '4GB'),
(43, '64 Go'),
(44, '8 Go'),
(45, '24 Go'),
(46, '128 Go'),
(49, '4 x 16GB'),
(50, '8Go'),
(51, '16Go'),
(52, '16GB'),
(53, '8GB'),
(54, '4Go'),
(55, '3Go'),
(56, '4Go'),
(57, '6 x 16Go'),
(58, '2 x 120Gb'),
(59, '6 x 16Go'),
(60, '16 x 600Gb'),
(61, '3 x 16Gb'),
(62, '4 x 32GB'),
(63, '8Go'),
(64, '8GB'),
(65, '8 x 12/6GB'),
(66, '32 GB'),
(67, '8GB'),
(68, '16GB DDR4'),
(69, '1To'),
(70, '512MB'),
(71, '8GB DDR4'),
(72, '2 x 32GB DDR4'),
(73, '2GB DDR3L'),
(74, '256GB'),
(75, '32Go DDR4'),
(76, '4 x 32Gb DDR4'),
(77, '32GB SSD'),
(78, '8Go DDR4'),
(79, '2 x 16GB DDR4'),
(80, '16Go SATA/SAS');

-- --------------------------------------------------------

--
-- Structure de la table `produit_technique`
--

CREATE TABLE `produit_technique` (
  `id_produit_technique` smallint(6) UNSIGNED NOT NULL,
  `produit_technique` varchar(32) NOT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_technique`
--

INSERT INTO `produit_technique` (`id_produit_technique`, `produit_technique`, `ordre`) VALUES
(1, 'Imprimante laser couleur', 0),
(2, 'Imprimante laser monochrome', 0),
(3, 'Imprimante jet d\'encre', 0),
(4, 'Imprimante à Sublimation', 0),
(5, 'Imprimante matricielle', 0),
(6, 'Traceur', 0),
(7, 'Imprimante étiquette', 0),
(8, 'MULTIFONCTION', 0),
(9, 'Multifonction', 0),
(10, 'Imprimante thermique ticket', 0),
(11, 'Multifonction laser couleur', 0),
(12, 'A jet d\'encre thermique', 0),
(13, 'A jet d\'encre thermique', 0),
(14, 'A jet d\'encre thermique', 0),
(15, 'Jet d\'encre', 0),
(16, 'Jet d\'encre', 0),
(17, 'Imprimante thermique', 0),
(18, 'Jet d\'encre', 0),
(19, 'Imprimante jet d\'encre couleur', 0),
(20, 'Imprimante jet d\'encre couleur', 0),
(21, 'Multifonction laser monochrome', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_tel_produit`
--

CREATE TABLE `produit_tel_produit` (
  `id_produit_tel_produit` smallint(6) UNSIGNED NOT NULL,
  `produit_tel_produit` varchar(50) DEFAULT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_tel_produit`
--

INSERT INTO `produit_tel_produit` (`id_produit_tel_produit`, `produit_tel_produit`, `ordre`) VALUES
(1, 'Solution de Téléphonie Fixe', 0),
(2, 'Smartphone', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_tel_type`
--

CREATE TABLE `produit_tel_type` (
  `id_produit_tel_type` smallint(6) UNSIGNED NOT NULL,
  `produit_tel_type` varchar(50) DEFAULT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_tel_type`
--

INSERT INTO `produit_tel_type` (`id_produit_tel_type`, `produit_tel_type`, `ordre`) VALUES
(1, 'Solution classique (PABX)', 0),
(2, 'Solution voix sur IP (IPBX)', 0),
(3, 'Système Android', 0),
(4, 'iPhone', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_type`
--

CREATE TABLE `produit_type` (
  `id_produit_type` smallint(6) UNSIGNED NOT NULL,
  `produit_type` varchar(32) NOT NULL,
  `ordre` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_type`
--

INSERT INTO `produit_type` (`id_produit_type`, `produit_type`, `ordre`) VALUES
(1, 'Station', 0),
(2, 'Portable', 0),
(3, 'Serveur', 0),
(4, 'Client léger', 0),
(5, 'TABLETTE', 0),
(6, 'Tablette', 0),
(7, '2.4Ghz', 0);

-- --------------------------------------------------------

--
-- Structure de la table `produit_typeecran`
--

CREATE TABLE `produit_typeecran` (
  `id_produit_typeecran` smallint(6) UNSIGNED NOT NULL,
  `produit_typeecran` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_typeecran`
--

INSERT INTO `produit_typeecran` (`id_produit_typeecran`, `produit_typeecran`) VALUES
(1, 'Ecran TFT'),
(2, 'Ecran Cathodique'),
(3, 'Large Viewsonic'),
(4, 'Ecran tactile'),
(5, 'Ecran LCD'),
(6, 'Ecran plat panoramique'),
(7, 'Ecran LED Plat'),
(8, 'Large'),
(9, 'Ecran Retina'),
(10, 'Large Multipoint'),
(11, 'Ecran tactile UWVA HD'),
(12, 'Ecran Tactile HD 11.6\"'),
(13, 'Full HD'),
(14, 'CGD'),
(15, 'Ecran LCD a rétroéclairage LED'),
(16, 'Tactile'),
(17, 'LCD monochrome rétroéclairé'),
(18, 'LCD rétroéclairé'),
(19, 'LCD monochrome rétroéclairé'),
(20, 'Écran rétroéclairé par LED'),
(21, 'Écran LED'),
(22, 'Ecran LED'),
(23, 'Ecran LED Full HD'),
(24, 'Full HD'),
(25, 'Écran LED LDC'),
(26, 'Full HD');

-- --------------------------------------------------------

--
-- Structure de la table `produit_viewable`
--

CREATE TABLE `produit_viewable` (
  `id_produit_viewable` smallint(6) UNSIGNED NOT NULL,
  `produit_viewable` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `produit_viewable`
--

INSERT INTO `produit_viewable` (`id_produit_viewable`, `produit_viewable`) VALUES
(1, '12 pouces'),
(2, '13 pouces'),
(3, '14 pouces'),
(4, '15 pouces'),
(5, '16 pouces'),
(6, '17 pouces'),
(7, '18 pouces'),
(8, '19 pouces'),
(9, '20 pouces'),
(10, '21 pouces'),
(11, '22 pouces'),
(12, '23 pouces'),
(13, '24 pouces'),
(14, '25 pouces'),
(15, '26 pouces'),
(16, '27 pouces'),
(17, '28 pouces'),
(18, '29 pouces'),
(19, '30 pouces'),
(20, '31 pouces'),
(21, '32 pouces'),
(22, '21.5\"'),
(23, '13.3'),
(24, '15.6'),
(25, '18.5\"'),
(26, '3.7 \'\''),
(27, '17.3 pouces'),
(28, '10.1\"'),
(29, '10\"'),
(30, '21.5 pouces'),
(31, '15.6 HD'),
(32, '14\" HD'),
(33, '14\"HD'),
(34, '9.7\"'),
(35, '14\" LED'),
(36, '10.8\"'),
(37, '15.6 LED'),
(38, '14.1\'\''),
(39, '39.6'),
(40, '19.5 pouces'),
(41, '15.6\" Full HD'),
(42, '15.6\" HD Antireflet'),
(43, '3.5\"'),
(44, '5.25\"'),
(45, '43,94 cm (17.3\")'),
(46, '55,88 cm (22\")'),
(47, '50,8 cm (20\")'),
(48, '31,75 cm (12.5\")'),
(49, '68,58 cm (27\")'),
(50, '39,12 cm (15.4\")'),
(51, '35,81 cm (14.1\")'),
(52, '35,56 cm (14\")'),
(53, '39,62 cm (15.6\")'),
(54, '58,42 cm (23\")'),
(55, '25,91 cm (10.2\")'),
(56, '2.5\"'),
(57, '6,73 cm (2.65\")'),
(58, '54,61 cm (21.5\")'),
(59, '12,95 cm (5.1\")'),
(60, '25,65 cm (10.1\")'),
(61, '60,96 cm (24\")'),
(62, '17,78 cm (7\")'),
(63, '71,12 cm (28\")'),
(64, '11.6\"'),
(65, '4.3\"'),
(68, '5,08 cm (2\")'),
(69, '48 pouces'),
(70, '47.6 pouces'),
(71, '5,99 cm (2.36\")'),
(72, '36 cm (14\")'),
(73, '39.6cm (15.6\")'),
(74, '23 pouces tactile'),
(75, '23 pouces'),
(76, '14\" (1920 x 1080)'),
(77, '13,3 pouces'),
(78, '15.6 pouces'),
(79, '22 pouces'),
(80, '21.5\"'),
(81, '58,4 cm (23\")'),
(82, '9,4 cm (3.7\")'),
(83, '39,6 cm (15.6\")'),
(84, '120,9 cm (47.6\")'),
(85, '17 pouces'),
(86, '54,6 cm (21.5\")'),
(87, '8,89 cm (3.5\")'),
(88, '15.6\" Full HD'),
(89, '64.5cm (23.8\')'),
(90, '164cm'),
(91, '101.6cm'),
(92, '58.4cm'),
(93, '9.6\"'),
(94, '60,5 cm (23.8\")'),
(95, '43,9 cm (17.3\")'),
(96, '65 pouces'),
(97, '75 pouces'),
(98, '58.4 cm (23\')'),
(99, '23.8\''),
(100, '64.5 pouces');

-- --------------------------------------------------------

--
-- Structure de la table `profil`
--

CREATE TABLE `profil` (
  `id_profil` tinyint(3) UNSIGNED NOT NULL,
  `profil` varchar(256) NOT NULL,
  `seuil` int(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `profil`
--

INSERT INTO `profil` (`id_profil`, `profil`, `seuil`) VALUES
(1, 'Administrateur', 0),
(2, 'Commercial', 0),
(3, 'Directeur informatique client', 0),
(4, 'assistante de direction', 0),
(5, 'comptable', 0),
(6, 'Midas', 0),
(8, 'Admin_Fred', 0);

-- --------------------------------------------------------

--
-- Structure de la table `profil_privilege`
--

CREATE TABLE `profil_privilege` (
  `id_profil_privilege` mediumint(8) UNSIGNED NOT NULL,
  `id_profil` tinyint(3) UNSIGNED NOT NULL,
  `id_privilege` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Privilège associé (action ou ressource distincte) NULL si tous les privilèges',
  `id_module` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Module sollicité, NULL si cela concerne autre chose',
  `field` varchar(32) DEFAULT NULL COMMENT 'Champ particulier du module spécifié'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Droits sur les ressources par profil';

--
-- Déchargement des données de la table `profil_privilege`
--

INSERT INTO `profil_privilege` (`id_profil_privilege`, `id_profil`, `id_privilege`, `id_module`, `field`) VALUES
(11, 1, 1, 2, NULL),
(13, 1, 1, 3, NULL),
(12, 1, 1, 4, NULL),
(14, 1, 1, 9, NULL),
(1, 1, 1, 37, NULL),
(5, 1, 1, 39, NULL),
(9, 1, 1, 41, NULL),
(8, 1, 1, 45, NULL),
(10, 1, 1, 46, NULL),
(16, 1, 1, 47, NULL),
(32, 1, 1, 48, NULL),
(36, 1, 1, 49, NULL),
(41, 1, 1, 50, NULL),
(26, 1, 1, 51, NULL),
(17, 1, 1, 52, NULL),
(4663, 1, 1, 53, NULL),
(21, 1, 1, 54, NULL),
(24, 1, 1, 55, NULL),
(33, 1, 1, 61, NULL),
(35, 1, 1, 64, NULL),
(38, 1, 1, 65, NULL),
(40, 1, 1, 66, NULL),
(47, 1, 1, 68, NULL),
(46, 1, 1, 69, NULL),
(27, 1, 1, 75, NULL),
(37, 1, 1, 79, NULL),
(4481, 1, 1, 87, NULL),
(28, 1, 1, 101, NULL),
(29, 1, 1, 102, NULL),
(34, 1, 1, 104, NULL),
(31, 1, 1, 105, NULL),
(19, 1, 1, 106, NULL),
(15, 1, 1, 111, NULL),
(42, 1, 1, 114, NULL),
(49, 1, 1, 116, NULL),
(50, 1, 1, 117, NULL),
(55, 1, 1, 118, NULL),
(53, 1, 1, 119, NULL),
(52, 1, 1, 120, NULL),
(57, 1, 1, 122, NULL),
(51, 1, 1, 123, NULL),
(56, 1, 1, 124, NULL),
(1850, 1, 1, 126, NULL),
(44, 1, 1, 132, NULL),
(23, 1, 1, 139, NULL),
(30, 1, 1, 145, NULL),
(22, 1, 1, 151, NULL),
(59, 1, 1, 152, NULL),
(60, 1, 1, 155, NULL),
(1831, 1, 1, 156, NULL),
(2777, 1, 1, 158, NULL),
(1876, 1, 1, 159, NULL),
(4046, 1, 1, 162, NULL),
(1886, 1, 1, 163, NULL),
(1896, 1, 1, 164, NULL),
(4715, 1, 1, 172, NULL),
(4036, 1, 1, 174, NULL),
(2813, 1, 1, 176, NULL),
(2804, 1, 1, 177, NULL),
(2823, 1, 1, 179, NULL),
(2876, 1, 1, 180, NULL),
(4872, 1, 1, 181, NULL),
(4150, 1, 1, 182, NULL),
(4119, 1, 1, 183, NULL),
(4129, 1, 1, 184, NULL),
(4160, 1, 1, 186, NULL),
(4170, 1, 1, 187, NULL),
(4190, 1, 1, 188, NULL),
(4200, 1, 1, 189, NULL),
(4230, 1, 1, 190, NULL),
(4220, 1, 1, 191, NULL),
(4180, 1, 1, 192, NULL),
(4210, 1, 1, 193, NULL),
(4512, 1, 1, 195, NULL),
(4522, 1, 1, 196, NULL),
(4502, 1, 1, 197, NULL),
(4532, 1, 1, 198, NULL),
(4542, 1, 1, 199, NULL),
(4562, 1, 1, 200, NULL),
(4572, 1, 1, 201, NULL),
(4552, 1, 1, 202, NULL),
(4683, 1, 1, 203, NULL),
(4745, 1, 1, 204, NULL),
(4785, 1, 1, 205, NULL),
(4825, 1, 1, 206, NULL),
(4865, 1, 1, 207, NULL),
(4882, 1, 1, 208, NULL),
(194, 1, 2, 2, NULL),
(196, 1, 2, 3, NULL),
(195, 1, 2, 4, NULL),
(197, 1, 2, 9, NULL),
(184, 1, 2, 37, NULL),
(188, 1, 2, 39, NULL),
(192, 1, 2, 41, NULL),
(191, 1, 2, 45, NULL),
(193, 1, 2, 46, NULL),
(199, 1, 2, 47, NULL),
(215, 1, 2, 48, NULL),
(219, 1, 2, 49, NULL),
(224, 1, 2, 50, NULL),
(209, 1, 2, 51, NULL),
(200, 1, 2, 52, NULL),
(4662, 1, 2, 53, NULL),
(204, 1, 2, 54, NULL),
(207, 1, 2, 55, NULL),
(216, 1, 2, 61, NULL),
(218, 1, 2, 64, NULL),
(221, 1, 2, 65, NULL),
(223, 1, 2, 66, NULL),
(4482, 1, 2, 68, NULL),
(229, 1, 2, 69, NULL),
(210, 1, 2, 75, NULL),
(220, 1, 2, 79, NULL),
(211, 1, 2, 101, NULL),
(212, 1, 2, 102, NULL),
(217, 1, 2, 104, NULL),
(214, 1, 2, 105, NULL),
(202, 1, 2, 106, NULL),
(198, 1, 2, 111, NULL),
(225, 1, 2, 114, NULL),
(232, 1, 2, 116, NULL),
(233, 1, 2, 117, NULL),
(238, 1, 2, 118, NULL),
(236, 1, 2, 119, NULL),
(235, 1, 2, 120, NULL),
(240, 1, 2, 122, NULL),
(234, 1, 2, 123, NULL),
(239, 1, 2, 124, NULL),
(1849, 1, 2, 126, NULL),
(227, 1, 2, 132, NULL),
(206, 1, 2, 139, NULL),
(213, 1, 2, 145, NULL),
(205, 1, 2, 151, NULL),
(242, 1, 2, 152, NULL),
(243, 1, 2, 155, NULL),
(1832, 1, 2, 156, NULL),
(2776, 1, 2, 158, NULL),
(1875, 1, 2, 159, NULL),
(4045, 1, 2, 162, NULL),
(1885, 1, 2, 163, NULL),
(1895, 1, 2, 164, NULL),
(4714, 1, 2, 172, NULL),
(4035, 1, 2, 174, NULL),
(2812, 1, 2, 176, NULL),
(2803, 1, 2, 177, NULL),
(2822, 1, 2, 179, NULL),
(2877, 1, 2, 180, NULL),
(4149, 1, 2, 182, NULL),
(4118, 1, 2, 183, NULL),
(4128, 1, 2, 184, NULL),
(4159, 1, 2, 186, NULL),
(4169, 1, 2, 187, NULL),
(4189, 1, 2, 188, NULL),
(4199, 1, 2, 189, NULL),
(4229, 1, 2, 190, NULL),
(4219, 1, 2, 191, NULL),
(4179, 1, 2, 192, NULL),
(4209, 1, 2, 193, NULL),
(4511, 1, 2, 195, NULL),
(4521, 1, 2, 196, NULL),
(4501, 1, 2, 197, NULL),
(4531, 1, 2, 198, NULL),
(4541, 1, 2, 199, NULL),
(4561, 1, 2, 200, NULL),
(4571, 1, 2, 201, NULL),
(4682, 1, 2, 203, NULL),
(4744, 1, 2, 204, NULL),
(4784, 1, 2, 205, NULL),
(4824, 1, 2, 206, NULL),
(4864, 1, 2, 207, NULL),
(4881, 1, 2, 208, NULL),
(377, 1, 3, 2, NULL),
(379, 1, 3, 3, NULL),
(378, 1, 3, 4, NULL),
(380, 1, 3, 9, NULL),
(367, 1, 3, 37, NULL),
(371, 1, 3, 39, NULL),
(375, 1, 3, 41, NULL),
(374, 1, 3, 45, NULL),
(376, 1, 3, 46, NULL),
(382, 1, 3, 47, NULL),
(398, 1, 3, 48, NULL),
(402, 1, 3, 49, NULL),
(407, 1, 3, 50, NULL),
(392, 1, 3, 51, NULL),
(383, 1, 3, 52, NULL),
(4661, 1, 3, 53, NULL),
(387, 1, 3, 54, NULL),
(390, 1, 3, 55, NULL),
(399, 1, 3, 61, NULL),
(401, 1, 3, 64, NULL),
(404, 1, 3, 65, NULL),
(406, 1, 3, 66, NULL),
(413, 1, 3, 68, NULL),
(412, 1, 3, 69, NULL),
(393, 1, 3, 75, NULL),
(403, 1, 3, 79, NULL),
(394, 1, 3, 101, NULL),
(395, 1, 3, 102, NULL),
(400, 1, 3, 104, NULL),
(397, 1, 3, 105, NULL),
(385, 1, 3, 106, NULL),
(381, 1, 3, 111, NULL),
(408, 1, 3, 114, NULL),
(415, 1, 3, 116, NULL),
(416, 1, 3, 117, NULL),
(421, 1, 3, 118, NULL),
(419, 1, 3, 119, NULL),
(418, 1, 3, 120, NULL),
(423, 1, 3, 122, NULL),
(417, 1, 3, 123, NULL),
(422, 1, 3, 124, NULL),
(1848, 1, 3, 126, NULL),
(410, 1, 3, 132, NULL),
(389, 1, 3, 139, NULL),
(396, 1, 3, 145, NULL),
(388, 1, 3, 151, NULL),
(425, 1, 3, 152, NULL),
(426, 1, 3, 155, NULL),
(1833, 1, 3, 156, NULL),
(2775, 1, 3, 158, NULL),
(1874, 1, 3, 159, NULL),
(4044, 1, 3, 162, NULL),
(1884, 1, 3, 163, NULL),
(1894, 1, 3, 164, NULL),
(4713, 1, 3, 172, NULL),
(4034, 1, 3, 174, NULL),
(2811, 1, 3, 176, NULL),
(2802, 1, 3, 177, NULL),
(2821, 1, 3, 179, NULL),
(4148, 1, 3, 182, NULL),
(4117, 1, 3, 183, NULL),
(4127, 1, 3, 184, NULL),
(4158, 1, 3, 186, NULL),
(4168, 1, 3, 187, NULL),
(4188, 1, 3, 188, NULL),
(4198, 1, 3, 189, NULL),
(4228, 1, 3, 190, NULL),
(4218, 1, 3, 191, NULL),
(4178, 1, 3, 192, NULL),
(4208, 1, 3, 193, NULL),
(4510, 1, 3, 195, NULL),
(4520, 1, 3, 196, NULL),
(4500, 1, 3, 197, NULL),
(4530, 1, 3, 198, NULL),
(4540, 1, 3, 199, NULL),
(4560, 1, 3, 200, NULL),
(4570, 1, 3, 201, NULL),
(4550, 1, 3, 202, NULL),
(4681, 1, 3, 203, NULL),
(4743, 1, 3, 204, NULL),
(4783, 1, 3, 205, NULL),
(4823, 1, 3, 206, NULL),
(4863, 1, 3, 207, NULL),
(4880, 1, 3, 208, NULL),
(560, 1, 4, 2, NULL),
(562, 1, 4, 3, NULL),
(561, 1, 4, 4, NULL),
(563, 1, 4, 9, NULL),
(550, 1, 4, 37, NULL),
(554, 1, 4, 39, NULL),
(558, 1, 4, 41, NULL),
(557, 1, 4, 45, NULL),
(559, 1, 4, 46, NULL),
(565, 1, 4, 47, NULL),
(581, 1, 4, 48, NULL),
(585, 1, 4, 49, NULL),
(590, 1, 4, 50, NULL),
(575, 1, 4, 51, NULL),
(4660, 1, 4, 53, NULL),
(570, 1, 4, 54, NULL),
(573, 1, 4, 55, NULL),
(582, 1, 4, 61, NULL),
(584, 1, 4, 64, NULL),
(587, 1, 4, 65, NULL),
(589, 1, 4, 66, NULL),
(596, 1, 4, 68, NULL),
(595, 1, 4, 69, NULL),
(576, 1, 4, 75, NULL),
(586, 1, 4, 79, NULL),
(577, 1, 4, 101, NULL),
(578, 1, 4, 102, NULL),
(583, 1, 4, 104, NULL),
(580, 1, 4, 105, NULL),
(568, 1, 4, 106, NULL),
(564, 1, 4, 111, NULL),
(591, 1, 4, 114, NULL),
(598, 1, 4, 116, NULL),
(599, 1, 4, 117, NULL),
(604, 1, 4, 118, NULL),
(602, 1, 4, 119, NULL),
(601, 1, 4, 120, NULL),
(606, 1, 4, 122, NULL),
(600, 1, 4, 123, NULL),
(605, 1, 4, 124, NULL),
(1847, 1, 4, 126, NULL),
(593, 1, 4, 132, NULL),
(572, 1, 4, 139, NULL),
(579, 1, 4, 145, NULL),
(571, 1, 4, 151, NULL),
(608, 1, 4, 152, NULL),
(609, 1, 4, 155, NULL),
(1834, 1, 4, 156, NULL),
(2774, 1, 4, 158, NULL),
(1873, 1, 4, 159, NULL),
(4043, 1, 4, 162, NULL),
(1883, 1, 4, 163, NULL),
(1893, 1, 4, 164, NULL),
(4712, 1, 4, 172, NULL),
(4033, 1, 4, 174, NULL),
(2810, 1, 4, 176, NULL),
(2801, 1, 4, 177, NULL),
(2824, 1, 4, 179, NULL),
(2873, 1, 4, 180, NULL),
(4147, 1, 4, 182, NULL),
(4116, 1, 4, 183, NULL),
(4126, 1, 4, 184, NULL),
(4157, 1, 4, 186, NULL),
(4167, 1, 4, 187, NULL),
(4187, 1, 4, 188, NULL),
(4197, 1, 4, 189, NULL),
(4227, 1, 4, 190, NULL),
(4217, 1, 4, 191, NULL),
(4177, 1, 4, 192, NULL),
(4207, 1, 4, 193, NULL),
(4509, 1, 4, 195, NULL),
(4519, 1, 4, 196, NULL),
(4499, 1, 4, 197, NULL),
(4529, 1, 4, 198, NULL),
(4539, 1, 4, 199, NULL),
(4559, 1, 4, 200, NULL),
(4569, 1, 4, 201, NULL),
(4680, 1, 4, 203, NULL),
(4742, 1, 4, 204, NULL),
(4782, 1, 4, 205, NULL),
(4822, 1, 4, 206, NULL),
(4862, 1, 4, 207, NULL),
(4879, 1, 4, 208, NULL),
(743, 1, 5, 2, NULL),
(745, 1, 5, 3, NULL),
(744, 1, 5, 4, NULL),
(746, 1, 5, 9, NULL),
(733, 1, 5, 37, NULL),
(737, 1, 5, 39, NULL),
(741, 1, 5, 41, NULL),
(740, 1, 5, 45, NULL),
(742, 1, 5, 46, NULL),
(748, 1, 5, 47, NULL),
(764, 1, 5, 48, NULL),
(768, 1, 5, 49, NULL),
(773, 1, 5, 50, NULL),
(758, 1, 5, 51, NULL),
(749, 1, 5, 52, NULL),
(4659, 1, 5, 53, NULL),
(753, 1, 5, 54, NULL),
(756, 1, 5, 55, NULL),
(765, 1, 5, 61, NULL),
(767, 1, 5, 64, NULL),
(770, 1, 5, 65, NULL),
(772, 1, 5, 66, NULL),
(779, 1, 5, 68, NULL),
(778, 1, 5, 69, NULL),
(759, 1, 5, 75, NULL),
(769, 1, 5, 79, NULL),
(760, 1, 5, 101, NULL),
(761, 1, 5, 102, NULL),
(766, 1, 5, 104, NULL),
(763, 1, 5, 105, NULL),
(751, 1, 5, 106, NULL),
(747, 1, 5, 111, NULL),
(774, 1, 5, 114, NULL),
(781, 1, 5, 116, NULL),
(782, 1, 5, 117, NULL),
(787, 1, 5, 118, NULL),
(785, 1, 5, 119, NULL),
(784, 1, 5, 120, NULL),
(789, 1, 5, 122, NULL),
(783, 1, 5, 123, NULL),
(788, 1, 5, 124, NULL),
(1846, 1, 5, 126, NULL),
(776, 1, 5, 132, NULL),
(755, 1, 5, 139, NULL),
(762, 1, 5, 145, NULL),
(754, 1, 5, 151, NULL),
(791, 1, 5, 152, NULL),
(792, 1, 5, 155, NULL),
(1835, 1, 5, 156, NULL),
(2773, 1, 5, 158, NULL),
(1872, 1, 5, 159, NULL),
(4042, 1, 5, 162, NULL),
(1882, 1, 5, 163, NULL),
(1892, 1, 5, 164, NULL),
(4711, 1, 5, 172, NULL),
(4032, 1, 5, 174, NULL),
(2809, 1, 5, 176, NULL),
(2800, 1, 5, 177, NULL),
(2819, 1, 5, 179, NULL),
(2872, 1, 5, 180, NULL),
(4871, 1, 5, 181, NULL),
(4146, 1, 5, 182, NULL),
(4115, 1, 5, 183, NULL),
(4125, 1, 5, 184, NULL),
(4156, 1, 5, 186, NULL),
(4166, 1, 5, 187, NULL),
(4186, 1, 5, 188, NULL),
(4196, 1, 5, 189, NULL),
(4226, 1, 5, 190, NULL),
(4216, 1, 5, 191, NULL),
(4176, 1, 5, 192, NULL),
(4206, 1, 5, 193, NULL),
(4508, 1, 5, 195, NULL),
(4518, 1, 5, 196, NULL),
(4498, 1, 5, 197, NULL),
(4528, 1, 5, 198, NULL),
(4538, 1, 5, 199, NULL),
(4558, 1, 5, 200, NULL),
(4568, 1, 5, 201, NULL),
(4548, 1, 5, 202, NULL),
(4679, 1, 5, 203, NULL),
(4741, 1, 5, 204, NULL),
(4781, 1, 5, 205, NULL),
(4821, 1, 5, 206, NULL),
(4861, 1, 5, 207, NULL),
(4878, 1, 5, 208, NULL),
(926, 1, 6, 2, NULL),
(928, 1, 6, 3, NULL),
(927, 1, 6, 4, NULL),
(929, 1, 6, 9, NULL),
(916, 1, 6, 37, NULL),
(920, 1, 6, 39, NULL),
(924, 1, 6, 41, NULL),
(923, 1, 6, 45, NULL),
(925, 1, 6, 46, NULL),
(931, 1, 6, 47, NULL),
(947, 1, 6, 48, NULL),
(951, 1, 6, 49, NULL),
(956, 1, 6, 50, NULL),
(941, 1, 6, 51, NULL),
(932, 1, 6, 52, NULL),
(4658, 1, 6, 53, NULL),
(936, 1, 6, 54, NULL),
(939, 1, 6, 55, NULL),
(948, 1, 6, 61, NULL),
(950, 1, 6, 64, NULL),
(953, 1, 6, 65, NULL),
(955, 1, 6, 66, NULL),
(962, 1, 6, 68, NULL),
(961, 1, 6, 69, NULL),
(942, 1, 6, 75, NULL),
(952, 1, 6, 79, NULL),
(943, 1, 6, 101, NULL),
(944, 1, 6, 102, NULL),
(949, 1, 6, 104, NULL),
(946, 1, 6, 105, NULL),
(934, 1, 6, 106, NULL),
(930, 1, 6, 111, NULL),
(957, 1, 6, 114, NULL),
(964, 1, 6, 116, NULL),
(965, 1, 6, 117, NULL),
(970, 1, 6, 118, NULL),
(968, 1, 6, 119, NULL),
(967, 1, 6, 120, NULL),
(972, 1, 6, 122, NULL),
(966, 1, 6, 123, NULL),
(971, 1, 6, 124, NULL),
(1845, 1, 6, 126, NULL),
(959, 1, 6, 132, NULL),
(938, 1, 6, 139, NULL),
(945, 1, 6, 145, NULL),
(937, 1, 6, 151, NULL),
(974, 1, 6, 152, NULL),
(975, 1, 6, 155, NULL),
(1836, 1, 6, 156, NULL),
(2772, 1, 6, 158, NULL),
(1871, 1, 6, 159, NULL),
(4041, 1, 6, 162, NULL),
(1881, 1, 6, 163, NULL),
(1891, 1, 6, 164, NULL),
(4710, 1, 6, 172, NULL),
(4031, 1, 6, 174, NULL),
(2808, 1, 6, 176, NULL),
(2799, 1, 6, 177, NULL),
(2818, 1, 6, 179, NULL),
(2871, 1, 6, 180, NULL),
(4870, 1, 6, 181, NULL),
(4145, 1, 6, 182, NULL),
(4114, 1, 6, 183, NULL),
(4124, 1, 6, 184, NULL),
(4155, 1, 6, 186, NULL),
(4165, 1, 6, 187, NULL),
(4185, 1, 6, 188, NULL),
(4195, 1, 6, 189, NULL),
(4225, 1, 6, 190, NULL),
(4215, 1, 6, 191, NULL),
(4175, 1, 6, 192, NULL),
(4205, 1, 6, 193, NULL),
(4507, 1, 6, 195, NULL),
(4517, 1, 6, 196, NULL),
(4497, 1, 6, 197, NULL),
(4527, 1, 6, 198, NULL),
(4537, 1, 6, 199, NULL),
(4557, 1, 6, 200, NULL),
(4567, 1, 6, 201, NULL),
(4547, 1, 6, 202, NULL),
(4678, 1, 6, 203, NULL),
(4740, 1, 6, 204, NULL),
(4780, 1, 6, 205, NULL),
(4820, 1, 6, 206, NULL),
(4860, 1, 6, 207, NULL),
(4877, 1, 6, 208, NULL),
(1109, 1, 7, 2, NULL),
(1111, 1, 7, 3, NULL),
(1110, 1, 7, 4, NULL),
(1112, 1, 7, 9, NULL),
(1099, 1, 7, 37, NULL),
(1103, 1, 7, 39, NULL),
(1107, 1, 7, 41, NULL),
(1106, 1, 7, 45, NULL),
(1108, 1, 7, 46, NULL),
(1114, 1, 7, 47, NULL),
(1130, 1, 7, 48, NULL),
(1134, 1, 7, 49, NULL),
(1139, 1, 7, 50, NULL),
(1124, 1, 7, 51, NULL),
(1115, 1, 7, 52, NULL),
(4657, 1, 7, 53, NULL),
(1119, 1, 7, 54, NULL),
(1122, 1, 7, 55, NULL),
(1131, 1, 7, 61, NULL),
(1133, 1, 7, 64, NULL),
(1136, 1, 7, 65, NULL),
(1138, 1, 7, 66, NULL),
(1145, 1, 7, 68, NULL),
(1144, 1, 7, 69, NULL),
(1125, 1, 7, 75, NULL),
(1135, 1, 7, 79, NULL),
(1126, 1, 7, 101, NULL),
(1127, 1, 7, 102, NULL),
(1132, 1, 7, 104, NULL),
(1129, 1, 7, 105, NULL),
(1117, 1, 7, 106, NULL),
(1113, 1, 7, 111, NULL),
(1140, 1, 7, 114, NULL),
(1147, 1, 7, 116, NULL),
(1148, 1, 7, 117, NULL),
(1153, 1, 7, 118, NULL),
(1151, 1, 7, 119, NULL),
(1150, 1, 7, 120, NULL),
(1155, 1, 7, 122, NULL),
(1149, 1, 7, 123, NULL),
(1154, 1, 7, 124, NULL),
(1844, 1, 7, 126, NULL),
(1142, 1, 7, 132, NULL),
(1121, 1, 7, 139, NULL),
(1128, 1, 7, 145, NULL),
(1120, 1, 7, 151, NULL),
(1157, 1, 7, 152, NULL),
(1158, 1, 7, 155, NULL),
(1837, 1, 7, 156, NULL),
(2771, 1, 7, 158, NULL),
(1870, 1, 7, 159, NULL),
(4040, 1, 7, 162, NULL),
(1880, 1, 7, 163, NULL),
(1890, 1, 7, 164, NULL),
(4709, 1, 7, 172, NULL),
(4030, 1, 7, 174, NULL),
(2807, 1, 7, 176, NULL),
(2798, 1, 7, 177, NULL),
(2817, 1, 7, 179, NULL),
(2870, 1, 7, 180, NULL),
(4869, 1, 7, 181, NULL),
(4144, 1, 7, 182, NULL),
(4113, 1, 7, 183, NULL),
(4123, 1, 7, 184, NULL),
(4154, 1, 7, 186, NULL),
(4164, 1, 7, 187, NULL),
(4184, 1, 7, 188, NULL),
(4194, 1, 7, 189, NULL),
(4224, 1, 7, 190, NULL),
(4214, 1, 7, 191, NULL),
(4174, 1, 7, 192, NULL),
(4204, 1, 7, 193, NULL),
(4506, 1, 7, 195, NULL),
(4516, 1, 7, 196, NULL),
(4496, 1, 7, 197, NULL),
(4526, 1, 7, 198, NULL),
(4536, 1, 7, 199, NULL),
(4556, 1, 7, 200, NULL),
(4566, 1, 7, 201, NULL),
(4546, 1, 7, 202, NULL),
(4677, 1, 7, 203, NULL),
(4739, 1, 7, 204, NULL),
(4779, 1, 7, 205, NULL),
(4819, 1, 7, 206, NULL),
(4859, 1, 7, 207, NULL),
(4876, 1, 7, 208, NULL),
(1292, 1, 8, 2, NULL),
(1294, 1, 8, 3, NULL),
(1293, 1, 8, 4, NULL),
(1295, 1, 8, 9, NULL),
(1282, 1, 8, 37, NULL),
(1286, 1, 8, 39, NULL),
(1290, 1, 8, 41, NULL),
(1289, 1, 8, 45, NULL),
(1291, 1, 8, 46, NULL),
(1297, 1, 8, 47, NULL),
(1313, 1, 8, 48, NULL),
(1317, 1, 8, 49, NULL),
(1322, 1, 8, 50, NULL),
(1307, 1, 8, 51, NULL),
(1298, 1, 8, 52, NULL),
(4656, 1, 8, 53, NULL),
(1302, 1, 8, 54, NULL),
(1305, 1, 8, 55, NULL),
(1314, 1, 8, 61, NULL),
(1316, 1, 8, 64, NULL),
(1319, 1, 8, 65, NULL),
(1321, 1, 8, 66, NULL),
(1328, 1, 8, 68, NULL),
(1327, 1, 8, 69, NULL),
(1308, 1, 8, 75, NULL),
(1318, 1, 8, 79, NULL),
(1309, 1, 8, 101, NULL),
(1310, 1, 8, 102, NULL),
(1315, 1, 8, 104, NULL),
(1312, 1, 8, 105, NULL),
(1300, 1, 8, 106, NULL),
(1296, 1, 8, 111, NULL),
(1323, 1, 8, 114, NULL),
(1330, 1, 8, 116, NULL),
(1331, 1, 8, 117, NULL),
(1336, 1, 8, 118, NULL),
(1334, 1, 8, 119, NULL),
(1333, 1, 8, 120, NULL),
(1338, 1, 8, 122, NULL),
(1332, 1, 8, 123, NULL),
(1337, 1, 8, 124, NULL),
(1843, 1, 8, 126, NULL),
(1325, 1, 8, 132, NULL),
(1304, 1, 8, 139, NULL),
(1311, 1, 8, 145, NULL),
(1303, 1, 8, 151, NULL),
(1340, 1, 8, 152, NULL),
(1341, 1, 8, 155, NULL),
(1838, 1, 8, 156, NULL),
(2770, 1, 8, 158, NULL),
(1869, 1, 8, 159, NULL),
(4039, 1, 8, 162, NULL),
(1879, 1, 8, 163, NULL),
(1889, 1, 8, 164, NULL),
(4708, 1, 8, 172, NULL),
(4029, 1, 8, 174, NULL),
(2806, 1, 8, 176, NULL),
(2797, 1, 8, 177, NULL),
(2816, 1, 8, 179, NULL),
(2869, 1, 8, 180, NULL),
(4868, 1, 8, 181, NULL),
(4153, 1, 8, 186, NULL),
(4163, 1, 8, 187, NULL),
(4183, 1, 8, 188, NULL),
(4193, 1, 8, 189, NULL),
(4223, 1, 8, 190, NULL),
(4213, 1, 8, 191, NULL),
(4173, 1, 8, 192, NULL),
(4203, 1, 8, 193, NULL),
(4505, 1, 8, 195, NULL),
(4515, 1, 8, 196, NULL),
(4495, 1, 8, 197, NULL),
(4525, 1, 8, 198, NULL),
(4535, 1, 8, 199, NULL),
(4555, 1, 8, 200, NULL),
(4565, 1, 8, 201, NULL),
(4545, 1, 8, 202, NULL),
(4676, 1, 8, 203, NULL),
(4738, 1, 8, 204, NULL),
(4778, 1, 8, 205, NULL),
(4818, 1, 8, 206, NULL),
(4858, 1, 8, 207, NULL),
(4875, 1, 8, 208, NULL),
(1475, 1, 9, 2, NULL),
(1477, 1, 9, 3, NULL),
(1476, 1, 9, 4, NULL),
(1478, 1, 9, 9, NULL),
(1465, 1, 9, 37, NULL),
(1469, 1, 9, 39, NULL),
(1473, 1, 9, 41, NULL),
(1472, 1, 9, 45, NULL),
(1474, 1, 9, 46, NULL),
(1480, 1, 9, 47, NULL),
(1496, 1, 9, 48, NULL),
(1500, 1, 9, 49, NULL),
(1505, 1, 9, 50, NULL),
(1490, 1, 9, 51, NULL),
(1481, 1, 9, 52, NULL),
(4655, 1, 9, 53, NULL),
(1485, 1, 9, 54, NULL),
(1488, 1, 9, 55, NULL),
(1497, 1, 9, 61, NULL),
(1499, 1, 9, 64, NULL),
(1502, 1, 9, 65, NULL),
(1504, 1, 9, 66, NULL),
(1511, 1, 9, 68, NULL),
(1510, 1, 9, 69, NULL),
(1491, 1, 9, 75, NULL),
(1501, 1, 9, 79, NULL),
(1492, 1, 9, 101, NULL),
(1493, 1, 9, 102, NULL),
(1498, 1, 9, 104, NULL),
(1495, 1, 9, 105, NULL),
(1483, 1, 9, 106, NULL),
(1479, 1, 9, 111, NULL),
(1506, 1, 9, 114, NULL),
(1513, 1, 9, 116, NULL),
(1514, 1, 9, 117, NULL),
(1519, 1, 9, 118, NULL),
(1517, 1, 9, 119, NULL),
(1516, 1, 9, 120, NULL),
(1521, 1, 9, 122, NULL),
(1515, 1, 9, 123, NULL),
(1520, 1, 9, 124, NULL),
(1842, 1, 9, 126, NULL),
(1508, 1, 9, 132, NULL),
(1487, 1, 9, 139, NULL),
(1494, 1, 9, 145, NULL),
(1486, 1, 9, 151, NULL),
(1523, 1, 9, 152, NULL),
(1524, 1, 9, 155, NULL),
(1839, 1, 9, 156, NULL),
(2769, 1, 9, 158, NULL),
(1868, 1, 9, 159, NULL),
(4038, 1, 9, 162, NULL),
(1878, 1, 9, 163, NULL),
(1888, 1, 9, 164, NULL),
(4707, 1, 9, 172, NULL),
(4028, 1, 9, 174, NULL),
(2805, 1, 9, 176, NULL),
(2796, 1, 9, 177, NULL),
(2815, 1, 9, 179, NULL),
(2868, 1, 9, 180, NULL),
(4867, 1, 9, 181, NULL),
(4142, 1, 9, 182, NULL),
(4111, 1, 9, 183, NULL),
(4121, 1, 9, 184, NULL),
(4152, 1, 9, 186, NULL),
(4162, 1, 9, 187, NULL),
(4182, 1, 9, 188, NULL),
(4192, 1, 9, 189, NULL),
(4222, 1, 9, 190, NULL),
(4212, 1, 9, 191, NULL),
(4172, 1, 9, 192, NULL),
(4202, 1, 9, 193, NULL),
(4504, 1, 9, 195, NULL),
(4514, 1, 9, 196, NULL),
(4494, 1, 9, 197, NULL),
(4524, 1, 9, 198, NULL),
(4534, 1, 9, 199, NULL),
(4554, 1, 9, 200, NULL),
(4564, 1, 9, 201, NULL),
(4544, 1, 9, 202, NULL),
(4675, 1, 9, 203, NULL),
(4737, 1, 9, 204, NULL),
(4777, 1, 9, 205, NULL),
(4817, 1, 9, 206, NULL),
(4857, 1, 9, 207, NULL),
(4874, 1, 9, 208, NULL),
(1658, 1, 10, 2, NULL),
(1660, 1, 10, 3, NULL),
(1659, 1, 10, 4, NULL),
(1661, 1, 10, 9, NULL),
(1648, 1, 10, 37, NULL),
(1652, 1, 10, 39, NULL),
(1656, 1, 10, 41, NULL),
(1655, 1, 10, 45, NULL),
(1657, 1, 10, 46, NULL),
(1663, 1, 10, 47, NULL),
(1679, 1, 10, 48, NULL),
(1683, 1, 10, 49, NULL),
(1688, 1, 10, 50, NULL),
(1673, 1, 10, 51, NULL),
(1664, 1, 10, 52, NULL),
(4654, 1, 10, 53, NULL),
(1668, 1, 10, 54, NULL),
(1671, 1, 10, 55, NULL),
(1680, 1, 10, 61, NULL),
(1682, 1, 10, 64, NULL),
(1685, 1, 10, 65, NULL),
(1687, 1, 10, 66, NULL),
(1694, 1, 10, 68, NULL),
(1693, 1, 10, 69, NULL),
(1674, 1, 10, 75, NULL),
(1684, 1, 10, 79, NULL),
(1675, 1, 10, 101, NULL),
(1676, 1, 10, 102, NULL),
(1681, 1, 10, 104, NULL),
(1678, 1, 10, 105, NULL),
(1666, 1, 10, 106, NULL),
(1662, 1, 10, 111, NULL),
(1689, 1, 10, 114, NULL),
(1696, 1, 10, 116, NULL),
(1697, 1, 10, 117, NULL),
(1702, 1, 10, 118, NULL),
(1700, 1, 10, 119, NULL),
(1699, 1, 10, 120, NULL),
(1704, 1, 10, 122, NULL),
(1698, 1, 10, 123, NULL),
(1703, 1, 10, 124, NULL),
(1841, 1, 10, 126, NULL),
(1691, 1, 10, 132, NULL),
(1670, 1, 10, 139, NULL),
(1677, 1, 10, 145, NULL),
(1669, 1, 10, 151, NULL),
(1706, 1, 10, 152, NULL),
(1707, 1, 10, 155, NULL),
(1840, 1, 10, 156, NULL),
(2768, 1, 10, 158, NULL),
(1867, 1, 10, 159, NULL),
(4037, 1, 10, 162, NULL),
(1877, 1, 10, 163, NULL),
(1887, 1, 10, 164, NULL),
(4706, 1, 10, 172, NULL),
(4027, 1, 10, 174, NULL),
(2814, 1, 10, 179, NULL),
(2867, 1, 10, 180, NULL),
(4866, 1, 10, 181, NULL),
(4141, 1, 10, 182, NULL),
(4110, 1, 10, 183, NULL),
(4120, 1, 10, 184, NULL),
(4151, 1, 10, 186, NULL),
(4161, 1, 10, 187, NULL),
(4181, 1, 10, 188, NULL),
(4191, 1, 10, 189, NULL),
(4221, 1, 10, 190, NULL),
(4211, 1, 10, 191, NULL),
(4171, 1, 10, 192, NULL),
(4201, 1, 10, 193, NULL),
(4503, 1, 10, 195, NULL),
(4513, 1, 10, 196, NULL),
(4493, 1, 10, 197, NULL),
(4523, 1, 10, 198, NULL),
(4533, 1, 10, 199, NULL),
(4553, 1, 10, 200, NULL),
(4563, 1, 10, 201, NULL),
(4543, 1, 10, 202, NULL),
(4674, 1, 10, 203, NULL),
(4736, 1, 10, 204, NULL),
(4776, 1, 10, 205, NULL),
(4816, 1, 10, 206, NULL),
(4856, 1, 10, 207, NULL),
(4873, 1, 10, 208, NULL),
(71, 2, 1, 2, NULL),
(73, 2, 1, 3, NULL),
(72, 2, 1, 4, NULL),
(74, 2, 1, 9, NULL),
(61, 2, 1, 37, NULL),
(65, 2, 1, 39, NULL),
(69, 2, 1, 41, NULL),
(68, 2, 1, 45, NULL),
(70, 2, 1, 46, NULL),
(76, 2, 1, 47, NULL),
(88, 2, 1, 49, NULL),
(81, 2, 1, 51, NULL),
(77, 2, 1, 52, NULL),
(78, 2, 1, 53, NULL),
(79, 2, 1, 54, NULL),
(80, 2, 1, 55, NULL),
(85, 2, 1, 61, NULL),
(87, 2, 1, 64, NULL),
(90, 2, 1, 65, NULL),
(92, 2, 1, 66, NULL),
(3845, 2, 1, 68, NULL),
(3846, 2, 1, 68, NULL),
(82, 2, 1, 75, NULL),
(89, 2, 1, 79, NULL),
(83, 2, 1, 101, NULL),
(84, 2, 1, 102, NULL),
(86, 2, 1, 104, NULL),
(3866, 2, 1, 106, NULL),
(75, 2, 1, 111, NULL),
(1851, 2, 1, 126, NULL),
(3856, 2, 1, 139, NULL),
(93, 2, 1, 152, NULL),
(94, 2, 1, 155, NULL),
(3916, 2, 1, 158, NULL),
(2766, 2, 1, 159, NULL),
(3876, 2, 1, 162, NULL),
(3886, 2, 1, 163, NULL),
(3896, 2, 1, 164, NULL),
(3906, 2, 1, 174, NULL),
(2790, 2, 1, 176, NULL),
(2791, 2, 1, 177, NULL),
(2825, 2, 1, 179, NULL),
(2887, 2, 1, 180, NULL),
(3976, 2, 1, 181, NULL),
(4079, 2, 1, 182, NULL),
(4059, 2, 1, 183, NULL),
(4069, 2, 1, 184, NULL),
(4240, 2, 1, 186, NULL),
(4250, 2, 1, 187, NULL),
(4270, 2, 1, 188, NULL),
(4280, 2, 1, 189, NULL),
(4290, 2, 1, 190, NULL),
(4300, 2, 1, 191, NULL),
(4260, 2, 1, 192, NULL),
(4310, 2, 1, 193, NULL),
(4673, 2, 1, 203, NULL),
(4755, 2, 1, 204, NULL),
(4795, 2, 1, 205, NULL),
(4835, 2, 1, 206, NULL),
(254, 2, 2, 2, NULL),
(256, 2, 2, 3, NULL),
(255, 2, 2, 4, NULL),
(257, 2, 2, 9, NULL),
(244, 2, 2, 37, NULL),
(248, 2, 2, 39, NULL),
(252, 2, 2, 41, NULL),
(251, 2, 2, 45, NULL),
(253, 2, 2, 46, NULL),
(259, 2, 2, 47, NULL),
(271, 2, 2, 49, NULL),
(264, 2, 2, 51, NULL),
(260, 2, 2, 52, NULL),
(261, 2, 2, 53, NULL),
(262, 2, 2, 54, NULL),
(263, 2, 2, 55, NULL),
(268, 2, 2, 61, NULL),
(270, 2, 2, 64, NULL),
(273, 2, 2, 65, NULL),
(275, 2, 2, 66, NULL),
(265, 2, 2, 75, NULL),
(272, 2, 2, 79, NULL),
(266, 2, 2, 101, NULL),
(267, 2, 2, 102, NULL),
(269, 2, 2, 104, NULL),
(3865, 2, 2, 106, NULL),
(258, 2, 2, 111, NULL),
(1852, 2, 2, 126, NULL),
(3855, 2, 2, 139, NULL),
(276, 2, 2, 152, NULL),
(277, 2, 2, 155, NULL),
(2765, 2, 2, 159, NULL),
(3875, 2, 2, 162, NULL),
(3885, 2, 2, 163, NULL),
(3895, 2, 2, 164, NULL),
(3905, 2, 2, 174, NULL),
(2792, 2, 2, 177, NULL),
(2826, 2, 2, 179, NULL),
(2886, 2, 2, 180, NULL),
(4078, 2, 2, 182, NULL),
(4058, 2, 2, 183, NULL),
(4068, 2, 2, 184, NULL),
(4239, 2, 2, 186, NULL),
(4249, 2, 2, 187, NULL),
(4269, 2, 2, 188, NULL),
(4279, 2, 2, 189, NULL),
(4289, 2, 2, 190, NULL),
(4299, 2, 2, 191, NULL),
(4259, 2, 2, 192, NULL),
(4309, 2, 2, 193, NULL),
(4672, 2, 2, 203, NULL),
(4754, 2, 2, 204, NULL),
(4794, 2, 2, 205, NULL),
(4834, 2, 2, 206, NULL),
(437, 2, 3, 2, NULL),
(439, 2, 3, 3, NULL),
(438, 2, 3, 4, NULL),
(440, 2, 3, 9, NULL),
(427, 2, 3, 37, NULL),
(431, 2, 3, 39, NULL),
(435, 2, 3, 41, NULL),
(434, 2, 3, 45, NULL),
(436, 2, 3, 46, NULL),
(442, 2, 3, 47, NULL),
(454, 2, 3, 49, NULL),
(447, 2, 3, 51, NULL),
(443, 2, 3, 52, NULL),
(444, 2, 3, 53, NULL),
(445, 2, 3, 54, NULL),
(446, 2, 3, 55, NULL),
(451, 2, 3, 61, NULL),
(453, 2, 3, 64, NULL),
(456, 2, 3, 65, NULL),
(458, 2, 3, 66, NULL),
(448, 2, 3, 75, NULL),
(455, 2, 3, 79, NULL),
(449, 2, 3, 101, NULL),
(450, 2, 3, 102, NULL),
(452, 2, 3, 104, NULL),
(3864, 2, 3, 106, NULL),
(441, 2, 3, 111, NULL),
(3854, 2, 3, 139, NULL),
(459, 2, 3, 152, NULL),
(460, 2, 3, 155, NULL),
(2764, 2, 3, 159, NULL),
(3874, 2, 3, 162, NULL),
(3884, 2, 3, 163, NULL),
(3894, 2, 3, 164, NULL),
(3904, 2, 3, 174, NULL),
(2793, 2, 3, 177, NULL),
(4077, 2, 3, 182, NULL),
(4057, 2, 3, 183, NULL),
(4067, 2, 3, 184, NULL),
(4238, 2, 3, 186, NULL),
(4248, 2, 3, 187, NULL),
(4268, 2, 3, 188, NULL),
(4278, 2, 3, 189, NULL),
(4288, 2, 3, 190, NULL),
(4298, 2, 3, 191, NULL),
(4258, 2, 3, 192, NULL),
(4308, 2, 3, 193, NULL),
(4671, 2, 3, 203, NULL),
(4793, 2, 3, 205, NULL),
(620, 2, 4, 2, NULL),
(622, 2, 4, 3, NULL),
(621, 2, 4, 4, NULL),
(610, 2, 4, 37, NULL),
(614, 2, 4, 39, NULL),
(618, 2, 4, 41, NULL),
(617, 2, 4, 45, NULL),
(619, 2, 4, 46, NULL),
(625, 2, 4, 47, NULL),
(637, 2, 4, 49, NULL),
(630, 2, 4, 51, NULL),
(627, 2, 4, 53, NULL),
(628, 2, 4, 54, NULL),
(629, 2, 4, 55, NULL),
(634, 2, 4, 61, NULL),
(636, 2, 4, 64, NULL),
(639, 2, 4, 65, NULL),
(641, 2, 4, 66, NULL),
(631, 2, 4, 75, NULL),
(638, 2, 4, 79, NULL),
(632, 2, 4, 101, NULL),
(633, 2, 4, 102, NULL),
(635, 2, 4, 104, NULL),
(624, 2, 4, 111, NULL),
(642, 2, 4, 152, NULL),
(643, 2, 4, 155, NULL),
(2763, 2, 4, 159, NULL),
(2884, 2, 4, 180, NULL),
(4076, 2, 4, 182, NULL),
(4056, 2, 4, 183, NULL),
(4066, 2, 4, 184, NULL),
(4237, 2, 4, 186, NULL),
(4247, 2, 4, 187, NULL),
(4267, 2, 4, 188, NULL),
(4277, 2, 4, 189, NULL),
(4287, 2, 4, 190, NULL),
(4297, 2, 4, 191, NULL),
(4257, 2, 4, 192, NULL),
(4307, 2, 4, 193, NULL),
(4670, 2, 4, 203, NULL),
(4792, 2, 4, 205, NULL),
(803, 2, 5, 2, NULL),
(805, 2, 5, 3, NULL),
(804, 2, 5, 4, NULL),
(806, 2, 5, 9, NULL),
(793, 2, 5, 37, NULL),
(797, 2, 5, 39, NULL),
(801, 2, 5, 41, NULL),
(800, 2, 5, 45, NULL),
(802, 2, 5, 46, NULL),
(808, 2, 5, 47, NULL),
(820, 2, 5, 49, NULL),
(813, 2, 5, 51, NULL),
(809, 2, 5, 52, NULL),
(810, 2, 5, 53, NULL),
(811, 2, 5, 54, NULL),
(812, 2, 5, 55, NULL),
(817, 2, 5, 61, NULL),
(819, 2, 5, 64, NULL),
(822, 2, 5, 65, NULL),
(824, 2, 5, 66, NULL),
(814, 2, 5, 75, NULL),
(821, 2, 5, 79, NULL),
(815, 2, 5, 101, NULL),
(816, 2, 5, 102, NULL),
(818, 2, 5, 104, NULL),
(3862, 2, 5, 106, NULL),
(807, 2, 5, 111, NULL),
(3852, 2, 5, 139, NULL),
(825, 2, 5, 152, NULL),
(826, 2, 5, 155, NULL),
(3912, 2, 5, 158, NULL),
(2762, 2, 5, 159, NULL),
(3872, 2, 5, 162, NULL),
(3882, 2, 5, 163, NULL),
(3892, 2, 5, 164, NULL),
(3902, 2, 5, 174, NULL),
(2883, 2, 5, 180, NULL),
(3972, 2, 5, 181, NULL),
(4075, 2, 5, 182, NULL),
(4055, 2, 5, 183, NULL),
(4065, 2, 5, 184, NULL),
(4236, 2, 5, 186, NULL),
(4246, 2, 5, 187, NULL),
(4266, 2, 5, 188, NULL),
(4276, 2, 5, 189, NULL),
(4286, 2, 5, 190, NULL),
(4296, 2, 5, 191, NULL),
(4256, 2, 5, 192, NULL),
(4306, 2, 5, 193, NULL),
(4669, 2, 5, 203, NULL),
(4751, 2, 5, 204, NULL),
(4791, 2, 5, 205, NULL),
(4831, 2, 5, 206, NULL),
(986, 2, 6, 2, NULL),
(988, 2, 6, 3, NULL),
(987, 2, 6, 4, NULL),
(989, 2, 6, 9, NULL),
(976, 2, 6, 37, NULL),
(980, 2, 6, 39, NULL),
(984, 2, 6, 41, NULL),
(983, 2, 6, 45, NULL),
(985, 2, 6, 46, NULL),
(991, 2, 6, 47, NULL),
(1003, 2, 6, 49, NULL),
(996, 2, 6, 51, NULL),
(992, 2, 6, 52, NULL),
(993, 2, 6, 53, NULL),
(994, 2, 6, 54, NULL),
(995, 2, 6, 55, NULL),
(1000, 2, 6, 61, NULL),
(1002, 2, 6, 64, NULL),
(1005, 2, 6, 65, NULL),
(1007, 2, 6, 66, NULL),
(997, 2, 6, 75, NULL),
(1004, 2, 6, 79, NULL),
(998, 2, 6, 101, NULL),
(999, 2, 6, 102, NULL),
(1001, 2, 6, 104, NULL),
(3861, 2, 6, 106, NULL),
(990, 2, 6, 111, NULL),
(3851, 2, 6, 139, NULL),
(1008, 2, 6, 152, NULL),
(1009, 2, 6, 155, NULL),
(3911, 2, 6, 158, NULL),
(2761, 2, 6, 159, NULL),
(3871, 2, 6, 162, NULL),
(3881, 2, 6, 163, NULL),
(3891, 2, 6, 164, NULL),
(3901, 2, 6, 174, NULL),
(2882, 2, 6, 180, NULL),
(3971, 2, 6, 181, NULL),
(4074, 2, 6, 182, NULL),
(4054, 2, 6, 183, NULL),
(4064, 2, 6, 184, NULL),
(4235, 2, 6, 186, NULL),
(4245, 2, 6, 187, NULL),
(4265, 2, 6, 188, NULL),
(4275, 2, 6, 189, NULL),
(4285, 2, 6, 190, NULL),
(4295, 2, 6, 191, NULL),
(4255, 2, 6, 192, NULL),
(4305, 2, 6, 193, NULL),
(4668, 2, 6, 203, NULL),
(4750, 2, 6, 204, NULL),
(4790, 2, 6, 205, NULL),
(4830, 2, 6, 206, NULL),
(1169, 2, 7, 2, NULL),
(1171, 2, 7, 3, NULL),
(1170, 2, 7, 4, NULL),
(1172, 2, 7, 9, NULL),
(1159, 2, 7, 37, NULL),
(1163, 2, 7, 39, NULL),
(1167, 2, 7, 41, NULL),
(1166, 2, 7, 45, NULL),
(1168, 2, 7, 46, NULL),
(1174, 2, 7, 47, NULL),
(1186, 2, 7, 49, NULL),
(1179, 2, 7, 51, NULL),
(1175, 2, 7, 52, NULL),
(1176, 2, 7, 53, NULL),
(1177, 2, 7, 54, NULL),
(1178, 2, 7, 55, NULL),
(1183, 2, 7, 61, NULL),
(1185, 2, 7, 64, NULL),
(1188, 2, 7, 65, NULL),
(1190, 2, 7, 66, NULL),
(1180, 2, 7, 75, NULL),
(1187, 2, 7, 79, NULL),
(1181, 2, 7, 101, NULL),
(1182, 2, 7, 102, NULL),
(1184, 2, 7, 104, NULL),
(3860, 2, 7, 106, NULL),
(1173, 2, 7, 111, NULL),
(3850, 2, 7, 139, NULL),
(1191, 2, 7, 152, NULL),
(1192, 2, 7, 155, NULL),
(3910, 2, 7, 158, NULL),
(2760, 2, 7, 159, NULL),
(3870, 2, 7, 162, NULL),
(3880, 2, 7, 163, NULL),
(3890, 2, 7, 164, NULL),
(3900, 2, 7, 174, NULL),
(2881, 2, 7, 180, NULL),
(3970, 2, 7, 181, NULL),
(4073, 2, 7, 182, NULL),
(4053, 2, 7, 183, NULL),
(4063, 2, 7, 184, NULL),
(4234, 2, 7, 186, NULL),
(4244, 2, 7, 187, NULL),
(4264, 2, 7, 188, NULL),
(4274, 2, 7, 189, NULL),
(4284, 2, 7, 190, NULL),
(4294, 2, 7, 191, NULL),
(4254, 2, 7, 192, NULL),
(4304, 2, 7, 193, NULL),
(4667, 2, 7, 203, NULL),
(4749, 2, 7, 204, NULL),
(4789, 2, 7, 205, NULL),
(4829, 2, 7, 206, NULL),
(3909, 2, 8, 158, NULL),
(3969, 2, 8, 181, NULL),
(4233, 2, 8, 186, NULL),
(4243, 2, 8, 187, NULL),
(4263, 2, 8, 188, NULL),
(4273, 2, 8, 189, NULL),
(4283, 2, 8, 190, NULL),
(4293, 2, 8, 191, NULL),
(4253, 2, 8, 192, NULL),
(4303, 2, 8, 193, NULL),
(4666, 2, 8, 203, NULL),
(4788, 2, 8, 205, NULL),
(4828, 2, 8, 206, NULL),
(1535, 2, 9, 2, NULL),
(1537, 2, 9, 3, NULL),
(1536, 2, 9, 4, NULL),
(1538, 2, 9, 9, NULL),
(1525, 2, 9, 37, NULL),
(1529, 2, 9, 39, NULL),
(1533, 2, 9, 41, NULL),
(1532, 2, 9, 45, NULL),
(1534, 2, 9, 46, NULL),
(1540, 2, 9, 47, NULL),
(1552, 2, 9, 49, NULL),
(1545, 2, 9, 51, NULL),
(1541, 2, 9, 52, NULL),
(1542, 2, 9, 53, NULL),
(1543, 2, 9, 54, NULL),
(1544, 2, 9, 55, NULL),
(1549, 2, 9, 61, NULL),
(1551, 2, 9, 64, NULL),
(1554, 2, 9, 65, NULL),
(1556, 2, 9, 66, NULL),
(1546, 2, 9, 75, NULL),
(1553, 2, 9, 79, NULL),
(1547, 2, 9, 101, NULL),
(1548, 2, 9, 102, NULL),
(1550, 2, 9, 104, NULL),
(3858, 2, 9, 106, NULL),
(1539, 2, 9, 111, NULL),
(3848, 2, 9, 139, NULL),
(1557, 2, 9, 152, NULL),
(1558, 2, 9, 155, NULL),
(3908, 2, 9, 158, NULL),
(2758, 2, 9, 159, NULL),
(3868, 2, 9, 162, NULL),
(3878, 2, 9, 163, NULL),
(3888, 2, 9, 164, NULL),
(3898, 2, 9, 174, NULL),
(2879, 2, 9, 180, NULL),
(3968, 2, 9, 181, NULL),
(4071, 2, 9, 182, NULL),
(4051, 2, 9, 183, NULL),
(4061, 2, 9, 184, NULL),
(4232, 2, 9, 186, NULL),
(4242, 2, 9, 187, NULL),
(4262, 2, 9, 188, NULL),
(4272, 2, 9, 189, NULL),
(4282, 2, 9, 190, NULL),
(4292, 2, 9, 191, NULL),
(4252, 2, 9, 192, NULL),
(4302, 2, 9, 193, NULL),
(4665, 2, 9, 203, NULL),
(4747, 2, 9, 204, NULL),
(4787, 2, 9, 205, NULL),
(4827, 2, 9, 206, NULL),
(1718, 2, 10, 2, NULL),
(1720, 2, 10, 3, NULL),
(1719, 2, 10, 4, NULL),
(1721, 2, 10, 9, NULL),
(1708, 2, 10, 37, NULL),
(1712, 2, 10, 39, NULL),
(1716, 2, 10, 41, NULL),
(1715, 2, 10, 45, NULL),
(1717, 2, 10, 46, NULL),
(1723, 2, 10, 47, NULL),
(1735, 2, 10, 49, NULL),
(1728, 2, 10, 51, NULL),
(1724, 2, 10, 52, NULL),
(1725, 2, 10, 53, NULL),
(1726, 2, 10, 54, NULL),
(1727, 2, 10, 55, NULL),
(1732, 2, 10, 61, NULL),
(1734, 2, 10, 64, NULL),
(1737, 2, 10, 65, NULL),
(1739, 2, 10, 66, NULL),
(1729, 2, 10, 75, NULL),
(1736, 2, 10, 79, NULL),
(1730, 2, 10, 101, NULL),
(1731, 2, 10, 102, NULL),
(1733, 2, 10, 104, NULL),
(3857, 2, 10, 106, NULL),
(1722, 2, 10, 111, NULL),
(3847, 2, 10, 139, NULL),
(1740, 2, 10, 152, NULL),
(1741, 2, 10, 155, NULL),
(3907, 2, 10, 158, NULL),
(2757, 2, 10, 159, NULL),
(3867, 2, 10, 162, NULL),
(3877, 2, 10, 163, NULL),
(3887, 2, 10, 164, NULL),
(3897, 2, 10, 174, NULL),
(2878, 2, 10, 180, NULL),
(3967, 2, 10, 181, NULL),
(4070, 2, 10, 182, NULL),
(4050, 2, 10, 183, NULL),
(4060, 2, 10, 184, NULL),
(4231, 2, 10, 186, NULL),
(4241, 2, 10, 187, NULL),
(4261, 2, 10, 188, NULL),
(4271, 2, 10, 189, NULL),
(4281, 2, 10, 190, NULL),
(4291, 2, 10, 191, NULL),
(4251, 2, 10, 192, NULL),
(4301, 2, 10, 193, NULL),
(4664, 2, 10, 203, NULL),
(4746, 2, 10, 204, NULL),
(4786, 2, 10, 205, NULL),
(4826, 2, 10, 206, NULL),
(95, 3, 1, 2, NULL),
(96, 3, 1, 3, NULL),
(97, 3, 1, 47, NULL),
(98, 3, 1, 52, NULL),
(99, 3, 1, 54, NULL),
(100, 3, 1, 61, NULL),
(101, 3, 1, 155, NULL),
(2786, 3, 1, 176, NULL),
(2787, 3, 1, 177, NULL),
(2897, 3, 1, 180, NULL),
(278, 3, 2, 2, NULL),
(279, 3, 2, 3, NULL),
(280, 3, 2, 47, NULL),
(281, 3, 2, 52, NULL),
(282, 3, 2, 54, NULL),
(283, 3, 2, 61, NULL),
(284, 3, 2, 155, NULL),
(2788, 3, 2, 177, NULL),
(2896, 3, 2, 180, NULL),
(461, 3, 3, 2, NULL),
(462, 3, 3, 3, NULL),
(463, 3, 3, 47, NULL),
(464, 3, 3, 52, NULL),
(465, 3, 3, 54, NULL),
(466, 3, 3, 61, NULL),
(467, 3, 3, 155, NULL),
(2789, 3, 3, 177, NULL),
(644, 3, 4, 2, NULL),
(645, 3, 4, 3, NULL),
(646, 3, 4, 47, NULL),
(648, 3, 4, 54, NULL),
(649, 3, 4, 61, NULL),
(650, 3, 4, 155, NULL),
(2894, 3, 4, 180, NULL),
(827, 3, 5, 2, NULL),
(828, 3, 5, 3, NULL),
(829, 3, 5, 47, NULL),
(830, 3, 5, 52, NULL),
(831, 3, 5, 54, NULL),
(832, 3, 5, 61, NULL),
(833, 3, 5, 155, NULL),
(2893, 3, 5, 180, NULL),
(1010, 3, 6, 2, NULL),
(1011, 3, 6, 3, NULL),
(1012, 3, 6, 47, NULL),
(1013, 3, 6, 52, NULL),
(1014, 3, 6, 54, NULL),
(1015, 3, 6, 61, NULL),
(1016, 3, 6, 155, NULL),
(2892, 3, 6, 180, NULL),
(1193, 3, 7, 2, NULL),
(1194, 3, 7, 3, NULL),
(1195, 3, 7, 47, NULL),
(1196, 3, 7, 52, NULL),
(1197, 3, 7, 54, NULL),
(1198, 3, 7, 61, NULL),
(1199, 3, 7, 155, NULL),
(2891, 3, 7, 180, NULL),
(1376, 3, 8, 2, NULL),
(1377, 3, 8, 3, NULL),
(1378, 3, 8, 47, NULL),
(1379, 3, 8, 52, NULL),
(1380, 3, 8, 54, NULL),
(1381, 3, 8, 61, NULL),
(1382, 3, 8, 155, NULL),
(2890, 3, 8, 180, NULL),
(1559, 3, 9, 2, NULL),
(1560, 3, 9, 3, NULL),
(1561, 3, 9, 47, NULL),
(1562, 3, 9, 52, NULL),
(1563, 3, 9, 54, NULL),
(1564, 3, 9, 61, NULL),
(1565, 3, 9, 155, NULL),
(2889, 3, 9, 180, NULL),
(1742, 3, 10, 2, NULL),
(1743, 3, 10, 3, NULL),
(1744, 3, 10, 47, NULL),
(1745, 3, 10, 52, NULL),
(1746, 3, 10, 54, NULL),
(1747, 3, 10, 61, NULL),
(1748, 3, 10, 155, NULL),
(2888, 3, 10, 180, NULL),
(2026, 4, 1, 2, NULL),
(2056, 4, 1, 3, NULL),
(2046, 4, 1, 4, NULL),
(2116, 4, 1, 9, NULL),
(1966, 4, 1, 37, NULL),
(1976, 4, 1, 39, NULL),
(1996, 4, 1, 41, NULL),
(1986, 4, 1, 45, NULL),
(2006, 4, 1, 46, NULL),
(2146, 4, 1, 47, NULL),
(2416, 4, 1, 48, NULL),
(2456, 4, 1, 49, NULL),
(2326, 4, 1, 51, NULL),
(2156, 4, 1, 52, NULL),
(2166, 4, 1, 53, NULL),
(2196, 4, 1, 54, NULL),
(2296, 4, 1, 55, NULL),
(2426, 4, 1, 61, NULL),
(2446, 4, 1, 64, NULL),
(2476, 4, 1, 65, NULL),
(2486, 4, 1, 66, NULL),
(3926, 4, 1, 68, NULL),
(2746, 4, 1, 69, NULL),
(2346, 4, 1, 75, NULL),
(2466, 4, 1, 79, NULL),
(2356, 4, 1, 101, NULL),
(2366, 4, 1, 102, NULL),
(2436, 4, 1, 104, NULL),
(2396, 4, 1, 105, NULL),
(2216, 4, 1, 106, NULL),
(2136, 4, 1, 111, NULL),
(2076, 4, 1, 126, NULL),
(2226, 4, 1, 139, NULL),
(2376, 4, 1, 145, NULL),
(2756, 4, 1, 151, NULL),
(2126, 4, 1, 152, NULL),
(2406, 4, 1, 155, NULL),
(2866, 4, 1, 158, NULL),
(2276, 4, 1, 159, NULL),
(4735, 4, 1, 161, NULL),
(4725, 4, 1, 162, NULL),
(2246, 4, 1, 163, NULL),
(2266, 4, 1, 164, NULL),
(4705, 4, 1, 172, NULL),
(2256, 4, 1, 173, NULL),
(2782, 4, 1, 176, NULL),
(2783, 4, 1, 177, NULL),
(2831, 4, 1, 179, NULL),
(2907, 4, 1, 180, NULL),
(4006, 4, 1, 181, NULL),
(4109, 4, 1, 182, NULL),
(4089, 4, 1, 183, NULL),
(4099, 4, 1, 184, NULL),
(4320, 4, 1, 186, NULL),
(4330, 4, 1, 187, NULL),
(4350, 4, 1, 188, NULL),
(4360, 4, 1, 189, NULL),
(4370, 4, 1, 190, NULL),
(4380, 4, 1, 191, NULL),
(4340, 4, 1, 192, NULL),
(4390, 4, 1, 193, NULL),
(4592, 4, 1, 195, NULL),
(4582, 4, 1, 196, NULL),
(4602, 4, 1, 197, NULL),
(4612, 4, 1, 198, NULL),
(4902, 4, 1, 203, NULL),
(4765, 4, 1, 204, NULL),
(4805, 4, 1, 205, NULL),
(4845, 4, 1, 206, NULL),
(4855, 4, 1, 207, NULL),
(4892, 4, 1, 208, NULL),
(2025, 4, 2, 2, NULL),
(2055, 4, 2, 3, NULL),
(2045, 4, 2, 4, NULL),
(2115, 4, 2, 9, NULL),
(1965, 4, 2, 37, NULL),
(1975, 4, 2, 39, NULL),
(1995, 4, 2, 41, NULL),
(1985, 4, 2, 45, NULL),
(2005, 4, 2, 46, NULL),
(2145, 4, 2, 47, NULL),
(2415, 4, 2, 48, NULL),
(2455, 4, 2, 49, NULL),
(2325, 4, 2, 51, NULL),
(2155, 4, 2, 52, NULL),
(2165, 4, 2, 53, NULL),
(2195, 4, 2, 54, NULL),
(2295, 4, 2, 55, NULL),
(2425, 4, 2, 61, NULL),
(2445, 4, 2, 64, NULL),
(2475, 4, 2, 65, NULL),
(2485, 4, 2, 66, NULL),
(2745, 4, 2, 69, NULL),
(2345, 4, 2, 75, NULL),
(2465, 4, 2, 79, NULL),
(2355, 4, 2, 101, NULL),
(2365, 4, 2, 102, NULL),
(2435, 4, 2, 104, NULL),
(2395, 4, 2, 105, NULL),
(2215, 4, 2, 106, NULL),
(2135, 4, 2, 111, NULL),
(2075, 4, 2, 126, NULL),
(2225, 4, 2, 139, NULL),
(2375, 4, 2, 145, NULL),
(2755, 4, 2, 151, NULL),
(2125, 4, 2, 152, NULL),
(2405, 4, 2, 155, NULL),
(2865, 4, 2, 158, NULL),
(2275, 4, 2, 159, NULL),
(4734, 4, 2, 161, NULL),
(4724, 4, 2, 162, NULL),
(2245, 4, 2, 163, NULL),
(2265, 4, 2, 164, NULL),
(4704, 4, 2, 172, NULL),
(2255, 4, 2, 173, NULL),
(2784, 4, 2, 177, NULL),
(2832, 4, 2, 179, NULL),
(2906, 4, 2, 180, NULL),
(4108, 4, 2, 182, NULL),
(4088, 4, 2, 183, NULL),
(4098, 4, 2, 184, NULL),
(4319, 4, 2, 186, NULL),
(4329, 4, 2, 187, NULL),
(4349, 4, 2, 188, NULL),
(4359, 4, 2, 189, NULL),
(4369, 4, 2, 190, NULL),
(4379, 4, 2, 191, NULL),
(4339, 4, 2, 192, NULL),
(4389, 4, 2, 193, NULL),
(4591, 4, 2, 195, NULL),
(4581, 4, 2, 196, NULL),
(4601, 4, 2, 197, NULL),
(4611, 4, 2, 198, NULL),
(4901, 4, 2, 203, NULL),
(4764, 4, 2, 204, NULL),
(4804, 4, 2, 205, NULL),
(4844, 4, 2, 206, NULL),
(4854, 4, 2, 207, NULL),
(2024, 4, 3, 2, NULL),
(2054, 4, 3, 3, NULL),
(2044, 4, 3, 4, NULL),
(2114, 4, 3, 9, NULL),
(1964, 4, 3, 37, NULL),
(1974, 4, 3, 39, NULL),
(1994, 4, 3, 41, NULL),
(1984, 4, 3, 45, NULL),
(2004, 4, 3, 46, NULL),
(2144, 4, 3, 47, NULL),
(2414, 4, 3, 48, NULL),
(2454, 4, 3, 49, NULL),
(2324, 4, 3, 51, NULL),
(2154, 4, 3, 52, NULL),
(2164, 4, 3, 53, NULL),
(2194, 4, 3, 54, NULL),
(2294, 4, 3, 55, NULL),
(2424, 4, 3, 61, NULL),
(2444, 4, 3, 64, NULL),
(2474, 4, 3, 65, NULL),
(2484, 4, 3, 66, NULL),
(2744, 4, 3, 69, NULL),
(2344, 4, 3, 75, NULL),
(2464, 4, 3, 79, NULL),
(2354, 4, 3, 101, NULL),
(2364, 4, 3, 102, NULL),
(2434, 4, 3, 104, NULL),
(2394, 4, 3, 105, NULL),
(2214, 4, 3, 106, NULL),
(2134, 4, 3, 111, NULL),
(2074, 4, 3, 126, NULL),
(2224, 4, 3, 139, NULL),
(2374, 4, 3, 145, NULL),
(2754, 4, 3, 151, NULL),
(2124, 4, 3, 152, NULL),
(2404, 4, 3, 155, NULL),
(2864, 4, 3, 158, NULL),
(2274, 4, 3, 159, NULL),
(4733, 4, 3, 161, NULL),
(4723, 4, 3, 162, NULL),
(2244, 4, 3, 163, NULL),
(2264, 4, 3, 164, NULL),
(4703, 4, 3, 172, NULL),
(2254, 4, 3, 173, NULL),
(2785, 4, 3, 177, NULL),
(2835, 4, 3, 179, NULL),
(4107, 4, 3, 182, NULL),
(4087, 4, 3, 183, NULL),
(4097, 4, 3, 184, NULL),
(4318, 4, 3, 186, NULL),
(4328, 4, 3, 187, NULL),
(4348, 4, 3, 188, NULL),
(4358, 4, 3, 189, NULL),
(4368, 4, 3, 190, NULL),
(4378, 4, 3, 191, NULL),
(4338, 4, 3, 192, NULL),
(4388, 4, 3, 193, NULL),
(4590, 4, 3, 195, NULL),
(4580, 4, 3, 196, NULL),
(4600, 4, 3, 197, NULL),
(4610, 4, 3, 198, NULL),
(4900, 4, 3, 203, NULL),
(4763, 4, 3, 204, NULL),
(4803, 4, 3, 205, NULL),
(4843, 4, 3, 206, NULL),
(4853, 4, 3, 207, NULL),
(2023, 4, 4, 2, NULL),
(2053, 4, 4, 3, NULL),
(2043, 4, 4, 4, NULL),
(1963, 4, 4, 37, NULL),
(1973, 4, 4, 39, NULL),
(1993, 4, 4, 41, NULL),
(1983, 4, 4, 45, NULL),
(2003, 4, 4, 46, NULL),
(2143, 4, 4, 47, NULL),
(2413, 4, 4, 48, NULL),
(2453, 4, 4, 49, NULL),
(2323, 4, 4, 51, NULL),
(2153, 4, 4, 52, NULL),
(2163, 4, 4, 53, NULL),
(2193, 4, 4, 54, NULL),
(2293, 4, 4, 55, NULL),
(2423, 4, 4, 61, NULL),
(2443, 4, 4, 64, NULL),
(2473, 4, 4, 65, NULL),
(2483, 4, 4, 66, NULL),
(2743, 4, 4, 69, NULL),
(2343, 4, 4, 75, NULL),
(2463, 4, 4, 79, NULL),
(2353, 4, 4, 101, NULL),
(2363, 4, 4, 102, NULL),
(2433, 4, 4, 104, NULL),
(2393, 4, 4, 105, NULL),
(2213, 4, 4, 106, NULL),
(2133, 4, 4, 111, NULL),
(2073, 4, 4, 126, NULL),
(2223, 4, 4, 139, NULL),
(2373, 4, 4, 145, NULL),
(2753, 4, 4, 151, NULL),
(2123, 4, 4, 152, NULL),
(2403, 4, 4, 155, NULL),
(2863, 4, 4, 158, NULL),
(2273, 4, 4, 159, NULL),
(4732, 4, 4, 161, NULL),
(4722, 4, 4, 162, NULL),
(2243, 4, 4, 163, NULL),
(2263, 4, 4, 164, NULL),
(4702, 4, 4, 172, NULL),
(2253, 4, 4, 173, NULL),
(2904, 4, 4, 180, NULL),
(4106, 4, 4, 182, NULL),
(4086, 4, 4, 183, NULL),
(4096, 4, 4, 184, NULL),
(4317, 4, 4, 186, NULL),
(4327, 4, 4, 187, NULL),
(4347, 4, 4, 188, NULL),
(4357, 4, 4, 189, NULL),
(4367, 4, 4, 190, NULL),
(4377, 4, 4, 191, NULL),
(4337, 4, 4, 192, NULL),
(4387, 4, 4, 193, NULL),
(4589, 4, 4, 195, NULL),
(4579, 4, 4, 196, NULL),
(4599, 4, 4, 197, NULL),
(4609, 4, 4, 198, NULL),
(4899, 4, 4, 203, NULL),
(4802, 4, 4, 205, NULL),
(4852, 4, 4, 207, NULL),
(2022, 4, 5, 2, NULL),
(2052, 4, 5, 3, NULL),
(2042, 4, 5, 4, NULL),
(2112, 4, 5, 9, NULL),
(1962, 4, 5, 37, NULL),
(1972, 4, 5, 39, NULL),
(1992, 4, 5, 41, NULL),
(1982, 4, 5, 45, NULL),
(2002, 4, 5, 46, NULL),
(2142, 4, 5, 47, NULL),
(2412, 4, 5, 48, NULL),
(2452, 4, 5, 49, NULL),
(2322, 4, 5, 51, NULL),
(2152, 4, 5, 52, NULL),
(2162, 4, 5, 53, NULL),
(2192, 4, 5, 54, NULL),
(2292, 4, 5, 55, NULL),
(2422, 4, 5, 61, NULL),
(2442, 4, 5, 64, NULL),
(2472, 4, 5, 65, NULL),
(2482, 4, 5, 66, NULL),
(2742, 4, 5, 69, NULL),
(2342, 4, 5, 75, NULL),
(2462, 4, 5, 79, NULL),
(2352, 4, 5, 101, NULL),
(2362, 4, 5, 102, NULL),
(2432, 4, 5, 104, NULL),
(2392, 4, 5, 105, NULL),
(2212, 4, 5, 106, NULL),
(2132, 4, 5, 111, NULL),
(2072, 4, 5, 126, NULL),
(2222, 4, 5, 139, NULL),
(2372, 4, 5, 145, NULL),
(2752, 4, 5, 151, NULL),
(2122, 4, 5, 152, NULL),
(2402, 4, 5, 155, NULL),
(2862, 4, 5, 158, NULL),
(2272, 4, 5, 159, NULL),
(4731, 4, 5, 161, NULL),
(4721, 4, 5, 162, NULL),
(2242, 4, 5, 163, NULL),
(2262, 4, 5, 164, NULL),
(4701, 4, 5, 172, NULL),
(2252, 4, 5, 173, NULL),
(2903, 4, 5, 180, NULL),
(4002, 4, 5, 181, NULL),
(4105, 4, 5, 182, NULL),
(4085, 4, 5, 183, NULL),
(4095, 4, 5, 184, NULL),
(4316, 4, 5, 186, NULL),
(4326, 4, 5, 187, NULL),
(4346, 4, 5, 188, NULL),
(4356, 4, 5, 189, NULL),
(4366, 4, 5, 190, NULL),
(4376, 4, 5, 191, NULL),
(4336, 4, 5, 192, NULL),
(4386, 4, 5, 193, NULL),
(4588, 4, 5, 195, NULL),
(4578, 4, 5, 196, NULL),
(4598, 4, 5, 197, NULL),
(4608, 4, 5, 198, NULL),
(4903, 4, 5, 203, NULL),
(4761, 4, 5, 204, NULL),
(4801, 4, 5, 205, NULL),
(4841, 4, 5, 206, NULL),
(4851, 4, 5, 207, NULL),
(4888, 4, 5, 208, NULL),
(2021, 4, 6, 2, NULL),
(2051, 4, 6, 3, NULL),
(2041, 4, 6, 4, NULL),
(2111, 4, 6, 9, NULL),
(1961, 4, 6, 37, NULL),
(1971, 4, 6, 39, NULL),
(1991, 4, 6, 41, NULL),
(1981, 4, 6, 45, NULL),
(2001, 4, 6, 46, NULL),
(2141, 4, 6, 47, NULL),
(2411, 4, 6, 48, NULL),
(2451, 4, 6, 49, NULL),
(2321, 4, 6, 51, NULL),
(2151, 4, 6, 52, NULL),
(2161, 4, 6, 53, NULL),
(2191, 4, 6, 54, NULL),
(2291, 4, 6, 55, NULL),
(2421, 4, 6, 61, NULL),
(2441, 4, 6, 64, NULL),
(2471, 4, 6, 65, NULL),
(2481, 4, 6, 66, NULL),
(2741, 4, 6, 69, NULL),
(2341, 4, 6, 75, NULL),
(2461, 4, 6, 79, NULL),
(2351, 4, 6, 101, NULL),
(2361, 4, 6, 102, NULL),
(2431, 4, 6, 104, NULL),
(2391, 4, 6, 105, NULL),
(2211, 4, 6, 106, NULL),
(2131, 4, 6, 111, NULL),
(2071, 4, 6, 126, NULL),
(2221, 4, 6, 139, NULL),
(2371, 4, 6, 145, NULL),
(2751, 4, 6, 151, NULL),
(2121, 4, 6, 152, NULL),
(2401, 4, 6, 155, NULL),
(2861, 4, 6, 158, NULL),
(2271, 4, 6, 159, NULL),
(4730, 4, 6, 161, NULL),
(4720, 4, 6, 162, NULL),
(2241, 4, 6, 163, NULL),
(2261, 4, 6, 164, NULL),
(4700, 4, 6, 172, NULL),
(2251, 4, 6, 173, NULL),
(2902, 4, 6, 180, NULL),
(4001, 4, 6, 181, NULL),
(4104, 4, 6, 182, NULL),
(4084, 4, 6, 183, NULL),
(4094, 4, 6, 184, NULL),
(4315, 4, 6, 186, NULL),
(4325, 4, 6, 187, NULL),
(4345, 4, 6, 188, NULL),
(4355, 4, 6, 189, NULL),
(4365, 4, 6, 190, NULL),
(4375, 4, 6, 191, NULL),
(4335, 4, 6, 192, NULL),
(4385, 4, 6, 193, NULL),
(4587, 4, 6, 195, NULL),
(4577, 4, 6, 196, NULL),
(4597, 4, 6, 197, NULL),
(4607, 4, 6, 198, NULL),
(4897, 4, 6, 203, NULL),
(4760, 4, 6, 204, NULL),
(4800, 4, 6, 205, NULL),
(4840, 4, 6, 206, NULL),
(4850, 4, 6, 207, NULL),
(4887, 4, 6, 208, NULL),
(2020, 4, 7, 2, NULL),
(2050, 4, 7, 3, NULL),
(2040, 4, 7, 4, NULL),
(2110, 4, 7, 9, NULL),
(1960, 4, 7, 37, NULL),
(1970, 4, 7, 39, NULL),
(1990, 4, 7, 41, NULL),
(1980, 4, 7, 45, NULL),
(2000, 4, 7, 46, NULL),
(2140, 4, 7, 47, NULL),
(2410, 4, 7, 48, NULL),
(2450, 4, 7, 49, NULL),
(2320, 4, 7, 51, NULL),
(2150, 4, 7, 52, NULL),
(2160, 4, 7, 53, NULL),
(2190, 4, 7, 54, NULL),
(2290, 4, 7, 55, NULL),
(2420, 4, 7, 61, NULL),
(2440, 4, 7, 64, NULL),
(2470, 4, 7, 65, NULL),
(2480, 4, 7, 66, NULL),
(2740, 4, 7, 69, NULL),
(2340, 4, 7, 75, NULL),
(2460, 4, 7, 79, NULL),
(2350, 4, 7, 101, NULL),
(2360, 4, 7, 102, NULL),
(2430, 4, 7, 104, NULL),
(2390, 4, 7, 105, NULL),
(2210, 4, 7, 106, NULL),
(2130, 4, 7, 111, NULL),
(2070, 4, 7, 126, NULL),
(2220, 4, 7, 139, NULL),
(2370, 4, 7, 145, NULL),
(2750, 4, 7, 151, NULL),
(2120, 4, 7, 152, NULL),
(2400, 4, 7, 155, NULL),
(2860, 4, 7, 158, NULL),
(2270, 4, 7, 159, NULL),
(4729, 4, 7, 161, NULL),
(4719, 4, 7, 162, NULL),
(2240, 4, 7, 163, NULL),
(2260, 4, 7, 164, NULL),
(4699, 4, 7, 172, NULL),
(2250, 4, 7, 173, NULL),
(2901, 4, 7, 180, NULL),
(4000, 4, 7, 181, NULL),
(4103, 4, 7, 182, NULL),
(4083, 4, 7, 183, NULL),
(4093, 4, 7, 184, NULL),
(4314, 4, 7, 186, NULL),
(4324, 4, 7, 187, NULL),
(4344, 4, 7, 188, NULL),
(4354, 4, 7, 189, NULL),
(4364, 4, 7, 190, NULL),
(4374, 4, 7, 191, NULL),
(4334, 4, 7, 192, NULL),
(4384, 4, 7, 193, NULL),
(4586, 4, 7, 195, NULL),
(4576, 4, 7, 196, NULL),
(4596, 4, 7, 197, NULL),
(4606, 4, 7, 198, NULL),
(4896, 4, 7, 203, NULL),
(4759, 4, 7, 204, NULL),
(4799, 4, 7, 205, NULL),
(4839, 4, 7, 206, NULL),
(4849, 4, 7, 207, NULL),
(4886, 4, 7, 208, NULL),
(2019, 4, 8, 2, NULL),
(2049, 4, 8, 3, NULL),
(2039, 4, 8, 4, NULL),
(2109, 4, 8, 9, NULL),
(1959, 4, 8, 37, NULL),
(1969, 4, 8, 39, NULL),
(1989, 4, 8, 41, NULL),
(1979, 4, 8, 45, NULL),
(1999, 4, 8, 46, NULL),
(2139, 4, 8, 47, NULL),
(2409, 4, 8, 48, NULL),
(2449, 4, 8, 49, NULL),
(2319, 4, 8, 51, NULL),
(2149, 4, 8, 52, NULL),
(2159, 4, 8, 53, NULL),
(2189, 4, 8, 54, NULL),
(2289, 4, 8, 55, NULL),
(2419, 4, 8, 61, NULL),
(2439, 4, 8, 64, NULL),
(2469, 4, 8, 65, NULL),
(2479, 4, 8, 66, NULL),
(2739, 4, 8, 69, NULL),
(2339, 4, 8, 75, NULL),
(2459, 4, 8, 79, NULL),
(2349, 4, 8, 101, NULL),
(2359, 4, 8, 102, NULL),
(2429, 4, 8, 104, NULL),
(2389, 4, 8, 105, NULL),
(2209, 4, 8, 106, NULL),
(2129, 4, 8, 111, NULL),
(2069, 4, 8, 126, NULL),
(2219, 4, 8, 139, NULL),
(2369, 4, 8, 145, NULL),
(2749, 4, 8, 151, NULL),
(2119, 4, 8, 152, NULL),
(2399, 4, 8, 155, NULL),
(2859, 4, 8, 158, NULL),
(2269, 4, 8, 159, NULL),
(4728, 4, 8, 161, NULL),
(4718, 4, 8, 162, NULL),
(2239, 4, 8, 163, NULL),
(2259, 4, 8, 164, NULL),
(4698, 4, 8, 172, NULL),
(2249, 4, 8, 173, NULL),
(2900, 4, 8, 180, NULL),
(3999, 4, 8, 181, NULL),
(4313, 4, 8, 186, NULL),
(4323, 4, 8, 187, NULL),
(4343, 4, 8, 188, NULL),
(4353, 4, 8, 189, NULL),
(4363, 4, 8, 190, NULL),
(4373, 4, 8, 191, NULL),
(4333, 4, 8, 192, NULL),
(4383, 4, 8, 193, NULL),
(4585, 4, 8, 195, NULL),
(4575, 4, 8, 196, NULL),
(4595, 4, 8, 197, NULL),
(4605, 4, 8, 198, NULL),
(4895, 4, 8, 203, NULL),
(4758, 4, 8, 204, NULL),
(4798, 4, 8, 205, NULL),
(4838, 4, 8, 206, NULL),
(4848, 4, 8, 207, NULL),
(4885, 4, 8, 208, NULL),
(2018, 4, 9, 2, NULL),
(2048, 4, 9, 3, NULL),
(2038, 4, 9, 4, NULL),
(2108, 4, 9, 9, NULL),
(1958, 4, 9, 37, NULL),
(1968, 4, 9, 39, NULL),
(1988, 4, 9, 41, NULL),
(1978, 4, 9, 45, NULL),
(1998, 4, 9, 46, NULL),
(2138, 4, 9, 47, NULL),
(2408, 4, 9, 48, NULL),
(2448, 4, 9, 49, NULL),
(2318, 4, 9, 51, NULL),
(2148, 4, 9, 52, NULL),
(2158, 4, 9, 53, NULL),
(2188, 4, 9, 54, NULL),
(2288, 4, 9, 55, NULL),
(2418, 4, 9, 61, NULL),
(2438, 4, 9, 64, NULL),
(2468, 4, 9, 65, NULL),
(2478, 4, 9, 66, NULL),
(2738, 4, 9, 69, NULL),
(2338, 4, 9, 75, NULL),
(2458, 4, 9, 79, NULL),
(2348, 4, 9, 101, NULL),
(2358, 4, 9, 102, NULL),
(2428, 4, 9, 104, NULL),
(2388, 4, 9, 105, NULL),
(2208, 4, 9, 106, NULL),
(2128, 4, 9, 111, NULL),
(2068, 4, 9, 126, NULL),
(2218, 4, 9, 139, NULL),
(2368, 4, 9, 145, NULL),
(2748, 4, 9, 151, NULL),
(2118, 4, 9, 152, NULL),
(2398, 4, 9, 155, NULL),
(2858, 4, 9, 158, NULL),
(2268, 4, 9, 159, NULL),
(4727, 4, 9, 161, NULL),
(4717, 4, 9, 162, NULL),
(2238, 4, 9, 163, NULL),
(2258, 4, 9, 164, NULL),
(4697, 4, 9, 172, NULL),
(2248, 4, 9, 173, NULL),
(2899, 4, 9, 180, NULL),
(3998, 4, 9, 181, NULL),
(4101, 4, 9, 182, NULL),
(4081, 4, 9, 183, NULL),
(4091, 4, 9, 184, NULL),
(4312, 4, 9, 186, NULL),
(4322, 4, 9, 187, NULL),
(4342, 4, 9, 188, NULL),
(4352, 4, 9, 189, NULL),
(4362, 4, 9, 190, NULL),
(4372, 4, 9, 191, NULL),
(4332, 4, 9, 192, NULL),
(4382, 4, 9, 193, NULL),
(4584, 4, 9, 195, NULL),
(4574, 4, 9, 196, NULL),
(4594, 4, 9, 197, NULL),
(4604, 4, 9, 198, NULL),
(4894, 4, 9, 203, NULL),
(4757, 4, 9, 204, NULL),
(4797, 4, 9, 205, NULL),
(4837, 4, 9, 206, NULL),
(4847, 4, 9, 207, NULL),
(4884, 4, 9, 208, NULL),
(2017, 4, 10, 2, NULL),
(2047, 4, 10, 3, NULL),
(2037, 4, 10, 4, NULL),
(2107, 4, 10, 9, NULL),
(1957, 4, 10, 37, NULL),
(1967, 4, 10, 39, NULL),
(1987, 4, 10, 41, NULL),
(1977, 4, 10, 45, NULL),
(1997, 4, 10, 46, NULL),
(2137, 4, 10, 47, NULL),
(2407, 4, 10, 48, NULL),
(2447, 4, 10, 49, NULL),
(2317, 4, 10, 51, NULL),
(2147, 4, 10, 52, NULL),
(2157, 4, 10, 53, NULL),
(2187, 4, 10, 54, NULL),
(2287, 4, 10, 55, NULL),
(2417, 4, 10, 61, NULL),
(2437, 4, 10, 64, NULL),
(2467, 4, 10, 65, NULL),
(2477, 4, 10, 66, NULL),
(2737, 4, 10, 69, NULL),
(2337, 4, 10, 75, NULL),
(2457, 4, 10, 79, NULL),
(2347, 4, 10, 101, NULL),
(2357, 4, 10, 102, NULL),
(2427, 4, 10, 104, NULL),
(2387, 4, 10, 105, NULL),
(2207, 4, 10, 106, NULL),
(2127, 4, 10, 111, NULL),
(2067, 4, 10, 126, NULL),
(2217, 4, 10, 139, NULL),
(2367, 4, 10, 145, NULL),
(2747, 4, 10, 151, NULL),
(2117, 4, 10, 152, NULL),
(2397, 4, 10, 155, NULL),
(2857, 4, 10, 158, NULL),
(2267, 4, 10, 159, NULL),
(4726, 4, 10, 161, NULL),
(4716, 4, 10, 162, NULL),
(2237, 4, 10, 163, NULL),
(2257, 4, 10, 164, NULL),
(4696, 4, 10, 172, NULL),
(2247, 4, 10, 173, NULL),
(2898, 4, 10, 180, NULL),
(3997, 4, 10, 181, NULL),
(4100, 4, 10, 182, NULL),
(4080, 4, 10, 183, NULL),
(4090, 4, 10, 184, NULL),
(4311, 4, 10, 186, NULL),
(4321, 4, 10, 187, NULL),
(4341, 4, 10, 188, NULL),
(4351, 4, 10, 189, NULL),
(4361, 4, 10, 190, NULL),
(4371, 4, 10, 191, NULL),
(4331, 4, 10, 192, NULL),
(4381, 4, 10, 193, NULL),
(4583, 4, 10, 195, NULL),
(4573, 4, 10, 196, NULL),
(4593, 4, 10, 197, NULL),
(4603, 4, 10, 198, NULL),
(4893, 4, 10, 203, NULL),
(4756, 4, 10, 204, NULL),
(4796, 4, 10, 205, NULL),
(4836, 4, 10, 206, NULL),
(4846, 4, 10, 207, NULL),
(4883, 4, 10, 208, NULL),
(154, 5, 1, 2, NULL),
(156, 5, 1, 3, NULL),
(155, 5, 1, 4, NULL),
(157, 5, 1, 9, NULL),
(145, 5, 1, 37, NULL),
(149, 5, 1, 39, NULL),
(152, 5, 1, 45, NULL),
(153, 5, 1, 46, NULL),
(159, 5, 1, 47, NULL),
(174, 5, 1, 48, NULL),
(175, 5, 1, 49, NULL),
(180, 5, 1, 50, NULL),
(168, 5, 1, 51, NULL),
(160, 5, 1, 52, NULL),
(161, 5, 1, 53, NULL),
(164, 5, 1, 54, NULL),
(166, 5, 1, 55, NULL),
(177, 5, 1, 65, NULL),
(179, 5, 1, 66, NULL),
(169, 5, 1, 75, NULL),
(176, 5, 1, 79, NULL),
(170, 5, 1, 101, NULL),
(171, 5, 1, 102, NULL),
(173, 5, 1, 105, NULL),
(162, 5, 1, 106, NULL),
(158, 5, 1, 111, NULL),
(181, 5, 1, 114, NULL),
(1853, 5, 1, 126, NULL),
(165, 5, 1, 139, NULL),
(172, 5, 1, 145, NULL),
(182, 5, 1, 152, NULL),
(183, 5, 1, 155, NULL),
(1906, 5, 1, 159, NULL),
(1916, 5, 1, 163, NULL),
(1917, 5, 1, 164, NULL),
(2778, 5, 1, 176, NULL),
(2779, 5, 1, 177, NULL),
(2833, 5, 1, 179, NULL),
(2917, 5, 1, 180, NULL),
(4016, 5, 1, 181, NULL),
(4775, 5, 1, 204, NULL),
(337, 5, 2, 2, NULL),
(339, 5, 2, 3, NULL),
(338, 5, 2, 4, NULL),
(340, 5, 2, 9, NULL),
(328, 5, 2, 37, NULL),
(332, 5, 2, 39, NULL),
(335, 5, 2, 45, NULL),
(336, 5, 2, 46, NULL),
(342, 5, 2, 47, NULL),
(357, 5, 2, 48, NULL),
(358, 5, 2, 49, NULL),
(363, 5, 2, 50, NULL),
(351, 5, 2, 51, NULL),
(343, 5, 2, 52, NULL),
(344, 5, 2, 53, NULL),
(347, 5, 2, 54, NULL),
(349, 5, 2, 55, NULL),
(360, 5, 2, 65, NULL),
(362, 5, 2, 66, NULL),
(352, 5, 2, 75, NULL),
(359, 5, 2, 79, NULL),
(353, 5, 2, 101, NULL),
(354, 5, 2, 102, NULL),
(356, 5, 2, 105, NULL),
(345, 5, 2, 106, NULL),
(341, 5, 2, 111, NULL),
(364, 5, 2, 114, NULL),
(348, 5, 2, 139, NULL),
(355, 5, 2, 145, NULL),
(365, 5, 2, 152, NULL),
(366, 5, 2, 155, NULL),
(1905, 5, 2, 159, NULL),
(1915, 5, 2, 163, NULL),
(1918, 5, 2, 164, NULL),
(2780, 5, 2, 177, NULL),
(2834, 5, 2, 179, NULL),
(2916, 5, 2, 180, NULL),
(520, 5, 3, 2, NULL),
(522, 5, 3, 3, NULL),
(521, 5, 3, 4, NULL),
(523, 5, 3, 9, NULL),
(511, 5, 3, 37, NULL),
(515, 5, 3, 39, NULL),
(518, 5, 3, 45, NULL),
(519, 5, 3, 46, NULL),
(525, 5, 3, 47, NULL),
(540, 5, 3, 48, NULL),
(541, 5, 3, 49, NULL),
(546, 5, 3, 50, NULL),
(534, 5, 3, 51, NULL),
(526, 5, 3, 52, NULL),
(527, 5, 3, 53, NULL),
(530, 5, 3, 54, NULL),
(532, 5, 3, 55, NULL),
(543, 5, 3, 65, NULL),
(545, 5, 3, 66, NULL),
(535, 5, 3, 75, NULL),
(542, 5, 3, 79, NULL),
(536, 5, 3, 101, NULL),
(537, 5, 3, 102, NULL),
(539, 5, 3, 105, NULL),
(528, 5, 3, 106, NULL),
(524, 5, 3, 111, NULL),
(547, 5, 3, 114, NULL),
(531, 5, 3, 139, NULL),
(538, 5, 3, 145, NULL),
(548, 5, 3, 152, NULL),
(549, 5, 3, 155, NULL),
(1904, 5, 3, 159, NULL),
(1914, 5, 3, 163, NULL),
(1919, 5, 3, 164, NULL),
(2781, 5, 3, 177, NULL),
(703, 5, 4, 2, NULL),
(705, 5, 4, 3, NULL),
(704, 5, 4, 4, NULL),
(694, 5, 4, 37, NULL),
(698, 5, 4, 39, NULL),
(701, 5, 4, 45, NULL),
(702, 5, 4, 46, NULL),
(708, 5, 4, 47, NULL),
(723, 5, 4, 48, NULL),
(724, 5, 4, 49, NULL),
(729, 5, 4, 50, NULL),
(717, 5, 4, 51, NULL),
(710, 5, 4, 53, NULL),
(713, 5, 4, 54, NULL),
(715, 5, 4, 55, NULL),
(726, 5, 4, 65, NULL),
(728, 5, 4, 66, NULL),
(718, 5, 4, 75, NULL),
(725, 5, 4, 79, NULL),
(719, 5, 4, 101, NULL),
(720, 5, 4, 102, NULL),
(722, 5, 4, 105, NULL),
(711, 5, 4, 106, NULL),
(707, 5, 4, 111, NULL),
(730, 5, 4, 114, NULL),
(714, 5, 4, 139, NULL),
(721, 5, 4, 145, NULL),
(731, 5, 4, 152, NULL),
(732, 5, 4, 155, NULL),
(1903, 5, 4, 159, NULL),
(1913, 5, 4, 163, NULL),
(1920, 5, 4, 164, NULL),
(2914, 5, 4, 180, NULL),
(886, 5, 5, 2, NULL),
(888, 5, 5, 3, NULL),
(887, 5, 5, 4, NULL),
(889, 5, 5, 9, NULL),
(877, 5, 5, 37, NULL),
(881, 5, 5, 39, NULL),
(884, 5, 5, 45, NULL),
(885, 5, 5, 46, NULL),
(891, 5, 5, 47, NULL);
INSERT INTO `profil_privilege` (`id_profil_privilege`, `id_profil`, `id_privilege`, `id_module`, `field`) VALUES
(906, 5, 5, 48, NULL),
(907, 5, 5, 49, NULL),
(912, 5, 5, 50, NULL),
(900, 5, 5, 51, NULL),
(892, 5, 5, 52, NULL),
(893, 5, 5, 53, NULL),
(896, 5, 5, 54, NULL),
(898, 5, 5, 55, NULL),
(909, 5, 5, 65, NULL),
(911, 5, 5, 66, NULL),
(901, 5, 5, 75, NULL),
(908, 5, 5, 79, NULL),
(902, 5, 5, 101, NULL),
(903, 5, 5, 102, NULL),
(905, 5, 5, 105, NULL),
(894, 5, 5, 106, NULL),
(890, 5, 5, 111, NULL),
(913, 5, 5, 114, NULL),
(897, 5, 5, 139, NULL),
(904, 5, 5, 145, NULL),
(914, 5, 5, 152, NULL),
(915, 5, 5, 155, NULL),
(1902, 5, 5, 159, NULL),
(1912, 5, 5, 163, NULL),
(1921, 5, 5, 164, NULL),
(2913, 5, 5, 180, NULL),
(4012, 5, 5, 181, NULL),
(4771, 5, 5, 204, NULL),
(1069, 5, 6, 2, NULL),
(1071, 5, 6, 3, NULL),
(1070, 5, 6, 4, NULL),
(1072, 5, 6, 9, NULL),
(1060, 5, 6, 37, NULL),
(1064, 5, 6, 39, NULL),
(1067, 5, 6, 45, NULL),
(1068, 5, 6, 46, NULL),
(1074, 5, 6, 47, NULL),
(1089, 5, 6, 48, NULL),
(1090, 5, 6, 49, NULL),
(1095, 5, 6, 50, NULL),
(1083, 5, 6, 51, NULL),
(1075, 5, 6, 52, NULL),
(1076, 5, 6, 53, NULL),
(1079, 5, 6, 54, NULL),
(1081, 5, 6, 55, NULL),
(1092, 5, 6, 65, NULL),
(1094, 5, 6, 66, NULL),
(1084, 5, 6, 75, NULL),
(1091, 5, 6, 79, NULL),
(1085, 5, 6, 101, NULL),
(1086, 5, 6, 102, NULL),
(1088, 5, 6, 105, NULL),
(1077, 5, 6, 106, NULL),
(1073, 5, 6, 111, NULL),
(1096, 5, 6, 114, NULL),
(1080, 5, 6, 139, NULL),
(1087, 5, 6, 145, NULL),
(1097, 5, 6, 152, NULL),
(1098, 5, 6, 155, NULL),
(1901, 5, 6, 159, NULL),
(1911, 5, 6, 163, NULL),
(1922, 5, 6, 164, NULL),
(2912, 5, 6, 180, NULL),
(4011, 5, 6, 181, NULL),
(4770, 5, 6, 204, NULL),
(1252, 5, 7, 2, NULL),
(1254, 5, 7, 3, NULL),
(1253, 5, 7, 4, NULL),
(1255, 5, 7, 9, NULL),
(1243, 5, 7, 37, NULL),
(1247, 5, 7, 39, NULL),
(1250, 5, 7, 45, NULL),
(1251, 5, 7, 46, NULL),
(1257, 5, 7, 47, NULL),
(1272, 5, 7, 48, NULL),
(1273, 5, 7, 49, NULL),
(1278, 5, 7, 50, NULL),
(1266, 5, 7, 51, NULL),
(1258, 5, 7, 52, NULL),
(1259, 5, 7, 53, NULL),
(1262, 5, 7, 54, NULL),
(1264, 5, 7, 55, NULL),
(1275, 5, 7, 65, NULL),
(1277, 5, 7, 66, NULL),
(1267, 5, 7, 75, NULL),
(1274, 5, 7, 79, NULL),
(1268, 5, 7, 101, NULL),
(1269, 5, 7, 102, NULL),
(1271, 5, 7, 105, NULL),
(1260, 5, 7, 106, NULL),
(1256, 5, 7, 111, NULL),
(1279, 5, 7, 114, NULL),
(1263, 5, 7, 139, NULL),
(1270, 5, 7, 145, NULL),
(1280, 5, 7, 152, NULL),
(1281, 5, 7, 155, NULL),
(1900, 5, 7, 159, NULL),
(1910, 5, 7, 163, NULL),
(1923, 5, 7, 164, NULL),
(2911, 5, 7, 180, NULL),
(4010, 5, 7, 181, NULL),
(4769, 5, 7, 204, NULL),
(1435, 5, 8, 2, NULL),
(1437, 5, 8, 3, NULL),
(1436, 5, 8, 4, NULL),
(1438, 5, 8, 9, NULL),
(1426, 5, 8, 37, NULL),
(1430, 5, 8, 39, NULL),
(1433, 5, 8, 45, NULL),
(1434, 5, 8, 46, NULL),
(1440, 5, 8, 47, NULL),
(1455, 5, 8, 48, NULL),
(1456, 5, 8, 49, NULL),
(1461, 5, 8, 50, NULL),
(1449, 5, 8, 51, NULL),
(1441, 5, 8, 52, NULL),
(1442, 5, 8, 53, NULL),
(1445, 5, 8, 54, NULL),
(1447, 5, 8, 55, NULL),
(1458, 5, 8, 65, NULL),
(1460, 5, 8, 66, NULL),
(1450, 5, 8, 75, NULL),
(1457, 5, 8, 79, NULL),
(1451, 5, 8, 101, NULL),
(1452, 5, 8, 102, NULL),
(1454, 5, 8, 105, NULL),
(1443, 5, 8, 106, NULL),
(1439, 5, 8, 111, NULL),
(1462, 5, 8, 114, NULL),
(1446, 5, 8, 139, NULL),
(1453, 5, 8, 145, NULL),
(1463, 5, 8, 152, NULL),
(1464, 5, 8, 155, NULL),
(1899, 5, 8, 159, NULL),
(1909, 5, 8, 163, NULL),
(1924, 5, 8, 164, NULL),
(2910, 5, 8, 180, NULL),
(4009, 5, 8, 181, NULL),
(4768, 5, 8, 204, NULL),
(1618, 5, 9, 2, NULL),
(1620, 5, 9, 3, NULL),
(1619, 5, 9, 4, NULL),
(1621, 5, 9, 9, NULL),
(1609, 5, 9, 37, NULL),
(1613, 5, 9, 39, NULL),
(1616, 5, 9, 45, NULL),
(1617, 5, 9, 46, NULL),
(1623, 5, 9, 47, NULL),
(1638, 5, 9, 48, NULL),
(1639, 5, 9, 49, NULL),
(1644, 5, 9, 50, NULL),
(1632, 5, 9, 51, NULL),
(1624, 5, 9, 52, NULL),
(1625, 5, 9, 53, NULL),
(1628, 5, 9, 54, NULL),
(1630, 5, 9, 55, NULL),
(1641, 5, 9, 65, NULL),
(1643, 5, 9, 66, NULL),
(1633, 5, 9, 75, NULL),
(1640, 5, 9, 79, NULL),
(1634, 5, 9, 101, NULL),
(1635, 5, 9, 102, NULL),
(1637, 5, 9, 105, NULL),
(1626, 5, 9, 106, NULL),
(1622, 5, 9, 111, NULL),
(1645, 5, 9, 114, NULL),
(1629, 5, 9, 139, NULL),
(1636, 5, 9, 145, NULL),
(1646, 5, 9, 152, NULL),
(1647, 5, 9, 155, NULL),
(1898, 5, 9, 159, NULL),
(1908, 5, 9, 163, NULL),
(1925, 5, 9, 164, NULL),
(2909, 5, 9, 180, NULL),
(4008, 5, 9, 181, NULL),
(4767, 5, 9, 204, NULL),
(1801, 5, 10, 2, NULL),
(1803, 5, 10, 3, NULL),
(1802, 5, 10, 4, NULL),
(1804, 5, 10, 9, NULL),
(1792, 5, 10, 37, NULL),
(1796, 5, 10, 39, NULL),
(1799, 5, 10, 45, NULL),
(1800, 5, 10, 46, NULL),
(1806, 5, 10, 47, NULL),
(1821, 5, 10, 48, NULL),
(1822, 5, 10, 49, NULL),
(1827, 5, 10, 50, NULL),
(1815, 5, 10, 51, NULL),
(1807, 5, 10, 52, NULL),
(1808, 5, 10, 53, NULL),
(1811, 5, 10, 54, NULL),
(1813, 5, 10, 55, NULL),
(1824, 5, 10, 65, NULL),
(1826, 5, 10, 66, NULL),
(1816, 5, 10, 75, NULL),
(1823, 5, 10, 79, NULL),
(1817, 5, 10, 101, NULL),
(1818, 5, 10, 102, NULL),
(1820, 5, 10, 105, NULL),
(1809, 5, 10, 106, NULL),
(1805, 5, 10, 111, NULL),
(1828, 5, 10, 114, NULL),
(1812, 5, 10, 139, NULL),
(1819, 5, 10, 145, NULL),
(1829, 5, 10, 152, NULL),
(1830, 5, 10, 155, NULL),
(1897, 5, 10, 159, NULL),
(1907, 5, 10, 163, NULL),
(1926, 5, 10, 164, NULL),
(2908, 5, 10, 180, NULL),
(4007, 5, 10, 181, NULL),
(4766, 5, 10, 204, NULL),
(2851, 6, 1, 2, NULL),
(2837, 6, 1, 3, NULL),
(2838, 6, 1, 4, NULL),
(2836, 6, 1, 37, NULL),
(2839, 6, 1, 47, NULL),
(2848, 6, 1, 48, NULL),
(2840, 6, 1, 52, NULL),
(2841, 6, 1, 53, NULL),
(2855, 6, 1, 54, NULL),
(2849, 6, 1, 61, NULL),
(2844, 6, 1, 139, NULL),
(2856, 6, 1, 151, NULL),
(2846, 6, 1, 159, NULL),
(2843, 6, 1, 161, NULL),
(2845, 6, 1, 162, NULL),
(2847, 6, 1, 172, NULL),
(2852, 6, 7, 3, NULL),
(2853, 6, 7, 52, NULL),
(2854, 6, 7, 61, NULL),
(3838, 8, 1, 2, NULL),
(3836, 8, 1, 3, NULL),
(3837, 8, 1, 4, NULL),
(3835, 8, 1, 9, NULL),
(3843, 8, 1, 37, NULL),
(3842, 8, 1, 39, NULL),
(3840, 8, 1, 41, NULL),
(3841, 8, 1, 45, NULL),
(3839, 8, 1, 46, NULL),
(3833, 8, 1, 47, NULL),
(3819, 8, 1, 48, NULL),
(3815, 8, 1, 49, NULL),
(3811, 8, 1, 50, NULL),
(3825, 8, 1, 51, NULL),
(3832, 8, 1, 52, NULL),
(3831, 8, 1, 53, NULL),
(3829, 8, 1, 54, NULL),
(3826, 8, 1, 55, NULL),
(3818, 8, 1, 61, NULL),
(3816, 8, 1, 64, NULL),
(3813, 8, 1, 65, NULL),
(3812, 8, 1, 66, NULL),
(3807, 8, 1, 68, NULL),
(3808, 8, 1, 69, NULL),
(3824, 8, 1, 75, NULL),
(3814, 8, 1, 79, NULL),
(3806, 8, 1, 87, NULL),
(3823, 8, 1, 101, NULL),
(3822, 8, 1, 102, NULL),
(3817, 8, 1, 104, NULL),
(3820, 8, 1, 105, NULL),
(3830, 8, 1, 106, NULL),
(3834, 8, 1, 111, NULL),
(3810, 8, 1, 114, NULL),
(3805, 8, 1, 116, NULL),
(3804, 8, 1, 117, NULL),
(3800, 8, 1, 118, NULL),
(3801, 8, 1, 119, NULL),
(3802, 8, 1, 120, NULL),
(3798, 8, 1, 122, NULL),
(3803, 8, 1, 123, NULL),
(3799, 8, 1, 124, NULL),
(3345, 8, 1, 126, NULL),
(3809, 8, 1, 132, NULL),
(3827, 8, 1, 139, NULL),
(3821, 8, 1, 145, NULL),
(3828, 8, 1, 151, NULL),
(3797, 8, 1, 152, NULL),
(3796, 8, 1, 155, NULL),
(3364, 8, 1, 156, NULL),
(3305, 8, 1, 158, NULL),
(3335, 8, 1, 159, NULL),
(3956, 8, 1, 161, NULL),
(3325, 8, 1, 163, NULL),
(3315, 8, 1, 164, NULL),
(3287, 8, 1, 176, NULL),
(3296, 8, 1, 177, NULL),
(3278, 8, 1, 179, NULL),
(3269, 8, 1, 180, NULL),
(4400, 8, 1, 186, NULL),
(4410, 8, 1, 187, NULL),
(4430, 8, 1, 188, NULL),
(4440, 8, 1, 189, NULL),
(4450, 8, 1, 190, NULL),
(4460, 8, 1, 191, NULL),
(4420, 8, 1, 192, NULL),
(4470, 8, 1, 193, NULL),
(3790, 8, 2, 2, NULL),
(3788, 8, 2, 3, NULL),
(3789, 8, 2, 4, NULL),
(3787, 8, 2, 9, NULL),
(3795, 8, 2, 37, NULL),
(3794, 8, 2, 39, NULL),
(3792, 8, 2, 41, NULL),
(3793, 8, 2, 45, NULL),
(3791, 8, 2, 46, NULL),
(3785, 8, 2, 47, NULL),
(3771, 8, 2, 48, NULL),
(3767, 8, 2, 49, NULL),
(3763, 8, 2, 50, NULL),
(3777, 8, 2, 51, NULL),
(3784, 8, 2, 52, NULL),
(3783, 8, 2, 53, NULL),
(3781, 8, 2, 54, NULL),
(3778, 8, 2, 55, NULL),
(3770, 8, 2, 61, NULL),
(3768, 8, 2, 64, NULL),
(3765, 8, 2, 65, NULL),
(3764, 8, 2, 66, NULL),
(3759, 8, 2, 68, NULL),
(3760, 8, 2, 69, NULL),
(3776, 8, 2, 75, NULL),
(3766, 8, 2, 79, NULL),
(3758, 8, 2, 87, NULL),
(3775, 8, 2, 101, NULL),
(3774, 8, 2, 102, NULL),
(3769, 8, 2, 104, NULL),
(3772, 8, 2, 105, NULL),
(3782, 8, 2, 106, NULL),
(3786, 8, 2, 111, NULL),
(3762, 8, 2, 114, NULL),
(3757, 8, 2, 116, NULL),
(3756, 8, 2, 117, NULL),
(3752, 8, 2, 118, NULL),
(3753, 8, 2, 119, NULL),
(3754, 8, 2, 120, NULL),
(3750, 8, 2, 122, NULL),
(3755, 8, 2, 123, NULL),
(3751, 8, 2, 124, NULL),
(3346, 8, 2, 126, NULL),
(3761, 8, 2, 132, NULL),
(3779, 8, 2, 139, NULL),
(3773, 8, 2, 145, NULL),
(3780, 8, 2, 151, NULL),
(3749, 8, 2, 152, NULL),
(3748, 8, 2, 155, NULL),
(3363, 8, 2, 156, NULL),
(3306, 8, 2, 158, NULL),
(3336, 8, 2, 159, NULL),
(3955, 8, 2, 161, NULL),
(3326, 8, 2, 163, NULL),
(3316, 8, 2, 164, NULL),
(3288, 8, 2, 176, NULL),
(3297, 8, 2, 177, NULL),
(3279, 8, 2, 179, NULL),
(3268, 8, 2, 180, NULL),
(4399, 8, 2, 186, NULL),
(4409, 8, 2, 187, NULL),
(4429, 8, 2, 188, NULL),
(4439, 8, 2, 189, NULL),
(4449, 8, 2, 190, NULL),
(4459, 8, 2, 191, NULL),
(4419, 8, 2, 192, NULL),
(4469, 8, 2, 193, NULL),
(3742, 8, 3, 2, NULL),
(3740, 8, 3, 3, NULL),
(3741, 8, 3, 4, NULL),
(3739, 8, 3, 9, NULL),
(3747, 8, 3, 37, NULL),
(3746, 8, 3, 39, NULL),
(3744, 8, 3, 41, NULL),
(3745, 8, 3, 45, NULL),
(3743, 8, 3, 46, NULL),
(3737, 8, 3, 47, NULL),
(3723, 8, 3, 48, NULL),
(3719, 8, 3, 49, NULL),
(3715, 8, 3, 50, NULL),
(3729, 8, 3, 51, NULL),
(3736, 8, 3, 52, NULL),
(3735, 8, 3, 53, NULL),
(3733, 8, 3, 54, NULL),
(3730, 8, 3, 55, NULL),
(3722, 8, 3, 61, NULL),
(3720, 8, 3, 64, NULL),
(3717, 8, 3, 65, NULL),
(3716, 8, 3, 66, NULL),
(3711, 8, 3, 68, NULL),
(3712, 8, 3, 69, NULL),
(3728, 8, 3, 75, NULL),
(3718, 8, 3, 79, NULL),
(3710, 8, 3, 87, NULL),
(3727, 8, 3, 101, NULL),
(3726, 8, 3, 102, NULL),
(3721, 8, 3, 104, NULL),
(3724, 8, 3, 105, NULL),
(3734, 8, 3, 106, NULL),
(3738, 8, 3, 111, NULL),
(3714, 8, 3, 114, NULL),
(3709, 8, 3, 116, NULL),
(3708, 8, 3, 117, NULL),
(3704, 8, 3, 118, NULL),
(3705, 8, 3, 119, NULL),
(3706, 8, 3, 120, NULL),
(3702, 8, 3, 122, NULL),
(3707, 8, 3, 123, NULL),
(3703, 8, 3, 124, NULL),
(3347, 8, 3, 126, NULL),
(3713, 8, 3, 132, NULL),
(3731, 8, 3, 139, NULL),
(3725, 8, 3, 145, NULL),
(3732, 8, 3, 151, NULL),
(3701, 8, 3, 152, NULL),
(3700, 8, 3, 155, NULL),
(3362, 8, 3, 156, NULL),
(3307, 8, 3, 158, NULL),
(3337, 8, 3, 159, NULL),
(3954, 8, 3, 161, NULL),
(3327, 8, 3, 163, NULL),
(3317, 8, 3, 164, NULL),
(3289, 8, 3, 176, NULL),
(3298, 8, 3, 177, NULL),
(3280, 8, 3, 179, NULL),
(4398, 8, 3, 186, NULL),
(4408, 8, 3, 187, NULL),
(4428, 8, 3, 188, NULL),
(4438, 8, 3, 189, NULL),
(4448, 8, 3, 190, NULL),
(4458, 8, 3, 191, NULL),
(4418, 8, 3, 192, NULL),
(4468, 8, 3, 193, NULL),
(3694, 8, 4, 2, NULL),
(3692, 8, 4, 3, NULL),
(3693, 8, 4, 4, NULL),
(3691, 8, 4, 9, NULL),
(3699, 8, 4, 37, NULL),
(3698, 8, 4, 39, NULL),
(3696, 8, 4, 41, NULL),
(3697, 8, 4, 45, NULL),
(3695, 8, 4, 46, NULL),
(3689, 8, 4, 47, NULL),
(3676, 8, 4, 48, NULL),
(3672, 8, 4, 49, NULL),
(3668, 8, 4, 50, NULL),
(3682, 8, 4, 51, NULL),
(3844, 8, 4, 52, NULL),
(3688, 8, 4, 53, NULL),
(3686, 8, 4, 54, NULL),
(3683, 8, 4, 55, NULL),
(3675, 8, 4, 61, NULL),
(3673, 8, 4, 64, NULL),
(3670, 8, 4, 65, NULL),
(3669, 8, 4, 66, NULL),
(3664, 8, 4, 68, NULL),
(3665, 8, 4, 69, NULL),
(3681, 8, 4, 75, NULL),
(3671, 8, 4, 79, NULL),
(3663, 8, 4, 87, NULL),
(3680, 8, 4, 101, NULL),
(3679, 8, 4, 102, NULL),
(3674, 8, 4, 104, NULL),
(3677, 8, 4, 105, NULL),
(3687, 8, 4, 106, NULL),
(3690, 8, 4, 111, NULL),
(3667, 8, 4, 114, NULL),
(3662, 8, 4, 116, NULL),
(3661, 8, 4, 117, NULL),
(3657, 8, 4, 118, NULL),
(3658, 8, 4, 119, NULL),
(3659, 8, 4, 120, NULL),
(3655, 8, 4, 122, NULL),
(3660, 8, 4, 123, NULL),
(3656, 8, 4, 124, NULL),
(3348, 8, 4, 126, NULL),
(3666, 8, 4, 132, NULL),
(3684, 8, 4, 139, NULL),
(3678, 8, 4, 145, NULL),
(3685, 8, 4, 151, NULL),
(3654, 8, 4, 152, NULL),
(3653, 8, 4, 155, NULL),
(3361, 8, 4, 156, NULL),
(3308, 8, 4, 158, NULL),
(3338, 8, 4, 159, NULL),
(3953, 8, 4, 161, NULL),
(3328, 8, 4, 163, NULL),
(3318, 8, 4, 164, NULL),
(3290, 8, 4, 176, NULL),
(3299, 8, 4, 177, NULL),
(3277, 8, 4, 179, NULL),
(3270, 8, 4, 180, NULL),
(4397, 8, 4, 186, NULL),
(4407, 8, 4, 187, NULL),
(4427, 8, 4, 188, NULL),
(4437, 8, 4, 189, NULL),
(4447, 8, 4, 190, NULL),
(4457, 8, 4, 191, NULL),
(4417, 8, 4, 192, NULL),
(4467, 8, 4, 193, NULL),
(3647, 8, 5, 2, NULL),
(3645, 8, 5, 3, NULL),
(3646, 8, 5, 4, NULL),
(3644, 8, 5, 9, NULL),
(3652, 8, 5, 37, NULL),
(3651, 8, 5, 39, NULL),
(3649, 8, 5, 41, NULL),
(3650, 8, 5, 45, NULL),
(3648, 8, 5, 46, NULL),
(3642, 8, 5, 47, NULL),
(3628, 8, 5, 48, NULL),
(3624, 8, 5, 49, NULL),
(3620, 8, 5, 50, NULL),
(3634, 8, 5, 51, NULL),
(3641, 8, 5, 52, NULL),
(3640, 8, 5, 53, NULL),
(3638, 8, 5, 54, NULL),
(3635, 8, 5, 55, NULL),
(3627, 8, 5, 61, NULL),
(3625, 8, 5, 64, NULL),
(3622, 8, 5, 65, NULL),
(3621, 8, 5, 66, NULL),
(3616, 8, 5, 68, NULL),
(3617, 8, 5, 69, NULL),
(3633, 8, 5, 75, NULL),
(3623, 8, 5, 79, NULL),
(3615, 8, 5, 87, NULL),
(3632, 8, 5, 101, NULL),
(3631, 8, 5, 102, NULL),
(3626, 8, 5, 104, NULL),
(3629, 8, 5, 105, NULL),
(3639, 8, 5, 106, NULL),
(3643, 8, 5, 111, NULL),
(3619, 8, 5, 114, NULL),
(3614, 8, 5, 116, NULL),
(3613, 8, 5, 117, NULL),
(3609, 8, 5, 118, NULL),
(3610, 8, 5, 119, NULL),
(3611, 8, 5, 120, NULL),
(3607, 8, 5, 122, NULL),
(3612, 8, 5, 123, NULL),
(3608, 8, 5, 124, NULL),
(3349, 8, 5, 126, NULL),
(3618, 8, 5, 132, NULL),
(3636, 8, 5, 139, NULL),
(3630, 8, 5, 145, NULL),
(3637, 8, 5, 151, NULL),
(3606, 8, 5, 152, NULL),
(3605, 8, 5, 155, NULL),
(3360, 8, 5, 156, NULL),
(3309, 8, 5, 158, NULL),
(3339, 8, 5, 159, NULL),
(3952, 8, 5, 161, NULL),
(3329, 8, 5, 163, NULL),
(3319, 8, 5, 164, NULL),
(3291, 8, 5, 176, NULL),
(3300, 8, 5, 177, NULL),
(3281, 8, 5, 179, NULL),
(3271, 8, 5, 180, NULL),
(4396, 8, 5, 186, NULL),
(4406, 8, 5, 187, NULL),
(4426, 8, 5, 188, NULL),
(4436, 8, 5, 189, NULL),
(4446, 8, 5, 190, NULL),
(4456, 8, 5, 191, NULL),
(4416, 8, 5, 192, NULL),
(4466, 8, 5, 193, NULL),
(3599, 8, 6, 2, NULL),
(3597, 8, 6, 3, NULL),
(3598, 8, 6, 4, NULL),
(3596, 8, 6, 9, NULL),
(3604, 8, 6, 37, NULL),
(3603, 8, 6, 39, NULL),
(3601, 8, 6, 41, NULL),
(3602, 8, 6, 45, NULL),
(3600, 8, 6, 46, NULL),
(3594, 8, 6, 47, NULL),
(3580, 8, 6, 48, NULL),
(3576, 8, 6, 49, NULL),
(3572, 8, 6, 50, NULL),
(3586, 8, 6, 51, NULL),
(3593, 8, 6, 52, NULL),
(3592, 8, 6, 53, NULL),
(3590, 8, 6, 54, NULL),
(3587, 8, 6, 55, NULL),
(3579, 8, 6, 61, NULL),
(3577, 8, 6, 64, NULL),
(3574, 8, 6, 65, NULL),
(3573, 8, 6, 66, NULL),
(3568, 8, 6, 68, NULL),
(3569, 8, 6, 69, NULL),
(3585, 8, 6, 75, NULL),
(3575, 8, 6, 79, NULL),
(3567, 8, 6, 87, NULL),
(3584, 8, 6, 101, NULL),
(3583, 8, 6, 102, NULL),
(3578, 8, 6, 104, NULL),
(3581, 8, 6, 105, NULL),
(3591, 8, 6, 106, NULL),
(3595, 8, 6, 111, NULL),
(3571, 8, 6, 114, NULL),
(3566, 8, 6, 116, NULL),
(3565, 8, 6, 117, NULL),
(3561, 8, 6, 118, NULL),
(3562, 8, 6, 119, NULL),
(3563, 8, 6, 120, NULL),
(3559, 8, 6, 122, NULL),
(3564, 8, 6, 123, NULL),
(3560, 8, 6, 124, NULL),
(3350, 8, 6, 126, NULL),
(3570, 8, 6, 132, NULL),
(3588, 8, 6, 139, NULL),
(3582, 8, 6, 145, NULL),
(3589, 8, 6, 151, NULL),
(3558, 8, 6, 152, NULL),
(3557, 8, 6, 155, NULL),
(3359, 8, 6, 156, NULL),
(3310, 8, 6, 158, NULL),
(3340, 8, 6, 159, NULL),
(3951, 8, 6, 161, NULL),
(3330, 8, 6, 163, NULL),
(3320, 8, 6, 164, NULL),
(3292, 8, 6, 176, NULL),
(3301, 8, 6, 177, NULL),
(3282, 8, 6, 179, NULL),
(3272, 8, 6, 180, NULL),
(4395, 8, 6, 186, NULL),
(4405, 8, 6, 187, NULL),
(4425, 8, 6, 188, NULL),
(4435, 8, 6, 189, NULL),
(4445, 8, 6, 190, NULL),
(4455, 8, 6, 191, NULL),
(4415, 8, 6, 192, NULL),
(4465, 8, 6, 193, NULL),
(3551, 8, 7, 2, NULL),
(3549, 8, 7, 3, NULL),
(3550, 8, 7, 4, NULL),
(3548, 8, 7, 9, NULL),
(3556, 8, 7, 37, NULL),
(3555, 8, 7, 39, NULL),
(3553, 8, 7, 41, NULL),
(3554, 8, 7, 45, NULL),
(3552, 8, 7, 46, NULL),
(3546, 8, 7, 47, NULL),
(3532, 8, 7, 48, NULL),
(3528, 8, 7, 49, NULL),
(3524, 8, 7, 50, NULL),
(3538, 8, 7, 51, NULL),
(3545, 8, 7, 52, NULL),
(3544, 8, 7, 53, NULL),
(3542, 8, 7, 54, NULL),
(3539, 8, 7, 55, NULL),
(3531, 8, 7, 61, NULL),
(3529, 8, 7, 64, NULL),
(3526, 8, 7, 65, NULL),
(3525, 8, 7, 66, NULL),
(3520, 8, 7, 68, NULL),
(3521, 8, 7, 69, NULL),
(3537, 8, 7, 75, NULL),
(3527, 8, 7, 79, NULL),
(3519, 8, 7, 87, NULL),
(3536, 8, 7, 101, NULL),
(3535, 8, 7, 102, NULL),
(3530, 8, 7, 104, NULL),
(3533, 8, 7, 105, NULL),
(3543, 8, 7, 106, NULL),
(3547, 8, 7, 111, NULL),
(3523, 8, 7, 114, NULL),
(3518, 8, 7, 116, NULL),
(3517, 8, 7, 117, NULL),
(3513, 8, 7, 118, NULL),
(3514, 8, 7, 119, NULL),
(3515, 8, 7, 120, NULL),
(3511, 8, 7, 122, NULL),
(3516, 8, 7, 123, NULL),
(3512, 8, 7, 124, NULL),
(3351, 8, 7, 126, NULL),
(3522, 8, 7, 132, NULL),
(3540, 8, 7, 139, NULL),
(3534, 8, 7, 145, NULL),
(3541, 8, 7, 151, NULL),
(3510, 8, 7, 152, NULL),
(3509, 8, 7, 155, NULL),
(3358, 8, 7, 156, NULL),
(3311, 8, 7, 158, NULL),
(3341, 8, 7, 159, NULL),
(3950, 8, 7, 161, NULL),
(3331, 8, 7, 163, NULL),
(3321, 8, 7, 164, NULL),
(3293, 8, 7, 176, NULL),
(3302, 8, 7, 177, NULL),
(3283, 8, 7, 179, NULL),
(3273, 8, 7, 180, NULL),
(4394, 8, 7, 186, NULL),
(4404, 8, 7, 187, NULL),
(4424, 8, 7, 188, NULL),
(4434, 8, 7, 189, NULL),
(4444, 8, 7, 190, NULL),
(4454, 8, 7, 191, NULL),
(4414, 8, 7, 192, NULL),
(4464, 8, 7, 193, NULL),
(3503, 8, 8, 2, NULL),
(3501, 8, 8, 3, NULL),
(3502, 8, 8, 4, NULL),
(3500, 8, 8, 9, NULL),
(3508, 8, 8, 37, NULL),
(3507, 8, 8, 39, NULL),
(3505, 8, 8, 41, NULL),
(3506, 8, 8, 45, NULL),
(3504, 8, 8, 46, NULL),
(3498, 8, 8, 47, NULL),
(3484, 8, 8, 48, NULL),
(3480, 8, 8, 49, NULL),
(3476, 8, 8, 50, NULL),
(3490, 8, 8, 51, NULL),
(3497, 8, 8, 52, NULL),
(3496, 8, 8, 53, NULL),
(3494, 8, 8, 54, NULL),
(3491, 8, 8, 55, NULL),
(3483, 8, 8, 61, NULL),
(3481, 8, 8, 64, NULL),
(3478, 8, 8, 65, NULL),
(3477, 8, 8, 66, NULL),
(3472, 8, 8, 68, NULL),
(3473, 8, 8, 69, NULL),
(3489, 8, 8, 75, NULL),
(3479, 8, 8, 79, NULL),
(3471, 8, 8, 87, NULL),
(3488, 8, 8, 101, NULL),
(3487, 8, 8, 102, NULL),
(3482, 8, 8, 104, NULL),
(3485, 8, 8, 105, NULL),
(3495, 8, 8, 106, NULL),
(3499, 8, 8, 111, NULL),
(3475, 8, 8, 114, NULL),
(3470, 8, 8, 116, NULL),
(3469, 8, 8, 117, NULL),
(3465, 8, 8, 118, NULL),
(3466, 8, 8, 119, NULL),
(3467, 8, 8, 120, NULL),
(3463, 8, 8, 122, NULL),
(3468, 8, 8, 123, NULL),
(3464, 8, 8, 124, NULL),
(3352, 8, 8, 126, NULL),
(3474, 8, 8, 132, NULL),
(3492, 8, 8, 139, NULL),
(3486, 8, 8, 145, NULL),
(3493, 8, 8, 151, NULL),
(3462, 8, 8, 152, NULL),
(3461, 8, 8, 155, NULL),
(3357, 8, 8, 156, NULL),
(3312, 8, 8, 158, NULL),
(3342, 8, 8, 159, NULL),
(3949, 8, 8, 161, NULL),
(3332, 8, 8, 163, NULL),
(3322, 8, 8, 164, NULL),
(3294, 8, 8, 176, NULL),
(3303, 8, 8, 177, NULL),
(3284, 8, 8, 179, NULL),
(3274, 8, 8, 180, NULL),
(4393, 8, 8, 186, NULL),
(4403, 8, 8, 187, NULL),
(4423, 8, 8, 188, NULL),
(4433, 8, 8, 189, NULL),
(4443, 8, 8, 190, NULL),
(4453, 8, 8, 191, NULL),
(4413, 8, 8, 192, NULL),
(4463, 8, 8, 193, NULL),
(3455, 8, 9, 2, NULL),
(3453, 8, 9, 3, NULL),
(3454, 8, 9, 4, NULL),
(3452, 8, 9, 9, NULL),
(3460, 8, 9, 37, NULL),
(3459, 8, 9, 39, NULL),
(3457, 8, 9, 41, NULL),
(3458, 8, 9, 45, NULL),
(3456, 8, 9, 46, NULL),
(3450, 8, 9, 47, NULL),
(3436, 8, 9, 48, NULL),
(3432, 8, 9, 49, NULL),
(3428, 8, 9, 50, NULL),
(3442, 8, 9, 51, NULL),
(3449, 8, 9, 52, NULL),
(3448, 8, 9, 53, NULL),
(3446, 8, 9, 54, NULL),
(3443, 8, 9, 55, NULL),
(3435, 8, 9, 61, NULL),
(3433, 8, 9, 64, NULL),
(3430, 8, 9, 65, NULL),
(3429, 8, 9, 66, NULL),
(3424, 8, 9, 68, NULL),
(3425, 8, 9, 69, NULL),
(3441, 8, 9, 75, NULL),
(3431, 8, 9, 79, NULL),
(3423, 8, 9, 87, NULL),
(3440, 8, 9, 101, NULL),
(3439, 8, 9, 102, NULL),
(3434, 8, 9, 104, NULL),
(3437, 8, 9, 105, NULL),
(3447, 8, 9, 106, NULL),
(3451, 8, 9, 111, NULL),
(3427, 8, 9, 114, NULL),
(3422, 8, 9, 116, NULL),
(3421, 8, 9, 117, NULL),
(3417, 8, 9, 118, NULL),
(3418, 8, 9, 119, NULL),
(3419, 8, 9, 120, NULL),
(3415, 8, 9, 122, NULL),
(3420, 8, 9, 123, NULL),
(3416, 8, 9, 124, NULL),
(3353, 8, 9, 126, NULL),
(3426, 8, 9, 132, NULL),
(3444, 8, 9, 139, NULL),
(3438, 8, 9, 145, NULL),
(3445, 8, 9, 151, NULL),
(3414, 8, 9, 152, NULL),
(3413, 8, 9, 155, NULL),
(3356, 8, 9, 156, NULL),
(3313, 8, 9, 158, NULL),
(3343, 8, 9, 159, NULL),
(3948, 8, 9, 161, NULL),
(3333, 8, 9, 163, NULL),
(3323, 8, 9, 164, NULL),
(3295, 8, 9, 176, NULL),
(3304, 8, 9, 177, NULL),
(3285, 8, 9, 179, NULL),
(3275, 8, 9, 180, NULL),
(4392, 8, 9, 186, NULL),
(4402, 8, 9, 187, NULL),
(4422, 8, 9, 188, NULL),
(4432, 8, 9, 189, NULL),
(4442, 8, 9, 190, NULL),
(4452, 8, 9, 191, NULL),
(4412, 8, 9, 192, NULL),
(4462, 8, 9, 193, NULL),
(3407, 8, 10, 2, NULL),
(3405, 8, 10, 3, NULL),
(3406, 8, 10, 4, NULL),
(3404, 8, 10, 9, NULL),
(3412, 8, 10, 37, NULL),
(3411, 8, 10, 39, NULL),
(3409, 8, 10, 41, NULL),
(3410, 8, 10, 45, NULL),
(3408, 8, 10, 46, NULL),
(3402, 8, 10, 47, NULL),
(3388, 8, 10, 48, NULL),
(3384, 8, 10, 49, NULL),
(3380, 8, 10, 50, NULL),
(3394, 8, 10, 51, NULL),
(3401, 8, 10, 52, NULL),
(3400, 8, 10, 53, NULL),
(3398, 8, 10, 54, NULL),
(3395, 8, 10, 55, NULL),
(3387, 8, 10, 61, NULL),
(3385, 8, 10, 64, NULL),
(3382, 8, 10, 65, NULL),
(3381, 8, 10, 66, NULL),
(3376, 8, 10, 68, NULL),
(3377, 8, 10, 69, NULL),
(3393, 8, 10, 75, NULL),
(3383, 8, 10, 79, NULL),
(3375, 8, 10, 87, NULL),
(3392, 8, 10, 101, NULL),
(3391, 8, 10, 102, NULL),
(3386, 8, 10, 104, NULL),
(3389, 8, 10, 105, NULL),
(3399, 8, 10, 106, NULL),
(3403, 8, 10, 111, NULL),
(3379, 8, 10, 114, NULL),
(3374, 8, 10, 116, NULL),
(3373, 8, 10, 117, NULL),
(3369, 8, 10, 118, NULL),
(3370, 8, 10, 119, NULL),
(3371, 8, 10, 120, NULL),
(3367, 8, 10, 122, NULL),
(3372, 8, 10, 123, NULL),
(3368, 8, 10, 124, NULL),
(3354, 8, 10, 126, NULL),
(3378, 8, 10, 132, NULL),
(3396, 8, 10, 139, NULL),
(3390, 8, 10, 145, NULL),
(3397, 8, 10, 151, NULL),
(3366, 8, 10, 152, NULL),
(3365, 8, 10, 155, NULL),
(3355, 8, 10, 156, NULL),
(3314, 8, 10, 158, NULL),
(3344, 8, 10, 159, NULL),
(3947, 8, 10, 161, NULL),
(3334, 8, 10, 163, NULL),
(3324, 8, 10, 164, NULL),
(3286, 8, 10, 179, NULL),
(3276, 8, 10, 180, NULL),
(4391, 8, 10, 186, NULL),
(4401, 8, 10, 187, NULL),
(4421, 8, 10, 188, NULL),
(4431, 8, 10, 189, NULL),
(4441, 8, 10, 190, NULL),
(4451, 8, 10, 191, NULL),
(4411, 8, 10, 192, NULL),
(4461, 8, 10, 193, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `prolongation`
--

CREATE TABLE `prolongation` (
  `id_prolongation` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `ref` varchar(12) NOT NULL,
  `id_refinanceur` mediumint(8) UNSIGNED DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `date_arret` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `refinanceur`
--

CREATE TABLE `refinanceur` (
  `id_refinanceur` mediumint(8) UNSIGNED NOT NULL,
  `refinanceur` varchar(32) NOT NULL,
  `code` varchar(3) NOT NULL,
  `adresse` varchar(128) DEFAULT NULL,
  `adresse_2` varchar(128) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `statut` enum('sa','sarl','eurl','snc','sprl') NOT NULL DEFAULT 'sarl',
  `siren` varchar(9) DEFAULT NULL,
  `capital` varchar(64) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tel` varchar(16) DEFAULT NULL,
  `gsm` varchar(16) DEFAULT NULL,
  `fax` varchar(16) DEFAULT NULL,
  `numero_emetteur` varchar(25) DEFAULT NULL,
  `code_refi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `region`
--

CREATE TABLE `region` (
  `id_region` tinyint(3) UNSIGNED NOT NULL,
  `region` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `reglement`
--

CREATE TABLE `reglement` (
  `id_reglement` mediumint(8) UNSIGNED NOT NULL,
  `reglement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `reglement`
--

INSERT INTO `reglement` (`id_reglement`, `reglement`) VALUES
(2, 'Factures Fournisseurs: il est impératif de noter le numéro de contrat CLEODIS sur les factures (si possible en rouge) cela facilite la saisie en comptabilité. Merci à TOUS.'),
(3, '<span style=\"background-color: rgb(255, 255, 255);\">PROCESS CP AU 24.11.14<br><br><br><br></span>');

-- --------------------------------------------------------

--
-- Structure de la table `relance`
--

CREATE TABLE `relance` (
  `id_relance` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('premiere','seconde','mise_en_demeure') NOT NULL,
  `texte` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `relance_facture`
--

CREATE TABLE `relance_facture` (
  `id_relance_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL,
  `id_relance` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `restitution_anticipee`
--

CREATE TABLE `restitution_anticipee` (
  `id_restitution_anticipee` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `echeance` int(11) NOT NULL,
  `montant_ht` float(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `scanner`
--

CREATE TABLE `scanner` (
  `id_scanner` int(10) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `nbpages` int(11) DEFAULT NULL,
  `provenance` varchar(255) NOT NULL,
  `transfert` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `secteur_commercial`
--

CREATE TABLE `secteur_commercial` (
  `id_secteur_commercial` mediumint(8) UNSIGNED NOT NULL,
  `secteur_commercial` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `secteur_geographique`
--

CREATE TABLE `secteur_geographique` (
  `id_secteur_geographique` mediumint(8) UNSIGNED NOT NULL,
  `secteur_geographique` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sell_and_sign`
--

CREATE TABLE `sell_and_sign` (
  `id_sell_and_sign` mediumint(8) UNSIGNED NOT NULL,
  `sell_and_sign` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `dataCustomer_number` varchar(255) NOT NULL,
  `contractor_id` varchar(255) NOT NULL,
  `contract_id` varchar(255) NOT NULL,
  `document_id` varchar(255) NOT NULL,
  `contractorTo_id` varchar(255) NOT NULL,
  `bundle_id` varchar(255) DEFAULT NULL,
  `statut` enum('non_traite','traite','stocke','absent','abandonne') NOT NULL DEFAULT 'non_traite' COMMENT 'Different statut utilisé par le script de traitement des liasses ',
  `certificat_de_preuve` enum('present','absent') NOT NULL DEFAULT 'absent',
  `contrat_signe` enum('present','absent') NOT NULL DEFAULT 'absent',
  `etat` varchar(100) DEFAULT NULL,
  `fonction` varchar(255) DEFAULT NULL COMMENT 'Fonction du signataire'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `site_article`
--

CREATE TABLE `site_article` (
  `id_site_article` smallint(5) UNSIGNED NOT NULL,
  `titre` varchar(512) NOT NULL,
  `id_site_menu` smallint(5) UNSIGNED NOT NULL,
  `position` int(11) NOT NULL DEFAULT 1,
  `visible` enum('oui','non') NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `site_article_contenu`
--

CREATE TABLE `site_article_contenu` (
  `id_site_article_contenu` mediumint(8) UNSIGNED NOT NULL,
  `id_site_article` smallint(5) UNSIGNED NOT NULL,
  `texte` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `site_associe`
--

CREATE TABLE `site_associe` (
  `id_site_associe` mediumint(8) UNSIGNED NOT NULL,
  `site_associe` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `steps_tunnel` text NOT NULL,
  `id_client` mediumint(8) UNSIGNED NOT NULL,
  `url_front` varchar(500) NOT NULL COMMENT 'URL du front de souscription',
  `cs_score_minimal` tinyint(3) UNSIGNED NOT NULL DEFAULT 50 COMMENT 'Score CS minimal personnalisé',
  `age_minimal` tinyint(3) UNSIGNED NOT NULL DEFAULT 2 COMMENT 'Nombre d''année d''existance minimal',
  `export_middleware` enum('oui','non') NOT NULL DEFAULT 'non',
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `color_dominant` varchar(6) NOT NULL DEFAULT '000000',
  `color_footer` varchar(6) NOT NULL DEFAULT '000000',
  `color_links` varchar(6) NOT NULL DEFAULT '000000',
  `color_titles` varchar(6) NOT NULL DEFAULT '000000',
  `id_societe_footer_mail` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Liaison avec la société, pour la récupération des informations à mettre en footer des mails',
  `can_update_bic_iban` tinyint(4) DEFAULT 1,
  `id_type_affaire` int(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `site_en_tete`
--

CREATE TABLE `site_en_tete` (
  `id_site_en_tete` tinyint(3) UNSIGNED NOT NULL,
  `site_en_tete` varchar(256) NOT NULL,
  `sous_site_en_tete` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `site_menu`
--

CREATE TABLE `site_menu` (
  `id_site_menu` smallint(5) UNSIGNED NOT NULL,
  `titre_menu` varchar(50) NOT NULL,
  `visible` enum('oui','non') NOT NULL DEFAULT 'oui',
  `url` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `site_offre`
--

CREATE TABLE `site_offre` (
  `id_site_offre` mediumint(8) UNSIGNED NOT NULL,
  `site_offre` varchar(50) NOT NULL,
  `texte_offre` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `slimpay_transaction`
--

CREATE TABLE `slimpay_transaction` (
  `id_slimpay_transaction` mediumint(8) UNSIGNED NOT NULL,
  `ref_slimpay` varchar(38) NOT NULL,
  `date_execution` datetime NOT NULL,
  `executionStatus` enum('processing','rejected','processed','notprocessed','transformed','contested','toreplay','togenerate','toprocess') DEFAULT NULL,
  `retour` text NOT NULL,
  `id_facture` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `societe`
--

CREATE TABLE `societe` (
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `joignable` enum('tous','commerce','personne') NOT NULL DEFAULT 'tous',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `code_groupe` varchar(32) DEFAULT NULL,
  `ref` varchar(11) DEFAULT NULL,
  `id_owner` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_assistante` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_pays` char(2) NOT NULL DEFAULT 'BE',
  `langue` enum('FR','NL') NOT NULL DEFAULT 'FR',
  `id_famille` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `siren` varchar(9) DEFAULT NULL,
  `CIF` varchar(50) DEFAULT NULL,
  `siret` varchar(32) DEFAULT NULL,
  `avis_credit` enum('NC','LJ','RJ','x','NR','R','@','@@','@@@','@@@@','ER','PC','PS','CA') NOT NULL DEFAULT 'NC' COMMENT 'Note de Cleodis donnée à la société',
  `cs_avis_credit` decimal(11,2) DEFAULT NULL,
  `score` enum('NC','0','1','2','3','4','5','6','7','8','9','10','RJ','LJ','ER','PC','PS','CA') NOT NULL DEFAULT 'NC' COMMENT 'Score arbitraire de Cléodis',
  `cs_score` tinyint(3) UNSIGNED DEFAULT NULL,
  `lastScoreDate` varchar(25) DEFAULT NULL,
  `contentieux` enum('Avocat','Huissier','Rejet','Recouvrement') DEFAULT NULL COMMENT 'H = Huissier et A = Avocat.',
  `naf` varchar(5) DEFAULT NULL,
  `societe` varchar(128) NOT NULL,
  `nom_commercial` varchar(128) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `adresse_2` varchar(255) DEFAULT NULL,
  `adresse_3` varchar(255) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `id_contact_facturation` mediumint(8) UNSIGNED DEFAULT NULL,
  `facturation_id_pays` varchar(2) DEFAULT 'BE',
  `facturation_adresse` varchar(255) DEFAULT NULL,
  `facturation_adresse_2` varchar(255) DEFAULT NULL,
  `facturation_adresse_3` varchar(255) DEFAULT NULL,
  `facturation_cp` varchar(5) DEFAULT NULL,
  `facturation_ville` varchar(100) DEFAULT NULL,
  `livraison_id_pays` varchar(2) DEFAULT 'FR',
  `livraison_adresse` varchar(255) DEFAULT NULL,
  `livraison_adresse_2` varchar(255) DEFAULT NULL,
  `livraison_adresse_3` varchar(255) DEFAULT NULL,
  `livraison_cp` varchar(5) DEFAULT NULL,
  `livraison_ville` varchar(100) DEFAULT NULL,
  `reference_tva` varchar(24) DEFAULT NULL,
  `ville_rcs` varchar(100) DEFAULT NULL,
  `tva` decimal(4,3) NOT NULL DEFAULT 1.000,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `web` varchar(128) DEFAULT NULL,
  `activite` varchar(255) DEFAULT NULL,
  `etat` enum('actif','inactif','veille','supprime','non_diffusable','ferme','transfere','cesse','liquide') CHARACTER SET latin1 NOT NULL DEFAULT 'actif',
  `nb_employe` mediumint(8) DEFAULT NULL,
  `effectif` enum('1','10','50','100','500','1000') DEFAULT NULL,
  `id_secteur_geographique` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact_commercial` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_secteur_commercial` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_fournisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_campagne` smallint(6) DEFAULT NULL,
  `id_apporteur` mediumint(8) UNSIGNED DEFAULT NULL,
  `liens` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `ca` varchar(12) DEFAULT NULL,
  `id_devise` mediumint(8) UNSIGNED NOT NULL DEFAULT 1,
  `structure` varchar(128) DEFAULT NULL,
  `capital` bigint(20) NOT NULL DEFAULT 0,
  `date_creation` date DEFAULT NULL,
  `id_filiale` mediumint(8) UNSIGNED DEFAULT NULL,
  `facturer_la_filiale` tinyint(1) DEFAULT NULL,
  `facturer_le_siege` enum('oui','non') NOT NULL DEFAULT 'non',
  `adresse_siege_social` text DEFAULT NULL,
  `fournisseur` enum('oui','non') CHARACTER SET latin1 NOT NULL DEFAULT 'non',
  `partenaire` enum('oui','non') CHARACTER SET latin1 NOT NULL DEFAULT 'non',
  `revendeur` enum('oui','non') NOT NULL DEFAULT 'non',
  `code_fournisseur` varchar(32) DEFAULT NULL,
  `cle_externe` varchar(32) DEFAULT NULL,
  `code_client` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `code_client_partenaire` varchar(255) DEFAULT NULL,
  `divers_2` enum('prelevement','mandat','virement','cheque') CHARACTER SET latin1 DEFAULT 'prelevement',
  `divers_3` enum('Midas','Optic_2000','Norauto','Atol','-') CHARACTER SET latin1 DEFAULT NULL,
  `id_accompagnateur` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_prospection` mediumint(8) UNSIGNED DEFAULT NULL,
  `relation` enum('prospect','client','suspect','concurrent') NOT NULL DEFAULT 'suspect',
  `id_contact_signataire` mediumint(8) UNSIGNED DEFAULT NULL,
  `RIB` varchar(32) DEFAULT NULL,
  `IBAN` varchar(35) DEFAULT NULL,
  `BIC` varchar(32) DEFAULT NULL,
  `ics` varchar(255) DEFAULT NULL,
  `RUM` varchar(32) DEFAULT NULL,
  `recallCounter` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Compteur pour le rappel de prospection par mobile',
  `nom_banque` varchar(24) CHARACTER SET latin1 DEFAULT NULL,
  `ville_banque` varchar(100) DEFAULT NULL,
  `lastaccountdate` varchar(20) DEFAULT NULL,
  `receivables` varchar(20) DEFAULT NULL,
  `securitieandcash` varchar(20) DEFAULT NULL,
  `operatingincome` varchar(20) DEFAULT NULL,
  `netturnover` varchar(20) DEFAULT NULL,
  `operationgprofitless` varchar(20) DEFAULT NULL,
  `financialincome` varchar(20) DEFAULT NULL,
  `financialcharges` varchar(20) DEFAULT NULL,
  `verriers` enum('ESSILOR','ZEISS','BBGR','AUTRE') DEFAULT NULL,
  `typologie` enum('association','commune','divers','education','pme','prof_lib','public','sante') DEFAULT NULL,
  `lead` varchar(128) DEFAULT NULL,
  `particulier_civilite` enum('M','Mme','Mlle') DEFAULT NULL,
  `particulier_nom` varchar(50) DEFAULT NULL,
  `particulier_prenom` varchar(50) DEFAULT NULL,
  `particulier_portable` varchar(15) DEFAULT NULL,
  `num_carte_fidelite` varchar(50) DEFAULT NULL,
  `dernier_magasin` varchar(50) DEFAULT NULL,
  `optin_offre_commerciales` enum('oui','non') DEFAULT NULL,
  `optin_offre_commerciale_partenaire` enum('oui','non') DEFAULT NULL,
  `particulier_fixe` varchar(20) DEFAULT NULL,
  `particulier_fax` varchar(20) DEFAULT NULL,
  `particulier_email` varchar(255) DEFAULT NULL,
  `sms` varchar(6) DEFAULT NULL COMMENT '	Code SMS pour confirmer l''identité d''un particulier',
  `sms_validite` timestamp NULL DEFAULT NULL,
  `sms_tentative` tinyint(4) NOT NULL DEFAULT 0,
  `date_blocage` timestamp NULL DEFAULT NULL COMMENT '	Date de fin du blocage d''un compte particulier (BtoB) pour les applications utilisant le tunnel de souscription (btwin, ...)',
  `date_cs_data` date DEFAULT NULL COMMENT 'Date de mise à jour des données Crédit Safe',
  `force_acceptation` enum('oui','non','sans') NOT NULL DEFAULT 'sans' COMMENT 'Permet de forcer l''acceptation de la souscription sur l''API',
  `mauvais_payeur` enum('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Permet de savoir si le client a des contrats en contentieux',
  `contentieux_depuis` enum('1_mois','2_mois','plus_3_mois') DEFAULT NULL COMMENT 'Permet de savoir depuis combien de temps le client est mauvais payeur (Date max impayée)',
  `date_envoi_mail_creation_compte` date DEFAULT NULL COMMENT 'Date de l''envoi du mail de création de compte sur l''espace client pro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `societe_domaine`
--

CREATE TABLE `societe_domaine` (
  `id_societe_domaine` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_domaine` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `sous_categorie`
--

CREATE TABLE `sous_categorie` (
  `id_sous_categorie` mediumint(8) UNSIGNED NOT NULL,
  `id_categorie` mediumint(8) UNSIGNED NOT NULL,
  `sous_categorie` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `stat_snap`
--

CREATE TABLE `stat_snap` (
  `id_stat_snap` mediumint(8) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `nb` int(11) NOT NULL,
  `stat_concerne` varchar(250) NOT NULL,
  `id_agence` mediumint(8) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `suivi`
--

CREATE TABLE `suivi` (
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `origine` enum('societe_devis','societe_commande','societe_location') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'societe_devis' COMMENT 'endroit ou le suivi a Ã©tÃ© crÃ©Ã©',
  `type` enum('note','fichier','RDV','appel','courrier','sms','email') NOT NULL DEFAULT 'note',
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `texte` text CHARACTER SET utf8 NOT NULL,
  `public` enum('oui','non') CHARACTER SET utf8 NOT NULL DEFAULT 'non',
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `type_suivi` enum('Devis','Contrat','Refinancement','Comptabilité','Broke','Contentieux','Mis en place','Restitution','Autre','Prolongation','Resiliation','Sinistre','Transfert','Fournisseur','Requête','BDC','Flottes','Installation','Passage_comite','demande_comite','Audit en cours','Assurance','Formation','Maintenance','Livraison','Blocage') DEFAULT NULL,
  `id_formation_devis` mediumint(8) UNSIGNED DEFAULT NULL,
  `attente_reponse` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `suivi_contact`
--

CREATE TABLE `suivi_contact` (
  `id_suivi_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre contact et suivi';

-- --------------------------------------------------------

--
-- Structure de la table `suivi_notifie`
--

CREATE TABLE `suivi_notifie` (
  `id_suivi_notifie` mediumint(8) UNSIGNED NOT NULL,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `suivi_portail`
--

CREATE TABLE `suivi_portail` (
  `id_suivi_portail` mediumint(8) UNSIGNED NOT NULL,
  `suivi_portail` text NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL,
  `portail_associe` enum('Midas','Optic_2000') NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_devis` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_parc` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_bon_de_commande` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_opportunite` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `suivi_portail_contact`
--

CREATE TABLE `suivi_portail_contact` (
  `id_suivi_portail` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `suivi_societe`
--

CREATE TABLE `suivi_societe` (
  `id_suivi_societe` mediumint(8) UNSIGNED NOT NULL,
  `id_suivi` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre user et suivi';

-- --------------------------------------------------------

--
-- Structure de la table `tache`
--

CREATE TABLE `tache` (
  `id_tache` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_suivi` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `origine` enum('societe_devis','societe_commande','societe_location') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'endroit ou la tache a été créé',
  `tache` text NOT NULL,
  `horaire_debut` datetime NOT NULL,
  `horaire_fin` datetime NOT NULL,
  `date_validation` datetime DEFAULT NULL,
  `etat` enum('en_cours','fini','annule') NOT NULL DEFAULT 'en_cours',
  `id_aboutisseur` mediumint(8) UNSIGNED DEFAULT NULL,
  `type` enum('vevent','vtodo') NOT NULL DEFAULT 'vtodo',
  `lieu` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `priorite` enum('pas_specifie','petite','moyenne','grande') NOT NULL DEFAULT 'pas_specifie',
  `complete` enum('0','20','40','60','80','100') NOT NULL DEFAULT '0',
  `public` enum('oui','non') CHARACTER SET utf8 NOT NULL DEFAULT 'non',
  `id_affaire` mediumint(8) UNSIGNED DEFAULT NULL,
  `type_tache` enum('note','fichier','RDV','appel','courrier','demande_comite','creation_contrat') DEFAULT NULL,
  `decision_comite` enum('refus_comite','accord_portage','accord_reserve_cession','accord_cession_portage','attente_retour') DEFAULT NULL,
  `id_user_valid_1` mediumint(8) UNSIGNED DEFAULT NULL,
  `validation_1` tinyint(1) DEFAULT NULL,
  `decision_1` varchar(50) DEFAULT NULL,
  `id_user_valid_2` mediumint(8) UNSIGNED DEFAULT NULL,
  `validation_2` tinyint(1) DEFAULT NULL,
  `decision_2` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='id_user = créateur !!!';

-- --------------------------------------------------------

--
-- Structure de la table `tache_contact`
--

CREATE TABLE `tache_contact` (
  `id_tache_contact` mediumint(8) UNSIGNED NOT NULL,
  `id_tache` mediumint(8) UNSIGNED NOT NULL,
  `id_contact` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tache_user`
--

CREATE TABLE `tache_user` (
  `id_tache_user` mediumint(8) UNSIGNED NOT NULL,
  `id_tache` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Table de jointure entre tache et user, permet d''assigner une';

-- --------------------------------------------------------

--
-- Structure de la table `termes`
--

CREATE TABLE `termes` (
  `id_termes` tinyint(3) UNSIGNED NOT NULL,
  `termes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Termes de paiements pour les devis smart proposal' PACK_KEYS=0;

--
-- Déchargement des données de la table `termes`
--

INSERT INTO `termes` (`id_termes`, `termes`) VALUES
(1, 'A réception de facture'),
(2, '30% à la commande, le solde à réception de facture'),
(3, 'A la livraison'),
(4, '30% à la commande, le solde à la livraison'),
(5, 'A 30 jours'),
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
(23, 'Financement spécifique');

-- --------------------------------------------------------

--
-- Structure de la table `tmp`
--

CREATE TABLE `tmp` (
  `id_bon_de_commande_ligne` varchar(64) NOT NULL,
  `ref` varchar(64) NOT NULL,
  `id_produit` mediumint(9) NOT NULL,
  `id_commande` mediumint(9) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `tmp_table`
--

CREATE TABLE `tmp_table` (
  `id` varchar(10) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `tracabilite`
--

CREATE TABLE `tracabilite` (
  `id_tracabilite` mediumint(8) UNSIGNED NOT NULL,
  `tracabilite` enum('insert','update','delete') NOT NULL DEFAULT 'insert',
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_module` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_element` mediumint(8) UNSIGNED DEFAULT NULL,
  `nom_element` varchar(256) DEFAULT NULL,
  `avant_modification` longtext DEFAULT NULL,
  `modification` longtext DEFAULT NULL COMMENT 'Enregistrement de ce qui a t modifi',
  `id_tracabilite_parent` mediumint(8) UNSIGNED DEFAULT NULL,
  `rollback` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'dtermine si l''on a fait un rollback sur cette trace'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `transaction_banque`
--

CREATE TABLE `transaction_banque` (
  `id_transaction_banque` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `data` text NOT NULL,
  `response_code` varchar(2) NOT NULL,
  `amount` varchar(10) NOT NULL,
  `transaction_id` varchar(10) NOT NULL,
  `merchant_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `transporteur`
--

CREATE TABLE `transporteur` (
  `id_transporteur` mediumint(8) UNSIGNED NOT NULL,
  `transporteur` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `transporteur`
--

INSERT INTO `transporteur` (`id_transporteur`, `transporteur`) VALUES
(1, 'DHL'),
(2, 'TAT'),
(3, 'Absystech');

-- --------------------------------------------------------

--
-- Structure de la table `type_affaire`
--

CREATE TABLE `type_affaire` (
  `id_type_affaire` int(11) NOT NULL,
  `type_affaire` varchar(255) NOT NULL,
  `libelle_pdf` varchar(255) NOT NULL DEFAULT 'Cléodis',
  `devis_template` varchar(255) NOT NULL DEFAULT 'devis',
  `contrat_template` varchar(255) NOT NULL DEFAULT 'contrat',
  `assurance_sans_tva` enum('oui','non') NOT NULL DEFAULT 'non'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `type_affaire_params`
--

CREATE TABLE `type_affaire_params` (
  `id_type_affaire_params` int(11) NOT NULL,
  `id_societe` int(11) NOT NULL,
  `id_type_affaire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

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
  `civilite` enum('M','Mlle','Mme') NOT NULL DEFAULT 'M',
  `prenom` varchar(32) NOT NULL,
  `nom` varchar(32) NOT NULL,
  `adresse` varchar(64) DEFAULT NULL,
  `adresse_2` varchar(64) DEFAULT NULL,
  `adresse_3` varchar(64) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `ville` varchar(64) DEFAULT NULL,
  `gsm` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `id_pays` varchar(2) DEFAULT NULL,
  `id_agence` mediumint(8) UNSIGNED DEFAULT NULL,
  `custom` text DEFAULT NULL,
  `id_superieur` mediumint(8) UNSIGNED DEFAULT NULL,
  `last_news` timestamp NULL DEFAULT NULL,
  `newsletter` enum('oui','non') NOT NULL DEFAULT 'oui',
  `id_localisation_langue` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `id_phone` mediumint(8) UNSIGNED DEFAULT NULL,
  `graphe_reseau` enum('oui','non') NOT NULL DEFAULT 'non',
  `graphe_autre` enum('oui','non') NOT NULL DEFAULT 'non',
  `fonction` varchar(50) DEFAULT NULL,
  `api_key` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `user_portail`
--

CREATE TABLE `user_portail` (
  `id_user_portail` mediumint(8) UNSIGNED NOT NULL,
  `id_user` tinyint(4) NOT NULL,
  `portail` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `vue`
--

CREATE TABLE `vue` (
  `id_vue` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `vue` varchar(256) NOT NULL,
  `ordre_colonne` text DEFAULT NULL,
  `tronque` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `zonegeo`
--

CREATE TABLE `zonegeo` (
  `id_zonegeo` mediumint(8) UNSIGNED NOT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `code` varchar(2) NOT NULL,
  `id_pays` char(2) NOT NULL DEFAULT '',
  `zonegeo` varchar(50) DEFAULT NULL,
  `latitude` varchar(32) DEFAULT NULL,
  `longitude` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la vue `bon_de_commande_non_envoyes`
--
DROP TABLE IF EXISTS `bon_de_commande_non_envoyes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`mfleurquin`@`%` SQL SECURITY DEFINER VIEW `bon_de_commande_non_envoyes`  AS  select `bon_de_commande`.`id_bon_de_commande` AS `id_bon_de_commande`,`bon_de_commande`.`ref` AS `ref`,`bon_de_commande`.`prix` AS `prix`,`client`.`societe` AS `client`,`affaire`.`ref` AS `ref_affaire`,`affaire`.`affaire` AS `affaire`,`bon_de_commande`.`envoye_par_mail` AS `envoye_par_mail`,`fournisseur`.`id_societe` AS `id_fournisseur`,`fournisseur`.`societe` AS `fournisseur` from (((`bon_de_commande` left join `affaire` on(`affaire`.`id_affaire` = `bon_de_commande`.`id_affaire`)) left join `societe` `client` on(`client`.`id_societe` = `bon_de_commande`.`id_societe`)) left join `societe` `fournisseur` on(`fournisseur`.`id_societe` = `bon_de_commande`.`id_fournisseur`)) where `bon_de_commande`.`envoye_par_mail` is null ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `accompagnateur`
--
ALTER TABLE `accompagnateur`
  ADD PRIMARY KEY (`id_accompagnateur`);

--
-- Index pour la table `affaire`
--
ALTER TABLE `affaire`
  ADD PRIMARY KEY (`id_affaire`),
  ADD UNIQUE KEY `ref` (`ref`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_filiale` (`id_filiale`),
  ADD KEY `nature` (`nature`),
  ADD KEY `id_fille` (`id_fille`),
  ADD KEY `id_parent` (`id_parent`),
  ADD KEY `id_partenaire` (`id_partenaire`),
  ADD KEY `id_magasin` (`id_magasin`),
  ADD KEY `id_panier` (`id_panier`),
  ADD KEY `id_apporteur` (`id_apporteur`),
  ADD KEY `id_type_affaire` (`id_type_affaire`),
  ADD KEY `id_commercial` (`id_commercial`);

--
-- Index pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  ADD PRIMARY KEY (`id_affaire_etat`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `agence`
--
ALTER TABLE `agence`
  ADD PRIMARY KEY (`id_agence`);

--
-- Index pour la table `assurance`
--
ALTER TABLE `assurance`
  ADD PRIMARY KEY (`id_assurance`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `asterisk`
--
ALTER TABLE `asterisk`
  ADD PRIMARY KEY (`id_asterisk`);

--
-- Index pour la table `base_de_connaissance`
--
ALTER TABLE `base_de_connaissance`
  ADD PRIMARY KEY (`id_base_de_connaissance`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `bon_de_commande`
--
ALTER TABLE `bon_de_commande`
  ADD PRIMARY KEY (`id_bon_de_commande`),
  ADD UNIQUE KEY `ref` (`ref`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_contact` (`id_contact`);

--
-- Index pour la table `bon_de_commande_ligne`
--
ALTER TABLE `bon_de_commande_ligne`
  ADD PRIMARY KEY (`id_bon_de_commande_ligne`),
  ADD KEY `id_bon_de_commande` (`id_bon_de_commande`),
  ADD KEY `id_commande_ligne` (`id_commande_ligne`);

--
-- Index pour la table `campagne`
--
ALTER TABLE `campagne`
  ADD PRIMARY KEY (`id_campagne`);

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`),
  ADD UNIQUE KEY `categorie` (`categorie`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id_client`);

--
-- Index pour la table `collaborateur`
--
ALTER TABLE `collaborateur`
  ADD PRIMARY KEY (`id_collaborateur`),
  ADD KEY `id_magasin` (`id_magasin`);

--
-- Index pour la table `colonne`
--
ALTER TABLE `colonne`
  ADD PRIMARY KEY (`id_colonne`),
  ADD KEY `id_vue` (`id_vue`),
  ADD KEY `id_vue_2` (`id_vue`);

--
-- Index pour la table `comite`
--
ALTER TABLE `comite`
  ADD PRIMARY KEY (`id_comite`),
  ADD KEY `id_refinanceur` (`id_refinanceur`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_societe` (`id_societe`);

--
-- Index pour la table `commande`
--
ALTER TABLE `commande`
  ADD PRIMARY KEY (`id_commande`),
  ADD UNIQUE KEY `ref` (`ref`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_devis` (`id_devis`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `commande_ligne`
--
ALTER TABLE `commande_ligne`
  ADD PRIMARY KEY (`id_commande_ligne`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_affaire_provenance` (`id_affaire_provenance`),
  ADD KEY `id_sous_categorie` (`id_sous_categorie`),
  ADD KEY `id_pack_produit` (`id_pack_produit`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `conge`
--
ALTER TABLE `conge`
  ADD PRIMARY KEY (`id_conge`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `constante`
--
ALTER TABLE `constante`
  ADD PRIMARY KEY (`id_constante`);

--
-- Index pour la table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id_contact`),
  ADD UNIQUE KEY `cle_externe` (`cle_externe`),
  ADD UNIQUE KEY `divers_4` (`divers_4`,`divers_5`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_owner` (`id_owner`);

--
-- Index pour la table `demande_refi`
--
ALTER TABLE `demande_refi`
  ADD PRIMARY KEY (`id_demande_refi`),
  ADD KEY `id_refinanceur` (`id_refinanceur`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_societe` (`id_societe`);

--
-- Index pour la table `departement`
--
ALTER TABLE `departement`
  ADD PRIMARY KEY (`id_departement`),
  ADD KEY `id_region` (`id_region`),
  ADD KEY `code` (`code`);

--
-- Index pour la table `devis`
--
ALTER TABLE `devis`
  ADD PRIMARY KEY (`id_devis`),
  ADD UNIQUE KEY `ref` (`ref`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_opportunite` (`id_opportunite`),
  ADD KEY `etat` (`etat`),
  ADD KEY `id_filiale` (`id_filiale`);

--
-- Index pour la table `devise`
--
ALTER TABLE `devise`
  ADD PRIMARY KEY (`id_devise`);

--
-- Index pour la table `devis_ligne`
--
ALTER TABLE `devis_ligne`
  ADD PRIMARY KEY (`id_devis_ligne`),
  ADD KEY `id_devis` (`id_devis`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_affaire_provenance` (`id_affaire_provenance`),
  ADD KEY `id_sous_categorie` (`id_sous_categorie`),
  ADD KEY `id_pack_produit` (`id_pack_produit`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id_document`);

--
-- Index pour la table `document_contrat`
--
ALTER TABLE `document_contrat`
  ADD PRIMARY KEY (`id_document_contrat`);

--
-- Index pour la table `document_revendeur`
--
ALTER TABLE `document_revendeur`
  ADD PRIMARY KEY (`id_document_revendeur`),
  ADD KEY `id_societe` (`id_societe`);

--
-- Index pour la table `domaine`
--
ALTER TABLE `domaine`
  ADD PRIMARY KEY (`id_domaine`);

--
-- Index pour la table `emailing_contact`
--
ALTER TABLE `emailing_contact`
  ADD PRIMARY KEY (`id_emailing_contact`),
  ADD UNIQUE KEY `id_source` (`id_emailing_source`,`email`),
  ADD KEY `id_societe` (`societe`),
  ADD KEY `id_owner` (`id_owner`),
  ADD KEY `id_source_2` (`id_source`);

--
-- Index pour la table `emailing_erreur`
--
ALTER TABLE `emailing_erreur`
  ADD PRIMARY KEY (`id_erreur`);

--
-- Index pour la table `emailing_job`
--
ALTER TABLE `emailing_job`
  ADD PRIMARY KEY (`id_emailing_job`),
  ADD KEY `FK_emailing_job_1` (`id_emailing_projet`),
  ADD KEY `FK_emailing_job_2` (`id_emailing_liste`);

--
-- Index pour la table `emailing_job_email`
--
ALTER TABLE `emailing_job_email`
  ADD PRIMARY KEY (`id_emailing_job_email`),
  ADD UNIQUE KEY `id_emailing_job` (`id_emailing_job`,`id_emailing_liste_contact`),
  ADD KEY `FK_emailing_job_email_2` (`id_emailing_liste_contact`);

--
-- Index pour la table `emailing_lien`
--
ALTER TABLE `emailing_lien`
  ADD PRIMARY KEY (`id_emailing_lien`);

--
-- Index pour la table `emailing_liste`
--
ALTER TABLE `emailing_liste`
  ADD PRIMARY KEY (`id_emailing_liste`);

--
-- Index pour la table `emailing_liste_contact`
--
ALTER TABLE `emailing_liste_contact`
  ADD PRIMARY KEY (`id_emailing_liste_contact`),
  ADD UNIQUE KEY `id_emailing_liste` (`id_emailing_liste`,`id_emailing_contact`),
  ADD KEY `id_emailing_contact` (`id_emailing_contact`);

--
-- Index pour la table `emailing_projet`
--
ALTER TABLE `emailing_projet`
  ADD PRIMARY KEY (`id_emailing_projet`);

--
-- Index pour la table `emailing_projet_lien`
--
ALTER TABLE `emailing_projet_lien`
  ADD PRIMARY KEY (`id_emailing_projet_lien`),
  ADD KEY `FK_emailing_projet_lien_1` (`id_emailing_projet`);

--
-- Index pour la table `emailing_source`
--
ALTER TABLE `emailing_source`
  ADD PRIMARY KEY (`id_emailing_source`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `emailing_tracking`
--
ALTER TABLE `emailing_tracking`
  ADD PRIMARY KEY (`id_emailing_tracking`),
  ADD KEY `FK_emailing_tracking_1` (`id_emailing_job_email`),
  ADD KEY `FK_emailing_tracking_2` (`id_emailing_projet_lien`);

--
-- Index pour la table `exporter`
--
ALTER TABLE `exporter`
  ADD PRIMARY KEY (`id_exporter`),
  ADD KEY `FK_exporter_1` (`id_user`),
  ADD KEY `FK_exporter_2` (`id_module`);

--
-- Index pour la table `export_facture`
--
ALTER TABLE `export_facture`
  ADD PRIMARY KEY (`id_export_facture`),
  ADD KEY `id_facture` (`id_facture`);

--
-- Index pour la table `fabriquant`
--
ALTER TABLE `fabriquant`
  ADD PRIMARY KEY (`id_fabriquant`),
  ADD UNIQUE KEY `fabriquant` (`fabriquant`);

--
-- Index pour la table `facturation`
--
ALTER TABLE `facturation`
  ADD PRIMARY KEY (`id_facturation`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_facture` (`id_facture`);

--
-- Index pour la table `facturation_attente`
--
ALTER TABLE `facturation_attente`
  ADD PRIMARY KEY (`id_facturation_attente`),
  ADD KEY `id_facture` (`id_facture`),
  ADD KEY `id_facturation` (`id_facturation`);

--
-- Index pour la table `facturation_fournisseur`
--
ALTER TABLE `facturation_fournisseur`
  ADD PRIMARY KEY (`id_facturation_fournisseur`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_facture_fournisseur` (`id_bon_de_commande`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_bon_de_commande` (`id_bon_de_commande`);

--
-- Index pour la table `facturation_fournisseur_ligne`
--
ALTER TABLE `facturation_fournisseur_ligne`
  ADD PRIMARY KEY (`id_facturation_fournisseur_ligne`),
  ADD KEY `id_facturation_fournisseur` (`id_facturation_fournisseur`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_commande_ligne` (`id_commande_ligne`);

--
-- Index pour la table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`id_facture`),
  ADD UNIQUE KEY `ref` (`ref`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `type` (`type_facture`),
  ADD KEY `id_demande_refi` (`id_demande_refi`),
  ADD KEY `id_refinanceur` (`id_refinanceur`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_fournisseur_prepaiement` (`id_fournisseur_prepaiement`);

--
-- Index pour la table `facture_fournisseur`
--
ALTER TABLE `facture_fournisseur`
  ADD PRIMARY KEY (`id_facture_fournisseur`),
  ADD KEY `id_bon_de_commande` (`id_bon_de_commande`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_fournisseur` (`id_fournisseur`);

--
-- Index pour la table `facture_fournisseur_ligne`
--
ALTER TABLE `facture_fournisseur_ligne`
  ADD PRIMARY KEY (`id_facture_fournisseur_ligne`),
  ADD KEY `id_commande_ligne` (`id_bon_de_commande_ligne`),
  ADD KEY `id_facture_fournisseur` (`id_facture_fournisseur`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `facture_ligne`
--
ALTER TABLE `facture_ligne`
  ADD PRIMARY KEY (`id_facture_ligne`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_facture` (`id_facture`),
  ADD KEY `id_affaire_provenance` (`id_affaire_provenance`);

--
-- Index pour la table `facture_magasin`
--
ALTER TABLE `facture_magasin`
  ADD PRIMARY KEY (`id_facture_magasin`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `facture_magasin_recu`
--
ALTER TABLE `facture_magasin_recu`
  ADD PRIMARY KEY (`id_facture_magasin_recu`);

--
-- Index pour la table `facture_non_parvenue`
--
ALTER TABLE `facture_non_parvenue`
  ADD PRIMARY KEY (`id_facture_non_parvenue`),
  ADD KEY `id_facture_fournisseur` (`id_facture_fournisseur`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_bon_de_commande` (`id_bon_de_commande`);

--
-- Index pour la table `facture_transaction`
--
ALTER TABLE `facture_transaction`
  ADD PRIMARY KEY (`id_facture_transaction`),
  ADD KEY `id_facture` (`id_facture`);

--
-- Index pour la table `famille`
--
ALTER TABLE `famille`
  ADD PRIMARY KEY (`id_famille`),
  ADD UNIQUE KEY `cle_externe` (`cle_externe`);

--
-- Index pour la table `filtre_defaut`
--
ALTER TABLE `filtre_defaut`
  ADD PRIMARY KEY (`id_filtre_defaut`),
  ADD UNIQUE KEY `id_user_2` (`id_user`,`div`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `filtre_optima`
--
ALTER TABLE `filtre_optima`
  ADD PRIMARY KEY (`id_filtre_optima`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_module` (`id_module`,`id_user`);

--
-- Index pour la table `filtre_user`
--
ALTER TABLE `filtre_user`
  ADD PRIMARY KEY (`id_filtre_user`),
  ADD UNIQUE KEY `id_filtre_optima_2` (`id_filtre_optima`,`id_user`,`id_module`),
  ADD KEY `id_filtre_optima` (`id_filtre_optima`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_module` (`id_module`);

--
-- Index pour la table `formation_attestation_presence`
--
ALTER TABLE `formation_attestation_presence`
  ADD PRIMARY KEY (`id_formation_attestation_presence`),
  ADD KEY `id_formation_devis` (`id_formation_commande`,`id_contact`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_formation_devis_2` (`id_formation_devis`);

--
-- Index pour la table `formation_bon_de_commande_fournisseur`
--
ALTER TABLE `formation_bon_de_commande_fournisseur`
  ADD PRIMARY KEY (`id_formation_bon_de_commande_fournisseur`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_formation_devis` (`id_formation_devis`),
  ADD KEY `id_fournisseur` (`id_fournisseur`);

--
-- Index pour la table `formation_commande`
--
ALTER TABLE `formation_commande`
  ADD PRIMARY KEY (`id_formation_commande`),
  ADD KEY `id_formation_devis` (`id_formation_devis`);

--
-- Index pour la table `formation_commande_fournisseur`
--
ALTER TABLE `formation_commande_fournisseur`
  ADD PRIMARY KEY (`id_formation_commande_fournisseur`),
  ADD KEY `id_formation_devis` (`id_formation_devis`,`id_formation_commande`),
  ADD KEY `id_formation_commande` (`id_formation_commande`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `formation_devis`
--
ALTER TABLE `formation_devis`
  ADD PRIMARY KEY (`id_formation_devis`),
  ADD KEY `id_formateur` (`id_societe`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_formateur_2` (`id_formateur`),
  ADD KEY `id_lieu_formation` (`id_lieu_formation`),
  ADD KEY `id_apporteur_affaire` (`id_apporteur_affaire`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_owner` (`id_owner`);

--
-- Index pour la table `formation_devis_fournisseur`
--
ALTER TABLE `formation_devis_fournisseur`
  ADD PRIMARY KEY (`id_formation_devis_fournisseur`),
  ADD KEY `id_fournisseur` (`id_societe`),
  ADD KEY `id_formation_devis` (`id_formation_devis`);

--
-- Index pour la table `formation_devis_ligne`
--
ALTER TABLE `formation_devis_ligne`
  ADD PRIMARY KEY (`id_formation_devis_ligne`),
  ADD KEY `id_formation_devis` (`id_formation_devis`);

--
-- Index pour la table `formation_facture`
--
ALTER TABLE `formation_facture`
  ADD PRIMARY KEY (`id_formation_facture`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_formation_devis` (`id_formation_devis`);

--
-- Index pour la table `formation_facture_fournisseur`
--
ALTER TABLE `formation_facture_fournisseur`
  ADD PRIMARY KEY (`id_formation_facture_fournisseur`),
  ADD KEY `id_formation_devis` (`id_formation_devis`),
  ADD KEY `id_formation_bon_de_commande_fournisseur` (`id_formation_bon_de_commande_fournisseur`),
  ADD KEY `id_societe` (`id_societe`);

--
-- Index pour la table `formation_facture_fournisseur_ligne`
--
ALTER TABLE `formation_facture_fournisseur_ligne`
  ADD PRIMARY KEY (`id_formation_facture_fournisseur_ligne`),
  ADD KEY `id_formation_facture_fournisseur` (`id_formation_facture_fournisseur`);

--
-- Index pour la table `formation_participant`
--
ALTER TABLE `formation_participant`
  ADD PRIMARY KEY (`id_formation_participant`),
  ADD KEY `id_formation_devis` (`id_formation_devis`,`id_contact`),
  ADD KEY `id_contact` (`id_contact`);

--
-- Index pour la table `formation_priseEnCharge`
--
ALTER TABLE `formation_priseEnCharge`
  ADD PRIMARY KEY (`id_formation_priseEnCharge`),
  ADD KEY `id_formation_devis` (`id_formation_devis`),
  ADD KEY `opca` (`opca`);

--
-- Index pour la table `ged`
--
ALTER TABLE `ged`
  ADD PRIMARY KEY (`id_ged`),
  ADD UNIQUE KEY `id_owner` (`id_owner`,`id_societe`,`ged`,`version`,`weight`,`id_parent`),
  ADD KEY `id_parent` (`id_parent`),
  ADD KEY `id_societe` (`id_societe`);

--
-- Index pour la table `importer`
--
ALTER TABLE `importer`
  ADD PRIMARY KEY (`id_importer`),
  ADD KEY `id_module` (`id_module`),
  ADD KEY `user` (`id_user`);

--
-- Index pour la table `importer_ligne`
--
ALTER TABLE `importer_ligne`
  ADD PRIMARY KEY (`id_importer_ligne`),
  ADD KEY `id_importer` (`id_importer`);

--
-- Index pour la table `import_facture_fournisseur`
--
ALTER TABLE `import_facture_fournisseur`
  ADD PRIMARY KEY (`id_import_facture_fournisseur`),
  ADD KEY `user` (`id_user`);

--
-- Index pour la table `licence`
--
ALTER TABLE `licence`
  ADD PRIMARY KEY (`id_licence`),
  ADD KEY `id_commande_ligne` (`id_commande_ligne`),
  ADD KEY `id_licence_type` (`id_licence_type`);

--
-- Index pour la table `licence_type`
--
ALTER TABLE `licence_type`
  ADD PRIMARY KEY (`id_licence_type`);

--
-- Index pour la table `localisation_langue`
--
ALTER TABLE `localisation_langue`
  ADD PRIMARY KEY (`id_localisation_langue`),
  ADD KEY `id_pays` (`id_pays`);

--
-- Index pour la table `loyer`
--
ALTER TABLE `loyer`
  ADD PRIMARY KEY (`id_loyer`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `loyer_prolongation`
--
ALTER TABLE `loyer_prolongation`
  ADD PRIMARY KEY (`id_loyer_prolongation`),
  ADD KEY `id_prolongation` (`id_prolongation`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `magasin`
--
ALTER TABLE `magasin`
  ADD PRIMARY KEY (`id_magasin`),
  ADD KEY `id_societe` (`id_societe`);

--
-- Index pour la table `magasin_vendeur`
--
ALTER TABLE `magasin_vendeur`
  ADD PRIMARY KEY (`id_magasin_vendeur`),
  ADD KEY `id_magasin` (`id_magasin`);

--
-- Index pour la table `messagerie`
--
ALTER TABLE `messagerie`
  ADD PRIMARY KEY (`id_messagerie`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id_module`),
  ADD UNIQUE KEY `module` (`module`),
  ADD KEY `id_parent` (`id_parent`);

--
-- Index pour la table `module_privilege`
--
ALTER TABLE `module_privilege`
  ADD KEY `id_module` (`id_module`),
  ADD KEY `id_privilege` (`id_privilege`);

--
-- Index pour la table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id_news`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `opca`
--
ALTER TABLE `opca`
  ADD PRIMARY KEY (`id_opca`);

--
-- Index pour la table `opportunite`
--
ALTER TABLE `opportunite`
  ADD PRIMARY KEY (`id_opportunite`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_target` (`id_target`);

--
-- Index pour la table `pack_produit`
--
ALTER TABLE `pack_produit`
  ADD PRIMARY KEY (`id_pack_produit`),
  ADD KEY `id_pack_produit_besoin` (`id_pack_produit_besoin`),
  ADD KEY `id_pack_produit_produit` (`id_pack_produit_produit`),
  ADD KEY `id_document_contrat` (`id_document_contrat`),
  ADD KEY `specifique_partenaire` (`specifique_partenaire`);

--
-- Index pour la table `pack_produit_besoin`
--
ALTER TABLE `pack_produit_besoin`
  ADD PRIMARY KEY (`id_pack_produit_besoin`);

--
-- Index pour la table `pack_produit_fournisseur`
--
ALTER TABLE `pack_produit_fournisseur`
  ADD PRIMARY KEY (`id_pack_produit_fournisseur`),
  ADD KEY `id_pack_produit` (`id_pack_produit`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_fournisseur` (`id_fournisseur`);

--
-- Index pour la table `pack_produit_ligne`
--
ALTER TABLE `pack_produit_ligne`
  ADD PRIMARY KEY (`id_pack_produit_ligne`),
  ADD KEY `id_pack_produit` (`id_pack_produit`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_partenaire` (`id_partenaire`);

--
-- Index pour la table `pack_produit_produit`
--
ALTER TABLE `pack_produit_produit`
  ADD PRIMARY KEY (`id_pack_produit_produit`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id_panier`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `parc`
--
ALTER TABLE `parc`
  ADD PRIMARY KEY (`id_parc`),
  ADD UNIQUE KEY `serial` (`serial`,`date`,`etat`,`ref`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `provenance` (`provenance`),
  ADD KEY `date` (`date`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_bon_de_commande` (`id_bon_de_commande`);

--
-- Index pour la table `password_reset`
--
ALTER TABLE `password_reset`
  ADD PRIMARY KEY (`id_password_reset`);

--
-- Index pour la table `pays`
--
ALTER TABLE `pays`
  ADD PRIMARY KEY (`id_pays`);

--
-- Index pour la table `pdf_affaire`
--
ALTER TABLE `pdf_affaire`
  ADD PRIMARY KEY (`id_pdf_affaire`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `pdf_societe`
--
ALTER TABLE `pdf_societe`
  ADD PRIMARY KEY (`id_pdf_societe`),
  ADD KEY `id_societe` (`id_societe`);

--
-- Index pour la table `phone`
--
ALTER TABLE `phone`
  ADD PRIMARY KEY (`id_phone`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_asterisk` (`id_asterisk`);

--
-- Index pour la table `politesse`
--
ALTER TABLE `politesse`
  ADD PRIMARY KEY (`id_politesse`);

--
-- Index pour la table `privilege`
--
ALTER TABLE `privilege`
  ADD PRIMARY KEY (`id_privilege`);

--
-- Index pour la table `processeur`
--
ALTER TABLE `processeur`
  ADD PRIMARY KEY (`id_processeur`);

--
-- Index pour la table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id_produit`),
  ADD UNIQUE KEY `ref` (`ref`),
  ADD KEY `id_fabriquant` (`id_fabriquant`),
  ADD KEY `id_sous_categorie` (`id_sous_categorie`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_produit_dd` (`id_produit_dd`),
  ADD KEY `id_produit_dotpitch` (`id_produit_dotpitch`),
  ADD KEY `id_produit_format` (`id_produit_format`),
  ADD KEY `id_produit_garantie_uc` (`id_produit_garantie_uc`),
  ADD KEY `id_produit_garantie_ecran` (`id_produit_garantie_ecran`),
  ADD KEY `id_produit_garantie_imprimante` (`id_produit_garantie_imprimante`),
  ADD KEY `id_produit_lan` (`id_produit_lan`),
  ADD KEY `id_produit_lecteur` (`id_produit_lecteur`),
  ADD KEY `id_produit_OS` (`id_produit_OS`),
  ADD KEY `id_produit_ram` (`id_produit_ram`),
  ADD KEY `id_produit_puissance` (`id_produit_puissance`),
  ADD KEY `id_produit_technique` (`id_produit_technique`),
  ADD KEY `id_processeur` (`id_processeur`),
  ADD KEY `id_produit_type` (`id_produit_type`),
  ADD KEY `id_produit_typeecran` (`id_produit_typeecran`),
  ADD KEY `id_produit_viewable` (`id_produit_viewable`),
  ADD KEY `id_produit_env` (`id_produit_env`),
  ADD KEY `id_produit_besoins` (`id_produit_besoins`),
  ADD KEY `id_produit_tel_produit` (`id_produit_tel_produit`),
  ADD KEY `id_produit_tel_type` (`id_produit_tel_type`),
  ADD KEY `id_licence_type` (`id_licence_type`),
  ADD KEY `id_document_contrat` (`id_document_contrat`);

--
-- Index pour la table `produit_besoins`
--
ALTER TABLE `produit_besoins`
  ADD PRIMARY KEY (`id_produit_besoins`);

--
-- Index pour la table `produit_dd`
--
ALTER TABLE `produit_dd`
  ADD PRIMARY KEY (`id_produit_dd`);

--
-- Index pour la table `produit_dotpitch`
--
ALTER TABLE `produit_dotpitch`
  ADD PRIMARY KEY (`id_produit_dotpitch`);

--
-- Index pour la table `produit_env`
--
ALTER TABLE `produit_env`
  ADD PRIMARY KEY (`id_produit_env`);

--
-- Index pour la table `produit_format`
--
ALTER TABLE `produit_format`
  ADD PRIMARY KEY (`id_produit_format`);

--
-- Index pour la table `produit_fournisseur_loyer`
--
ALTER TABLE `produit_fournisseur_loyer`
  ADD PRIMARY KEY (`id_produit_fournisseur_loyer`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_fournisseur` (`id_fournisseur`);

--
-- Index pour la table `produit_garantie`
--
ALTER TABLE `produit_garantie`
  ADD PRIMARY KEY (`id_produit_garantie`);

--
-- Index pour la table `produit_lan`
--
ALTER TABLE `produit_lan`
  ADD PRIMARY KEY (`id_produit_lan`);

--
-- Index pour la table `produit_lecteur`
--
ALTER TABLE `produit_lecteur`
  ADD PRIMARY KEY (`id_produit_lecteur`);

--
-- Index pour la table `produit_OS`
--
ALTER TABLE `produit_OS`
  ADD PRIMARY KEY (`id_produit_OS`);

--
-- Index pour la table `produit_puissance`
--
ALTER TABLE `produit_puissance`
  ADD PRIMARY KEY (`id_produit_puissance`);

--
-- Index pour la table `produit_ram`
--
ALTER TABLE `produit_ram`
  ADD PRIMARY KEY (`id_produit_ram`);

--
-- Index pour la table `produit_technique`
--
ALTER TABLE `produit_technique`
  ADD PRIMARY KEY (`id_produit_technique`);

--
-- Index pour la table `produit_tel_produit`
--
ALTER TABLE `produit_tel_produit`
  ADD PRIMARY KEY (`id_produit_tel_produit`);

--
-- Index pour la table `produit_tel_type`
--
ALTER TABLE `produit_tel_type`
  ADD PRIMARY KEY (`id_produit_tel_type`);

--
-- Index pour la table `produit_type`
--
ALTER TABLE `produit_type`
  ADD PRIMARY KEY (`id_produit_type`);

--
-- Index pour la table `produit_typeecran`
--
ALTER TABLE `produit_typeecran`
  ADD PRIMARY KEY (`id_produit_typeecran`);

--
-- Index pour la table `produit_viewable`
--
ALTER TABLE `produit_viewable`
  ADD PRIMARY KEY (`id_produit_viewable`);

--
-- Index pour la table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id_profil`),
  ADD UNIQUE KEY `profil` (`profil`);

--
-- Index pour la table `profil_privilege`
--
ALTER TABLE `profil_privilege`
  ADD PRIMARY KEY (`id_profil_privilege`),
  ADD UNIQUE KEY `privilege` (`id_profil`,`id_privilege`,`id_module`,`field`),
  ADD KEY `id_module` (`id_module`),
  ADD KEY `id_profil` (`id_profil`),
  ADD KEY `id_privilege` (`id_privilege`);

--
-- Index pour la table `prolongation`
--
ALTER TABLE `prolongation`
  ADD PRIMARY KEY (`id_prolongation`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_refinanceur` (`id_refinanceur`);

--
-- Index pour la table `refinanceur`
--
ALTER TABLE `refinanceur`
  ADD PRIMARY KEY (`id_refinanceur`);

--
-- Index pour la table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`id_region`);

--
-- Index pour la table `reglement`
--
ALTER TABLE `reglement`
  ADD PRIMARY KEY (`id_reglement`);

--
-- Index pour la table `relance`
--
ALTER TABLE `relance`
  ADD PRIMARY KEY (`id_relance`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_contact` (`id_contact`);

--
-- Index pour la table `relance_facture`
--
ALTER TABLE `relance_facture`
  ADD PRIMARY KEY (`id_relance_facture`),
  ADD UNIQUE KEY `id_facture` (`id_facture`,`id_relance`),
  ADD KEY `id_relance` (`id_relance`);

--
-- Index pour la table `restitution_anticipee`
--
ALTER TABLE `restitution_anticipee`
  ADD PRIMARY KEY (`id_restitution_anticipee`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `scanner`
--
ALTER TABLE `scanner`
  ADD PRIMARY KEY (`id_scanner`);

--
-- Index pour la table `secteur_commercial`
--
ALTER TABLE `secteur_commercial`
  ADD PRIMARY KEY (`id_secteur_commercial`);

--
-- Index pour la table `secteur_geographique`
--
ALTER TABLE `secteur_geographique`
  ADD PRIMARY KEY (`id_secteur_geographique`);

--
-- Index pour la table `sell_and_sign`
--
ALTER TABLE `sell_and_sign`
  ADD PRIMARY KEY (`id_sell_and_sign`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `certificat_de_preuve` (`certificat_de_preuve`),
  ADD KEY `contrat_signe` (`contrat_signe`);

--
-- Index pour la table `site_article`
--
ALTER TABLE `site_article`
  ADD PRIMARY KEY (`id_site_article`),
  ADD KEY `id_site_menu` (`id_site_menu`);

--
-- Index pour la table `site_article_contenu`
--
ALTER TABLE `site_article_contenu`
  ADD PRIMARY KEY (`id_site_article_contenu`),
  ADD KEY `id_site_article` (`id_site_article`),
  ADD KEY `id_site_article_2` (`id_site_article`);

--
-- Index pour la table `site_associe`
--
ALTER TABLE `site_associe`
  ADD PRIMARY KEY (`id_site_associe`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_societe_footer_mail` (`id_societe_footer_mail`),
  ADD KEY `id_type_affaire` (`id_type_affaire`);

--
-- Index pour la table `site_en_tete`
--
ALTER TABLE `site_en_tete`
  ADD PRIMARY KEY (`id_site_en_tete`);

--
-- Index pour la table `site_menu`
--
ALTER TABLE `site_menu`
  ADD PRIMARY KEY (`id_site_menu`);

--
-- Index pour la table `site_offre`
--
ALTER TABLE `site_offre`
  ADD PRIMARY KEY (`id_site_offre`);

--
-- Index pour la table `slimpay_transaction`
--
ALTER TABLE `slimpay_transaction`
  ADD PRIMARY KEY (`id_slimpay_transaction`),
  ADD KEY `id_facture` (`id_facture`);

--
-- Index pour la table `societe`
--
ALTER TABLE `societe`
  ADD PRIMARY KEY (`id_societe`),
  ADD UNIQUE KEY `ref` (`ref`),
  ADD UNIQUE KEY `cle_externe` (`cle_externe`),
  ADD UNIQUE KEY `societe` (`societe`,`adresse`),
  ADD KEY `id_famille` (`id_famille`),
  ADD KEY `id_owner` (`id_owner`),
  ADD KEY `id_filiale` (`id_filiale`),
  ADD KEY `id_secteur_geographique` (`id_secteur_geographique`),
  ADD KEY `id_secteur_commercial` (`id_secteur_commercial`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `id_accompagnateur` (`id_accompagnateur`),
  ADD KEY `effectif` (`effectif`),
  ADD KEY `id_contact_signataire` (`id_contact_signataire`),
  ADD KEY `id_pays` (`id_pays`),
  ADD KEY `id_contact_facturation` (`id_contact_facturation`),
  ADD KEY `facturation_id_pays` (`facturation_id_pays`),
  ADD KEY `id_contact_commercial` (`id_contact_commercial`),
  ADD KEY `id_devise` (`id_devise`),
  ADD KEY `recallCounter` (`recallCounter`),
  ADD KEY `avis_credit` (`avis_credit`),
  ADD KEY `score` (`score`),
  ADD KEY `contentieux` (`contentieux`),
  ADD KEY `id_apporteur` (`id_apporteur`),
  ADD KEY `id_prospection` (`id_prospection`),
  ADD KEY `id_assistante` (`id_assistante`),
  ADD KEY `id_campagne` (`id_campagne`),
  ADD KEY `revendeur` (`revendeur`),
  ADD KEY `sms` (`sms`);

--
-- Index pour la table `societe_domaine`
--
ALTER TABLE `societe_domaine`
  ADD PRIMARY KEY (`id_societe_domaine`),
  ADD UNIQUE KEY `UNIQUE` (`id_societe`,`id_domaine`),
  ADD KEY `id_domaine` (`id_domaine`);

--
-- Index pour la table `sous_categorie`
--
ALTER TABLE `sous_categorie`
  ADD PRIMARY KEY (`id_sous_categorie`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `stat_snap`
--
ALTER TABLE `stat_snap`
  ADD PRIMARY KEY (`id_stat_snap`),
  ADD KEY `id_agence` (`id_agence`);

--
-- Index pour la table `suivi`
--
ALTER TABLE `suivi`
  ADD PRIMARY KEY (`id_suivi`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_formation_devis` (`id_formation_devis`);

--
-- Index pour la table `suivi_contact`
--
ALTER TABLE `suivi_contact`
  ADD PRIMARY KEY (`id_suivi_contact`),
  ADD UNIQUE KEY `id_contact` (`id_contact`,`id_suivi`),
  ADD KEY `id_suivi` (`id_suivi`);

--
-- Index pour la table `suivi_notifie`
--
ALTER TABLE `suivi_notifie`
  ADD PRIMARY KEY (`id_suivi_notifie`),
  ADD UNIQUE KEY `id_user` (`id_user`,`id_suivi`),
  ADD KEY `id_suivi` (`id_suivi`);

--
-- Index pour la table `suivi_portail`
--
ALTER TABLE `suivi_portail`
  ADD PRIMARY KEY (`id_suivi_portail`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_devis` (`id_devis`,`id_commande`,`id_parc`),
  ADD KEY `id_bon_de_commande` (`id_bon_de_commande`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_parc` (`id_parc`),
  ADD KEY `id_opportunite` (`id_opportunite`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `suivi_portail_contact`
--
ALTER TABLE `suivi_portail_contact`
  ADD UNIQUE KEY `id_suivi_portail` (`id_suivi_portail`,`id_contact`),
  ADD KEY `id_contact` (`id_contact`);

--
-- Index pour la table `suivi_societe`
--
ALTER TABLE `suivi_societe`
  ADD PRIMARY KEY (`id_suivi_societe`),
  ADD UNIQUE KEY `id_user` (`id_user`,`id_suivi`),
  ADD KEY `id_suivi` (`id_suivi`);

--
-- Index pour la table `tache`
--
ALTER TABLE `tache`
  ADD PRIMARY KEY (`id_tache`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_aboutisseur` (`id_aboutisseur`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_suivi` (`id_suivi`);

--
-- Index pour la table `tache_contact`
--
ALTER TABLE `tache_contact`
  ADD PRIMARY KEY (`id_tache_contact`),
  ADD UNIQUE KEY `id_tache` (`id_tache`,`id_contact`),
  ADD KEY `id_contact` (`id_contact`);

--
-- Index pour la table `tache_user`
--
ALTER TABLE `tache_user`
  ADD PRIMARY KEY (`id_tache_user`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_tache` (`id_tache`);

--
-- Index pour la table `termes`
--
ALTER TABLE `termes`
  ADD PRIMARY KEY (`id_termes`);

--
-- Index pour la table `tmp`
--
ALTER TABLE `tmp`
  ADD KEY `a` (`id_bon_de_commande_ligne`);

--
-- Index pour la table `tracabilite`
--
ALTER TABLE `tracabilite`
  ADD PRIMARY KEY (`id_tracabilite`),
  ADD KEY `id_module` (`id_module`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_tracabilite_parent` (`id_tracabilite_parent`);

--
-- Index pour la table `transaction_banque`
--
ALTER TABLE `transaction_banque`
  ADD PRIMARY KEY (`id_transaction_banque`),
  ADD KEY `id_affaire` (`id_affaire`);

--
-- Index pour la table `transporteur`
--
ALTER TABLE `transporteur`
  ADD PRIMARY KEY (`id_transporteur`);

--
-- Index pour la table `type_affaire`
--
ALTER TABLE `type_affaire`
  ADD PRIMARY KEY (`id_type_affaire`);

--
-- Index pour la table `type_affaire_params`
--
ALTER TABLE `type_affaire_params`
  ADD PRIMARY KEY (`id_type_affaire_params`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `api_key` (`api_key`),
  ADD KEY `id_societe` (`id_societe`),
  ADD KEY `id_pays` (`id_pays`),
  ADD KEY `id_agence` (`id_agence`),
  ADD KEY `id_superieur` (`id_superieur`),
  ADD KEY `id_profil` (`id_profil`),
  ADD KEY `password` (`password`,`login`),
  ADD KEY `id_localisation_langue` (`id_localisation_langue`),
  ADD KEY `id_phone` (`id_phone`);

--
-- Index pour la table `user_portail`
--
ALTER TABLE `user_portail`
  ADD PRIMARY KEY (`id_user_portail`);

--
-- Index pour la table `vue`
--
ALTER TABLE `vue`
  ADD PRIMARY KEY (`id_vue`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_user_2` (`id_user`);

--
-- Index pour la table `zonegeo`
--
ALTER TABLE `zonegeo`
  ADD PRIMARY KEY (`id_zonegeo`),
  ADD KEY `CP` (`cp`),
  ADD KEY `code` (`code`),
  ADD KEY `latitude` (`latitude`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `accompagnateur`
--
ALTER TABLE `accompagnateur`
  MODIFY `id_accompagnateur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `affaire`
--
ALTER TABLE `affaire`
  MODIFY `id_affaire` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  MODIFY `id_affaire_etat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `agence`
--
ALTER TABLE `agence`
  MODIFY `id_agence` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `assurance`
--
ALTER TABLE `assurance`
  MODIFY `id_assurance` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `asterisk`
--
ALTER TABLE `asterisk`
  MODIFY `id_asterisk` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `base_de_connaissance`
--
ALTER TABLE `base_de_connaissance`
  MODIFY `id_base_de_connaissance` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `bon_de_commande`
--
ALTER TABLE `bon_de_commande`
  MODIFY `id_bon_de_commande` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `bon_de_commande_ligne`
--
ALTER TABLE `bon_de_commande_ligne`
  MODIFY `id_bon_de_commande_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `campagne`
--
ALTER TABLE `campagne`
  MODIFY `id_campagne` smallint(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `id_client` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `colonne`
--
ALTER TABLE `colonne`
  MODIFY `id_colonne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `comite`
--
ALTER TABLE `comite`
  MODIFY `id_comite` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `commande`
  MODIFY `id_commande` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commande_ligne`
--
ALTER TABLE `commande_ligne`
  MODIFY `id_commande_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `conge`
--
ALTER TABLE `conge`
  MODIFY `id_conge` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `constante`
--
ALTER TABLE `constante`
  MODIFY `id_constante` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `contact`
--
ALTER TABLE `contact`
  MODIFY `id_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `demande_refi`
--
ALTER TABLE `demande_refi`
  MODIFY `id_demande_refi` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `departement`
--
ALTER TABLE `departement`
  MODIFY `id_departement` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `devis`
--
ALTER TABLE `devis`
  MODIFY `id_devis` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `devise`
--
ALTER TABLE `devise`
  MODIFY `id_devise` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `devis_ligne`
--
ALTER TABLE `devis_ligne`
  MODIFY `id_devis_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document`
--
ALTER TABLE `document`
  MODIFY `id_document` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document_contrat`
--
ALTER TABLE `document_contrat`
  MODIFY `id_document_contrat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document_revendeur`
--
ALTER TABLE `document_revendeur`
  MODIFY `id_document_revendeur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `domaine`
--
ALTER TABLE `domaine`
  MODIFY `id_domaine` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `emailing_contact`
--
ALTER TABLE `emailing_contact`
  MODIFY `id_emailing_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_erreur`
--
ALTER TABLE `emailing_erreur`
  MODIFY `id_erreur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_job`
--
ALTER TABLE `emailing_job`
  MODIFY `id_emailing_job` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_job_email`
--
ALTER TABLE `emailing_job_email`
  MODIFY `id_emailing_job_email` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_lien`
--
ALTER TABLE `emailing_lien`
  MODIFY `id_emailing_lien` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_liste`
--
ALTER TABLE `emailing_liste`
  MODIFY `id_emailing_liste` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_liste_contact`
--
ALTER TABLE `emailing_liste_contact`
  MODIFY `id_emailing_liste_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_projet`
--
ALTER TABLE `emailing_projet`
  MODIFY `id_emailing_projet` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_projet_lien`
--
ALTER TABLE `emailing_projet_lien`
  MODIFY `id_emailing_projet_lien` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_source`
--
ALTER TABLE `emailing_source`
  MODIFY `id_emailing_source` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `emailing_tracking`
--
ALTER TABLE `emailing_tracking`
  MODIFY `id_emailing_tracking` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `exporter`
--
ALTER TABLE `exporter`
  MODIFY `id_exporter` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `export_facture`
--
ALTER TABLE `export_facture`
  MODIFY `id_export_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `fabriquant`
--
ALTER TABLE `fabriquant`
  MODIFY `id_fabriquant` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facturation`
--
ALTER TABLE `facturation`
  MODIFY `id_facturation` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facturation_attente`
--
ALTER TABLE `facturation_attente`
  MODIFY `id_facturation_attente` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facturation_fournisseur`
--
ALTER TABLE `facturation_fournisseur`
  MODIFY `id_facturation_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facturation_fournisseur_ligne`
--
ALTER TABLE `facturation_fournisseur_ligne`
  MODIFY `id_facturation_fournisseur_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture`
--
ALTER TABLE `facture`
  MODIFY `id_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture_fournisseur`
--
ALTER TABLE `facture_fournisseur`
  MODIFY `id_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture_fournisseur_ligne`
--
ALTER TABLE `facture_fournisseur_ligne`
  MODIFY `id_facture_fournisseur_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture_ligne`
--
ALTER TABLE `facture_ligne`
  MODIFY `id_facture_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture_magasin`
--
ALTER TABLE `facture_magasin`
  MODIFY `id_facture_magasin` mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture_magasin_recu`
--
ALTER TABLE `facture_magasin_recu`
  MODIFY `id_facture_magasin_recu` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture_non_parvenue`
--
ALTER TABLE `facture_non_parvenue`
  MODIFY `id_facture_non_parvenue` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `facture_transaction`
--
ALTER TABLE `facture_transaction`
  MODIFY `id_facture_transaction` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `famille`
--
ALTER TABLE `famille`
  MODIFY `id_famille` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `filtre_defaut`
--
ALTER TABLE `filtre_defaut`
  MODIFY `id_filtre_defaut` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7065;

--
-- AUTO_INCREMENT pour la table `filtre_optima`
--
ALTER TABLE `filtre_optima`
  MODIFY `id_filtre_optima` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16758;

--
-- AUTO_INCREMENT pour la table `filtre_user`
--
ALTER TABLE `filtre_user`
  MODIFY `id_filtre_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_attestation_presence`
--
ALTER TABLE `formation_attestation_presence`
  MODIFY `id_formation_attestation_presence` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_bon_de_commande_fournisseur`
--
ALTER TABLE `formation_bon_de_commande_fournisseur`
  MODIFY `id_formation_bon_de_commande_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_commande`
--
ALTER TABLE `formation_commande`
  MODIFY `id_formation_commande` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_commande_fournisseur`
--
ALTER TABLE `formation_commande_fournisseur`
  MODIFY `id_formation_commande_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_devis`
--
ALTER TABLE `formation_devis`
  MODIFY `id_formation_devis` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_devis_fournisseur`
--
ALTER TABLE `formation_devis_fournisseur`
  MODIFY `id_formation_devis_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_devis_ligne`
--
ALTER TABLE `formation_devis_ligne`
  MODIFY `id_formation_devis_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_facture`
--
ALTER TABLE `formation_facture`
  MODIFY `id_formation_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_facture_fournisseur`
--
ALTER TABLE `formation_facture_fournisseur`
  MODIFY `id_formation_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_facture_fournisseur_ligne`
--
ALTER TABLE `formation_facture_fournisseur_ligne`
  MODIFY `id_formation_facture_fournisseur_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_participant`
--
ALTER TABLE `formation_participant`
  MODIFY `id_formation_participant` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation_priseEnCharge`
--
ALTER TABLE `formation_priseEnCharge`
  MODIFY `id_formation_priseEnCharge` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ged`
--
ALTER TABLE `ged`
  MODIFY `id_ged` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `importer`
--
ALTER TABLE `importer`
  MODIFY `id_importer` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `importer_ligne`
--
ALTER TABLE `importer_ligne`
  MODIFY `id_importer_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `import_facture_fournisseur`
--
ALTER TABLE `import_facture_fournisseur`
  MODIFY `id_import_facture_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `licence`
--
ALTER TABLE `licence`
  MODIFY `id_licence` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `licence_type`
--
ALTER TABLE `licence_type`
  MODIFY `id_licence_type` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `localisation_langue`
--
ALTER TABLE `localisation_langue`
  MODIFY `id_localisation_langue` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT pour la table `loyer`
--
ALTER TABLE `loyer`
  MODIFY `id_loyer` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `loyer_prolongation`
--
ALTER TABLE `loyer_prolongation`
  MODIFY `id_loyer_prolongation` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `magasin`
--
ALTER TABLE `magasin`
  MODIFY `id_magasin` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messagerie`
--
ALTER TABLE `messagerie`
  MODIFY `id_messagerie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `module`
--
ALTER TABLE `module`
  MODIFY `id_module` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=222;

--
-- AUTO_INCREMENT pour la table `news`
--
ALTER TABLE `news`
  MODIFY `id_news` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `opportunite`
--
ALTER TABLE `opportunite`
  MODIFY `id_opportunite` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pack_produit`
--
ALTER TABLE `pack_produit`
  MODIFY `id_pack_produit` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pack_produit_besoin`
--
ALTER TABLE `pack_produit_besoin`
  MODIFY `id_pack_produit_besoin` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pack_produit_fournisseur`
--
ALTER TABLE `pack_produit_fournisseur`
  MODIFY `id_pack_produit_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pack_produit_ligne`
--
ALTER TABLE `pack_produit_ligne`
  MODIFY `id_pack_produit_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pack_produit_produit`
--
ALTER TABLE `pack_produit_produit`
  MODIFY `id_pack_produit_produit` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id_panier` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `parc`
--
ALTER TABLE `parc`
  MODIFY `id_parc` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `password_reset`
--
ALTER TABLE `password_reset`
  MODIFY `id_password_reset` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pdf_affaire`
--
ALTER TABLE `pdf_affaire`
  MODIFY `id_pdf_affaire` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pdf_societe`
--
ALTER TABLE `pdf_societe`
  MODIFY `id_pdf_societe` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `phone`
--
ALTER TABLE `phone`
  MODIFY `id_phone` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `politesse`
--
ALTER TABLE `politesse`
  MODIFY `id_politesse` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `privilege`
--
ALTER TABLE `privilege`
  MODIFY `id_privilege` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `processeur`
--
ALTER TABLE `processeur`
  MODIFY `id_processeur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT pour la table `produit`
--
ALTER TABLE `produit`
  MODIFY `id_produit` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produit_besoins`
--
ALTER TABLE `produit_besoins`
  MODIFY `id_produit_besoins` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `produit_dd`
--
ALTER TABLE `produit_dd`
  MODIFY `id_produit_dd` smallint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=557;

--
-- AUTO_INCREMENT pour la table `produit_dotpitch`
--
ALTER TABLE `produit_dotpitch`
  MODIFY `id_produit_dotpitch` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `produit_env`
--
ALTER TABLE `produit_env`
  MODIFY `id_produit_env` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `produit_format`
--
ALTER TABLE `produit_format`
  MODIFY `id_produit_format` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `produit_fournisseur_loyer`
--
ALTER TABLE `produit_fournisseur_loyer`
  MODIFY `id_produit_fournisseur_loyer` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produit_garantie`
--
ALTER TABLE `produit_garantie`
  MODIFY `id_produit_garantie` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `produit_lan`
--
ALTER TABLE `produit_lan`
  MODIFY `id_produit_lan` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `produit_lecteur`
--
ALTER TABLE `produit_lecteur`
  MODIFY `id_produit_lecteur` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `produit_OS`
--
ALTER TABLE `produit_OS`
  MODIFY `id_produit_OS` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT pour la table `produit_puissance`
--
ALTER TABLE `produit_puissance`
  MODIFY `id_produit_puissance` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT pour la table `produit_ram`
--
ALTER TABLE `produit_ram`
  MODIFY `id_produit_ram` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT pour la table `produit_technique`
--
ALTER TABLE `produit_technique`
  MODIFY `id_produit_technique` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `produit_tel_produit`
--
ALTER TABLE `produit_tel_produit`
  MODIFY `id_produit_tel_produit` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `produit_tel_type`
--
ALTER TABLE `produit_tel_type`
  MODIFY `id_produit_tel_type` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `produit_type`
--
ALTER TABLE `produit_type`
  MODIFY `id_produit_type` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `produit_typeecran`
--
ALTER TABLE `produit_typeecran`
  MODIFY `id_produit_typeecran` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `produit_viewable`
--
ALTER TABLE `produit_viewable`
  MODIFY `id_produit_viewable` smallint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT pour la table `profil`
--
ALTER TABLE `profil`
  MODIFY `id_profil` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `profil_privilege`
--
ALTER TABLE `profil_privilege`
  MODIFY `id_profil_privilege` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4904;

--
-- AUTO_INCREMENT pour la table `prolongation`
--
ALTER TABLE `prolongation`
  MODIFY `id_prolongation` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `refinanceur`
--
ALTER TABLE `refinanceur`
  MODIFY `id_refinanceur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `region`
--
ALTER TABLE `region`
  MODIFY `id_region` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reglement`
--
ALTER TABLE `reglement`
  MODIFY `id_reglement` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `relance`
--
ALTER TABLE `relance`
  MODIFY `id_relance` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `relance_facture`
--
ALTER TABLE `relance_facture`
  MODIFY `id_relance_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `restitution_anticipee`
--
ALTER TABLE `restitution_anticipee`
  MODIFY `id_restitution_anticipee` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `scanner`
--
ALTER TABLE `scanner`
  MODIFY `id_scanner` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `secteur_commercial`
--
ALTER TABLE `secteur_commercial`
  MODIFY `id_secteur_commercial` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `secteur_geographique`
--
ALTER TABLE `secteur_geographique`
  MODIFY `id_secteur_geographique` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sell_and_sign`
--
ALTER TABLE `sell_and_sign`
  MODIFY `id_sell_and_sign` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `site_article`
--
ALTER TABLE `site_article`
  MODIFY `id_site_article` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `site_article_contenu`
--
ALTER TABLE `site_article_contenu`
  MODIFY `id_site_article_contenu` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `site_associe`
--
ALTER TABLE `site_associe`
  MODIFY `id_site_associe` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `site_en_tete`
--
ALTER TABLE `site_en_tete`
  MODIFY `id_site_en_tete` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `site_menu`
--
ALTER TABLE `site_menu`
  MODIFY `id_site_menu` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `site_offre`
--
ALTER TABLE `site_offre`
  MODIFY `id_site_offre` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `slimpay_transaction`
--
ALTER TABLE `slimpay_transaction`
  MODIFY `id_slimpay_transaction` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `societe`
--
ALTER TABLE `societe`
  MODIFY `id_societe` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `societe_domaine`
--
ALTER TABLE `societe_domaine`
  MODIFY `id_societe_domaine` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sous_categorie`
--
ALTER TABLE `sous_categorie`
  MODIFY `id_sous_categorie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `stat_snap`
--
ALTER TABLE `stat_snap`
  MODIFY `id_stat_snap` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suivi`
--
ALTER TABLE `suivi`
  MODIFY `id_suivi` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suivi_contact`
--
ALTER TABLE `suivi_contact`
  MODIFY `id_suivi_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suivi_notifie`
--
ALTER TABLE `suivi_notifie`
  MODIFY `id_suivi_notifie` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suivi_portail`
--
ALTER TABLE `suivi_portail`
  MODIFY `id_suivi_portail` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `suivi_societe`
--
ALTER TABLE `suivi_societe`
  MODIFY `id_suivi_societe` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tache`
--
ALTER TABLE `tache`
  MODIFY `id_tache` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tache_contact`
--
ALTER TABLE `tache_contact`
  MODIFY `id_tache_contact` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tache_user`
--
ALTER TABLE `tache_user`
  MODIFY `id_tache_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `termes`
--
ALTER TABLE `termes`
  MODIFY `id_termes` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `tracabilite`
--
ALTER TABLE `tracabilite`
  MODIFY `id_tracabilite` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `transaction_banque`
--
ALTER TABLE `transaction_banque`
  MODIFY `id_transaction_banque` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `transporteur`
--
ALTER TABLE `transporteur`
  MODIFY `id_transporteur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `type_affaire`
--
ALTER TABLE `type_affaire`
  MODIFY `id_type_affaire` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `type_affaire_params`
--
ALTER TABLE `type_affaire_params`
  MODIFY `id_type_affaire_params` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_portail`
--
ALTER TABLE `user_portail`
  MODIFY `id_user_portail` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vue`
--
ALTER TABLE `vue`
  MODIFY `id_vue` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `zonegeo`
--
ALTER TABLE `zonegeo`
  MODIFY `id_zonegeo` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affaire`
--
ALTER TABLE `affaire`
  ADD CONSTRAINT `affaire_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_2` FOREIGN KEY (`id_filiale`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_3` FOREIGN KEY (`id_fille`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_4` FOREIGN KEY (`id_parent`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_5` FOREIGN KEY (`id_partenaire`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_6` FOREIGN KEY (`id_apporteur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_ibfk_7` FOREIGN KEY (`id_commercial`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  ADD CONSTRAINT `affaire_etat_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_etat_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `assurance`
--
ALTER TABLE `assurance`
  ADD CONSTRAINT `assurance_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `base_de_connaissance`
--
ALTER TABLE `base_de_connaissance`
  ADD CONSTRAINT `base_de_connaissance_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Contraintes pour la table `bon_de_commande`
--
ALTER TABLE `bon_de_commande`
  ADD CONSTRAINT `bon_de_commande_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_3` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_4` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ibfk_5` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `bon_de_commande_ibfk_6` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `bon_de_commande_ligne`
--
ALTER TABLE `bon_de_commande_ligne`
  ADD CONSTRAINT `bon_de_commande_ligne_ibfk_1` FOREIGN KEY (`id_commande_ligne`) REFERENCES `commande_ligne` (`id_commande_ligne`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ligne_ibfk_2` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bon_de_commande_ligne_ibfk_3` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `colonne`
--
ALTER TABLE `colonne`
  ADD CONSTRAINT `colonne_ibfk_1` FOREIGN KEY (`id_vue`) REFERENCES `vue` (`id_vue`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `colonne_ibfk_2` FOREIGN KEY (`id_vue`) REFERENCES `vue` (`id_vue`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `comite`
--
ALTER TABLE `comite`
  ADD CONSTRAINT `comite_ibfk_1` FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur` (`id_refinanceur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comite_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `comite_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `comite_ibfk_4` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `commande`
  ADD CONSTRAINT `commande_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_2` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ibfk_4` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `commande_ligne`
--
ALTER TABLE `commande_ligne`
  ADD CONSTRAINT `commande_ligne_ibfk_3` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ligne_ibfk_4` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ligne_ibfk_5` FOREIGN KEY (`id_affaire_provenance`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commande_ligne_ibfk_7` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `demande_refi`
--
ALTER TABLE `demande_refi`
  ADD CONSTRAINT `demande_refi_ibfk_1` FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur` (`id_refinanceur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `demande_refi_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `demande_refi_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `demande_refi_ibfk_4` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `departement`
--
ALTER TABLE `departement`
  ADD CONSTRAINT `departement_ibfk_1` FOREIGN KEY (`id_region`) REFERENCES `region` (`id_region`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `devis_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_3` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_4` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_5` FOREIGN KEY (`id_opportunite`) REFERENCES `opportunite` (`id_opportunite`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ibfk_6` FOREIGN KEY (`id_filiale`) REFERENCES `societe` (`id_societe`);

--
-- Contraintes pour la table `devis_ligne`
--
ALTER TABLE `devis_ligne`
  ADD CONSTRAINT `devis_ligne_ibfk_1` FOREIGN KEY (`id_devis`) REFERENCES `devis` (`id_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ligne_ibfk_3` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ligne_ibfk_4` FOREIGN KEY (`id_affaire_provenance`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `devis_ligne_ibfk_5` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `document_revendeur`
--
ALTER TABLE `document_revendeur`
  ADD CONSTRAINT `document_revendeur_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `emailing_contact`
--
ALTER TABLE `emailing_contact`
  ADD CONSTRAINT `FK_emailing_contact_1` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `emailing_contact_ibfk_1` FOREIGN KEY (`id_emailing_source`) REFERENCES `emailing_source` (`id_emailing_source`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Contraintes pour la table `emailing_projet_lien`
--
ALTER TABLE `emailing_projet_lien`
  ADD CONSTRAINT `FK_emailing_projet_lien_1` FOREIGN KEY (`id_emailing_projet`) REFERENCES `emailing_projet` (`id_emailing_projet`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `FK_emailing_tracking_2` FOREIGN KEY (`id_emailing_projet_lien`) REFERENCES `emailing_projet_lien` (`id_emailing_projet_lien`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `exporter`
--
ALTER TABLE `exporter`
  ADD CONSTRAINT `FK_exporter_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_exporter_2` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `export_facture`
--
ALTER TABLE `export_facture`
  ADD CONSTRAINT `export_facture_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facturation`
--
ALTER TABLE `facturation`
  ADD CONSTRAINT `facturation_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturation_ibfk_4` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturation_ibfk_5` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `facturation_fournisseur`
--
ALTER TABLE `facturation_fournisseur`
  ADD CONSTRAINT `facturation_fournisseur_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturation_fournisseur_ibfk_3` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturation_fournisseur_ibfk_4` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `facturation_fournisseur_ligne`
--
ALTER TABLE `facturation_fournisseur_ligne`
  ADD CONSTRAINT `facturation_fournisseur_ligne_ibfk_1` FOREIGN KEY (`id_facturation_fournisseur`) REFERENCES `facturation_fournisseur` (`id_facturation_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facturation_fournisseur_ligne_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facturation_fournisseur_ligne_ibfk_3` FOREIGN KEY (`id_commande_ligne`) REFERENCES `commande_ligne` (`id_commande_ligne`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `facture_ibfk_2` FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur` (`id_refinanceur`),
  ADD CONSTRAINT `facture_ibfk_3` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`),
  ADD CONSTRAINT `facture_ibfk_4` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `facture_ibfk_5` FOREIGN KEY (`id_demande_refi`) REFERENCES `demande_refi` (`id_demande_refi`),
  ADD CONSTRAINT `facture_ibfk_6` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`),
  ADD CONSTRAINT `facture_ibfk_7` FOREIGN KEY (`id_commande`) REFERENCES `commande` (`id_commande`),
  ADD CONSTRAINT `facture_ibfk_8` FOREIGN KEY (`id_fournisseur_prepaiement`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_fournisseur`
--
ALTER TABLE `facture_fournisseur`
  ADD CONSTRAINT `facture_fournisseur_ibfk_1` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_fournisseur_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_fournisseur_ibfk_3` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_fournisseur_ligne`
--
ALTER TABLE `facture_fournisseur_ligne`
  ADD CONSTRAINT `facture_fournisseur_ligne_ibfk_2` FOREIGN KEY (`id_facture_fournisseur`) REFERENCES `facture_fournisseur` (`id_facture_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_fournisseur_ligne_ibfk_3` FOREIGN KEY (`id_bon_de_commande_ligne`) REFERENCES `bon_de_commande_ligne` (`id_bon_de_commande_ligne`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_fournisseur_ligne_ibfk_4` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_ligne`
--
ALTER TABLE `facture_ligne`
  ADD CONSTRAINT `facture_ligne_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ligne_ibfk_4` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ligne_ibfk_5` FOREIGN KEY (`id_affaire_provenance`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_ligne_ibfk_6` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_magasin`
--
ALTER TABLE `facture_magasin`
  ADD CONSTRAINT `facture_magasin_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_magasin_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_non_parvenue`
--
ALTER TABLE `facture_non_parvenue`
  ADD CONSTRAINT `facture_non_parvenue_ibfk_1` FOREIGN KEY (`id_facture_fournisseur`) REFERENCES `facture_fournisseur` (`id_facture_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_non_parvenue_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_non_parvenue_ibfk_3` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_non_parvenue_ibfk_4` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande` (`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Contraintes pour la table `formation_attestation_presence`
--
ALTER TABLE `formation_attestation_presence`
  ADD CONSTRAINT `formation_attestation_presence_ibfk_1` FOREIGN KEY (`id_formation_commande`) REFERENCES `formation_commande` (`id_formation_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_attestation_presence_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_attestation_presence_ibfk_3` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_bon_de_commande_fournisseur`
--
ALTER TABLE `formation_bon_de_commande_fournisseur`
  ADD CONSTRAINT `formation_bon_de_commande_fournisseur_ibfk_1` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_bon_de_commande_fournisseur_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_bon_de_commande_fournisseur_ibfk_3` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_bon_de_commande_fournisseur_ibfk_4` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_commande`
--
ALTER TABLE `formation_commande`
  ADD CONSTRAINT `formation_commande_ibfk_1` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_commande_fournisseur`
--
ALTER TABLE `formation_commande_fournisseur`
  ADD CONSTRAINT `formation_commande_fournisseur_ibfk_1` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_commande_fournisseur_ibfk_2` FOREIGN KEY (`id_formation_commande`) REFERENCES `formation_commande` (`id_formation_commande`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_commande_fournisseur_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_devis`
--
ALTER TABLE `formation_devis`
  ADD CONSTRAINT `formation_devis_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_devis_ibfk_3` FOREIGN KEY (`id_formateur`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `formation_devis_ibfk_4` FOREIGN KEY (`id_lieu_formation`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_devis_ibfk_5` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_devis_ibfk_6` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_devis_fournisseur`
--
ALTER TABLE `formation_devis_fournisseur`
  ADD CONSTRAINT `formation_devis_fournisseur_ibfk_2` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_devis_fournisseur_ibfk_3` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_devis_ligne`
--
ALTER TABLE `formation_devis_ligne`
  ADD CONSTRAINT `formation_devis_ligne_ibfk_1` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_facture`
--
ALTER TABLE `formation_facture`
  ADD CONSTRAINT `formation_facture_ibfk_1` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_facture_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_facture_fournisseur`
--
ALTER TABLE `formation_facture_fournisseur`
  ADD CONSTRAINT `formation_facture_fournisseur_ibfk_2` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_facture_fournisseur_ibfk_3` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_facture_fournisseur_ligne`
--
ALTER TABLE `formation_facture_fournisseur_ligne`
  ADD CONSTRAINT `formation_facture_fournisseur_ligne_ibfk_1` FOREIGN KEY (`id_formation_facture_fournisseur`) REFERENCES `formation_facture_fournisseur` (`id_formation_facture_fournisseur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_participant`
--
ALTER TABLE `formation_participant`
  ADD CONSTRAINT `formation_participant_ibfk_1` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_participant_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `formation_priseEnCharge`
--
ALTER TABLE `formation_priseEnCharge`
  ADD CONSTRAINT `formation_priseEnCharge_ibfk_1` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `formation_priseEnCharge_ibfk_2` FOREIGN KEY (`opca`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `ged`
--
ALTER TABLE `ged`
  ADD CONSTRAINT `ged_ibfk_1` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ged_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `ged_ibfk_3` FOREIGN KEY (`id_parent`) REFERENCES `ged` (`id_ged`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Contraintes pour la table `licence`
--
ALTER TABLE `licence`
  ADD CONSTRAINT `licence_ibfk_1` FOREIGN KEY (`id_commande_ligne`) REFERENCES `commande_ligne` (`id_commande_ligne`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `licence_ibfk_2` FOREIGN KEY (`id_licence_type`) REFERENCES `licence_type` (`id_licence_type`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `localisation_langue`
--
ALTER TABLE `localisation_langue`
  ADD CONSTRAINT `localisation_langue_ibfk_1` FOREIGN KEY (`id_pays`) REFERENCES `pays` (`id_pays`);

--
-- Contraintes pour la table `loyer`
--
ALTER TABLE `loyer`
  ADD CONSTRAINT `loyer_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `loyer_prolongation`
--
ALTER TABLE `loyer_prolongation`
  ADD CONSTRAINT `loyer_prolongation_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `loyer_prolongation_ibfk_2` FOREIGN KEY (`id_prolongation`) REFERENCES `prolongation` (`id_prolongation`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `magasin`
--
ALTER TABLE `magasin`
  ADD CONSTRAINT `magasin_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`);

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
-- Contraintes pour la table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `pack_produit`
--
ALTER TABLE `pack_produit`
  ADD CONSTRAINT `pack_produit_document` FOREIGN KEY (`id_document_contrat`) REFERENCES `document_contrat` (`id_document_contrat`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ibfk_1` FOREIGN KEY (`id_pack_produit_besoin`) REFERENCES `pack_produit_besoin` (`id_pack_produit_besoin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ibfk_2` FOREIGN KEY (`id_pack_produit_produit`) REFERENCES `pack_produit_produit` (`id_pack_produit_produit`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ibfk_3` FOREIGN KEY (`specifique_partenaire`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `pack_produit_fournisseur`
--
ALTER TABLE `pack_produit_fournisseur`
  ADD CONSTRAINT `pack_produit_fournisseur_ibfk_1` FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit` (`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_fournisseur_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_fournisseur_ibfk_3` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `pack_produit_ligne`
--
ALTER TABLE `pack_produit_ligne`
  ADD CONSTRAINT `pack_produit_ligne_ibfk_1` FOREIGN KEY (`id_partenaire`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ligne_ibfk_2` FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit` (`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ligne_ibfk_3` FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit` (`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ligne_ibfk_4` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ligne_ibfk_5` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ligne_ibfk_6` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ligne_ibfk_7` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_ligne_ibfk_8` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `parc`
--
ALTER TABLE `parc`
  ADD CONSTRAINT `parc_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `parc_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `parc_ibfk_6` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `pdf_societe`
--
ALTER TABLE `pdf_societe`
  ADD CONSTRAINT `pdf_societe_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `phone`
--
ALTER TABLE `phone`
  ADD CONSTRAINT `phone_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `phone_ibfk_2` FOREIGN KEY (`id_asterisk`) REFERENCES `asterisk` (`id_asterisk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `produit_ibfk_1` FOREIGN KEY (`id_sous_categorie`) REFERENCES `sous_categorie` (`id_sous_categorie`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_10` FOREIGN KEY (`id_produit_lecteur`) REFERENCES `produit_lecteur` (`id_produit_lecteur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_11` FOREIGN KEY (`id_produit_OS`) REFERENCES `produit_OS` (`id_produit_OS`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_12` FOREIGN KEY (`id_produit_puissance`) REFERENCES `produit_puissance` (`id_produit_puissance`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_13` FOREIGN KEY (`id_produit_ram`) REFERENCES `produit_ram` (`id_produit_ram`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_14` FOREIGN KEY (`id_produit_technique`) REFERENCES `produit_technique` (`id_produit_technique`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_15` FOREIGN KEY (`id_produit_type`) REFERENCES `produit_type` (`id_produit_type`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_16` FOREIGN KEY (`id_produit_typeecran`) REFERENCES `produit_typeecran` (`id_produit_typeecran`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_17` FOREIGN KEY (`id_produit_viewable`) REFERENCES `produit_viewable` (`id_produit_viewable`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_19` FOREIGN KEY (`id_processeur`) REFERENCES `processeur` (`id_processeur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_2` FOREIGN KEY (`id_fabriquant`) REFERENCES `fabriquant` (`id_fabriquant`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_20` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_21` FOREIGN KEY (`id_produit_dd`) REFERENCES `produit_dd` (`id_produit_dd`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_22` FOREIGN KEY (`id_produit_dotpitch`) REFERENCES `produit_dotpitch` (`id_produit_dotpitch`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_23` FOREIGN KEY (`id_produit_format`) REFERENCES `produit_format` (`id_produit_format`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_24` FOREIGN KEY (`id_produit_garantie_uc`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_25` FOREIGN KEY (`id_produit_garantie_ecran`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_26` FOREIGN KEY (`id_produit_garantie_imprimante`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_27` FOREIGN KEY (`id_produit_lan`) REFERENCES `produit_lan` (`id_produit_lan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_28` FOREIGN KEY (`id_produit_lecteur`) REFERENCES `produit_lecteur` (`id_produit_lecteur`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_29` FOREIGN KEY (`id_produit_OS`) REFERENCES `produit_OS` (`id_produit_OS`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_3` FOREIGN KEY (`id_produit_dd`) REFERENCES `produit_dd` (`id_produit_dd`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_30` FOREIGN KEY (`id_produit_puissance`) REFERENCES `produit_puissance` (`id_produit_puissance`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_31` FOREIGN KEY (`id_produit_ram`) REFERENCES `produit_ram` (`id_produit_ram`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_32` FOREIGN KEY (`id_produit_technique`) REFERENCES `produit_technique` (`id_produit_technique`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_33` FOREIGN KEY (`id_produit_type`) REFERENCES `produit_type` (`id_produit_type`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_34` FOREIGN KEY (`id_produit_typeecran`) REFERENCES `produit_typeecran` (`id_produit_typeecran`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_35` FOREIGN KEY (`id_produit_viewable`) REFERENCES `produit_viewable` (`id_produit_viewable`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_36` FOREIGN KEY (`id_processeur`) REFERENCES `processeur` (`id_processeur`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_37` FOREIGN KEY (`id_produit_env`) REFERENCES `produit_env` (`id_produit_env`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `produit_ibfk_38` FOREIGN KEY (`id_produit_besoins`) REFERENCES `produit_besoins` (`id_produit_besoins`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `produit_ibfk_39` FOREIGN KEY (`id_produit_tel_type`) REFERENCES `produit_tel_type` (`id_produit_tel_type`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `produit_ibfk_4` FOREIGN KEY (`id_produit_dotpitch`) REFERENCES `produit_dotpitch` (`id_produit_dotpitch`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_40` FOREIGN KEY (`id_produit_tel_produit`) REFERENCES `produit_tel_produit` (`id_produit_tel_produit`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `produit_ibfk_41` FOREIGN KEY (`id_licence_type`) REFERENCES `licence_type` (`id_licence_type`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_42` FOREIGN KEY (`id_document_contrat`) REFERENCES `document_contrat` (`id_document_contrat`),
  ADD CONSTRAINT `produit_ibfk_5` FOREIGN KEY (`id_produit_format`) REFERENCES `produit_format` (`id_produit_format`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_6` FOREIGN KEY (`id_produit_garantie_uc`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_7` FOREIGN KEY (`id_produit_garantie_ecran`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_8` FOREIGN KEY (`id_produit_garantie_imprimante`) REFERENCES `produit_garantie` (`id_produit_garantie`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_ibfk_9` FOREIGN KEY (`id_produit_lan`) REFERENCES `produit_lan` (`id_produit_lan`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `produit_fournisseur_loyer`
--
ALTER TABLE `produit_fournisseur_loyer`
  ADD CONSTRAINT `produit_fournisseur_loyer_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produit_fournisseur_loyer_ibfk_2` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `profil_privilege`
--
ALTER TABLE `profil_privilege`
  ADD CONSTRAINT `profil_privilege_ibfk_1` FOREIGN KEY (`id_profil`) REFERENCES `profil` (`id_profil`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profil_privilege_ibfk_2` FOREIGN KEY (`id_privilege`) REFERENCES `privilege` (`id_privilege`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profil_privilege_ibfk_3` FOREIGN KEY (`id_module`) REFERENCES `module` (`id_module`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `prolongation`
--
ALTER TABLE `prolongation`
  ADD CONSTRAINT `prolongation_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prolongation_ibfk_2` FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur` (`id_refinanceur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `prolongation_ibfk_3` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `relance`
--
ALTER TABLE `relance`
  ADD CONSTRAINT `relance_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relance_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `relance_facture`
--
ALTER TABLE `relance_facture`
  ADD CONSTRAINT `relance_facture_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relance_facture_ibfk_2` FOREIGN KEY (`id_relance`) REFERENCES `relance` (`id_relance`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `restitution_anticipee`
--
ALTER TABLE `restitution_anticipee`
  ADD CONSTRAINT `restitution_anticipee_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sell_and_sign`
--
ALTER TABLE `sell_and_sign`
  ADD CONSTRAINT `sell_and_sign_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `site_article`
--
ALTER TABLE `site_article`
  ADD CONSTRAINT `site_article_ibfk_1` FOREIGN KEY (`id_site_menu`) REFERENCES `site_menu` (`id_site_menu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `site_article_ibfk_2` FOREIGN KEY (`id_site_menu`) REFERENCES `site_menu` (`id_site_menu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `site_article_contenu`
--
ALTER TABLE `site_article_contenu`
  ADD CONSTRAINT `site_article_contenu_ibfk_1` FOREIGN KEY (`id_site_article`) REFERENCES `site_article` (`id_site_article`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `site_associe`
--
ALTER TABLE `site_associe`
  ADD CONSTRAINT `site_associe_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `client` (`id_client`),
  ADD CONSTRAINT `site_associe_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`),
  ADD CONSTRAINT `site_associe_ibfk_3` FOREIGN KEY (`id_type_affaire`) REFERENCES `type_affaire` (`id_type_affaire`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `slimpay_transaction`
--
ALTER TABLE `slimpay_transaction`
  ADD CONSTRAINT `slimpay_transaction_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `societe`
--
ALTER TABLE `societe`
  ADD CONSTRAINT `societe_ibfk_1` FOREIGN KEY (`id_prospection`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_2` FOREIGN KEY (`id_owner`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_3` FOREIGN KEY (`id_contact_facturation`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_4` FOREIGN KEY (`id_campagne`) REFERENCES `campagne` (`id_campagne`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `societe_ibfk_assistante` FOREIGN KEY (`id_assistante`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Contraintes pour la table `suivi`
--
ALTER TABLE `suivi`
  ADD CONSTRAINT `suivi_ibfk_4` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_ibfk_5` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_ibfk_6` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_ibfk_7` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_ibfk_8` FOREIGN KEY (`id_formation_devis`) REFERENCES `formation_devis` (`id_formation_devis`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Contraintes pour la table `suivi_portail_contact`
--
ALTER TABLE `suivi_portail_contact`
  ADD CONSTRAINT `suivi_portail_contact_ibfk_1` FOREIGN KEY (`id_suivi_portail`) REFERENCES `suivi_portail` (`id_suivi_portail`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suivi_portail_contact_ibfk_2` FOREIGN KEY (`id_suivi_portail`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `tache_ibfk_4` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_ibfk_6` FOREIGN KEY (`id_suivi`) REFERENCES `suivi` (`id_suivi`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_ibfk_7` FOREIGN KEY (`id_aboutisseur`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_ibfk_8` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `tache_contact`
--
ALTER TABLE `tache_contact`
  ADD CONSTRAINT `tache_contact_ibfk_1` FOREIGN KEY (`id_tache`) REFERENCES `tache` (`id_tache`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tache_contact_ibfk_2` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Contraintes pour la table `transaction_banque`
--
ALTER TABLE `transaction_banque`
  ADD CONSTRAINT `id_affaire` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`id_societe`) REFERENCES `societe` (`id_societe`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_3` FOREIGN KEY (`id_superieur`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `user_ibfk_4` FOREIGN KEY (`id_profil`) REFERENCES `profil` (`id_profil`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_5` FOREIGN KEY (`id_agence`) REFERENCES `agence` (`id_agence`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_6` FOREIGN KEY (`id_profil`) REFERENCES `profil` (`id_profil`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_ibfk_7` FOREIGN KEY (`id_localisation_langue`) REFERENCES `localisation_langue` (`id_localisation_langue`);

--
-- Contraintes pour la table `vue`
--
ALTER TABLE `vue`
  ADD CONSTRAINT `vue_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vue_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
