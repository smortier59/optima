ALTER TABLE `contact` ADD `login` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Login espace client' AFTER `disponibilite`, ADD `pwd` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Mot de passe espace client' AFTER `login`;

ALTER TABLE `affaire` ADD `suivi_ec` BOOLEAN NOT NULL DEFAULT FALSE COMMENT 'Flag d\'activation du suivi sur l\'espace client' AFTER `nature`;

--
-- Structure de la table `jalon`
--

CREATE TABLE `jalon` (
  `id_jalon` mediumint(8) UNSIGNED NOT NULL,
  `jalon` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `jalon`
--

INSERT INTO `jalon` (`id_jalon`, `jalon`, `module`, `category`) VALUES
(1, 'Préparation en cours', 'affaire', 'absystech'),
(2, 'Préparation terminé. Expédition programmé', 'affaire', 'absystech'),
(3, 'Colis remis au transporteur', 'affaire', 'absystech'),
(4, 'Colis livré par le transporteur', 'affaire', 'absystech'),
(5, 'Le système a détecté les téléphones', 'affaire', 'absystech');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `jalon`
--
ALTER TABLE `jalon`
  ADD PRIMARY KEY (`id_jalon`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `jalon`
--
ALTER TABLE `jalon`
  MODIFY `id_jalon` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


--
-- Structure de la table `affaire_etat`
--

CREATE TABLE `affaire_etat` (
  `id_affaire_etat` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_contact` mediumint(8) UNSIGNED DEFAULT NULL,
  `id_jalon` mediumint(8) UNSIGNED NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour la table `affaire_etat`
--
ALTER TABLE `affaire_etat`
  ADD PRIMARY KEY (`id_affaire_etat`),
  ADD KEY `id_affaire` (`id_affaire`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_jalon` (`id_jalon`);

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
  ADD CONSTRAINT `affaire_etat_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_etat_ibfk_3` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `affaire_etat_ibfk_4` FOREIGN KEY (`id_jalon`) REFERENCES `jalon` (`id_jalon`) ON DELETE CASCADE ON UPDATE CASCADE;

