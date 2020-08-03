-- Lot JUIN - Mise en place sauvegarde doc S&Sign depuis portail sign
ALTER TABLE `sell_and_sign` CHANGE `bundle_id` `bundle_id` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `affaire` CHANGE `type_affaire` `type_affaire` ENUM('normal','2SI','Boulanger Pro','Consommables_com','DIB','Dyadem','FLEXFUEL','Instore','LAFI','Manganelli','NRC','OLISYS - Ma Solution IT','Proxi Pause','Trekk','ZENCONNECT – ZEN PACK','Hexamed Leasing','LFS','LocEvo') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'normal';

--- Arreter Contentieux
ALTER TABLE `commande` CHANGE `etat` `etat` ENUM('non_loyer','mis_loyer','prolongation','AR','arreter','vente','restitution','mis_loyer_contentieux','prolongation_contentieux','restitution_contentieux','arreter_contentieux','non_loyer_contentieux') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'non_loyer';
ALTER TABLE `affaire` CHANGE `etat` `etat` ENUM('devis','commande','facture','terminee','perdue','demande_refi','facture_refi','terminee_contentieux') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'devis';

ALTER TABLE affaire MODIFY COLUMN site_associe enum('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','dib','locevo','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL NULL;
ALTER TABLE pack_produit MODIFY COLUMN site_associe enum('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL NULL;
ALTER TABLE produit MODIFY COLUMN site_associe enum('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL NULL;
ALTER TABLE magasin MODIFY COLUMN site_associe enum('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL NULL;
ALTER TABLE document_revendeur MODIFY COLUMN site_associe enum('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL NULL;

ALTER TABLE affaire MODIFY COLUMN provenance enum('toshiba','cleodis','vendeur','partenaire','la_poste','btwin','boulangerpro','hexamed','dib','locevo') CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL NULL;

