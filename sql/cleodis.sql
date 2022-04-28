ALTER TABLE `type_affaire` ADD `contrat_template` VARCHAR(75) NOT NULL DEFAULT 'contrat' AFTER `devis_template`;ALTER TABLE `type_affaire` ADD `contrat_template` VARCHAR(255) NOT NULL DEFAULT 'contrat' AFTER `devis_template`;
ALTER TABLE `pack_produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','haccp','axa','worldline','h2c','volfoni','aubureau','leon','hippopotamus','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','haccp','axa','worldline','h2c','volfoni','aubureau','leon','hippopotamus','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `affaire` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','dib','locevo','haccp','axa','worldline','h2c','volfoni','aubureau','leon','hippopotamus','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire','la_poste','btwin','boulangerpro','hexamed','dib','locevo','haccp','axa','worldline','h2c','volfoni','aubureau','leon','hippopotamus','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `document_revendeur` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','haccp','axa','worldline','h2c','volfoni','aubureau','leon','hippopotamus','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `affaire_client` AS
select
    `affaire`.`id_societe` AS `id_societe`,
    `affaire`.`id_affaire` AS `id_affaire`,
    `affaire`.`ref` AS `ref`,
    `affaire`.`ref_externe` AS `ref_externe`,
    `affaire`.`date` AS `date`,
    `affaire`.`affaire` AS `affaire`,
    `affaire`.`id_parent` AS `id_parent`,
    `affaire`.`id_fille` AS `id_fille`,
    `affaire`.`nature` AS `nature`,
    `affaire`.`date_garantie` AS `date_garantie`,
    `affaire`.`site_associe` AS `site_associe`,
    `affaire`.`mail_signature` AS `mail_signature`,
    `affaire`.`date_signature` AS `date_signature`,
    `affaire`.`signataire` AS `signataire`,
    `affaire`.`langue` AS `langue`,
    `affaire`.`adresse_livraison` AS `adresse_livraison`,
    `affaire`.`adresse_livraison_2` AS `adresse_livraison_2`,
    `affaire`.`adresse_livraison_3` AS `adresse_livraison_3`,
    `affaire`.`cp_adresse_livraison` AS `adresse_livraison_cp`,
    `affaire`.`ville_adresse_livraison` AS `adresse_livraison_ville`,
    `affaire`.`adresse_facturation` AS `adresse_facturation`,
    `affaire`.`adresse_facturation_2` AS `adresse_facturation_2`,
    `affaire`.`adresse_facturation_3` AS `adresse_facturation_3`,
    `affaire`.`cp_adresse_facturation` AS `adresse_facturation_cp`,
    `affaire`.`ville_adresse_facturation` AS `adresse_facturation_ville`,
    `affaire`.`id_partenaire` AS `id_partenaire`,
    `affaire`.`id_magasin` AS `id_magasin`,
    `affaire`.`vendeur` AS `vendeur`,
    `magasin`.`magasin` AS `magasin`,
    `partenaire`.`societe` AS `partenaire`,
    `apporteur`.`id_apporteur` AS `id_apporteur`
from
    (((((`affaire`
left join `magasin` on
    (`magasin`.`id_magasin` = `affaire`.`id_magasin`))
left join `societe` `client` on
    (`client`.`id_societe` = `affaire`.`id_societe`))
left join `societe` `partenaire` on
    (`affaire`.`id_partenaire` = `partenaire`.`id_societe`))
left join `societe` `apporteur` on
    (`affaire`.`id_apporteur` = `apporteur`.`id_societe`))
left join `commande` on
    (`commande`.`id_affaire` = `affaire`.`id_affaire`))
where
    `affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and (`commande`.`etat` in ('mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux')
        or `affaire`.`nature` = 'vente')


INSERT INTO `client` (`id_client`, `client`, `etat`, `client_id`, `client_secret`)  VALUES
(NULL, 'volfoni', 'actif', 'vDjFsZtgyNpqq5RpZ4g8ZT7hh3vVGyXT', '$2a$10$FfDP19p/zWbPqZ1SnsKnKuLKjHWFKbI8wRSKgmrAK.GCy4Vjkxnqu'),
(NULL, 'aubureau', 'actif', 'y2VTmkMStf76qYTdHuVYJY2fhMRGFNMV', '$2a$10$C1xfYbb6XO2DQN/qDQjfT.WmAbaJEy4pqJXDdWGa97TEJAy2xR4g'),
(NULL, 'leon', 'actif', 'WTgXfRTmcJqGTmpcZJhcKd5ewGP7xPe8', '$2a$10$63/8MkUFFODedb5IUzdj6uIWHckF3uUwGFpiKtLNNftUVeKyiCxxW'),
(NULL, 'hippopotamus', 'actif', 'sMn2QXPxxQwEwvK7NCAmnWrR9Qvhd6WE', '$2a$10$oIxKhCOYvn9/.SXqTy.9zeSMPWr29TUpiajE4.FwCBZIJ.2bNgvze');


