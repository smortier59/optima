
INSERT INTO `constante` (`id_constante`, `constante`, `valeur`)
VALUES (NULL, '__FTP_SERVER__', '212.83.139.254'), (NULL, '__FTP_USERNAME__', 'cyrilPierreKO'), (NULL, '__FTP_USERPASS__', '8fSiD!wFm');



INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES (NULL, '__APP_ID_IBAN__', 'lmacheckout01'), (NULL, '__APP_SECRET_IBAN__', 'feVOPMVF0FIx4YTghpo7Kt7UgDk3EpSMFricUv0i')


ALTER TABLE `parc` CHANGE `serial` `serial` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `parc` CHANGE `etat` `etat` ENUM('broke','loue','reloue','vole','vendu','attente_location') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `parc` ADD `provenanceParcReloue` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `provenance`, ADD INDEX (`provenanceParcReloue`);
ALTER TABLE `parc` ADD FOREIGN KEY (`provenanceParcReloue`) REFERENCES `parc`(`id_parc`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `parc` CHANGE `provenanceParcReloue` `provenanceParcReloue` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL COMMENT 'Parc repris du stock (dont l\'etat est attente_location)';

ALTER TABLE `parc` ADD FOREIGN KEY (`provenance`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;