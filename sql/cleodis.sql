ALTER TABLE `affaire` CHANGE `type_affaire` `type_affaire`
ENUM('normal',
  '2SI',
'Boulanger Pro',
'Consommables_com',
'DIB',
'Dyadem',
'FLEXFUEL',
'Instore',
'LAFI',
'Manganelli',
'NRC',
'OLISYS - Ma Solution IT',
'Proxi Pause',
'Trekk',
'ZENCONNECT – ZEN PACK',
'Hexamed Leasing',
'LFS') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'normal';



# Création des vues pour l'espace CLIENT pro CLEODIS
CREATE VIEW coordonnees_client AS SELECT id_societe, ref, societe, famille.famille as type_client,  nom_commercial, adresse, adresse_2, adresse_3, cp, ville, facturation_adresse, facturation_adresse_2, facturation_adresse_3, facturation_cp, facturation_ville, livraison_adresse, livraison_adresse_2, livraison_adresse_3, livraison_cp, livraison_ville, email, tel,particulier_civilite, particulier_nom, particulier_prenom, particulier_portable, num_carte_fidelite, particulier_fixe, particulier_email
FROM societe
INNER JOIN famille ON societe.id_famille = societe.id_famille;



CREATE VIEW factures_client AS SELECT id_facture, ref, ref_externe, id_societe, prix, etat, date, date_paiement, type_facture, date_periode_debut, date_periode_fin, tva, id_affaire, mode_paiement, nature, rejet, date_rejet, date_regularisation FROM facture;


CREATE VIEW parc_client AS SELECT id_societe, ref, libelle, divers, serial, code, date, date_inactif, date_garantie, date_achat, existence FROM parc;

CREATE VIEW affaire_client AS SELECT id_societe, id_affaire, ref, ref_externe, date, affaire, id_parent, id_fille, nature, date_garantie, site_associe, mail_signature, date_signature, signataire, langue, adresse_livraison, adresse_livraison_2, adresse_livraison_3, cp_adresse_livraison as adresse_livraison_cp, ville_adresse_livraison as adresse_livraison_ville, adresse_facturation, adresse_facturation_2, adresse_facturation_3, cp_adresse_facturation as adresse_facturation_cp, ville_adresse_facturation as adresse_facturation_ville, id_magasin FROM affaire;


CREATE VIEW abonnement_client AS SELECT commande.id_societe, commande.id_affaire, commande.ref AS num_dossier, commande.commande AS dossier, commande.etat as statut, commande.date, date_debut, date_evolution AS date_fin, date_arret
FROM commande
INNER JOIN affaire ON commande.id_affaire = affaire.id_affaire
WHERE affaire.etat NOT IN ("devis", "perdue", "demande_refi", "facture_refi");


CREATE VIEW historique_affaire AS SELECT id_affaire, date, etat, commentaire FROM affaire_etat ORDER BY id_affaire_etat ASC;

CREATE VIEW loyer_affaire AS SELECT id_affaire, type,
									COALESCE(loyer,0) +
									COALESCE(assurance,0) +
									COALESCE(frais_de_gestion,0) +
									COALESCE(serenite,0) +
									COALESCE(maintenance,0) +
									COALESCE(hotline,0) +
									COALESCE(supervision,0) +
									COALESCE(support,0) AS loyer, frequence_loyer AS frequence FROM loyer;


CREATE VIEW abonnement_detail AS SELECT commande.id_affaire, commande_ligne.ref AS ref, produit, quantite
FROM commande_ligne
INNER JOIN commande ON commande_ligne.id_commande = commande.id_commande
WHERE visible = "oui"
ORDER BY commande.id_affaire ASC, ordre ASC;

--- MISE A NIVEAU BDD CLEODIS

