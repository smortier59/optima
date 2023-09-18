ALTER TABLE `societe` ADD `province` VARCHAR(200) NULL DEFAULT NULL AFTER `ville`;
ALTER TABLE `societe` ADD `facturation_province` VARCHAR(200) NULL DEFAULT NULL AFTER `facturation_ville`;

ALTER TABLE affaire ADD `date_demarrage_previsionnel` DATE NULL AFTER `date_garantie`;
ALTER TABLE societe ADD DNI VARCHAR(100) NULL AFTER CIF;
ALTER TABLE societe ADD adresse_banque VARCHAR(500) NULL DEFAULT NULL AFTER `nom_banque`;
ALTER TABLE societe ADD cp_banque VARCHAR(50) NULL DEFAULT NULL AFTER `adresse_banque`;
ALTER TABLE societe ADD province_banque VARCHAR(200) NULL DEFAULT NULL AFTER `ville_banque`;

ALTER TABLE affaire ADD adresse_banque VARCHAR(500) NULL DEFAULT NULL AFTER `nom_banque`;
ALTER TABLE affaire ADD cp_banque VARCHAR(50) NULL DEFAULT NULL AFTER `adresse_banque`;
ALTER TABLE affaire ADD province_banque VARCHAR(200) NULL DEFAULT NULL AFTER `ville_banque`;

ALTER TABLE contact ADD `num_dni` VARCHAR(50) NULL AFTER `id_contact`;
ALTER TABLE contact ADD `date_autorisation_pouvoir` VARCHAR(50) NULL;
ALTER TABLE contact ADD `num_ordre_notaire` VARCHAR(50) NULL;
ALTER TABLE `contact` ADD `province` VARCHAR(255) NOT NULL AFTER `ville`;

CREATE TABLE societe_structure (
    `id_societe_structure` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `structure` VARCHAR(255) NOT NULL,
    `type` ENUM('autonome','entreprise') NOT NULL DEFAULT 'entreprise',
    PRIMARY KEY (`id_societe_structure`)
) ENGINE = InnoDB;

CREATE TABLE `societe_signataire` (
  `id_societe_signataire` mediumint(8) UNSIGNED NOT NULL,
  `id_societe` mediumint(8) UNSIGNED NOT NULL,
  id_contact  mediumint(8) UNSIGNED NOT NULL,
  date_autorisation_pouvoir DATE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `societe_signataire`  ADD PRIMARY KEY (`id_societe_signataire`),  ADD KEY `id_societe` (`id_societe`), ADD KEY `id_contact` (`id_contact`);
ALTER TABLE `societe_signataire`  MODIFY `id_societe_signataire` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `affaire_garant` (
  `id_affaire_garant` mediumint(8) UNSIGNED NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `id_societe`  mediumint(8) UNSIGNED NULL,
  `id_contact`  mediumint(8) UNSIGNED NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `affaire_garant`  ADD PRIMARY KEY (`id_affaire_garant`),  ADD KEY `id_affaire` (`id_affaire`), ADD KEY `id_societe` (`id_societe`), ADD KEY `id_contact` (`id_contact`);
ALTER TABLE `affaire_garant`  MODIFY `id_affaire_garant` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `societe` ADD `lieu_registre` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `societe` ADD `numero_tomo` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `societe` ADD `numero_folio` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `societe` ADD `numero_hoja` VARCHAR(255) NULL DEFAULT NULL;
ALTER TABLE `societe` ADD `numero_inscription` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `societe` ADD `id_contact_notaire` MEDIUMINT UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `societe` ADD FOREIGN KEY (`id_contact_notaire`) REFERENCES `contact`(`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `contact` ADD `num_ordre_notaire` VARCHAR(50) NULL DEFAULT NULL;
