-- rct_optima_arrow.abonnement_client source

-- rct_optima_cleodis.affaire_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`affaire_client` AS
select
    `rct_optima_arrow`.`affaire`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`affaire`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`affaire`.`ref` AS `ref`,
    `rct_optima_arrow`.`affaire`.`ref_externe` AS `ref_externe`,
    `rct_optima_arrow`.`affaire`.`date` AS `date`,
    `rct_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `rct_optima_arrow`.`affaire`.`id_parent` AS `id_parent`,
    `rct_optima_arrow`.`affaire`.`id_fille` AS `id_fille`,
    `rct_optima_arrow`.`affaire`.`nature` AS `nature`,
    `rct_optima_arrow`.`affaire`.`etat` AS `etat`,
    `rct_optima_arrow`.`affaire`.`date_garantie` AS `date_garantie`,
    `rct_optima_arrow`.`affaire`.`site_associe` AS `site_associe`,
    `rct_optima_arrow`.`affaire`.`mail_signature` AS `mail_signature`,
    `rct_optima_arrow`.`affaire`.`date_signature` AS `date_signature`,
    `rct_optima_arrow`.`affaire`.`signataire` AS `signataire`,
    `rct_optima_arrow`.`affaire`.`langue` AS `langue`,
    `rct_optima_arrow`.`affaire`.`adresse_livraison` AS `adresse_livraison`,
    `rct_optima_arrow`.`affaire`.`adresse_livraison_2` AS `adresse_livraison_2`,
    `rct_optima_arrow`.`affaire`.`adresse_livraison_3` AS `adresse_livraison_3`,
    `rct_optima_arrow`.`affaire`.`cp_adresse_livraison` AS `adresse_livraison_cp`,
    `rct_optima_arrow`.`affaire`.`ville_adresse_livraison` AS `adresse_livraison_ville`,
    `rct_optima_arrow`.`affaire`.`adresse_facturation` AS `adresse_facturation`,
    `rct_optima_arrow`.`affaire`.`adresse_facturation_2` AS `adresse_facturation_2`,
    `rct_optima_arrow`.`affaire`.`adresse_facturation_3` AS `adresse_facturation_3`,
    `rct_optima_arrow`.`affaire`.`cp_adresse_facturation` AS `adresse_facturation_cp`,
    `rct_optima_arrow`.`affaire`.`ville_adresse_facturation` AS `adresse_facturation_ville`,
    `rct_optima_arrow`.`affaire`.`id_partenaire` AS `id_partenaire`,
    `rct_optima_arrow`.`affaire`.`id_apporteur` AS `id_apporteur`,
    `rct_optima_arrow`.`affaire`.`id_commercial` AS `id_commercial`,
    `rct_optima_arrow`.`affaire`.`id_magasin` AS `id_magasin`,
    `rct_optima_arrow`.`affaire`.`vendeur` AS `vendeur`,
    `rct_optima_arrow`.`affaire`.`demande_lixxbail` AS `demande_lixxbail`,
    `rct_optima_arrow`.`magasin`.`magasin` AS `magasin`,
    `partenaire`.`societe` AS `partenaire`,
    `apporteur`.`societe` AS `apporteur`
from
    (((((`rct_optima_arrow`.`affaire`
left join `rct_optima_arrow`.`magasin` on
    (`rct_optima_arrow`.`magasin`.`id_magasin` = `rct_optima_arrow`.`affaire`.`id_magasin`))
left join `rct_optima_arrow`.`societe` `client` on
    (`client`.`id_societe` = `rct_optima_arrow`.`affaire`.`id_societe`))
left join `rct_optima_arrow`.`societe` `partenaire` on
    (`rct_optima_arrow`.`affaire`.`id_partenaire` = `partenaire`.`id_societe`))
left join `rct_optima_arrow`.`societe` `apporteur` on
    (`rct_optima_arrow`.`affaire`.`id_apporteur` = `apporteur`.`id_societe`))
left join `rct_optima_arrow`.`commande` on
    (`rct_optima_arrow`.`commande`.`id_affaire` = `rct_optima_arrow`.`affaire`.`id_affaire`))
where
    `rct_optima_arrow`.`affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and (`rct_optima_arrow`.`commande`.`etat` in ('non_loyer', 'mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux', 'AR', 'arreter')
        or `rct_optima_arrow`.`affaire`.`nature` = 'vente');

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`abonnement_client` AS
select
    `rct_optima_arrow`.`commande`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`commande`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`commande`.`id_commande` AS `id_commande`,
    `rct_optima_arrow`.`commande`.`ref` AS `num_dossier`,
    `rct_optima_arrow`.`commande`.`commande` AS `dossier`,
    `rct_optima_arrow`.`commande`.`etat` AS `statut`,
    `rct_optima_arrow`.`commande`.`date` AS `date`,
    `rct_optima_arrow`.`commande`.`date_debut` AS `date_debut`,
    `rct_optima_arrow`.`commande`.`date_evolution` AS `date_fin`,
    `rct_optima_arrow`.`commande`.`date_arret` AS `date_arret`,
    `rct_optima_arrow`.`commande`.`retour_contrat` AS `retour_contrat`,
    `rct_optima_arrow`.`affaire`.`IBAN` AS `IBAN`,
    `rct_optima_arrow`.`affaire`.`BIC` AS `BIC`,
    `rct_optima_arrow`.`affaire`.`RUM` AS `RUM`,
    `rct_optima_arrow`.`affaire`.`site_associe` AS `site_associe`,
    `rct_optima_arrow`.`affaire`.`id_type_affaire` AS `id_type_affaire`,
    `rct_optima_arrow`.`contact`.`prenom` AS `titulaire_prenom`,
    `rct_optima_arrow`.`contact`.`nom` AS `titulaire_nom`
from
    (((`rct_optima_arrow`.`commande`
left join `rct_optima_arrow`.`affaire` on
    (`rct_optima_arrow`.`commande`.`id_affaire` = `rct_optima_arrow`.`affaire`.`id_affaire`))
left join `rct_optima_arrow`.`societe` on
    (`rct_optima_arrow`.`societe`.`id_societe` = `rct_optima_arrow`.`affaire`.`id_societe`))
left join `rct_optima_arrow`.`contact` on
    (`rct_optima_arrow`.`contact`.`id_contact` = `rct_optima_arrow`.`societe`.`id_contact_facturation`))
where
    `rct_optima_arrow`.`affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and (`rct_optima_arrow`.`commande`.`etat` in ('non_loyer', 'mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux', 'AR', 'arreter')
        or `rct_optima_arrow`.`affaire`.`nature` = 'vente'
        or `rct_optima_arrow`.`affaire`.`nature` = 'AR');


-- rct_optima_arrow.abonnement_detail source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`abonnement_detail` AS
select
    `rct_optima_arrow`.`commande`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`commande`.`id_commande` AS `id_commande`,
    `rct_optima_arrow`.`commande_ligne`.`ref` AS `ref`,
    `rct_optima_arrow`.`commande_ligne`.`produit` AS `produit`,
    `rct_optima_arrow`.`commande_ligne`.`quantite` AS `quantite`
from
    (`rct_optima_arrow`.`commande_ligne`
join `rct_optima_arrow`.`commande` on
    (`rct_optima_arrow`.`commande_ligne`.`id_commande` = `rct_optima_arrow`.`commande`.`id_commande`))
where
    `rct_optima_arrow`.`commande_ligne`.`visible` = 'oui'
order by
    `rct_optima_arrow`.`commande`.`id_affaire`,
    `rct_optima_arrow`.`commande_ligne`.`ordre`;


-- rct_optima_arrow.affaire_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`affaire_client` AS
select
    `rct_optima_arrow`.`affaire`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`affaire`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`affaire`.`ref` AS `ref`,
    `rct_optima_arrow`.`affaire`.`ref_externe` AS `ref_externe`,
    `rct_optima_arrow`.`affaire`.`date` AS `date`,
    `rct_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `rct_optima_arrow`.`affaire`.`id_parent` AS `id_parent`,
    `rct_optima_arrow`.`affaire`.`id_fille` AS `id_fille`,
    `rct_optima_arrow`.`affaire`.`nature` AS `nature`,
    `rct_optima_arrow`.`affaire`.`etat` AS `etat`,
    `rct_optima_arrow`.`affaire`.`date_garantie` AS `date_garantie`,
    `rct_optima_arrow`.`affaire`.`site_associe` AS `site_associe`,
    `rct_optima_arrow`.`affaire`.`mail_signature` AS `mail_signature`,
    `rct_optima_arrow`.`affaire`.`date_signature` AS `date_signature`,
    `rct_optima_arrow`.`affaire`.`signataire` AS `signataire`,
    `rct_optima_arrow`.`affaire`.`langue` AS `langue`,
    `rct_optima_arrow`.`affaire`.`adresse_livraison` AS `adresse_livraison`,
    `rct_optima_arrow`.`affaire`.`adresse_livraison_2` AS `adresse_livraison_2`,
    `rct_optima_arrow`.`affaire`.`adresse_livraison_3` AS `adresse_livraison_3`,
    `rct_optima_arrow`.`affaire`.`cp_adresse_livraison` AS `adresse_livraison_cp`,
    `rct_optima_arrow`.`affaire`.`ville_adresse_livraison` AS `adresse_livraison_ville`,
    `rct_optima_arrow`.`affaire`.`adresse_facturation` AS `adresse_facturation`,
    `rct_optima_arrow`.`affaire`.`adresse_facturation_2` AS `adresse_facturation_2`,
    `rct_optima_arrow`.`affaire`.`adresse_facturation_3` AS `adresse_facturation_3`,
    `rct_optima_arrow`.`affaire`.`cp_adresse_facturation` AS `adresse_facturation_cp`,
    `rct_optima_arrow`.`affaire`.`ville_adresse_facturation` AS `adresse_facturation_ville`,
    `rct_optima_arrow`.`affaire`.`id_partenaire` AS `id_partenaire`,
    `rct_optima_arrow`.`affaire`.`id_apporteur` AS `id_apporteur`,
    `rct_optima_arrow`.`affaire`.`id_commercial` AS `id_commercial`,
    `rct_optima_arrow`.`affaire`.`id_magasin` AS `id_magasin`,
    `rct_optima_arrow`.`affaire`.`vendeur` AS `vendeur`,
    `rct_optima_arrow`.`affaire`.`demande_lixxbail` AS `demande_lixxbail`,
    `rct_optima_arrow`.`magasin`.`magasin` AS `magasin`,
    `partenaire`.`societe` AS `partenaire`,
    `apporteur`.`societe` AS `apporteur`
