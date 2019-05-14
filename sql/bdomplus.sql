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


ALTER TABLE `licence` CHANGE `id_licence` `id_licence` MEDIUMINT(9) NOT NULL AUTO_INCREMENT;
ALTER TABLE `pack_produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','top office','burger king','flunch','toshiba','btwin','boulangerpro','bdomplus','sans') CHARACTER SET utf8 COLLATE utf8_general_ci NULL;



CREATE TABLE `licence_type` (
  `id_licence_type` mediumint(8) UNSIGNED NOT NULL,
  `licence_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `licence_type` (`id_licence_type`, `licence_type`) VALUES
(1, 'Office 365 Personnel'),
(2, 'Office 365 Famille'),
(3, 'Norton Sécurity Platinium'),
(4, 'Norton Sécurity Standard');


ALTER TABLE `licence_type` ADD PRIMARY KEY (`id_licence_type`);
ALTER TABLE `licence_type` MODIFY `id_licence_type` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `licence_type` ADD `url_telechargement` VARCHAR(500) NOT NULL AFTER `licence_type`;
COMMIT;

ALTER TABLE `licence` ADD `id_licence_type` MEDIUMINT UNSIGNED NOT NULL AFTER `part_2`, ADD INDEX (`id_licence_type`);
ALTER TABLE `licence` ADD  FOREIGN KEY (`id_licence_type`) REFERENCES `licence_type`(`id_licence_type`) ON DELETE RESTRICT ON UPDATE CASCADE;


ALTER TABLE `produit` ADD `id_licence_type` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `id_document_contrat`, ADD INDEX (`id_licence_type`);
ALTER TABLE `produit` ADD FOREIGN KEY (`id_licence_type`) REFERENCES `licence_type`(`id_licence_type`) ON DELETE RESTRICT ON UPDATE CASCADE;

UPDATE licence_type SET url_telechargement = "https://my.norton.com/home/setup?inid=nortoncom_nav_setup_products-services:home" WHERE id_licence_type = 4;
UPDATE licence_type SET url_telechargement = "https://my.norton.com/home/setup?inid=nortoncom_nav_setup_products-services:home" WHERE id_licence_type = 3;
UPDATE licence_type SET url_telechargement = "https://setup.office.com/downloadoffice/" WHERE id_licence_type = 1;


UPDATE produit SET id_licence_type = 4 WHERE produit = "Norton Security standard";
UPDATE produit SET id_licence_type = 3 WHERE produit = "Norton Platinium";
UPDATE produit SET id_licence_type = 1 WHERE produit = "Office 365 Personnel";



ALTER TABLE `facture` ADD `ref_externe` VARCHAR(11) NOT NULL AFTER `ref`;


UPDATE `licence` SET `id_commande_ligne` = NULL;

