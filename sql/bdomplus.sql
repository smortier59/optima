ALTER TABLE `pack_produit` ADD `prolongation` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `id_document_contrat`;

ALTER TABLE `affaire`
ADD `ref_sign` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Reference de signature chez le prestataire de signature' AFTER `snapshot_pack_produit`,
ADD `ref_mandate` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Reference du mandat chez le prestataire de signature' AFTER `ref_sign`,
ADD `subscriber_reference` VARCHAR(80) NULL DEFAULT NULL COMMENT 'Reference du client chez le prestataire de signature' AFTER `ref_mandate`,
ADD `prestataire_signature` ENUM('sellandsign','slimpay') NULL DEFAULT NULL COMMENT 'Permet d\'identifier quel prestataire de signature est utilisé pour cette affaire' AFTER `subscriber_reference`;


ALTER TABLE `affaire` CHANGE `site_associe` `site_associe` ENUM('toshiba','btwin','location_evolutive','boulangerpro','bdomplus') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire','la_poste','btwin','boulangerpro','bdomplus') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `magasin` CHANGE `site_associe` `site_associe` ENUM('toshiba','btwin','bdomplus') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


CREATE TABLE `licence` (
  `id_licence` mediumint(9) NOT NULL,
  `part_1` varchar(10) NOT NULL,
  `part_2` varchar(4) NOT NULL,
  `id_commande_ligne` mediumint(8) UNSIGNED DEFAULT NULL COMMENT 'Permet de savoir si la licence est déja utilisée et pour quelle affaire'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `licence`
  ADD PRIMARY KEY (`id_licence`),
  ADD KEY `id_commande_ligne` (`id_commande_ligne`);

ALTER TABLE `licence`
  ADD CONSTRAINT `licence_ibfk_1` FOREIGN KEY (`id_commande_ligne`) REFERENCES `commande_ligne` (`id_commande_ligne`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;