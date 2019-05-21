UPDATE `licence` SET `id_commande_ligne` = NULL;

CREATE TABLE `export_facture` ( `id_export_facture` mediumint(8) UNSIGNED NOT NULL,
								 `id_facture` mediumint(8) UNSIGNED NOT NULL,
								 `date_export` timestamp NOT NULL DEFAULT current_timestamp(),
								 `fichier_export` enum('flux_vente') NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `export_facture` ADD PRIMARY KEY (`id_export_facture`), ADD KEY `id_facture` (`id_facture`);
ALTER TABLE `export_facture` MODIFY `id_export_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `facture_non_parvenue` CHANGE `ref` `ref` VARCHAR(36) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `facture` ADD `ref_magasin` VARCHAR(32) NULL AFTER `ref_externe`;


CREATE TABLE `dev_optima_bdomplus`.`facture_magasin` ( `id_facture_magasin` MEDIUMINT NOT NULL AUTO_INCREMENT , `ref_facture` VARCHAR(25) NOT NULL , `id_affaire` MEDIUMINT NOT NULL , PRIMARY KEY (`id_facture_magasin`), INDEX (`id_affaire`)) ENGINE = InnoDB;
ALTER TABLE `facture_magasin` CHANGE `id_affaire` `id_affaire` MEDIUMINT(9) UNSIGNED NOT NULL;
ALTER TABLE `facture_magasin` CHANGE `id_facture_magasin` `id_facture_magasin` MEDIUMINT(9) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `facture_magasin` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `facture_magasin` ADD `etat` ENUM('non_recu','paye') NOT NULL DEFAULT 'non_recu' AFTER `id_affaire`;