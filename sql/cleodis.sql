ALTER TABLE `facture` ADD `date_envoi` DATE NULL DEFAULT NULL AFTER `envoye_mail`;
ALTER TABLE `facture` ADD `envoye` ENUM('oui','non','erreur') NULL DEFAULT 'non' AFTER `date_envoi`;
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
ALTER TABLE `produit`
ADD `assurance` FLOAT(6,3) NULL AFTER `loyer`,
ADD `frais_de_gestion` FLOAT(6,3) NULL AFTER `assurance`,
ADD `serenite` FLOAT(6,3) NULL AFTER `frais_de_gestion`,
ADD `maintenance` FLOAT(6,3) NULL AFTER `serenite`,
ADD `hotline` FLOAT(6,3) NULL AFTER `maintenance`,
ADD `supervision` FLOAT(6,3) NULL AFTER `hotline`,
ADD `support` FLOAT(6,3) NULL AFTER `supervision`;
INSERT INTO constante
(constante, valeur)
VALUES('__SUIVI_SLIMPAY_UPDATE_BMN__', '');



ALTER TABLE `produit` DROP `loyer_sans_tva`;