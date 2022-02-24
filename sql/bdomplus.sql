ALTER TABLE `affaire` ADD `renouveller` ENUM('oui','non') NOT NULL DEFAULT 'oui' AFTER `id_type_affaire`

INSERT INTO dev_optima_bdomplus.constante
(constante, valeur)
VALUES('__EMAIL_BATCH_COMPTE_ECP__', 'gwendoline.smit@cleodis.com');
INSERT INTO dev_optima_bdomplus.constante
(constante, valeur)
VALUES('__EMAIL_SOUSCRIPTION__', 'gwendoline.smit@cleodis.com');
