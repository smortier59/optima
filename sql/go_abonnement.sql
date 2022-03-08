ALTER TABLE `affaire` ADD `num_chassis` VARCHAR(255) NULL DEFAULT NULL AFTER `id_type_affaire`;
UPDATE societe SET ref = REPLACE(ref, 'GO', '0G');

ALTER TABLE `pack_produit` CHANGE `site_associe` `site_associe` ENUM('bymycar','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;