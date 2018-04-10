ALTER TABLE `pack_produit` CHANGE `type_pack_magasin` `type_pack_magasin` ENUM('chaudiere','adoucisseur','alarme','enki') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;



INSERT INTO `constante` (`id_constante`, `constante`, `valeur`)
VALUES (NULL, '__FTP_SERVER__', '212.83.139.254'), (NULL, '__FTP_USERNAME__', 'cyrilPierreKO'), (NULL, '__FTP_USERPASS__', '8fSiD!wFm');



INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES (NULL, '__APP_ID_IBAN__', 'lmacheckout01'), (NULL, '__APP_SECRET_IBAN__', 'feVOPMVF0FIx4YTghpo7Kt7UgDk3EpSMFricUv0i')