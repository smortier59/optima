-- dev_optima_arrow.abonnement_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`abonnement_client` AS
select
    `dev_optima_arrow`.`commande`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`commande`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`commande`.`id_commande` AS `id_commande`,
    `dev_optima_arrow`.`commande`.`ref` AS `num_dossier`,
    `dev_optima_arrow`.`commande`.`commande` AS `dossier`,
    `dev_optima_arrow`.`commande`.`etat` AS `statut`,
    `dev_optima_arrow`.`commande`.`date` AS `date`,
    `dev_optima_arrow`.`commande`.`date_debut` AS `date_debut`,
    `dev_optima_arrow`.`commande`.`date_evolution` AS `date_fin`,
    `dev_optima_arrow`.`commande`.`date_arret` AS `date_arret`,
    `dev_optima_arrow`.`commande`.`retour_contrat` AS `retour_contrat`,
    `dev_optima_arrow`.`affaire`.`IBAN` AS `IBAN`,
    `dev_optima_arrow`.`affaire`.`BIC` AS `BIC`,
    `dev_optima_arrow`.`affaire`.`RUM` AS `RUM`,
    `dev_optima_arrow`.`affaire`.`site_associe` AS `site_associe`,
    `dev_optima_arrow`.`affaire`.`id_type_affaire` AS `id_type_affaire`,
    `dev_optima_arrow`.`contact`.`prenom` AS `titulaire_prenom`,
    `dev_optima_arrow`.`contact`.`nom` AS `titulaire_nom`
from
    (((`dev_optima_arrow`.`commande`
left join `dev_optima_arrow`.`affaire` on
    (`dev_optima_arrow`.`commande`.`id_affaire` = `dev_optima_arrow`.`affaire`.`id_affaire`))
left join `dev_optima_arrow`.`societe` on
    (`dev_optima_arrow`.`societe`.`id_societe` = `dev_optima_arrow`.`affaire`.`id_societe`))
left join `dev_optima_arrow`.`contact` on
    (`dev_optima_arrow`.`contact`.`id_contact` = `dev_optima_arrow`.`societe`.`id_contact_facturation`))
where
    `dev_optima_arrow`.`affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and (`dev_optima_arrow`.`commande`.`etat` in ('non_loyer', 'mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux', 'AR', 'arreter')
        or `dev_optima_arrow`.`affaire`.`nature` = 'vente'
        or `dev_optima_arrow`.`affaire`.`nature` = 'AR');


-- dev_optima_arrow.abonnement_detail source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`abonnement_detail` AS
select
    `dev_optima_arrow`.`commande`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`commande`.`id_commande` AS `id_commande`,
    `dev_optima_arrow`.`commande_ligne`.`ref` AS `ref`,
    `dev_optima_arrow`.`commande_ligne`.`produit` AS `produit`,
    `dev_optima_arrow`.`commande_ligne`.`quantite` AS `quantite`
from
    (`dev_optima_arrow`.`commande_ligne`
join `dev_optima_arrow`.`commande` on
    (`dev_optima_arrow`.`commande_ligne`.`id_commande` = `dev_optima_arrow`.`commande`.`id_commande`))
where
    `dev_optima_arrow`.`commande_ligne`.`visible` = 'oui'
order by
    `dev_optima_arrow`.`commande`.`id_affaire`,
    `dev_optima_arrow`.`commande_ligne`.`ordre`;


-- dev_optima_arrow.affaire_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`affaire_client` AS
select
    `dev_optima_arrow`.`affaire`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`affaire`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`affaire`.`ref` AS `ref`,
    `dev_optima_arrow`.`affaire`.`ref_externe` AS `ref_externe`,
    `dev_optima_arrow`.`affaire`.`date` AS `date`,
    `dev_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `dev_optima_arrow`.`affaire`.`id_parent` AS `id_parent`,
    `dev_optima_arrow`.`affaire`.`id_fille` AS `id_fille`,
    `dev_optima_arrow`.`affaire`.`nature` AS `nature`,
    `dev_optima_arrow`.`affaire`.`etat` AS `etat`,
    `dev_optima_arrow`.`affaire`.`date_garantie` AS `date_garantie`,
    `dev_optima_arrow`.`affaire`.`site_associe` AS `site_associe`,
    `dev_optima_arrow`.`affaire`.`mail_signature` AS `mail_signature`,
    `dev_optima_arrow`.`affaire`.`date_signature` AS `date_signature`,
    `dev_optima_arrow`.`affaire`.`signataire` AS `signataire`,
    `dev_optima_arrow`.`affaire`.`langue` AS `langue`,
    `dev_optima_arrow`.`affaire`.`adresse_livraison` AS `adresse_livraison`,
    `dev_optima_arrow`.`affaire`.`adresse_livraison_2` AS `adresse_livraison_2`,
    `dev_optima_arrow`.`affaire`.`adresse_livraison_3` AS `adresse_livraison_3`,
    `dev_optima_arrow`.`affaire`.`cp_adresse_livraison` AS `adresse_livraison_cp`,
    `dev_optima_arrow`.`affaire`.`ville_adresse_livraison` AS `adresse_livraison_ville`,
    `dev_optima_arrow`.`affaire`.`adresse_facturation` AS `adresse_facturation`,
    `dev_optima_arrow`.`affaire`.`adresse_facturation_2` AS `adresse_facturation_2`,
    `dev_optima_arrow`.`affaire`.`adresse_facturation_3` AS `adresse_facturation_3`,
    `dev_optima_arrow`.`affaire`.`cp_adresse_facturation` AS `adresse_facturation_cp`,
    `dev_optima_arrow`.`affaire`.`ville_adresse_facturation` AS `adresse_facturation_ville`,
    `dev_optima_arrow`.`affaire`.`id_partenaire` AS `id_partenaire`,
    `dev_optima_arrow`.`affaire`.`id_apporteur` AS `id_apporteur`,
    `dev_optima_arrow`.`affaire`.`id_commercial` AS `id_commercial`,
    `dev_optima_arrow`.`affaire`.`id_magasin` AS `id_magasin`,
    `dev_optima_arrow`.`affaire`.`vendeur` AS `vendeur`,
    `dev_optima_arrow`.`affaire`.`demande_lixxbail` AS `demande_lixxbail`,
    `dev_optima_arrow`.`magasin`.`magasin` AS `magasin`,
    `partenaire`.`societe` AS `partenaire`,
    `apporteur`.`societe` AS `apporteur`
