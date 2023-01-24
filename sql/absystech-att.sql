ALTER TABLE `societe` ADD `id_contact_signataire` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `id_contact_facturation`, ADD INDEX (`id_contact_signataire`);
ALTER TABLE `societe` ADD FOREIGN KEY (`id_contact_signataire`) REFERENCES `contact`(`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `devis` ADD `id_contrat_sell_and_sign` INT NULL COMMENT 'ID du contrat coté sell And Sign' AFTER `duree_contrat_cout_copie`;
ALTER TABLE `devis` ADD `date_signature` DATE NULL COMMENT 'Date de la signature du document via portail Sign' AFTER `id_contrat_sell_and_sign`;

INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES
(NULL, '__OODRIVE_HOST__', 'https://cloud.sellandsign.com'),
(NULL, '__OODRIVE_JTOKEN__', 'TSDSSBX|14D1TPTfDjl3unie8OwZ5HHNAtTEfGFSSyHLd68IpiY=')