from
    (((((`rct_optima_arrow`.`affaire`
left join `rct_optima_arrow`.`magasin` on
    (`rct_optima_arrow`.`magasin`.`id_magasin` = `rct_optima_arrow`.`affaire`.`id_magasin`))
left join `rct_optima_arrow`.`societe` `client` on
    (`client`.`id_societe` = `rct_optima_arrow`.`affaire`.`id_societe`))
left join `rct_optima_arrow`.`societe` `partenaire` on
    (`rct_optima_arrow`.`affaire`.`id_partenaire` = `partenaire`.`id_societe`))
left join `rct_optima_arrow`.`societe` `apporteur` on
    (`rct_optima_arrow`.`affaire`.`id_apporteur` = `apporteur`.`id_societe`))
left join `rct_optima_arrow`.`commande` on
    (`rct_optima_arrow`.`commande`.`id_affaire` = `rct_optima_arrow`.`affaire`.`id_affaire`))
where
    `rct_optima_arrow`.`affaire`.`etat` not in ('demande_refi', 'facture_refi')
    and (`rct_optima_arrow`.`commande`.`etat` in ('non_loyer', 'mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux', 'AR', 'arreter')
        or `rct_optima_arrow`.`affaire`.`nature` = 'vente');


-- rct_optima_arrow.bon_de_commande_non_envoyes source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`bon_de_commande_non_envoyes` AS
select
    `rct_optima_arrow`.`bon_de_commande`.`id_bon_de_commande` AS `id_bon_de_commande`,
    `rct_optima_arrow`.`bon_de_commande`.`ref` AS `ref`,
    `rct_optima_arrow`.`bon_de_commande`.`prix` AS `prix`,
    `client`.`societe` AS `client`,
    `rct_optima_arrow`.`affaire`.`ref` AS `ref_affaire`,
    `rct_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `rct_optima_arrow`.`bon_de_commande`.`envoye_par_mail` AS `envoye_par_mail`,
    `fournisseur`.`id_societe` AS `id_fournisseur`,
    `fournisseur`.`societe` AS `fournisseur`
from
    (((`rct_optima_arrow`.`bon_de_commande`
left join `rct_optima_arrow`.`affaire` on
    (`rct_optima_arrow`.`affaire`.`id_affaire` = `rct_optima_arrow`.`bon_de_commande`.`id_affaire`))
left join `rct_optima_arrow`.`societe` `client` on
    (`client`.`id_societe` = `rct_optima_arrow`.`bon_de_commande`.`id_societe`))
left join `rct_optima_arrow`.`societe` `fournisseur` on
    (`fournisseur`.`id_societe` = `rct_optima_arrow`.`bon_de_commande`.`id_fournisseur`))
where
    `rct_optima_arrow`.`bon_de_commande`.`envoye_par_mail` is null;


-- rct_optima_arrow.commande_attente source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`commande_attente` AS
select
    `rct_optima_arrow`.`commande`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`commande`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`commande`.`id_commande` AS `id_commande`,
    `rct_optima_arrow`.`commande`.`ref` AS `num_dossier`,
    `rct_optima_arrow`.`commande`.`commande` AS `dossier`,
    `rct_optima_arrow`.`commande`.`etat` AS `statut`,
    `rct_optima_arrow`.`commande`.`date` AS `date`,
    `rct_optima_arrow`.`commande`.`date_debut` AS `date_debut`,
    `rct_optima_arrow`.`commande`.`date_evolution` AS `date_fin`,
    `rct_optima_arrow`.`commande`.`date_arret` AS `date_arret`,
    `rct_optima_arrow`.`commande`.`retour_contrat` AS `retour_contrat`
from
    `rct_optima_arrow`.`commande`
where
    `rct_optima_arrow`.`commande`.`etat` = 'non_loyer'
order by
    `rct_optima_arrow`.`commande`.`ref` desc;


-- rct_optima_arrow.contact_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`contact_client` AS
select
    `rct_optima_arrow`.`contact`.`id_contact` AS `id_contact`,
    `rct_optima_arrow`.`contact`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`contact`.`email` AS `email`,
    `rct_optima_arrow`.`contact`.`anniversaire` AS `anniversaire`,
    `rct_optima_arrow`.`contact`.`civilite` AS `civilite`,
    `rct_optima_arrow`.`contact`.`nom` AS `nom`,
    `rct_optima_arrow`.`contact`.`prenom` AS `prenom`,
    `rct_optima_arrow`.`contact`.`situation_maritale` AS `situation_maritale`,
    `rct_optima_arrow`.`contact`.`situation_perso` AS `situation_perso`,
    `rct_optima_arrow`.`contact`.`fonction` AS `fonction`,
    `rct_optima_arrow`.`contact`.`situation_pro` AS `situation_pro`
from
    `rct_optima_arrow`.`contact`
where
    `rct_optima_arrow`.`contact`.`etat` = 'actif';


-- rct_optima_arrow.coordonnees_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`coordonnees_client` AS
select
    `rct_optima_arrow`.`societe`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`societe`.`ref` AS `ref`,
    `rct_optima_arrow`.`societe`.`societe` AS `societe`,
    `rct_optima_arrow`.`famille`.`famille` AS `type_client`,
    `rct_optima_arrow`.`societe`.`id_famille` AS `id_famille`,
    `rct_optima_arrow`.`societe`.`nom_commercial` AS `nom_commercial`,
    `rct_optima_arrow`.`societe`.`adresse` AS `adresse`,
    `rct_optima_arrow`.`societe`.`adresse_2` AS `adresse_2`,
    `rct_optima_arrow`.`societe`.`adresse_3` AS `adresse_3`,
    `rct_optima_arrow`.`societe`.`cp` AS `cp`,
    `rct_optima_arrow`.`societe`.`ville` AS `ville`,
    `rct_optima_arrow`.`societe`.`facturation_adresse` AS `facturation_adresse`,
    `rct_optima_arrow`.`societe`.`facturation_adresse_2` AS `facturation_adresse_2`,
    `rct_optima_arrow`.`societe`.`facturation_adresse_3` AS `facturation_adresse_3`,
    `rct_optima_arrow`.`societe`.`facturation_cp` AS `facturation_cp`,
    `rct_optima_arrow`.`societe`.`facturation_ville` AS `facturation_ville`,
    `rct_optima_arrow`.`societe`.`livraison_adresse` AS `livraison_adresse`,
    `rct_optima_arrow`.`societe`.`livraison_adresse_2` AS `livraison_adresse_2`,
    `rct_optima_arrow`.`societe`.`livraison_adresse_3` AS `livraison_adresse_3`,
    `rct_optima_arrow`.`societe`.`livraison_cp` AS `livraison_cp`,
    `rct_optima_arrow`.`societe`.`livraison_ville` AS `livraison_ville`,
    `rct_optima_arrow`.`societe`.`email` AS `email`,
    `rct_optima_arrow`.`societe`.`tel` AS `tel`,
    `rct_optima_arrow`.`societe`.`particulier_civilite` AS `particulier_civilite`,
    `rct_optima_arrow`.`societe`.`particulier_nom` AS `particulier_nom`,
    `rct_optima_arrow`.`societe`.`particulier_prenom` AS `particulier_prenom`,
    `rct_optima_arrow`.`societe`.`particulier_portable` AS `particulier_portable`,
    `rct_optima_arrow`.`societe`.`num_carte_fidelite` AS `num_carte_fidelite`,
    `rct_optima_arrow`.`societe`.`particulier_fixe` AS `particulier_fixe`,
    `rct_optima_arrow`.`societe`.`particulier_email` AS `particulier_email`,
    `rct_optima_arrow`.`societe`.`code_client` AS `code_client`,
    `rct_optima_arrow`.`societe`.`id_apporteur` AS `id_apporteur`,
    `rct_optima_arrow`.`societe`.`langue` AS `langue`,
    `rct_optima_arrow`.`societe`.`siret` AS `siret`
from
    (`rct_optima_arrow`.`societe`
join `rct_optima_arrow`.`famille` on
    (`rct_optima_arrow`.`famille`.`id_famille` = `rct_optima_arrow`.`societe`.`id_famille`));


-- rct_optima_arrow.echeancier source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`echeancier` AS
select
    `rct_optima_arrow`.`facturation`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`facturation`.`id_societe` AS `societe`,
    `rct_optima_arrow`.`facturation`.`date_periode_debut` AS `debut_periode`,
    `rct_optima_arrow`.`facturation`.`date_periode_fin` AS `fin_periode`,
    coalesce(`rct_optima_arrow`.`facturation`.`frais_de_gestion`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`montant`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`serenite`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`maintenance`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`hotline`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`supervision`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`support`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`assurance`, 0) AS `montant`,
    coalesce(`rct_optima_arrow`.`facturation`.`frais_de_gestion`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`montant`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`serenite`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`maintenance`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`hotline`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`supervision`, 0) + coalesce(`rct_optima_arrow`.`facturation`.`support`, 0) AS `montant_sans_assurance`,
    coalesce(`rct_optima_arrow`.`facturation`.`assurance`, 0) AS `assurance`,
    coalesce(`rct_optima_arrow`.`type_affaire`.`assurance_sans_tva`, 'non') AS `assurance_sans_tva`,
    `rct_optima_arrow`.`facturation`.`type` AS `type`,
    `rct_optima_arrow`.`facturation`.`id_facture` AS `id_facture`,
    `rct_optima_arrow`.`devis`.`tva` AS `tva`
from
    (((`rct_optima_arrow`.`facturation`
left join `rct_optima_arrow`.`affaire` on
    (`rct_optima_arrow`.`affaire`.`id_affaire` = `rct_optima_arrow`.`facturation`.`id_affaire`))
left join `rct_optima_arrow`.`devis` on
    (`rct_optima_arrow`.`devis`.`id_affaire` = `rct_optima_arrow`.`affaire`.`id_affaire`))
left join `rct_optima_arrow`.`type_affaire` on
    (`rct_optima_arrow`.`type_affaire`.`id_type_affaire` = `rct_optima_arrow`.`affaire`.`id_type_affaire`));


-- rct_optima_arrow.espace_client_inscription source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`espace_client_inscription` AS
select
    `rct_optima_arrow`.`affaire`.`ref` AS `dossier`,
    `rct_optima_arrow`.`societe`.`societe` AS `societe`,
    `rct_optima_arrow`.`societe`.`code_client` AS `code_client`,
    case
        when `rct_optima_arrow`.`societe`.`id_famille` = 9 then `rct_optima_arrow`.`societe`.`particulier_nom`
        else `rct_optima_arrow`.`contact`.`nom`
    end AS `nom`,
    case
        when `rct_optima_arrow`.`societe`.`id_famille` = 9 then `rct_optima_arrow`.`societe`.`particulier_prenom`
        else `rct_optima_arrow`.`contact`.`prenom`
    end AS `prenom`,
    case
        when `rct_optima_arrow`.`societe`.`id_famille` = 9 then `rct_optima_arrow`.`societe`.`particulier_portable`
        else `rct_optima_arrow`.`contact`.`gsm`
    end AS `gsm`,
    case
        when `rct_optima_arrow`.`societe`.`id_famille` = 9 then `rct_optima_arrow`.`societe`.`particulier_email`
        else `rct_optima_arrow`.`contact`.`email`
    end AS `email`,
    `rct_optima_arrow`.`societe`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`contact`.`id_contact` AS `id_contact`,
    `rct_optima_arrow`.`societe`.`id_famille` AS `id_famille`,
    `rct_optima_arrow`.`famille`.`famille` AS `famille`
from
    (((`rct_optima_arrow`.`affaire`
left join `rct_optima_arrow`.`contact` on
    (`rct_optima_arrow`.`contact`.`id_societe` = `rct_optima_arrow`.`affaire`.`id_societe`))
left join `rct_optima_arrow`.`societe` on
    (`rct_optima_arrow`.`societe`.`id_societe` = `rct_optima_arrow`.`contact`.`id_societe`))
left join `rct_optima_arrow`.`famille` on
    (`rct_optima_arrow`.`famille`.`id_famille` = `rct_optima_arrow`.`societe`.`id_famille`))
where
    `rct_optima_arrow`.`contact`.`etat` = 'actif';


-- rct_optima_arrow.factures_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`factures_client` AS
select
    `rct_optima_arrow`.`facture`.`id_facture` AS `id_facture`,
    `rct_optima_arrow`.`facture`.`ref` AS `ref`,
    `rct_optima_arrow`.`facture`.`ref_externe` AS `ref_externe`,
    `rct_optima_arrow`.`facture`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`facture`.`prix` AS `prix`,
    `rct_optima_arrow`.`facture`.`etat` AS `etat`,
    `rct_optima_arrow`.`facture`.`date` AS `date`,
    `rct_optima_arrow`.`facture`.`date_paiement` AS `date_paiement`,
    `rct_optima_arrow`.`facture`.`type_facture` AS `type_facture`,
    `rct_optima_arrow`.`facture`.`date_periode_debut` AS `date_periode_debut`,
    `rct_optima_arrow`.`facture`.`date_periode_fin` AS `date_periode_fin`,
    `rct_optima_arrow`.`facture`.`tva` AS `tva`,
    `rct_optima_arrow`.`facture`.`prix_sans_tva` AS `prix_sans_tva`,
    `rct_optima_arrow`.`facture`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`facture`.`mode_paiement` AS `mode_paiement`,
    `rct_optima_arrow`.`facture`.`nature` AS `nature`,
    `rct_optima_arrow`.`facture`.`rejet` AS `rejet`,
    `rct_optima_arrow`.`facture`.`date_rejet` AS `date_rejet`,
    `rct_optima_arrow`.`facture`.`date_regularisation` AS `date_regularisation`,
    `rct_optima_arrow`.`facture`.`date_envoi` AS `date_envoi`,
    `rct_optima_arrow`.`facture`.`envoye` AS `envoye`
from
    `rct_optima_arrow`.`facture`
where
    `rct_optima_arrow`.`facture`.`type_facture` <> 'refi'
    and `rct_optima_arrow`.`facture`.`id_affaire` in (
    select
        `affaire_client`.`id_affaire`
    from
        `rct_optima_arrow`.`affaire_client`);


-- rct_optima_arrow.factures_conseiller source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`factures_conseiller` AS
select
    `rct_optima_arrow`.`facture`.`id_facture` AS `id_facture`,
    `rct_optima_arrow`.`facture`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`facture`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`facture`.`date` AS `date`,
    `rct_optima_arrow`.`facture`.`ref` AS `ref_facture`,
    `rct_optima_arrow`.`affaire`.`ref` AS `ref_affaire`,
    `rct_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `rct_optima_arrow`.`societe`.`code_client` AS `code_client`,
    `rct_optima_arrow`.`societe`.`societe` AS `societe`,
    `rct_optima_arrow`.`facture`.`prix` AS `prix`,
    `rct_optima_arrow`.`facture`.`tva` AS `tva`,
    `rct_optima_arrow`.`facture`.`prix_sans_tva` AS `prix_sans_tva`,
    `rct_optima_arrow`.`facture`.`etat` AS `etat`,
    `rct_optima_arrow`.`facture`.`date_envoi` AS `date_envoi`,
    `rct_optima_arrow`.`facture`.`envoye` AS `envoye`
