#Site Toshiba / Portail partenaire
ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire','la_poste') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;




#DEVIS CLEODIS V2
ALTER TABLE `devis_ligne` ADD `options` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `commentaire`;
ALTER TABLE `devis` ADD `commentaire_offre_partenaire` TEXT NULL DEFAULT NULL AFTER `raison_refus`;
ALTER TABLE `devis` ADD `offre_partenaire` varchar(80) NULL DEFAULT NULL AFTER `raison_refus`;

#Portail FLUNCH
CREATE TABLE `brouillon_fl` (
  `id_brouillon_fl` mediumint(8) UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `data_encode` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `questionnaire_fl` (
  `id_questionnaire_fl` mediumint(8) UNSIGNED NOT NULL,
  `question` varchar(500) NOT NULL,
  `type_reponse` enum('oui_non','pack_produit','texte','zone_texte','nombre') NOT NULL DEFAULT 'oui_non',
  `index_question` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `questionnaire_fl` VALUES(16, 'Faut-il prévoir un lien de secours : routeur 4G Bouygues ?', 'oui_non', 2);
INSERT INTO `questionnaire_fl` VALUES(19, 'Faut-il prévoir une téléphonie unifiée ORANGE : lien SDSL + téléphonie fixe ?', 'oui_non', 1);
INSERT INTO `questionnaire_fl` VALUES(21, 'Combien de TPE souhaitez vous ?', 'pack_produit', 3);
INSERT INTO `questionnaire_fl` VALUES(22, 'Allez vous utiliser Office 365 fourni par Becloud ?', 'oui_non', 4);
INSERT INTO `questionnaire_fl` VALUES(23, 'Allez vous diffuser la radio avec Radioshop ?', 'oui_non', 5);
INSERT INTO `questionnaire_fl` VALUES(24, 'Faut-il prévoir l''installation wifi par ZEN CONNECT ?', 'oui_non', 6);
INSERT INTO `questionnaire_fl` VALUES(25, 'Souhaitez vous une couverture de maintenance étendue 7j*16h par ZEN CONNECT ?', 'oui_non', 7);
INSERT INTO `questionnaire_fl` VALUES(26, 'Combien de caisses POS sont à prévoir ?', 'pack_produit', 8);
INSERT INTO `questionnaire_fl` VALUES(29, 'Combien d''écrans DMB fourni par IRIS souhaitez vous ?', 'pack_produit', 12);
INSERT INTO `questionnaire_fl` VALUES(30, 'Avez vous prévu un DRIVE ?', 'oui_non', 17);
INSERT INTO `questionnaire_fl` VALUES(31, 'Combien de KIOSQUES TILLSTER souhaitez vous ?', 'nombre', 13);
INSERT INTO `questionnaire_fl` VALUES(32, 'Combien de supports MURAUX pour KIOSQUES ?', 'nombre', 14);
INSERT INTO `questionnaire_fl` VALUES(33, 'Combien de PIED pour KIOSQUES ?', 'nombre', 15);
INSERT INTO `questionnaire_fl` VALUES(34, 'Combien de STANDS PERSONNALISES pour KIOSQUES ?', 'nombre', 16);
INSERT INTO `questionnaire_fl` VALUES(35, 'Combien souhaitez vous de PC FIXES fourni par CALIPAGE ?', 'nombre', 18);
INSERT INTO `questionnaire_fl` VALUES(36, 'Combien souhaitez vous de PC PORTABLES fourni par CALIPAGE ?', 'nombre', 19);
INSERT INTO `questionnaire_fl` VALUES(37, 'Combien souhaitez vous de MULTIFONCTIONS fourni par CALIPAGE ?', 'nombre', 20);
INSERT INTO `questionnaire_fl` VALUES(38, 'Combien souhaitez vous d''imprimantes SATO pour gérer les DLC ?', 'nombre', 21);
INSERT INTO `questionnaire_fl` VALUES(39, 'Combien de supports muraux pour imprimante SATO ?', 'nombre', 22);
INSERT INTO `questionnaire_fl` VALUES(40, 'Souhaitez vous une solution GPOS pour les TR ?', 'nombre', 23);
INSERT INTO `questionnaire_fl` VALUES(41, 'Souhaitez vous un PC GURU pour les formations ?', 'oui_non', 24);
INSERT INTO `questionnaire_fl` VALUES(42, 'Combien d''écran de contrôle KDS MYTEC souhaitez vous ?', 'nombre', 9);
INSERT INTO `questionnaire_fl` VALUES(44, 'Combien d''ORS MYTEC souhaitez vous ?', 'nombre', 10);

CREATE TABLE `questionnaire_fl_ligne` (
  `id_questionnaire_fl_ligne` mediumint(8) UNSIGNED NOT NULL,
  `id_questionnaire_fl` mediumint(8) UNSIGNED NOT NULL,
  `id_pack_produit` mediumint(8) UNSIGNED NOT NULL,
  `nom` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `questionnaire_fl_ligne` VALUES(1, 1, 1, '4 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(2, 1, 2, '5 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(3, 1, 3, '6 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(4, 4, 4, 'DRIVE AC2D et MYTECH');
INSERT INTO `questionnaire_fl_ligne` VALUES(5, 5, 1, '4 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(6, 5, 2, '5 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(7, 5, 3, '6 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(8, 10, 4, 'DRIVE AC2D et MYTECH');
INSERT INTO `questionnaire_fl_ligne` VALUES(9, 11, 1, '4 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(10, 11, 2, '5 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(11, 11, 3, '6 kiosques');
INSERT INTO `questionnaire_fl_ligne` VALUES(12, 12, 6, '1 kiosque');
INSERT INTO `questionnaire_fl_ligne` VALUES(13, 13, 7, 'ORANGE');
INSERT INTO `questionnaire_fl_ligne` VALUES(14, 14, 8, 'BOUYGUES');
INSERT INTO `questionnaire_fl_ligne` VALUES(15, 15, 8, 'BOUYGUES');
INSERT INTO `questionnaire_fl_ligne` VALUES(16, 16, 8, 'BOUYGUES');
INSERT INTO `questionnaire_fl_ligne` VALUES(17, 17, 7, 'ORANGE');
INSERT INTO `questionnaire_fl_ligne` VALUES(18, 18, 7, 'ORANGE');
INSERT INTO `questionnaire_fl_ligne` VALUES(19, 19, 7, 'ORANGE');
INSERT INTO `questionnaire_fl_ligne` VALUES(20, 20, 9, '4 TPE Be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(21, 20, 10, '5 TPE Be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(22, 20, 11, '6 TPE Be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(23, 20, 12, '7 TPE Be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(24, 20, 13, '8 TPE Be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(25, 20, 14, '9 TPE Be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(26, 21, 14, '9 TPE Be2bill + 1 de secours');
INSERT INTO `questionnaire_fl_ligne` VALUES(27, 21, 13, '8 TPE Be2bill + 1 de secours');
INSERT INTO `questionnaire_fl_ligne` VALUES(28, 21, 12, '7 TPE Be2bill  + 1 de secours');
INSERT INTO `questionnaire_fl_ligne` VALUES(29, 21, 11, '6 TPE Be2bill  + 1 de secours');
INSERT INTO `questionnaire_fl_ligne` VALUES(30, 21, 10, '5 TPE Be2bill  + 1 de secours');
INSERT INTO `questionnaire_fl_ligne` VALUES(31, 21, 9, '4 TPE Be2bill + 1 de secours');
INSERT INTO `questionnaire_fl_ligne` VALUES(32, 22, 15, 'BECLOUD');
INSERT INTO `questionnaire_fl_ligne` VALUES(33, 23, 17, 'RADIOSHOP');
INSERT INTO `questionnaire_fl_ligne` VALUES(34, 24, 18, 'WIFI ZEN CONNECT');
INSERT INTO `questionnaire_fl_ligne` VALUES(35, 25, 19, 'ETENDU ZENCONNECT');
INSERT INTO `questionnaire_fl_ligne` VALUES(36, 26, 39, '5 POS +1 MASTER');
INSERT INTO `questionnaire_fl_ligne` VALUES(37, 26, 40, '6 POS +1 MASTER');
INSERT INTO `questionnaire_fl_ligne` VALUES(38, 26, 41, '7 POS +1 MASTER');
INSERT INTO `questionnaire_fl_ligne` VALUES(39, 27, 4, 'DRIVE AC2D / MYTECH /be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(40, 28, 4, 'DRIVE AC2D / MYTECH /be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(41, 29, 26, '5 DMB IRIS et MYTEC');
INSERT INTO `questionnaire_fl_ligne` VALUES(42, 29, 42, '6 DMB IRIS et MYTEC');
INSERT INTO `questionnaire_fl_ligne` VALUES(43, 29, 43, '7 DMB IRIS et MYTEC');
INSERT INTO `questionnaire_fl_ligne` VALUES(44, 29, 44, '8 DMB IRIS et MYTEC');
INSERT INTO `questionnaire_fl_ligne` VALUES(45, 30, 4, 'DRIVE AC2D / MYTECH /be2bill');
INSERT INTO `questionnaire_fl_ligne` VALUES(46, 31, 6, '1 kiosque TILLSTER');
INSERT INTO `questionnaire_fl_ligne` VALUES(47, 32, 34, 'Fixation 3 : Support mural KIOSQUE TILLSTER');
INSERT INTO `questionnaire_fl_ligne` VALUES(48, 33, 35, 'Fixation 1 : Poteau acier Inox KIOSQUE TILLSTER');
INSERT INTO `questionnaire_fl_ligne` VALUES(49, 34, 36, 'Fixation 2 : Stand perso. fl KIOSQUE TILLSTER');
INSERT INTO `questionnaire_fl_ligne` VALUES(50, 35, 21, 'PORTABLE CALIPAGE');
INSERT INTO `questionnaire_fl_ligne` VALUES(51, 36, 21, 'PORTABLE CALIPAGE');
INSERT INTO `questionnaire_fl_ligne` VALUES(52, 37, 22, 'MFP CALIPAGE');
INSERT INTO `questionnaire_fl_ligne` VALUES(53, 38, 23, 'Imprimante SATO');
INSERT INTO `questionnaire_fl_ligne` VALUES(54, 39, 24, 'Support mural SATO');
INSERT INTO `questionnaire_fl_ligne` VALUES(55, 40, 25, 'TR GPOS');
INSERT INTO `questionnaire_fl_ligne` VALUES(56, 41, 46, 'PC GURU');
INSERT INTO `questionnaire_fl_ligne` VALUES(57, 42, 47, 'KDS');
INSERT INTO `questionnaire_fl_ligne` VALUES(58, 43, 48, 'ORS');
INSERT INTO `questionnaire_fl_ligne` VALUES(59, 44, 48, 'ORS');


ALTER TABLE `brouillon_fl`
  ADD PRIMARY KEY (`id_brouillon_fl`);

ALTER TABLE `questionnaire_fl`
  ADD PRIMARY KEY (`id_questionnaire_fl`);

ALTER TABLE `questionnaire_fl_ligne`
  ADD PRIMARY KEY (`id_questionnaire_fl_ligne`),
  ADD KEY `id_questionnaire_fl` (`id_questionnaire_fl`),
  ADD KEY `id_pack_produit` (`id_pack_produit`);

ALTER TABLE `brouillon_fl`
  MODIFY `id_brouillon_fl` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `questionnaire_fl`
  MODIFY `id_questionnaire_fl` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

ALTER TABLE `questionnaire_fl_ligne`
  MODIFY `id_questionnaire_fl_ligne` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;




ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;