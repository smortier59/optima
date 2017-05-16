ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('commande','commande_pris_en_compte','validation_commande','livraison_initie','livraison_en_cours','debut_contrat','fin_engagement','annulation_commande','installee','diagnostic_effectue') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


ALTER TABLE `affaire` CHANGE `etat` `etat` ENUM('devis','slimpay_en_cours','attente_comite','commande','facture','terminee','perdue','demande_refi','facture_refi','refuse','abandon') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'devis';


-- LMA V2
ALTER TABLE `produit` ADD `text` VARCHAR(500) NULL DEFAULT NULL AFTER `sous_produit_unique`, ADD `popin` TEXT NULL DEFAULT NULL AFTER `text`, ADD `question` VARCHAR(500) NULL DEFAULT NULL AFTER `popin`;
ALTER TABLE `pack_produit` ADD `pack_alarme` ENUM('maison','appartement') NULL DEFAULT NULL AFTER `type_pack_magasin`, ADD UNIQUE (`pack_alarme`);
ALTER TABLE `produit` ADD `nb_produit_inclus` INT NOT NULL DEFAULT '0' AFTER `question`;