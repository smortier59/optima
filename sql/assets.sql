-- Ref avenant
ALTER TABLE `affaire` CHANGE `ref` `ref` VARCHAR(16) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
ALTER TABLE `bon_de_commande` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `facture_fournisseur` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `facture_non_parvenue` CHANGE `ref` `ref` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `sell_and_sign` CHANGE `bundle_id` `bundle_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;