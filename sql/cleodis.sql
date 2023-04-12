-- Panier et affaire de vente depuis tunnel/api
    -- PASSAGE des loyer produit a 4 chiffres (max 9999,99)
ALTER TABLE `produit` CHANGE `loyer` `loyer` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `assurance` `assurance` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `frais_de_gestion` `frais_de_gestion` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `serenite` `serenite` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `maintenance` `maintenance` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `hotline` `hotline` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `supervision` `supervision` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `produit` CHANGE `support` `support` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `devis_ligne` CHANGE `loyer` `loyer` FLOAT(7,3) NULL DEFAULT NULL;
ALTER TABLE `commande_ligne` CHANGE `loyer` `loyer` FLOAT(7,3) NULL DEFAULT NULL;

    -- Affaire de vente
ALTER TABLE `panier` ADD `nature` ENUM('location','vente') NOT NULL DEFAULT 'location' AFTER `date`;
ALTER TABLE `produit` ADD `prix_vente` FLOAT(8,2) NULL DEFAULT NULL AFTER `support`;
INSERT INTO `type_affaire` (`id_type_affaire`, `type_affaire`, `libelle_pdf`, `devis_template`, `contrat_template`, `assurance_sans_tva`)
VALUES
(NULL, 'SIMPEL FLUX', 'Cléodis', 'devis', 'contrat_simpel_flux', 'non'),
(NULL, 'SIMPEL START', 'Cléodis', 'devis', 'contrat_simpel_start', 'non');