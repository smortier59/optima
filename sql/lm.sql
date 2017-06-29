ALTER TABLE `affaire_etat` CHANGE `etat` `etat` ENUM('commande','commande_pris_en_compte','validation_commande','livraison_initie','livraison_en_cours','debut_contrat','fin_engagement','annulation_commande','installee','diagnostic_effectue') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) 
VALUES (NULL, '__FTP_SERVER__', '212.83.139.254'), (NULL, '__FTP_USERNAME__', 'cyrilPierreKO'), (NULL, '__FTP_USERPASS__', '8fSiD!wFm');