from
    (((((`dev_optima_arrow`.`affaire`
left join `dev_optima_arrow`.`magasin` on
    (`dev_optima_arrow`.`magasin`.`id_magasin` = `dev_optima_arrow`.`affaire`.`id_magasin`))
left join `dev_optima_arrow`.`societe` `client` on
    (`client`.`id_societe` = `dev_optima_arrow`.`affaire`.`id_societe`))
left join `dev_optima_arrow`.`societe` `partenaire` on
    (`dev_optima_arrow`.`affaire`.`id_partenaire` = `partenaire`.`id_societe`))
left join `dev_optima_arrow`.`societe` `apporteur` on
    (`dev_optima_arrow`.`affaire`.`id_apporteur` = `apporteur`.`id_societe`))
left join `dev_optima_arrow`.`commande` on
    (`dev_optima_arrow`.`commande`.`id_affaire` = `dev_optima_arrow`.`affaire`.`id_affaire`))
where
    `dev_optima_arrow`.`affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and (`dev_optima_arrow`.`commande`.`etat` in ('non_loyer', 'mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux', 'AR', 'arreter')
        or `dev_optima_arrow`.`affaire`.`nature` = 'vente');


-- dev_optima_arrow.bon_de_commande_non_envoyes source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`bon_de_commande_non_envoyes` AS
select
    `dev_optima_arrow`.`bon_de_commande`.`id_bon_de_commande` AS `id_bon_de_commande`,
    `dev_optima_arrow`.`bon_de_commande`.`ref` AS `ref`,
    `dev_optima_arrow`.`bon_de_commande`.`prix` AS `prix`,
    `client`.`societe` AS `client`,
    `dev_optima_arrow`.`affaire`.`ref` AS `ref_affaire`,
    `dev_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `dev_optima_arrow`.`bon_de_commande`.`envoye_par_mail` AS `envoye_par_mail`,
    `fournisseur`.`id_societe` AS `id_fournisseur`,
    `fournisseur`.`societe` AS `fournisseur`
