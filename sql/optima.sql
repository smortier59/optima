--
-- Base de données :  `extranet_v3_absystech`
--

-- --------------------------------------------------------

--
-- Structure de la table `export_comptable`
--

CREATE TABLE `export_comptable` (
  `id_export_comptable` mediumint(8) UNSIGNED NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `factures` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `export_comptable`
--
ALTER TABLE `export_comptable`
  ADD PRIMARY KEY (`id_export_comptable`),
  ADD KEY `date_debut` (`date_debut`,`date_fin`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `export_comptable`
--
ALTER TABLE `export_comptable`
  MODIFY `id_export_comptable` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `export_comptable`
--
ALTER TABLE `export_comptable`
  ADD CONSTRAINT `export_comptable_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Ajout du champs et de la contraite pour la liaison avec l'export comptable dans la table des factures
--
ALTER TABLE `facture` ADD `id_export_comptable` MEDIUMINT(8) UNSIGNED NULL AFTER `id_echeancier`, ADD INDEX (`id_export_comptable`);
ALTER TABLE `facture` ADD FOREIGN KEY (`id_export_comptable`) REFERENCES `export_comptable`(`id_export_comptable`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ajout du champs, de l'index et de la clé unique pour la référence comptable
--
ALTER TABLE `societe` ADD `ref_comptable` VARCHAR(20) NULL AFTER `ref`, ADD INDEX (`ref_comptable`);
ALTER TABLE `societe` ADD UNIQUE(`ref_comptable`);
