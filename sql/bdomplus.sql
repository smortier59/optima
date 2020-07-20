-- Probleme numerotation avenant BDOM
ALTER TABLE `affaire` CHANGE `ref` `ref` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `bon_de_commande` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `facture_fournisseur` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `facture_non_parvenue` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


-- Gestion des contentieux clients
ALTER TABLE `societe` ADD `mauvais_payeur` ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Permet de savoir si le client a des contrats en contentieux' AFTER `force_acceptation`,
                      ADD `contentieux_depuis` ENUM('1_mois','2_mois','plus_3_mois') NULL DEFAULT NULL COMMENT 'Permet de savoir depuis combien de temps le client est mauvais payeur (Date max impayée)' AFTER `mauvais_payeur`;

-- Renouvellement BDOM +
INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES
(NULL, '__URL_SITE_SOUSCRIPTION__', 'https://bdom-front.local'),
(NULL, '__URL_ESPACE_CLIENT__', 'https://rct-bdomplus-espaceclient.cleodis.test');

ALTER TABLE `sell_and_sign` CHANGE `bundle_id` `bundle_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;