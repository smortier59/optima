ALTER TABLE `suivi` CHANGE `type` `type` ENUM('note','fichier','RDV','appel','courrier','prestataire') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'note';


ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('commande','commande_pris_en_compte','validation_commande','livraison_initie','livraison_en_cours','debut_contrat','fin_engagement','annulation_commande','installee') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `suivi` CHANGE `type_suivi` `type_suivi` ENUM('Devis','Contrat','Refinancement','Comptabilité','Broke','Contentieux','Mis en place','Restitution','Autre','Prolongation','Resiliation','Sinistre','Transfert','Fournisseur','Requête','BDC','Flottes','Installation','Passage_comite','demande_comite','Audit en cours','Assurance','Formation','Commentaire') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

ALTER TABLE `commande` ADD `etat_service1` ENUM('client_order_received','user_created','site_created','service_enabled','mail_sent','client_order_confirmed','client_order_installed') NULL DEFAULT NULL AFTER `etat`;