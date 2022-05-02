ALTER TABLE `facture` ADD `date_envoi` DATE NULL DEFAULT NULL AFTER `envoye_mail`;
ALTER TABLE `facture` ADD `envoye` ENUM('oui','non','erreur') NULL DEFAULT 'non' AFTER `date_envoi`;
CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `abonnement_client` AS
select
    `commande`.`id_societe` AS `id_societe`,
    `commande`.`id_affaire` AS `id_affaire`,
    `commande`.`id_commande` AS `id_commande`,
    `commande`.`ref` AS `num_dossier`,
    `commande`.`commande` AS `dossier`,
    `commande`.`etat` AS `statut`,
    `commande`.`date` AS `date`,
    `commande`.`date_debut` AS `date_debut`,
    `commande`.`date_evolution` AS `date_fin`,
    `commande`.`date_arret` AS `date_arret`,
    `commande`.`retour_contrat` AS `retour_contrat`,
    `affaire`.`IBAN` AS `IBAN`,
    `affaire`.`BIC` AS `BIC`,
    `affaire`.`RUM` AS `RUM`,
    `affaire`.`site_associe` AS `site_associe`
from
    (`commande`
join `affaire` on
    (`commande`.`id_affaire` = `affaire`.`id_affaire`))
where
    `affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and `commande`.`etat` in ('mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux');
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
        or `affaire`.`nature` = 'vente');

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `factures_client` AS
select
    `facture`.`id_facture` AS `id_facture`,
    `facture`.`ref` AS `ref`,
    `facture`.`ref_externe` AS `ref_externe`,
    `facture`.`id_societe` AS `id_societe`,
    `facture`.`prix` AS `prix`,
    `facture`.`etat` AS `etat`,
    `facture`.`date` AS `date`,
    `facture`.`date_paiement` AS `date_paiement`,
    `facture`.`type_facture` AS `type_facture`,
    `facture`.`date_periode_debut` AS `date_periode_debut`,
    `facture`.`date_periode_fin` AS `date_periode_fin`,
    `facture`.`tva` AS `tva`,
    `facture`.`id_affaire` AS `id_affaire`,
    `facture`.`mode_paiement` AS `mode_paiement`,
    `facture`.`nature` AS `nature`,
    `facture`.`rejet` AS `rejet`,
    `facture`.`date_rejet` AS `date_rejet`,
    `facture`.`date_regularisation` AS `date_regularisation`
from
    `facture`
where
    `facture`.`type_facture` <> 'refi'
    and `facture`.`id_affaire` in (
    select
        `affaire_client`.`id_affaire`
    from
        `affaire_client`);



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
CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `abonnement_client` AS
select
    `commande`.`id_societe` AS `id_societe`,
    `commande`.`id_affaire` AS `id_affaire`,
    `commande`.`id_commande` AS `id_commande`,
    `commande`.`ref` AS `num_dossier`,
    `commande`.`commande` AS `dossier`,
    `commande`.`etat` AS `statut`,
    `commande`.`date` AS `date`,
    `commande`.`date_debut` AS `date_debut`,
    `commande`.`date_evolution` AS `date_fin`,
    `commande`.`date_arret` AS `date_arret`,
    `commande`.`retour_contrat` AS `retour_contrat`,
    `affaire`.`IBAN` AS `IBAN`,
    `affaire`.`BIC` AS `BIC`,
    `affaire`.`RUM` AS `RUM`,
    `affaire`.`site_associe` AS `site_associe`
from
    (`commande`
join `affaire` on
    (`commande`.`id_affaire` = `affaire`.`id_affaire`))
where
    `affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and (`commande`.`etat` in ('mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux')
        or `affaire`.`nature` = 'vente');


ALTER TABLE `loyer_kilometrage` ADD `echeance` INT NOT NULL AFTER `id_affaire`, ADD `montant_ht` FLOAT(8,2) NOT NULL AFTER `echeance`;

CREATE TABLE `restitution_anticipee` (
  `id_loyer_kilometrage` mediumint(8) UNSIGNED NOT NULL,
  `loyer` float(8,2) NOT NULL,
  `kilometrage` mediumint(9) NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `echeance` int(11) NOT NULL,
  `montant_ht` float(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

ALTER TABLE `restitution_anticipee`
  ADD PRIMARY KEY (`id_loyer_kilometrage`),
  ADD KEY `id_affaire` (`id_affaire`);

ALTER TABLE `restitution_anticipee` MODIFY `id_loyer_kilometrage` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `restitution_anticipee` ADD CONSTRAINT `restitution_anticipee_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
ALTER TABLE `type_affaire` ADD `contrat_template` VARCHAR(75) NOT NULL DEFAULT 'contrat' AFTER `devis_template`;ALTER TABLE `type_affaire` ADD `contrat_template` VARCHAR(255) NOT NULL DEFAULT 'contrat' AFTER `devis_template`;