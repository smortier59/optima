-- Lot JUIN - Mise en place sauvegarde doc S&Sign depuis portail sign
ALTER TABLE `sell_and_sign` CHANGE `bundle_id` `bundle_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;

--- Arreter Contentieux
ALTER TABLE `commande` CHANGE `etat` `etat` ENUM('non_loyer','mis_loyer','prolongation','AR','arreter','vente','restitution','mis_loyer_contentieux','prolongation_contentieux','restitution_contentieux','arreter_contentieux','non_loyer_contentieux') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'non_loyer';
ALTER TABLE `affaire` CHANGE `etat` `etat` ENUM('devis','commande','facture','terminee','perdue','demande_refi','facture_refi','terminee_contentieux') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'devis';