from
    ((`rct_optima_arrow`.`facture`
left join `rct_optima_arrow`.`societe` on
    (`rct_optima_arrow`.`societe`.`id_societe` = `rct_optima_arrow`.`facture`.`id_societe`))
left join `rct_optima_arrow`.`affaire` on
    (`rct_optima_arrow`.`affaire`.`id_affaire` = `rct_optima_arrow`.`facture`.`id_affaire`))
where
    `rct_optima_arrow`.`facture`.`type_facture` <> 'refi'
    and `rct_optima_arrow`.`facture`.`id_affaire` in (
    select
        `affaire_client`.`id_affaire`
    from
        `rct_optima_arrow`.`affaire_client`)
    and `rct_optima_arrow`.`facture`.`envoye` = 'non';


-- rct_optima_arrow.fournisseur source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`fournisseur` AS
select
    `rct_optima_arrow`.`societe`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`societe`.`societe` AS `societe`,
    `rct_optima_arrow`.`societe`.`siret` AS `siret`,
    `rct_optima_arrow`.`societe`.`nom_commercial` AS `nom_commercial`,
    `rct_optima_arrow`.`societe`.`code_fournisseur` AS `code_fournisseur`
from
    `rct_optima_arrow`.`societe`
where
    `rct_optima_arrow`.`societe`.`fournisseur` = 'oui'
    and `rct_optima_arrow`.`societe`.`siret` is not null;