ALTER TABLE `commande_ligne` ADD `frequence_fournisseur` ENUM('sans','mois','bimestre','trimestre','quadrimestre','semestre','an') CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT 'sans' COMMENT 'Frequence des commandes fournisseurs' AFTER `ordre`;
ALTER TABLE `devis_ligne` ADD `frequence_fournisseur` ENUM('sans','mois','bimestre','trimestre','quadrimestre','semestre','an') CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci NOT NULL DEFAULT 'sans' COMMENT 'Récurrence des commande fournisseur pour ce produit' AFTER `ordre`;
ALTER TABLE `facture` ADD `ref_magasin` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL AFTER `ref_externe`;
ALTER TABLE `magasin` CHANGE `site_associe` `site_associe` ENUM('toshiba','btwin','bdomplus','boulanger-cafe') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `magasin` ADD `adresse` VARCHAR(64) NULL DEFAULT NULL AFTER `id_societe`, ADD `adresse_2` VARCHAR(64) NULL DEFAULT NULL AFTER `adresse`, ADD `adresse_3` VARCHAR(64) NULL DEFAULT NULL AFTER `adresse_2`, ADD `cp` VARCHAR(64) NULL DEFAULT NULL AFTER `adresse_3`, ADD `ville` VARCHAR(64) NULL DEFAULT NULL AFTER `cp`, ADD `statut` ENUM('ouvert','ferme') NOT NULL DEFAULT 'ouvert' AFTER `ville`;
ALTER TABLE `pack_produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','top office','burger king','flunch','toshiba','btwin','boulangerpro','bdomplus','sans','boulanger-cafe') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `pack_produit` CHANGE `frequence` `frequence` ENUM('jour','mois','trimestre','semestre','an','bimestre') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'mois';
ALTER TABLE `pack_produit` ADD `prolongation` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `id_document_contrat`, ADD `val_plancher` INT(10) NOT NULL DEFAULT '0' COMMENT 'Total de point minimum possible pour ce pack' AFTER `prolongation`, ADD `val_plafond` INT(10) NOT NULL DEFAULT '0' COMMENT 'Total de point maximum possible pour ce pack' AFTER `val_plancher`, ADD `max_qte` INT(10) NULL DEFAULT NULL AFTER `val_plafond`;
ALTER TABLE `pack_produit_ligne` ADD `frequence_fournisseur` ENUM('mois','bimestre','trimestre','quadrimestre','semestre','an','sans') CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL DEFAULT 'sans' COMMENT 'Fréquence' AFTER `id_fournisseur`;
ALTER TABLE `pack_produit_ligne` ADD `val_modifiable` ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Ce produit est il modifiable au mois le mois' AFTER `principal`, ADD `valeur` INT(10) UNSIGNED NOT NULL COMMENT 'Nombre de point pour ce produit' AFTER `val_modifiable`;
ALTER TABLE `produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `produit` ADD `id_licence_type` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `ref_garantie`, ADD `increment` INT(10) NOT NULL AFTER `id_licence_type`, ADD INDEX (`id_licence_type`);
ALTER TABLE `produit` ADD FOREIGN KEY (`id_licence_type`) REFERENCES `licence_type`(`id_licence_type`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `pack_produit_ligne` CHANGE `valeur` `valeur` INT(10) UNSIGNED NULL COMMENT 'Nombre de point pour ce produit';

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de données :  `dev_optima_bdomplus`
--

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
  `bundle_id` varchar(255) NOT NULL,
  `statut` enum('non_traite','traite','stocke','absent','abandonne') NOT NULL DEFAULT 'non_traite' COMMENT 'Different statut utilisé par le script de traitement des liasses ',
  `certificat_de_preuve` enum('present','absent') NOT NULL DEFAULT 'absent',
  `contrat_signe` enum('present','absent') NOT NULL DEFAULT 'absent',
  `etat` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `export_facture`
--
ALTER TABLE `export_facture`
  ADD PRIMARY KEY (`id_export_facture`),
  ADD KEY `id_facture` (`id_facture`);

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
-- Index pour la table `facture_transaction`
--
ALTER TABLE `facture_transaction`
  ADD PRIMARY KEY (`id_facture_transaction`),
  ADD KEY `id_facture` (`id_facture`);

--
-- Index pour la table `pack_produit_fournisseur`
--
ALTER TABLE `pack_produit_fournisseur`
  ADD PRIMARY KEY (`id_pack_produit_fournisseur`),
  ADD KEY `id_pack_produit` (`id_pack_produit`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_fournisseur` (`id_fournisseur`);

--
-- Index pour la table `sell_and_sign`
--
ALTER TABLE `sell_and_sign`
  ADD PRIMARY KEY (`id_sell_and_sign`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `certificat_de_preuve` (`certificat_de_preuve`),
  ADD KEY `contrat_signe` (`contrat_signe`);

--
-- Index pour la table `slimpay_transaction`
--
ALTER TABLE `slimpay_transaction`
  ADD PRIMARY KEY (`id_slimpay_transaction`),
  ADD KEY `id_facture` (`id_facture`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `export_facture`
--
ALTER TABLE `export_facture`
  MODIFY `id_export_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT pour la table `facture_transaction`
--
ALTER TABLE `facture_transaction`
  MODIFY `id_facture_transaction` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pack_produit_fournisseur`
--
ALTER TABLE `pack_produit_fournisseur`
  MODIFY `id_pack_produit_fournisseur` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sell_and_sign`
--
ALTER TABLE `sell_and_sign`
  MODIFY `id_sell_and_sign` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `slimpay_transaction`
--
ALTER TABLE `slimpay_transaction`
  MODIFY `id_slimpay_transaction` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `export_facture`
--
ALTER TABLE `export_facture`
  ADD CONSTRAINT `export_facture_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Contraintes pour la table `facture_magasin`
--
ALTER TABLE `facture_magasin`
  ADD CONSTRAINT `facture_magasin_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE,
  ADD CONSTRAINT `facture_magasin_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `pack_produit_fournisseur`
--
ALTER TABLE `pack_produit_fournisseur`
  ADD CONSTRAINT `pack_produit_fournisseur_ibfk_1` FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit` (`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_fournisseur_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id_produit`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pack_produit_fournisseur_ibfk_3` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe` (`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `sell_and_sign`
--
ALTER TABLE `sell_and_sign`
  ADD CONSTRAINT `sell_and_sign_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `slimpay_transaction`
--
ALTER TABLE `slimpay_transaction`
  ADD CONSTRAINT `slimpay_transaction_ibfk_1` FOREIGN KEY (`id_facture`) REFERENCES `facture` (`id_facture`) ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;



-- MISE A NIVEAU BDD BOULANGER

ALTER TABLE `parc` ADD `id_bon_de_commande` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `existence`, ADD INDEX (`id_bon_de_commande`);
ALTER TABLE `parc` ADD CONSTRAINT `parc_ibfk_1` FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `parc` ADD CONSTRAINT `parc_ibfk_3` FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `parc` ADD CONSTRAINT `parc_ibfk_6` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande`(`id_bon_de_commande`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `pdf_societe` ADD `date_expiration` DATE NULL DEFAULT NULL AFTER `nom_document`;
ALTER TABLE `produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;



-- Lot JUIN - Mise en place sauvegarde doc S&Sign depuis portail sign
ALTER TABLE `sell_and_sign` CHANGE `bundle_id` `bundle_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;