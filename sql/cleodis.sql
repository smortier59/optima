    -- Affaire de vente
ALTER TABLE `panier` ADD `nature` ENUM('location','vente') NOT NULL DEFAULT 'location' AFTER `date`;
ALTER TABLE `produit` ADD `prix_vente` FLOAT(8,2) NULL DEFAULT NULL AFTER `support`;
-- MAJ VUE Affaire Espace client pour afficher si la demande lixxbail est faite
ALTER TABLE `affaire` ADD `demande_lixxbail` ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Permet de savoir si une demande Lixxbail a été faite pour ce dossier' AFTER `id_type_affaire`;

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
    `affaire`.`etat` AS `etat`,
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
    `affaire`.`id_apporteur` AS `id_apporteur`,
    `affaire`.`id_commercial` AS `id_commercial`,
    `affaire`.`id_magasin` AS `id_magasin`,
    `affaire`.`vendeur` AS `vendeur`,
    `affaire`.`demande_lixxbail` AS `demande_lixxbail`,
    `magasin`.`magasin` AS `magasin`,
    `partenaire`.`societe` AS `partenaire`,
    `apporteur`.`societe` AS `apporteur`
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
    and (`commande`.`etat` in ('non_loyer', 'mis_loyer', 'prolongation', 'restitution', 'mis_loyer_contentieux', 'prolongation_contentieux', 'restitution_contentieux', 'AR', 'arreter')
        or `affaire`.`nature` = 'vente');


-- TH 30409 - Export Vente - Num Facture
ALTER TABLE `facture` ADD `numero` VARCHAR(12) NULL DEFAULT NULL AFTER `ref`;


-- TH 30592 - Grille tarifaire - Calcul de loyer automatisé
CREATE TABLE grille_tarifaire (
    `id_grille_tarifaire` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `nom` VARCHAR(50) NOT NULL ,
    `description` TEXT NOT NULL ,
    `id_type_affaire` INT NULL,
    `etat` ENUM('ACTIF','INACTIF') NOT NULL DEFAULT 'ACTIF' ,
    `date_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id_grille_tarifaire`),
    INDEX (`id_type_affaire`))
ENGINE = InnoDB;
ALTER TABLE `grille_tarifaire` ADD FOREIGN KEY (`id_type_affaire`) REFERENCES `type_affaire`(`id_type_affaire`) ON DELETE RESTRICT ON UPDATE RESTRICT;

CREATE TABLE grille_tarifaire_ligne (
    `id_grille_tarifaire_ligne` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_grille_tarifaire` MEDIUMINT UNSIGNED NOT NULL ,
    `montant_max` MEDIUMINT NOT NULL ,
    `duree` SMALLINT NOT NULL ,
    `periodicite` ENUM('MOIS','TRIMESTRE','SEMESTRE','AN') NOT NULL DEFAULT 'MOIS' ,
    `coefficient` DECIMAL(6,3) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id_grille_tarifaire_ligne`),
    INDEX (`id_grille_tarifaire`))
ENGINE = InnoDB;
ALTER TABLE `grille_tarifaire_ligne` ADD FOREIGN KEY (`id_grille_tarifaire`) REFERENCES `grille_tarifaire`(`id_grille_tarifaire`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `pack_produit_partenaire` ( `id_pack_produit_partenaire` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
 `id_pack_produit` MEDIUMINT UNSIGNED NOT NULL ,
 `id_partenaire` MEDIUMINT UNSIGNED NOT NULL ,
 `etat` ENUM('actif','inactif') NOT NULL DEFAULT 'actif' ,
PRIMARY KEY (`id_pack_produit_partenaire`)) ENGINE = InnoDB;


ALTER TABLE `pack_produit_partenaire` ADD FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit`(`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `pack_produit_partenaire` ADD FOREIGN KEY (`id_partenaire`) REFERENCES `societe`(`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

GRANT SELECT ON `optima_arrow`.`pack_produit_partenaire` TO 'espace-client-cleodis'@'%';
GRANT SELECT ON `optima_assets`.`pack_produit_partenaire` TO 'espace-client-cleodis'@'%';
GRANT SELECT ON `optima_cleodis`.`pack_produit_partenaire` TO 'espace-client-cleodis'@'%';
GRANT SELECT ON `optima_cleodisbe`.`pack_produit_partenaire` TO 'espace-client-cleodis'@'%';
GRANT SELECT ON `optima_itrenting`.`pack_produit_partenaire` TO 'espace-client-cleodis'@'%';
GRANT SELECT ON `optima_solo`.`pack_produit_partenaire` TO 'espace-client-cleodis'@'%';


-- TH  - Contact prospection du partenaire au niveau de l'affaire
ALTER TABLE `affaire` ADD `id_prospection` MEDIUMINT UNSIGNED NULL DEFAULT NULL COMMENT 'Contact prospection du partenaire' AFTER `id_partenaire`;
ALTER TABLE `affaire` ADD FOREIGN KEY (`id_prospection`) REFERENCES `contact`(`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE;