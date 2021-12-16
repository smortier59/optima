
ALTER TABLE `optima_cleodis`.`affaire` ADD `id_commercial` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_apporteur`, ADD INDEX (`id_commercial`);
ALTER TABLE `optima_cleodis`.`affaire` ADD FOREIGN KEY (`id_commercial`) REFERENCES `user`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `optima_cleodisbe`.`affaire` ADD `id_commercial` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_apporteur`, ADD INDEX (`id_commercial`);
ALTER TABLE `optima_cleodisbe`.`affaire` ADD FOREIGN KEY (`id_commercial`) REFERENCES `user`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `optima_assets`.`affaire` ADD `id_commercial` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_apporteur`, ADD INDEX (`id_commercial`);
ALTER TABLE `optima_assets`.`affaire` ADD FOREIGN KEY (`id_commercial`) REFERENCES `user`(`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;


-- DB CLEODIS et CLEODISBE
INSERT INTO constante
(id_constante, constante, valeur)
VALUES(NULL, '__DESTINATAIRE_NOTIFIE_TACHE_AFFAIRE_PARTENAIRE__', 'jvasut,mmysoet,egerard,btronquit,pcaminel,lhochart');

-- DB GO_ABONNEMENT
INSERT INTO constante
(id_constante, constante, valeur)
VALUES(38, '__DESTINATAIRE_NOTIFIE_TACHE_AFFAIRE_PARTENAIRE__', 'btronquit');