-- rct_optima_arrow.historique_affaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`historique_affaire` AS
select
    `rct_optima_arrow`.`affaire_etat`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`affaire_etat`.`date` AS `date`,
    `rct_optima_arrow`.`affaire_etat`.`etat` AS `etat`,
    `rct_optima_arrow`.`affaire_etat`.`commentaire` AS `commentaire`
from
    `rct_optima_arrow`.`affaire_etat`
order by
    `rct_optima_arrow`.`affaire_etat`.`id_affaire_etat`;


-- rct_optima_arrow.licence_affaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`licence_affaire` AS
select
    `rct_optima_arrow`.`affaire`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`affaire`.`affaire` AS `affaire`,
    `rct_optima_arrow`.`affaire`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`affaire`.`ref` AS `ref`,
    `rct_optima_arrow`.`affaire`.`ref_externe` AS `ref_externe`,
    `rct_optima_arrow`.`licence`.`part_1` AS `licence_part1`,
    `rct_optima_arrow`.`licence`.`part_2` AS `licence_part2`,
    `rct_optima_arrow`.`licence`.`date_envoi` AS `date_envoi`,
    `rct_optima_arrow`.`licence`.`id_licence` AS `id_licence`,
    `rct_optima_arrow`.`licence_type`.`licence_type` AS `licence_type`,
    `rct_optima_arrow`.`licence_type`.`url_telechargement` AS `url_telechargement`
from
    ((((`rct_optima_arrow`.`licence`
left join `rct_optima_arrow`.`licence_type` on
    (`rct_optima_arrow`.`licence`.`id_licence_type` = `rct_optima_arrow`.`licence_type`.`id_licence_type`))
left join `rct_optima_arrow`.`commande_ligne` on
    (`rct_optima_arrow`.`commande_ligne`.`id_commande_ligne` = `rct_optima_arrow`.`licence`.`id_commande_ligne`))
left join `rct_optima_arrow`.`commande` on
    (`rct_optima_arrow`.`commande_ligne`.`id_commande` = `rct_optima_arrow`.`commande`.`id_commande`))
left join `rct_optima_arrow`.`affaire` on
    (`rct_optima_arrow`.`commande`.`id_affaire` = `rct_optima_arrow`.`affaire`.`id_affaire`))
where
    `rct_optima_arrow`.`licence`.`id_commande_ligne` is not null;


-- rct_optima_arrow.loyer_affaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`loyer_affaire` AS
select
    `rct_optima_arrow`.`loyer`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`loyer`.`type` AS `type`,
    coalesce(`rct_optima_arrow`.`loyer`.`loyer`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`assurance`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`frais_de_gestion`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`serenite`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`maintenance`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`hotline`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`supervision`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`support`, 0) AS `loyer`,
    coalesce(`rct_optima_arrow`.`loyer`.`loyer`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`frais_de_gestion`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`serenite`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`maintenance`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`hotline`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`supervision`, 0) + coalesce(`rct_optima_arrow`.`loyer`.`support`, 0) AS `loyer_sans_assurance`,
    coalesce(`rct_optima_arrow`.`loyer`.`assurance`, 0) AS `assurance`,
    `rct_optima_arrow`.`loyer`.`frequence_loyer` AS `frequence`,
    `rct_optima_arrow`.`loyer`.`duree` AS `duree`,
    `rct_optima_arrow`.`devis`.`tva` AS `tva`,
    `rct_optima_arrow`.`loyer`.`id_loyer` AS `id_loyer`,
    coalesce(`rct_optima_arrow`.`type_affaire`.`assurance_sans_tva`, 'non') AS `assurance_sans_tva`
