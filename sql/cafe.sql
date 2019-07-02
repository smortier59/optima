ALTER TABLE `pack_produit` ADD `val_plancher` INT UNSIGNED NOT NULL COMMENT 'Total de point minimum possible pour ce pack' AFTER `prolongation`,
						   ADD `val_plafond` INT UNSIGNED NOT NULL COMMENT 'Total de point maximum possible pour ce pack' AFTER `val_plancher`;
ALTER TABLE `pack_produit_ligne` ADD `val_modifiable` ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Ce produit est il modifiable au mois le mois';
ALTER TABLE `pack_produit_ligne` ADD `valeur` INT UNSIGNED NOT NULL COMMENT 'Nombre de point pour ce produit' AFTER `val_modifiable`;
ALTER TABLE `pack_produit_ligne` ADD `frequence_fournisseur` ENUM('mois','bimestre','trimestre','quadrimestre','semestre','an') NULL DEFAULT NULL COMMENT 'Frequence ' AFTER `id_fournisseur`;



# Jointure manquante
ALTER TABLE `devis_ligne` ADD `id_pack_produit_ligne` MEDIUMINT UNSIGNED NOT NULL COMMENT 'Ligne de pack produit ', ADD INDEX (`id_pack_produit_ligne`);
ALTER TABLE `commande_ligne` ADD `id_pack_produit_ligne` MEDIUMINT UNSIGNED NOT NULL COMMENT 'Ligne de pack produit ', ADD INDEX (`id_pack_produit_ligne`);


ALTER TABLE `devis_ligne` ADD FOREIGN KEY (`id_pack_produit_ligne`) REFERENCES `pack_produit_ligne`(`id_pack_produit_ligne`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `commande_ligne` ADD FOREIGN KEY (`id_pack_produit_ligne`) REFERENCES `pack_produit_ligne`(`id_pack_produit_ligne`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `contact` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `loyer` CHANGE `frequence_loyer` `frequence_loyer` ENUM('jour','mois','bimestre','trimestre','semestre','an') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'mois';
ALTER TABLE `loyer_prolongation` CHANGE `frequence_loyer` `frequence_loyer` ENUM('mois','bimestre','trimestre','semestre','an') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'mois';
ALTER TABLE `pack_produit` CHANGE `frequence` `frequence` ENUM('jour','mois','bimestre','trimestre','semestre','an') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'mois';

ALTER TABLE `magasin` CHANGE `site_associe` `site_associe` ENUM('toshiba','btwin','bdomplus','cafe') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `pack_produit_ligne` ADD  FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit`(`id_pack_produit`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `pack_produit_ligne` ADD FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE RESTRICT ON UPDATE CASCADE; ALTER TABLE `pack_produit_ligne` ADD FOREIGN KEY (`id_partenaire`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE CASCADE; ALTER TABLE `pack_produit_ligne` ADD FOREIGN KEY (`id_fournisseur`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `bon_de_commande_ligne` DROP FOREIGN KEY `bon_de_commande_ligne_ibfk_1`; ALTER TABLE `bon_de_commande_ligne` ADD CONSTRAINT `bon_de_commande_ligne_ibfk_1` FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande`(`id_bon_de_commande`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `bon_de_commande_ligne` ADD FOREIGN KEY (`id_commande_ligne`) REFERENCES `commande_ligne`(`id_commande_ligne`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `comite` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `comite` ADD FOREIGN KEY (`id_contact`) REFERENCES `contact`(`id_contact`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `comite` ADD FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur`(`id_refinanceur`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `comite` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `commande` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `commande` ADD FOREIGN KEY (`id_devis`) REFERENCES `devis`(`id_devis`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `commande` ADD FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `commande` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT;

ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_affaire_provenance`) REFERENCES `affaire`(`id_affaire`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_categorie`) REFERENCES `categorie`(`id_categorie`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_commande`) REFERENCES `commande`(`id_commande`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_fournisseur`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_pack_produit`) REFERENCES `pack_produit`(`id_pack_produit`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_pack_produit_ligne`) REFERENCES `pack_produit_ligne`(`id_pack_produit_ligne`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_produit`) REFERENCES `produit`(`id_produit`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `commande_ligne` ADD  FOREIGN KEY (`id_sous_categorie`) REFERENCES `sous_categorie`(`id_sous_categorie`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `contact` ADD FOREIGN KEY (`id_owner`) REFERENCES `user`(`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `demande_refi` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `demande_refi` ADD FOREIGN KEY (`id_contact`) REFERENCES `contact`(`id_contact`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `demande_refi` ADD FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur`(`id_refinanceur`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `demande_refi` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `devis` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `devis` ADD FOREIGN KEY (`id_contact`) REFERENCES `contact`(`id_contact`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `devis` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `devis` ADD FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `devis` ADD FOREIGN KEY (`id_filiale`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `devis` ADD FOREIGN KEY (`id_opportunite`) REFERENCES `opportunite`(`id_opportunite`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `devis_ligne` DROP FOREIGN KEY `devis_ligne_ibfk_2`; ALTER TABLE `devis_ligne` ADD CONSTRAINT `devis_ligne_ibfk_2` FOREIGN KEY (`id_affaire_provenance`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `devis_ligne` DROP FOREIGN KEY `devis_ligne_ibfk_4`; ALTER TABLE `devis_ligne` ADD CONSTRAINT `devis_ligne_ibfk_4` FOREIGN KEY (`id_devis`) REFERENCES `devis`(`id_devis`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `devis_ligne` DROP FOREIGN KEY `devis_ligne_ibfk_5`; ALTER TABLE `devis_ligne` ADD CONSTRAINT `devis_ligne_ibfk_5` FOREIGN KEY (`id_fournisseur`) REFERENCES `societe`(`id_societe`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `facture` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `facture` ADD FOREIGN KEY (`id_commande`) REFERENCES `commande`(`id_commande`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `facture` ADD FOREIGN KEY (`id_demande_refi`) REFERENCES `demande_refi`(`id_demande_refi`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `facture` ADD FOREIGN KEY (`id_fournisseur_prepaiement`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `facture` ADD FOREIGN KEY (`id_refinanceur`) REFERENCES `refinanceur`(`id_refinanceur`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `facture` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `facture` ADD FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `facture_fournisseur` ADD FOREIGN KEY (`id_bon_de_commande`) REFERENCES `bon_de_commande`(`id_bon_de_commande`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `facture_fournisseur` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `loyer` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `loyer_prolongation` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `pack_produit` ADD FOREIGN KEY (`id_pack_produit_besoin`) REFERENCES `pack_produit_besoin`(`id_pack_produit_besoin`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `pack_produit` ADD FOREIGN KEY (`id_pack_produit_produit`) REFERENCES `pack_produit_produit`(`id_pack_produit_produit`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `pack_produit` ADD FOREIGN KEY (`id_document_contrat`) REFERENCES `document_contrat`(`id_document_contrat`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `loyer_prolongation` ADD FOREIGN KEY (`id_prolongation`) REFERENCES `prolongation`(`id_prolongation`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `tache` ADD FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `tache` ADD FOREIGN KEY (`id_contact`) REFERENCES `contact`(`id_contact`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `tache` ADD FOREIGN KEY (`id_societe`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `tache` ADD FOREIGN KEY (`id_suivi`) REFERENCES `suivi`(`id_suivi`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `tache` ADD FOREIGN KEY (`id_user`) REFERENCES `user`(`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT;

