-- Ref avenant
ALTER TABLE `affaire` CHANGE `ref` `ref` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `bon_de_commande` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `facture_fournisseur` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `facture_non_parvenue` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;