from
    (((`rct_optima_arrow`.`loyer`
left join `rct_optima_arrow`.`affaire` on
    (`rct_optima_arrow`.`affaire`.`id_affaire` = `rct_optima_arrow`.`loyer`.`id_affaire`))
left join `rct_optima_arrow`.`devis` on
    (`rct_optima_arrow`.`devis`.`id_affaire` = `rct_optima_arrow`.`affaire`.`id_affaire`))
left join `rct_optima_arrow`.`type_affaire` on
    (`rct_optima_arrow`.`type_affaire`.`id_type_affaire` = `rct_optima_arrow`.`affaire`.`id_type_affaire`))
order by
    `rct_optima_arrow`.`loyer`.`id_loyer`;


-- rct_optima_arrow.parc_client source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`parc_client` AS
select
    `rct_optima_arrow`.`parc`.`id_parc` AS `id_parc`,
    `rct_optima_arrow`.`parc`.`id_societe` AS `id_societe`,
    `rct_optima_arrow`.`affaire`.`id_affaire` AS `id_affaire`,
    `rct_optima_arrow`.`affaire`.`ref` AS `ref_affaire`,
    `rct_optima_arrow`.`parc`.`ref` AS `ref`,
    `rct_optima_arrow`.`parc`.`libelle` AS `libelle`,
    `rct_optima_arrow`.`parc`.`divers` AS `divers`,
    `rct_optima_arrow`.`parc`.`serial` AS `serial`,
    `rct_optima_arrow`.`parc`.`code` AS `code`,
    `rct_optima_arrow`.`parc`.`date` AS `date`,
    `rct_optima_arrow`.`parc`.`date_inactif` AS `date_inactif`,
    `rct_optima_arrow`.`parc`.`date_garantie` AS `date_garantie`,
    `rct_optima_arrow`.`parc`.`date_achat` AS `date_achat`,
    `rct_optima_arrow`.`parc`.`existence` AS `existence`,
    `rct_optima_arrow`.`parc`.`etat` AS `etat`
