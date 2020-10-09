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


# Type affaire Mariton
ALTER TABLE `affaire` CHANGE `type_affaire` `type_affaire` ENUM('normal','2SI','Boulanger Pro','Consommables_com','DIB','Dyadem','FLEXFUEL','Instore','LAFI','Manganelli','NRC','OLISYS - Ma Solution IT','Proxi Pause','Trekk','ZENCONNECT – ZEN PACK','Hexamed Leasing','LFS','LocEvo','haccp','Mariton') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'normal';



-- Portail AXA

INSERT INTO `client` (`id_client`, `client`, `etat`, `client_id`, `client_secret`) VALUES (NULL, 'axa', 'actif', 'jBUpU4b5hjZ7jnyNt863CAUKy9N9TRUv', 'jBUpU4b5hjZ7jnyNt863CAUKy9N9TRUv');
INSERT INTO `site_associe` (`id_site_associe`, `site_associe`, `code`, `steps_tunnel`, `id_client`, `url_front`, `cs_score_minimal`, `age_minimal`, `export_middleware`, `id_societe`, `color_dominant`, `color_footer`, `color_links`, `color_titles`) VALUES (NULL, 'axa', NULL, 'COMPONENT_CONFIRM_CLIENT_ACCOUNT,COMPONENT_B2B_ALLINONE,COMPONENT_SIGNING_DOCUMENTS,COMPONENT_UPLOAD_PJ,COMPONENT_FINISHED_RECAP', '10', 'http://axa.location-evolutive.fr', '19', '0', 'oui', NULL, '003895', '003895', 'e70d2f', 'e70d2f');
ALTER TABLE `magasin` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','haccp','axa','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `document_revendeur` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','haccp','axa','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `pack_produit` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','locevo','dib','haccp','axa','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `affaire` CHANGE `site_associe` `site_associe` ENUM('cleodis','location_evolutive','toshiba','btwin','boulangerpro','bdomplus','boulanger-cafe','hexamed','top office','burger king','flunch','dib','locevo','haccp','axa','sans') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `affaire` CHANGE `type_affaire` `type_affaire` ENUM('normal','2SI','Boulanger Pro','Consommables_com','DIB','Dyadem','FLEXFUEL','Instore','LAFI','Manganelli','NRC','OLISYS - Ma Solution IT','Proxi Pause','Trekk','ZENCONNECT – ZEN PACK','Hexamed Leasing','LFS','LocEvo','haccp','Mariton','Axa') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'normal';
ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire','la_poste','btwin','boulangerpro','hexamed','dib','locevo','haccp','axa') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;


-- Type affaire
CREATE TABLE `type_affaire` (
  `id_type_affaire` int(11) NOT NULL,
  `type_affaire` varchar(255) NOT NULL,
  `libelle_pdf` varchar(255) NOT NULL DEFAULT 'Cléodis'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `type_affaire` (`id_type_affaire`, `type_affaire`, `libelle_pdf`) VALUES
(3, 'Boulanger Pro', 'Boulanger Pro'),
(5, '2SI', 'Cléodis'),
(6, 'Consommables.com', 'Cléodis'),
(7, 'DIB', 'DIB France'),
(8, 'Dyadem', 'Cléodis'),
(9, 'FLEXFUEL', 'Cléodis'),
(10, 'haccp', 'Cléodis'),
(11, 'Hexamed Leasing', 'Cléodis'),
(12, 'Instore', 'Cléodis'),
(13, 'LAFI', 'Cléodis'),
(14, 'LFS', 'Cléodis'),
(15, 'LocEvo', 'Cléodis'),
(16, 'Manganelli', 'Cléodis'),
(17, 'NRC', 'Cléodis'),
(18, 'OLISYS - Ma Solution IT', 'Cléodis'),
(19, 'Proxi Pause', 'Cléodis'),
(20, 'ZENCONNECT - ZEN PACK', 'Cléodis'),
(21, 'normal', 'Cléodis'),
(22, 'Trekk', 'Cléodis'),
(23, 'Axa', 'Cléodis'),
(24, 'Mariton', 'Mariton');



CREATE TABLE `type_affaire_params` (
  `id_type_affaire_params` int(11) NOT NULL,
  `id_societe` int(11) NOT NULL,
  `id_type_affaire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



ALTER TABLE `type_affaire`  ADD PRIMARY KEY (`id_type_affaire`);
ALTER TABLE `type_affaire_params`  ADD PRIMARY KEY (`id_type_affaire_params`);
ALTER TABLE `type_affaire`  MODIFY `id_type_affaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `type_affaire_params`
  MODIFY `id_type_affaire_params` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

ALTER TABLE `affaire` ADD INDEX(`id_type_affaire`);
ALTER TABLE `affaire` ADD  FOREIGN KEY (`id_type_affaire`) REFERENCES `type_affaire`(`id_type_affaire`) ON DELETE SET NULL ON UPDATE CASCADE;

UPDATE affaire SET id_type_affaire = (SELECT id_type_affaire FROM type_affaire WHERE type_affaire.type_affaire = affaire.type_affaire);

