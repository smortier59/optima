
ALTER TABLE `societe` CHANGE `lead` `lead` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

CREATE TABLE `affaire_etat` (
  `id_affaire_etat` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` enum('reception_demande','reception_pj','preparation_commande','refus_dossier','expedition_en_cours','colis_recu','valide_administratif','comite_cleodis_valide','comite_cleodis_refuse','refus_administratif') NOT NULL,
  `commentaire` varchar(500) DEFAULT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  ADD PRIMARY KEY (`id_affaire_etat`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  MODIFY `id_affaire_etat` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  ADD CONSTRAINT `affaire_etat_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_etat_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