INSERT INTO `site_associe` (`id_site_associe`, `site_associe`, `code`, `steps_tunnel`, `id_client`, `url_front`, `cs_score_minimal`, `age_minimal`, `export_middleware`, `id_societe`, `color_dominant`, `color_footer`, `color_links`, `color_titles`, `id_societe_footer_mail`, `can_update_bic_iban`, `id_type_affaire`) VALUES
(NULL, 'volfoni', NULL, 'COMPONENT_CONFIRM_CLIENT_ACCOUNT,COMPONENT_B2B_ALLINONE,COMPONENT_SIGNING_DOCUMENTS_IFRAME,COMPONENT_UPLOAD_PJ,COMPONENT_FINISHED_RECAP', '43', 'http://volfoni.cleodis.com', '40', '2', 'oui', '6738', '2e92e7', 'fae856', 'fae856', 'fae856', '6738', '0', '21'),
(NULL, 'aubureau', NULL, 'COMPONENT_CONFIRM_CLIENT_ACCOUNT,COMPONENT_B2B_ALLINONE,COMPONENT_SIGNING_DOCUMENTS_IFRAME,COMPONENT_UPLOAD_PJ,COMPONENT_FINISHED_RECAP', '44', 'http://aubureau.cleodis.com', '40', '2', 'oui', '6738', '2e92e7', 'fae856', 'fae856', 'fae856', '6738', '0', '21'),
(NULL, 'leon', NULL, 'COMPONENT_CONFIRM_CLIENT_ACCOUNT,COMPONENT_B2B_ALLINONE,COMPONENT_SIGNING_DOCUMENTS_IFRAME,COMPONENT_UPLOAD_PJ,COMPONENT_FINISHED_RECAP', '45', 'http://leon.cleodis.com', '40', '2', 'oui', '6738', '2e92e7', 'fae856', 'fae856', 'fae856', '6738', '0', '21'),
(NULL, 'hippopotamus', NULL, 'COMPONENT_CONFIRM_CLIENT_ACCOUNT,COMPONENT_B2B_ALLINONE,COMPONENT_SIGNING_DOCUMENTS_IFRAME,COMPONENT_UPLOAD_PJ,COMPONENT_FINISHED_RECAP', '46', 'http://hippopotamus.cleodis.com', '40', '2', 'oui', '6738', '2e92e7', 'fae856', 'fae856', 'fae856', '6738', '0', '21');

ALTER TABLE `site_associe` ADD `siret_partenaire` VARCHAR(25) NULL DEFAULT NULL COMMENT 'Partenaire des affaires utilisé dans le souscription class' AFTER `id_type_affaire`;

UPDATE `site_associe` SET `siret_partenaire`= "30613890003613" WHERE site_associe = 'btwin';
UPDATE `site_associe` SET `siret_partenaire`= "34738457002017" WHERE site_associe = 'bdomplus';
UPDATE `site_associe` SET `siret_partenaire`= "51028155300030" WHERE site_associe = 'hexamed';
UPDATE `site_associe` SET `siret_partenaire`= "45122067700087" WHERE site_associe = 'boulangerpro';
UPDATE `site_associe` SET `siret_partenaire`= "31007041200062" WHERE site_associe = 'volfoni';
UPDATE `site_associe` SET `siret_partenaire`= "31007041200062" WHERE site_associe = 'aubureau';
UPDATE `site_associe` SET `siret_partenaire`= "31007041200062" WHERE site_associe = 'leon';
UPDATE `site_associe` SET `siret_partenaire`= "31007041200062" WHERE site_associe = 'hippopotamus';
UPDATE `site_associe` SET `siret_partenaire`= "43939846200044" WHERE site_associe = 'h2c';
UPDATE `site_associe` SET `siret_partenaire`= "37890194600574" WHERE site_associe = 'worldline';
UPDATE `site_associe` SET `siret_partenaire`= "34020062500036" WHERE site_associe = 'axa';
UPDATE `site_associe` SET `siret_partenaire`= "31007041200062" WHERE site_associe = 'haccp';
UPDATE `site_associe` SET `siret_partenaire`= "42268731900059" WHERE site_associe = 'dib';
UPDATE `site_associe` SET `siret_partenaire`= "45307981600048" WHERE site_associe = 'locevo';