from
    (((`dev_optima_arrow`.`bon_de_commande`
left join `dev_optima_arrow`.`affaire` on
    (`dev_optima_arrow`.`affaire`.`id_affaire` = `dev_optima_arrow`.`bon_de_commande`.`id_affaire`))
left join `dev_optima_arrow`.`societe` `client` on
    (`client`.`id_societe` = `dev_optima_arrow`.`bon_de_commande`.`id_societe`))
left join `dev_optima_arrow`.`societe` `fournisseur` on
    (`fournisseur`.`id_societe` = `dev_optima_arrow`.`bon_de_commande`.`id_fournisseur`))
where
    `dev_optima_arrow`.`bon_de_commande`.`envoye_par_mail` is null;


-- dev_optima_arrow.commande_attente source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`commande_attente` AS
select
    `dev_optima_arrow`.`commande`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`commande`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`commande`.`id_commande` AS `id_commande`,
    `dev_optima_arrow`.`commande`.`ref` AS `num_dossier`,
    `dev_optima_arrow`.`commande`.`commande` AS `dossier`,
    `dev_optima_arrow`.`commande`.`etat` AS `statut`,
    `dev_optima_arrow`.`commande`.`date` AS `date`,
    `dev_optima_arrow`.`commande`.`date_debut` AS `date_debut`,
    `dev_optima_arrow`.`commande`.`date_evolution` AS `date_fin`,
    `dev_optima_arrow`.`commande`.`date_arret` AS `date_arret`,
    `dev_optima_arrow`.`commande`.`retour_contrat` AS `retour_contrat`
from
    `dev_optima_arrow`.`commande`
where
    `dev_optima_arrow`.`commande`.`etat` = 'non_loyer'
order by
    `dev_optima_arrow`.`commande`.`ref` desc;


-- dev_optima_arrow.contact_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`contact_client` AS
select
    `dev_optima_arrow`.`contact`.`id_contact` AS `id_contact`,
    `dev_optima_arrow`.`contact`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`contact`.`email` AS `email`,
    `dev_optima_arrow`.`contact`.`anniversaire` AS `anniversaire`,
    `dev_optima_arrow`.`contact`.`civilite` AS `civilite`,
    `dev_optima_arrow`.`contact`.`nom` AS `nom`,
    `dev_optima_arrow`.`contact`.`prenom` AS `prenom`,
    `dev_optima_arrow`.`contact`.`situation_maritale` AS `situation_maritale`,
    `dev_optima_arrow`.`contact`.`situation_perso` AS `situation_perso`,
    `dev_optima_arrow`.`contact`.`fonction` AS `fonction`,
    `dev_optima_arrow`.`contact`.`situation_pro` AS `situation_pro`
from
    `dev_optima_arrow`.`contact`
where
    `dev_optima_arrow`.`contact`.`etat` = 'actif';


-- dev_optima_arrow.coordonnees_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`coordonnees_client` AS
select
    `dev_optima_arrow`.`societe`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`societe`.`ref` AS `ref`,
    `dev_optima_arrow`.`societe`.`societe` AS `societe`,
    `dev_optima_arrow`.`famille`.`famille` AS `type_client`,
    `dev_optima_arrow`.`societe`.`id_famille` AS `id_famille`,
    `dev_optima_arrow`.`societe`.`nom_commercial` AS `nom_commercial`,
    `dev_optima_arrow`.`societe`.`adresse` AS `adresse`,
    `dev_optima_arrow`.`societe`.`adresse_2` AS `adresse_2`,
    `dev_optima_arrow`.`societe`.`adresse_3` AS `adresse_3`,
    `dev_optima_arrow`.`societe`.`cp` AS `cp`,
    `dev_optima_arrow`.`societe`.`ville` AS `ville`,
    `dev_optima_arrow`.`societe`.`facturation_adresse` AS `facturation_adresse`,
    `dev_optima_arrow`.`societe`.`facturation_adresse_2` AS `facturation_adresse_2`,
    `dev_optima_arrow`.`societe`.`facturation_adresse_3` AS `facturation_adresse_3`,
    `dev_optima_arrow`.`societe`.`facturation_cp` AS `facturation_cp`,
    `dev_optima_arrow`.`societe`.`facturation_ville` AS `facturation_ville`,
    `dev_optima_arrow`.`societe`.`livraison_adresse` AS `livraison_adresse`,
    `dev_optima_arrow`.`societe`.`livraison_adresse_2` AS `livraison_adresse_2`,
    `dev_optima_arrow`.`societe`.`livraison_adresse_3` AS `livraison_adresse_3`,
    `dev_optima_arrow`.`societe`.`livraison_cp` AS `livraison_cp`,
    `dev_optima_arrow`.`societe`.`livraison_ville` AS `livraison_ville`,
    `dev_optima_arrow`.`societe`.`email` AS `email`,
    `dev_optima_arrow`.`societe`.`tel` AS `tel`,
    `dev_optima_arrow`.`societe`.`particulier_civilite` AS `particulier_civilite`,
    `dev_optima_arrow`.`societe`.`particulier_nom` AS `particulier_nom`,
    `dev_optima_arrow`.`societe`.`particulier_prenom` AS `particulier_prenom`,
    `dev_optima_arrow`.`societe`.`particulier_portable` AS `particulier_portable`,
    `dev_optima_arrow`.`societe`.`num_carte_fidelite` AS `num_carte_fidelite`,
    `dev_optima_arrow`.`societe`.`particulier_fixe` AS `particulier_fixe`,
    `dev_optima_arrow`.`societe`.`particulier_email` AS `particulier_email`,
    `dev_optima_arrow`.`societe`.`code_client` AS `code_client`,
    `dev_optima_arrow`.`societe`.`id_apporteur` AS `id_apporteur`,
    `dev_optima_arrow`.`societe`.`langue` AS `langue`,
    `dev_optima_arrow`.`societe`.`siret` AS `siret`
