INSERT INTO dev_optima_cleodis.constante
(constante, valeur)
VALUES('__EMAIL_NOTIFIE_UPLOAD_FILE_PARTENAIRE__', 'adv@cleodis.com');

ALTER TABLE `loyer` CHANGE `type` `type` ENUM('engagement','liberatoire','prolongation') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'engagement';