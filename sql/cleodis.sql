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
    ((((`affaire`
left join `magasin` on
    (`magasin`.`id_magasin` = `affaire`.`id_magasin`))
left join `societe` `client` on
    (`client`.`id_societe` = `affaire`.`id_societe`))
left join `societe` `partenaire` on
    (`affaire`.`id_partenaire` = `partenaire`.`id_societe`))
left join `societe` `apporteur` on
    (`affaire`.`id_apporteur` = `apporteur`.`id_societe`))
WHERE nature = 'vente';

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
    `facture`.`type_facture` <> 'refi' AND `id_affaire` in (SELECT `id_affaire` from `abonnement_client`)
    OR `id_affaire` in (SELECT `id_affaire` from `affaire_client`);