from
    (`dev_optima_arrow`.`societe`
join `dev_optima_arrow`.`famille` on
    (`dev_optima_arrow`.`famille`.`id_famille` = `dev_optima_arrow`.`societe`.`id_famille`));


-- dev_optima_arrow.echeancier source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`echeancier` AS
select
    `dev_optima_arrow`.`facturation`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`facturation`.`id_societe` AS `societe`,
    `dev_optima_arrow`.`facturation`.`date_periode_debut` AS `debut_periode`,
    `dev_optima_arrow`.`facturation`.`date_periode_fin` AS `fin_periode`,
    coalesce(`dev_optima_arrow`.`facturation`.`frais_de_gestion`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`montant`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`serenite`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`maintenance`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`hotline`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`supervision`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`support`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`assurance`, 0) AS `montant`,
    coalesce(`dev_optima_arrow`.`facturation`.`frais_de_gestion`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`montant`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`serenite`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`maintenance`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`hotline`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`supervision`, 0) + coalesce(`dev_optima_arrow`.`facturation`.`support`, 0) AS `montant_sans_assurance`,
    coalesce(`dev_optima_arrow`.`facturation`.`assurance`, 0) AS `assurance`,
    coalesce(`dev_optima_arrow`.`type_affaire`.`assurance_sans_tva`, 'non') AS `assurance_sans_tva`,
    `dev_optima_arrow`.`facturation`.`type` AS `type`,
    `dev_optima_arrow`.`facturation`.`id_facture` AS `id_facture`,
    `dev_optima_arrow`.`devis`.`tva` AS `tva`
from
    (((`dev_optima_arrow`.`facturation`
left join `dev_optima_arrow`.`affaire` on
    (`dev_optima_arrow`.`affaire`.`id_affaire` = `dev_optima_arrow`.`facturation`.`id_affaire`))
left join `dev_optima_arrow`.`devis` on
    (`dev_optima_arrow`.`devis`.`id_affaire` = `dev_optima_arrow`.`affaire`.`id_affaire`))
left join `dev_optima_arrow`.`type_affaire` on
    (`dev_optima_arrow`.`type_affaire`.`id_type_affaire` = `dev_optima_arrow`.`affaire`.`id_type_affaire`));


-- dev_optima_arrow.espace_client_inscription source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`espace_client_inscription` AS
select
    `dev_optima_arrow`.`affaire`.`ref` AS `dossier`,
    `dev_optima_arrow`.`societe`.`societe` AS `societe`,
    `dev_optima_arrow`.`societe`.`code_client` AS `code_client`,
    case
        when `dev_optima_arrow`.`societe`.`id_famille` = 9 then `dev_optima_arrow`.`societe`.`particulier_nom`
        else `dev_optima_arrow`.`contact`.`nom`
    end AS `nom`,
    case
        when `dev_optima_arrow`.`societe`.`id_famille` = 9 then `dev_optima_arrow`.`societe`.`particulier_prenom`
        else `dev_optima_arrow`.`contact`.`prenom`
    end AS `prenom`,
    case
        when `dev_optima_arrow`.`societe`.`id_famille` = 9 then `dev_optima_arrow`.`societe`.`particulier_portable`
        else `dev_optima_arrow`.`contact`.`gsm`
    end AS `gsm`,
    case
        when `dev_optima_arrow`.`societe`.`id_famille` = 9 then `dev_optima_arrow`.`societe`.`particulier_email`
        else `dev_optima_arrow`.`contact`.`email`
    end AS `email`,
    `dev_optima_arrow`.`societe`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`contact`.`id_contact` AS `id_contact`,
    `dev_optima_arrow`.`societe`.`id_famille` AS `id_famille`,
    `dev_optima_arrow`.`famille`.`famille` AS `famille`