from
    (`rct_optima_arrow`.`parc`
left join `rct_optima_arrow`.`affaire` on
    (`rct_optima_arrow`.`affaire`.`id_affaire` = `rct_optima_arrow`.`parc`.`id_affaire`))
where
    `rct_optima_arrow`.`parc`.`existence` = 'actif'
    and (`rct_optima_arrow`.`parc`.`etat` = 'loue'
        or `rct_optima_arrow`.`parc`.`etat` = 'reloue'
        or `rct_optima_arrow`.`parc`.`etat` = 'broke');


-- rct_optima_arrow.pointage_horaire source

CREATE OR REPLACE
ALGORITHM = UNDEFINED VIEW `rct_optima_arrow`.`pointage_horaire` AS
select
    `rct_optima_arrow`.`tracabilite`.`id_tracabilite` AS `id_pointage_horaire`,
    `rct_optima_arrow`.`tracabilite`.`id_user` AS `id_user`,
    date_format(`rct_optima_arrow`.`tracabilite`.`date`, '%d-%m-%Y') AS `Jour`,
    date_format(min(`rct_optima_arrow`.`tracabilite`.`date`), '%H:%i') AS `debut`,
    date_format(max(`rct_optima_arrow`.`tracabilite`.`date`), '%H:%i') AS `fin`
from
    (`rct_optima_arrow`.`tracabilite`
join `rct_optima_arrow`.`user`)
where
    `rct_optima_arrow`.`tracabilite`.`id_user` = `rct_optima_arrow`.`user`.`id_user`
group by
    year(`rct_optima_arrow`.`tracabilite`.`date`),
    month(`rct_optima_arrow`.`tracabilite`.`date`),
    dayofmonth(`rct_optima_arrow`.`tracabilite`.`date`),
    `rct_optima_arrow`.`tracabilite`.`id_user`;