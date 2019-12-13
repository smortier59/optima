ALTER TABLE `commande_ligne` ADD `frequence_fournisseur` ENUM('sans','mois','bimestre','trimestre','quadrimestre','semestre','an') NOT NULL DEFAULT 'sans' COMMENT 'Frequence des commandes fournisseurs' AFTER `ordre`;
ALTER TABLE `devis_ligne`  ADD `frequence_fournisseur` ENUM('sans','mois','bimestre','trimestre','quadrimestre','semestre','an') NOT NULL DEFAULT 'sans' COMMENT 'Récurrence des commande fournisseur pour ce produit'  AFTER `ordre`;

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

--
-- Index pour les tables déchargées
--

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
-- Index pour la table `facture_transaction`
--
ALTER TABLE `facture_transaction`
  ADD PRIMARY KEY (`id_facture_transaction`),
  ADD KEY `id_facture` (`id_facture`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

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
-- AUTO_INCREMENT pour la table `facture_transaction`
--
ALTER TABLE `facture_transaction`
  MODIFY `id_facture_transaction` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

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


ALTER TABLE `magasin` ADD `adresse` VARCHAR(64) NULL DEFAULT NULL AFTER `id_societe`, ADD `adresse_2` VARCHAR(64) NULL DEFAULT NULL AFTER `adresse`, ADD `adresse_3` VARCHAR(64) NULL DEFAULT NULL AFTER `adresse_2`, ADD `cp` VARCHAR(64) NULL DEFAULT NULL AFTER `adresse_3`, ADD `ville` VARCHAR(64) NULL DEFAULT NULL AFTER `cp`;

ALTER TABLE `magasin` CHANGE `site_associe` `site_associe` ENUM('toshiba','btwin','bdomplus','boulanger-cafe') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `pack_produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','top office','burger king','flunch','toshiba','btwin','boulangerpro','bdomplus','sans','boulanger-cafe') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `pack_produit` CHANGE `frequence` `frequence` ENUM('jour','mois','trimestre','semestre','an','bimestre') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'mois';

ALTER TABLE `pack_produit` ADD `val_plancher` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Total de point minimum possible pour ce pack' AFTER `prolongation`, ADD `val_plafond` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Total de point maximum possible pour ce pack' AFTER `val_plancher`, ADD `max_qte` INT(10) NULL AFTER `val_plafond`;

ALTER TABLE `pack_produit_ligne` ADD `frequence_fournisseur` ENUM('mois','bimestre','trimestre','quadrimestre','semestre','an','sans') NOT NULL DEFAULT 'sans' COMMENT 'Fréquence' AFTER `id_fournisseur`;

ALTER TABLE `pack_produit_ligne` ADD `val_modifiable` ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Ce produit est il modifiable au mois le mois' AFTER `principal`, ADD `valeur` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT 'Nombre de point pour ce produit ' AFTER `val_modifiable`;

ALTER TABLE `produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','toshiba','btwin','boulangerpro','boulanger-cafe') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `produit` ADD `increment` INT(11) NOT NULL AFTER `id_licence_type`;
