INSERT INTO dev_optima_cleodis.constante
(constante, valeur)
VALUES('__EMAIL_NOTIFIE_UPLOAD_FILE_PARTENAIRE__', 'adv@cleodis.com');

ALTER TABLE `facture` ADD `designation` TEXT NULL DEFAULT NULL AFTER `ref_magasin`;