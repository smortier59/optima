INSERT INTO dev_optima_cleodis.constante
(constante, valeur)
VALUES('__EMAIL_NOTIFIE_UPLOAD_FILE_PARTENAIRE__', 'adv@cleodis.com');

ALTER TABLE `loyer` CHANGE `type` `type` ENUM('engagement','liberatoire','prolongation') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'engagement';

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_cleodis`.`abonnement_client` AS
select
    `dev_optima_cleodis`.`commande`.`id_societe` AS `id_societe`,
    `dev_optima_cleodis`.`commande`.`id_affaire` AS `id_affaire`,
    `dev_optima_cleodis`.`commande`.`id_commande` AS `id_commande`,
    `dev_optima_cleodis`.`commande`.`ref` AS `num_dossier`,
    `dev_optima_cleodis`.`commande`.`commande` AS `dossier`,
    `dev_optima_cleodis`.`commande`.`etat` AS `statut`,
    `dev_optima_cleodis`.`commande`.`date` AS `date`,
    `dev_optima_cleodis`.`commande`.`date_debut` AS `date_debut`,
    `dev_optima_cleodis`.`commande`.`date_evolution` AS `date_fin`,
    `dev_optima_cleodis`.`commande`.`date_arret` AS `date_arret`,
    `dev_optima_cleodis`.`commande`.`retour_contrat` AS `retour_contrat`,
    `dev_optima_cleodis`.`affaire`.`IBAN` AS `IBAN`,
    `dev_optima_cleodis`.`affaire`.`BIC` AS `BIC`,
    `dev_optima_cleodis`.`affaire`.`RUM` AS `RUM`,
    `dev_optima_cleodis`.`affaire`.`site_associe` AS `site_associe`
from
    (`dev_optima_cleodis`.`commande`
join `dev_optima_cleodis`.`affaire` on
    (`dev_optima_cleodis`.`commande`.`id_affaire` = `dev_optima_cleodis`.`affaire`.`id_affaire`))
where
    `dev_optima_cleodis`.`affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and `dev_optima_cleodis`.`commande`.`etat` in ('mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux');

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_cleodis`.`affaire_client` AS
select
    `dev_optima_cleodis`.`affaire`.`id_societe` AS `id_societe`,
    `dev_optima_cleodis`.`affaire`.`id_affaire` AS `id_affaire`,
    `dev_optima_cleodis`.`affaire`.`ref` AS `ref`,
    `dev_optima_cleodis`.`affaire`.`ref_externe` AS `ref_externe`,
    `dev_optima_cleodis`.`affaire`.`date` AS `date`,
    `dev_optima_cleodis`.`affaire`.`affaire` AS `affaire`,
    `dev_optima_cleodis`.`affaire`.`id_parent` AS `id_parent`,
    `dev_optima_cleodis`.`affaire`.`id_fille` AS `id_fille`,
    `dev_optima_cleodis`.`affaire`.`nature` AS `nature`,
    `dev_optima_cleodis`.`affaire`.`date_garantie` AS `date_garantie`,
    `dev_optima_cleodis`.`affaire`.`site_associe` AS `site_associe`,
    `dev_optima_cleodis`.`affaire`.`mail_signature` AS `mail_signature`,
    `dev_optima_cleodis`.`affaire`.`date_signature` AS `date_signature`,
    `dev_optima_cleodis`.`affaire`.`signataire` AS `signataire`,
    `dev_optima_cleodis`.`affaire`.`langue` AS `langue`,
    `dev_optima_cleodis`.`affaire`.`adresse_livraison` AS `adresse_livraison`,
    `dev_optima_cleodis`.`affaire`.`adresse_livraison_2` AS `adresse_livraison_2`,
    `dev_optima_cleodis`.`affaire`.`adresse_livraison_3` AS `adresse_livraison_3`,
    `dev_optima_cleodis`.`affaire`.`cp_adresse_livraison` AS `adresse_livraison_cp`,
    `dev_optima_cleodis`.`affaire`.`ville_adresse_livraison` AS `adresse_livraison_ville`,
    `dev_optima_cleodis`.`affaire`.`adresse_facturation` AS `adresse_facturation`,
    `dev_optima_cleodis`.`affaire`.`adresse_facturation_2` AS `adresse_facturation_2`,
    `dev_optima_cleodis`.`affaire`.`adresse_facturation_3` AS `adresse_facturation_3`,
    `dev_optima_cleodis`.`affaire`.`cp_adresse_facturation` AS `adresse_facturation_cp`,
    `dev_optima_cleodis`.`affaire`.`ville_adresse_facturation` AS `adresse_facturation_ville`,
    `dev_optima_cleodis`.`affaire`.`id_partenaire` AS `id_partenaire`,
    `dev_optima_cleodis`.`affaire`.`id_magasin` AS `id_magasin`,
    `dev_optima_cleodis`.`affaire`.`vendeur` AS `vendeur`,
    `dev_optima_cleodis`.`magasin`.`magasin` AS `magasin`,
    `partenaire`.`societe` AS `partenaire`,
    `apporteur`.`id_apporteur` AS `id_apporteur`
from
    ((((`dev_optima_cleodis`.`affaire`
left join `dev_optima_cleodis`.`magasin` on
    (`dev_optima_cleodis`.`magasin`.`id_magasin` = `dev_optima_cleodis`.`affaire`.`id_magasin`))
left join `dev_optima_cleodis`.`societe` `client` on
    (`client`.`id_societe` = `dev_optima_cleodis`.`affaire`.`id_societe`))
left join `dev_optima_cleodis`.`societe` `partenaire` on
    (`dev_optima_cleodis`.`affaire`.`id_partenaire` = `partenaire`.`id_societe`))
left join `dev_optima_cleodis`.`societe` `apporteur` on
    (`dev_optima_cleodis`.`affaire`.`id_apporteur` = `apporteur`.`id_societe`))
WHERE nature = 'vente';

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_cleodis`.`factures_client` AS
select
    `dev_optima_cleodis`.`facture`.`id_facture` AS `id_facture`,
    `dev_optima_cleodis`.`facture`.`ref` AS `ref`,
    `dev_optima_cleodis`.`facture`.`ref_externe` AS `ref_externe`,
    `dev_optima_cleodis`.`facture`.`id_societe` AS `id_societe`,
    `dev_optima_cleodis`.`facture`.`prix` AS `prix`,
    `dev_optima_cleodis`.`facture`.`etat` AS `etat`,
    `dev_optima_cleodis`.`facture`.`date` AS `date`,
    `dev_optima_cleodis`.`facture`.`date_paiement` AS `date_paiement`,
    `dev_optima_cleodis`.`facture`.`type_facture` AS `type_facture`,
    `dev_optima_cleodis`.`facture`.`date_periode_debut` AS `date_periode_debut`,
    `dev_optima_cleodis`.`facture`.`date_periode_fin` AS `date_periode_fin`,
    `dev_optima_cleodis`.`facture`.`tva` AS `tva`,
    `dev_optima_cleodis`.`facture`.`id_affaire` AS `id_affaire`,
    `dev_optima_cleodis`.`facture`.`mode_paiement` AS `mode_paiement`,
    `dev_optima_cleodis`.`facture`.`nature` AS `nature`,
    `dev_optima_cleodis`.`facture`.`rejet` AS `rejet`,
    `dev_optima_cleodis`.`facture`.`date_rejet` AS `date_rejet`,
    `dev_optima_cleodis`.`facture`.`date_regularisation` AS `date_regularisation`
from
    `dev_optima_cleodis`.`facture`
where
    `dev_optima_cleodis`.`facture`.`type_facture` <> 'refi' AND id_affaire in (SELECT id_affaire from dev_optima_cleodis.abonnement_client)
    OR id_affaire in (SELECT id_affaire from dev_optima_cleodis.affaire_client);

