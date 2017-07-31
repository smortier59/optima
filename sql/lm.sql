CREATE TABLE `courrier_information_pack` (
  `id_courrier_information_pack` smallint(5) UNSIGNED NOT NULL,
  `courrier_information_pack` int(11) NOT NULL,
  `template_mail_courrier` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `courrier_information_pack`  ADD PRIMARY KEY (`id_courrier_information_pack`);
ALTER TABLE `courrier_information_pack` CHANGE `id_courrier_information_pack` `id_courrier_information_pack` MEDIUMINT(5) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `courrier_information_pack` MODIFY `id_courrier_information_pack` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `courrier_information_pack` CHANGE `courrier_information_pack` `courrier_information_pack` VARCHAR(255) NOT NULL;


ALTER TABLE `pack_produit` ADD `id_courrier_information_pack` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `pack_alarme`, ADD INDEX (`id_courrier_information_pack`);

ALTER TABLE `pack_produit` ADD INDEX(`id_courrier_information_pack`);
ALTER TABLE `pack_produit` ADD  FOREIGN KEY (`id_courrier_information_pack`)
								REFERENCES `courrier_information_pack`(`id_courrier_information_pack`)
								ON DELETE SET NULL ON UPDATE CASCADE;

INSERT INTO `courrier_information_pack` (`id_courrier_information_pack`, `courrier_information_pack`, `template_mail_courrier`)
VALUES (NULL, 'Alarme évology', 'alarme-evology'),
	   (NULL, 'Alarme MyFox', 'alarme-myfox'),
	   (NULL, 'Adoucisseur', 'adoucisseur'),
	   (NULL, 'Chaudière', 'chaudiere');





ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('commande','commande_pris_en_compte','validation_commande','livraison_initie','livraison_en_cours','debut_contrat','fin_engagement','annulation_commande','installee','diagnostic_effectue') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

INSERT INTO `constante` (`id_constante`, `constante`, `valeur`)
VALUES (NULL, '__FTP_SERVER__', '212.83.139.254'), (NULL, '__FTP_USERNAME__', 'cyrilPierreKO'), (NULL, '__FTP_USERPASS__', '8fSiD!wFm');