from
    (((`dev_optima_arrow`.`affaire`
left join `dev_optima_arrow`.`contact` on
    (`dev_optima_arrow`.`contact`.`id_societe` = `dev_optima_arrow`.`affaire`.`id_societe`))
left join `dev_optima_arrow`.`societe` on
    (`dev_optima_arrow`.`societe`.`id_societe` = `dev_optima_arrow`.`contact`.`id_societe`))
left join `dev_optima_arrow`.`famille` on
    (`dev_optima_arrow`.`famille`.`id_famille` = `dev_optima_arrow`.`societe`.`id_famille`))
where
    `dev_optima_arrow`.`contact`.`etat` = 'actif';


-- dev_optima_arrow.factures_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`factures_client` AS
select
    `dev_optima_arrow`.`facture`.`id_facture` AS `id_facture`,
    `dev_optima_arrow`.`facture`.`ref` AS `ref`,
    `dev_optima_arrow`.`facture`.`ref_externe` AS `ref_externe`,
    `dev_optima_arrow`.`facture`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`facture`.`prix` AS `prix`,
    `dev_optima_arrow`.`facture`.`etat` AS `etat`,
    `dev_optima_arrow`.`facture`.`date` AS `date`,
    `dev_optima_arrow`.`facture`.`date_paiement` AS `date_paiement`,
    `dev_optima_arrow`.`facture`.`type_facture` AS `type_facture`,
    `dev_optima_arrow`.`facture`.`date_periode_debut` AS `date_periode_debut`,
    `dev_optima_arrow`.`facture`.`date_periode_fin` AS `date_periode_fin`,
    `dev_optima_arrow`.`facture`.`tva` AS `tva`,
    `dev_optima_arrow`.`facture`.`prix_sans_tva` AS `prix_sans_tva`,
    `dev_optima_arrow`.`facture`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`facture`.`mode_paiement` AS `mode_paiement`,
    `dev_optima_arrow`.`facture`.`nature` AS `nature`,
    `dev_optima_arrow`.`facture`.`rejet` AS `rejet`,
    `dev_optima_arrow`.`facture`.`date_rejet` AS `date_rejet`,
    `dev_optima_arrow`.`facture`.`date_regularisation` AS `date_regularisation`,
    `dev_optima_arrow`.`facture`.`date_envoi` AS `date_envoi`,
    `dev_optima_arrow`.`facture`.`envoye` AS `envoye`
from
    `dev_optima_arrow`.`facture`
where
    `dev_optima_arrow`.`facture`.`type_facture` <> 'refi'
    and `dev_optima_arrow`.`facture`.`id_affaire` in (
    select
        `affaire_client`.`id_affaire`
    from
        `dev_optima_arrow`.`affaire_client`);


