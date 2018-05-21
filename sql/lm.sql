
INSERT INTO `constante` (`id_constante`, `constante`, `valeur`)
VALUES (NULL, '__FTP_SERVER__', '212.83.139.254'), (NULL, '__FTP_USERNAME__', 'cyrilPierreKO'), (NULL, '__FTP_USERPASS__', '8fSiD!wFm');



INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES (NULL, '__APP_ID_IBAN__', 'lmacheckout01'), (NULL, '__APP_SECRET_IBAN__', 'feVOPMVF0FIx4YTghpo7Kt7UgDk3EpSMFricUv0i')


ALTER TABLE `parc` CHANGE `serial` `serial` VARCHAR(64) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `parc` CHANGE `etat` `etat` ENUM('broke','loue','reloue','vole','vendu','attente_location') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `parc` ADD `provenanceParcReloue` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `provenance`, ADD INDEX (`provenanceParcReloue`);
ALTER TABLE `parc` ADD FOREIGN KEY (`provenanceParcReloue`) REFERENCES `parc`(`id_parc`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `parc` CHANGE `provenanceParcReloue` `provenanceParcReloue` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL COMMENT 'Parc repris du stock (dont l\'etat est attente_location)';

ALTER TABLE `parc` ADD FOREIGN KEY (`provenance`) REFERENCES `affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `pack_produit` ADD `afficher_tout_les_produits` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `id_courrier_information_pack`;
ALTER TABLE `produit` ADD `prevenir_presta_arret` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `seuil`;
ALTER TABLE `produit` CHANGE `prevenir_presta_arret` `prevenir_presta_arret` ENUM('oui','non') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'oui' COMMENT 'Prevenir le prestataire en cas d\'arret du service? Permet de savoir si on envoi, un mail pour prevenir le presta lors de l\'arret du contrat';
ALTER TABLE `commande_ligne` ADD `confirmation_arret_service` ENUM('oui','non') NULL DEFAULT NULL AFTER `charge_fournisseur`;
ALTER TABLE `commande_ligne` ADD `date_arret_service` DATE NULL DEFAULT NULL AFTER `confirmation_arret_service`;
ALTER TABLE `affaire` CHANGE `status_prestataire` `status_prestataire` ENUM('commande_pris_en_compte','validation_commande','annulation_commande','installee','arret_service') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'status cotÃ© prestataire';