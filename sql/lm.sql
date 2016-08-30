# Ajout du module produit_fournisseur
INSERT INTO `module` (`id_module`, `id_parent`, `module`, `abstrait`, `priorite`, `visible`, `import`, `couleur_fond`, `couleur_texte`, `couleur`, `description`, `construct`) VALUES (NULL, '75', 'produit_fournisseur', '0', '0', '1', '0', 'FFFFFF', '000000', 'blue', NULL, NULL);
# NE PAS OUBLIER DE METTRE LES DROITS SUR LE MODULE pour qu'il apparaisse en onglet de produit.

#modification table sql pour l'api
ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('commande','commande_pris_en_compte','validation_commande','livraison_initie','livraison_en_cours','debut_contrat','fin_engagement','annulation_commande') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `affaire` ADD `status_prestataire` ENUM('commande_pris_en_compte','validation_commande','annulation_commande','installee') NULL DEFAULT NULL COMMENT 'status coté prestataire' AFTER `ville_adresse_facturation`;
###