-- dev_optima_arrow.factures_conseiller source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`factures_conseiller` AS
select
    `dev_optima_arrow`.`facture`.`id_facture` AS `id_facture`,
    `dev_optima_arrow`.`facture`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`facture`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`facture`.`date` AS `date`,
    `dev_optima_arrow`.`facture`.`ref` AS `ref_facture`,
    `dev_optima_arrow`.`affaire`.`ref` AS `ref_affaire`,
    `dev_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `dev_optima_arrow`.`societe`.`code_client` AS `code_client`,
    `dev_optima_arrow`.`societe`.`societe` AS `societe`,
    `dev_optima_arrow`.`facture`.`prix` AS `prix`,
    `dev_optima_arrow`.`facture`.`tva` AS `tva`,
    `dev_optima_arrow`.`facture`.`prix_sans_tva` AS `prix_sans_tva`,
    `dev_optima_arrow`.`facture`.`etat` AS `etat`,
    `dev_optima_arrow`.`facture`.`date_envoi` AS `date_envoi`,
    `dev_optima_arrow`.`facture`.`envoye` AS `envoye`
from
    ((`dev_optima_arrow`.`facture`
left join `dev_optima_arrow`.`societe` on
    (`dev_optima_arrow`.`societe`.`id_societe` = `dev_optima_arrow`.`facture`.`id_societe`))
left join `dev_optima_arrow`.`affaire` on
    (`dev_optima_arrow`.`affaire`.`id_affaire` = `dev_optima_arrow`.`facture`.`id_affaire`))
where
    `dev_optima_arrow`.`facture`.`type_facture` <> 'refi'
    and `dev_optima_arrow`.`facture`.`id_affaire` in (
    select
        `affaire_client`.`id_affaire`
    from
        `dev_optima_arrow`.`affaire_client`)
    and `dev_optima_arrow`.`facture`.`envoye` = 'non';


-- dev_optima_arrow.fournisseur source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`fournisseur` AS
select
    `dev_optima_arrow`.`societe`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`societe`.`societe` AS `societe`,
    `dev_optima_arrow`.`societe`.`siret` AS `siret`,
    `dev_optima_arrow`.`societe`.`nom_commercial` AS `nom_commercial`,
    `dev_optima_arrow`.`societe`.`code_fournisseur` AS `code_fournisseur`
from
    `dev_optima_arrow`.`societe`
where
    `dev_optima_arrow`.`societe`.`fournisseur` = 'oui'
    and `dev_optima_arrow`.`societe`.`siret` is not null;


-- dev_optima_arrow.historique_affaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`historique_affaire` AS
select
    `dev_optima_arrow`.`affaire_etat`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`affaire_etat`.`date` AS `date`,
    `dev_optima_arrow`.`affaire_etat`.`etat` AS `etat`,
    `dev_optima_arrow`.`affaire_etat`.`commentaire` AS `commentaire`
from
    `dev_optima_arrow`.`affaire_etat`
order by
    `dev_optima_arrow`.`affaire_etat`.`id_affaire_etat`;


-- dev_optima_arrow.licence_affaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`licence_affaire` AS
select
    `dev_optima_arrow`.`affaire`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `dev_optima_arrow`.`affaire`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`affaire`.`ref` AS `ref`,
    `dev_optima_arrow`.`affaire`.`ref_externe` AS `ref_externe`,
    `dev_optima_arrow`.`licence`.`part_1` AS `licence_part1`,
    `dev_optima_arrow`.`licence`.`part_2` AS `licence_part2`,
    `dev_optima_arrow`.`licence`.`date_envoi` AS `date_envoi`,
    `dev_optima_arrow`.`licence`.`id_licence` AS `id_licence`,
    `dev_optima_arrow`.`licence_type`.`licence_type` AS `licence_type`,
    `dev_optima_arrow`.`licence_type`.`url_telechargement` AS `url_telechargement`
from
    ((((`dev_optima_arrow`.`licence`
left join `dev_optima_arrow`.`licence_type` on
    (`dev_optima_arrow`.`licence`.`id_licence_type` = `dev_optima_arrow`.`licence_type`.`id_licence_type`))
left join `dev_optima_arrow`.`commande_ligne` on
    (`dev_optima_arrow`.`commande_ligne`.`id_commande_ligne` = `dev_optima_arrow`.`licence`.`id_commande_ligne`))
left join `dev_optima_arrow`.`commande` on
    (`dev_optima_arrow`.`commande_ligne`.`id_commande` = `dev_optima_arrow`.`commande`.`id_commande`))
left join `dev_optima_arrow`.`affaire` on
    (`dev_optima_arrow`.`commande`.`id_affaire` = `dev_optima_arrow`.`affaire`.`id_affaire`))
where
    `dev_optima_arrow`.`licence`.`id_commande_ligne` is not null;


-- dev_optima_arrow.loyer_affaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`loyer_affaire` AS
select
    `dev_optima_arrow`.`loyer`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`loyer`.`type` AS `type`,
    coalesce(`dev_optima_arrow`.`loyer`.`loyer`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`assurance`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`frais_de_gestion`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`serenite`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`maintenance`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`hotline`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`supervision`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`support`, 0) AS `loyer`,
    coalesce(`dev_optima_arrow`.`loyer`.`loyer`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`frais_de_gestion`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`serenite`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`maintenance`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`hotline`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`supervision`, 0) + coalesce(`dev_optima_arrow`.`loyer`.`support`, 0) AS `loyer_sans_assurance`,
    coalesce(`dev_optima_arrow`.`loyer`.`assurance`, 0) AS `assurance`,
    `dev_optima_arrow`.`loyer`.`frequence_loyer` AS `frequence`,
    `dev_optima_arrow`.`loyer`.`duree` AS `duree`,
    `dev_optima_arrow`.`devis`.`tva` AS `tva`,
    `dev_optima_arrow`.`loyer`.`id_loyer` AS `id_loyer`,
    coalesce(`dev_optima_arrow`.`type_affaire`.`assurance_sans_tva`, 'non') AS `assurance_sans_tva`
from
    (((`dev_optima_arrow`.`loyer`
left join `dev_optima_arrow`.`affaire` on
    (`dev_optima_arrow`.`affaire`.`id_affaire` = `dev_optima_arrow`.`loyer`.`id_affaire`))
left join `dev_optima_arrow`.`devis` on
    (`dev_optima_arrow`.`devis`.`id_affaire` = `dev_optima_arrow`.`affaire`.`id_affaire`))
left join `dev_optima_arrow`.`type_affaire` on
    (`dev_optima_arrow`.`type_affaire`.`id_type_affaire` = `dev_optima_arrow`.`affaire`.`id_type_affaire`))
order by
    `dev_optima_arrow`.`loyer`.`id_loyer`;


-- dev_optima_arrow.parc_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`parc_client` AS
select
    `dev_optima_arrow`.`parc`.`id_parc` AS `id_parc`,
    `dev_optima_arrow`.`parc`.`id_societe` AS `id_societe`,
    `dev_optima_arrow`.`affaire`.`id_affaire` AS `id_affaire`,
    `dev_optima_arrow`.`affaire`.`ref` AS `ref_affaire`,
    `dev_optima_arrow`.`parc`.`ref` AS `ref`,
    `dev_optima_arrow`.`parc`.`libelle` AS `libelle`,
    `dev_optima_arrow`.`parc`.`divers` AS `divers`,
    `dev_optima_arrow`.`parc`.`serial` AS `serial`,
    `dev_optima_arrow`.`parc`.`code` AS `code`,
    `dev_optima_arrow`.`parc`.`date` AS `date`,
    `dev_optima_arrow`.`parc`.`date_inactif` AS `date_inactif`,
    `dev_optima_arrow`.`parc`.`date_garantie` AS `date_garantie`,
    `dev_optima_arrow`.`parc`.`date_achat` AS `date_achat`,
    `dev_optima_arrow`.`parc`.`existence` AS `existence`,
    `dev_optima_arrow`.`parc`.`etat` AS `etat`
from
    (`dev_optima_arrow`.`parc`
left join `dev_optima_arrow`.`affaire` on
    (`dev_optima_arrow`.`affaire`.`id_affaire` = `dev_optima_arrow`.`parc`.`id_affaire`))
where
    `dev_optima_arrow`.`parc`.`existence` = 'actif'
    and (`dev_optima_arrow`.`parc`.`etat` = 'loue'
        or `dev_optima_arrow`.`parc`.`etat` = 'reloue'
        or `dev_optima_arrow`.`parc`.`etat` = 'broke');


-- dev_optima_arrow.pointage_horaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `dev_optima_arrow`.`pointage_horaire` AS
select
    `dev_optima_arrow`.`tracabilite`.`id_tracabilite` AS `id_pointage_horaire`,
    `dev_optima_arrow`.`tracabilite`.`id_user` AS `id_user`,
    date_format(`dev_optima_arrow`.`tracabilite`.`date`, '%d-%m-%Y') AS `Jour`,
    date_format(min(`dev_optima_arrow`.`tracabilite`.`date`), '%H:%i') AS `debut`,
    date_format(max(`dev_optima_arrow`.`tracabilite`.`date`), '%H:%i') AS `fin`
from
    (`dev_optima_arrow`.`tracabilite`
join `dev_optima_arrow`.`user`)
where
    `dev_optima_arrow`.`tracabilite`.`id_user` = `dev_optima_arrow`.`user`.`id_user`
group by
    year(`dev_optima_arrow`.`tracabilite`.`date`),
    month(`dev_optima_arrow`.`tracabilite`.`date`),
    dayofmonth(`dev_optima_arrow`.`tracabilite`.`date`),
    `dev_optima_arrow`.`tracabilite`.`id_user`;