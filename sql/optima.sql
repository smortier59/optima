ALTER TABLE `stat_snap` ADD `data` VARCHAR(2048) NULL DEFAULT NULL AFTER `valeur`;

ALTER TABLE `stat_snap` CHANGE `valeur` `valeur` FLOAT NULL;

ALTER TABLE `alerte` ADD UNIQUE( `id_hotline`, `nature`, `id_hotline_interaction`);

ALTER TABLE `echeancier` CHANGE `jour_paiement` `jour_facture` ENUM('1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','fin_mois') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1';

ALTER TABLE `echeancier` ADD `mise_en_service` DATE NOT NULL AFTER `commentaire`, ADD `methode_reglement` ENUM('prelevement','reception','jours') NOT NULL AFTER `mise_en_service`;
ALTER TABLE `echeancier` ADD `prochaine_echeance` DATE NOT NULL AFTER `methode_reglement`;

--
-- Tables echeancier_ligne_ponctuelle & echeancier_ligne_periodique
-- Charlier cyril <ccharlier@absystech.fr>

CREATE TABLE `echeancier_ligne_ponctuelle` (
  `id_echeancier_ponctuel` mediumint(8) UNSIGNED NOT NULL,
  `designation` varchar(255) NOT NULL,
  `id_echeancier` mediumint(8) UNSIGNED NOT NULL,
  `quantite` int(11) NOT NULL,
  `total` float NOT NULL,
  `puht` float NOT NULL,
  `date_valeur` date NOT NULL,
  `ventilation_analytique` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Index pour la table `echeancier_ligne_ponctuelle`
--
ALTER TABLE `echeancier_ligne_ponctuelle`
  ADD PRIMARY KEY (`id_echeancier_ponctuel`),
  ADD KEY `id_echeancier` (`id_echeancier`),
  ADD KEY `ventilation_analytique` (`ventilation_analytique`);

--
-- AUTO_INCREMENT pour la table `echeancier_ligne_ponctuelle`
--
ALTER TABLE `echeancier_ligne_ponctuelle`
  MODIFY `id_echeancier_ponctuel` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour la table `echeancier_ligne_ponctuelle`
--
ALTER TABLE `echeancier_ligne_ponctuelle`
  ADD CONSTRAINT `echeancier_ligne_ponctuelle_ibfk_1` FOREIGN KEY (`id_echeancier`) REFERENCES `echeancier` (`id_echeancier`) ON UPDATE CASCADE,
  ADD CONSTRAINT `echeancier_ligne_ponctuelle_ibfk_2` FOREIGN KEY (`ventilation_analytique`) REFERENCES `compte_absystech` (`id_compte_absystech`) ON UPDATE CASCADE;


CREATE TABLE `echeancier_ligne_periodique` (
  `id_echeancier_periodique` mediumint(8) UNSIGNED NOT NULL,
  `designation` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL,
  `puht` float NOT NULL,
  `total` float NOT NULL,
  `valeur_variable` enum('oui','non') NOT NULL,
  `facture_prorataisee` enum('oui','non') NOT NULL,
  `mise_en_service` date NOT NULL,
  `ventilation_analytique` mediumint(8) UNSIGNED NOT NULL,
  `id_echeancier` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour la table `echeancier_ligne_periodique`
--
ALTER TABLE `echeancier_ligne_periodique`
  ADD PRIMARY KEY (`id_echeancier_periodique`),
  ADD KEY `ventilation_analytique` (`ventilation_analytique`),
  ADD KEY `id_echeancier` (`id_echeancier`);


--
-- AUTO_INCREMENT pour la table `echeancier_ligne_periodique`
--
ALTER TABLE `echeancier_ligne_periodique`
  MODIFY `id_echeancier_periodique` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour la table `echeancier_ligne_periodique`
--
ALTER TABLE `echeancier_ligne_periodique`
  ADD CONSTRAINT `echeancier_ligne_periodique_ibfk_1` FOREIGN KEY (`id_echeancier`) REFERENCES `echeancier` (`id_echeancier`) ON UPDATE CASCADE,
  ADD CONSTRAINT `echeancier_ligne_periodique_ibfk_2` FOREIGN KEY (`ventilation_analytique`) REFERENCES `compte_absystech` (`id_compte_absystech`) ON UPDATE CASCADE;

ALTER TABLE `echeancier_ligne_periodique` ADD `ref` VARCHAR(32) NOT NULL AFTER `id_compte_absystech`;

ALTER TABLE `echeancier_ligne_ponctuelle` ADD `ref` VARCHAR(32) NOT NULL AFTER `date_valeur`;
ALTER TABLE `echeancier_ligne_periodique` DROP `total`;
ALTER TABLE `echeancier_ligne_periodique` DROP `total`;

ALTER TABLE `echeancier_ligne_periodique` CHANGE `quantite` `quantite` FLOAT NOT NULL;
ALTER TABLE `echeancier_ligne_ponctuelle` CHANGE `quantite` `quantite` FLOAT NOT NULL;
ALTER TABLE `echeancier_ligne_periodique` ADD INDEX(`ref`);
ALTER TABLE `echeancier_ligne_ponctuelle` ADD INDEX(`ref`);