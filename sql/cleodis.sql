
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


ALTER TABLE `loyer` CHANGE `type` `type` ENUM('engagement','liberatoire','prolongation') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'engagement';





INSERT INTO `user` (`id_user`, `login`, `password`, `id_societe`, `date`, `date_connection`, `date_activity`, `etat`, `id_profil`, `civilite`, `prenom`, `nom`, `adresse`, `adresse_2`, `adresse_3`, `cp`, `ville`, `gsm`, `email`, `id_pays`, `id_agence`, `custom`, `id_superieur`, `last_news`, `newsletter`, `id_localisation_langue`, `id_phone`, `graphe_reseau`, `graphe_autre`, `fonction`, `api_key`)
VALUES
(NULL, 'compta', 'secured', NULL, current_timestamp(), NULL, NULL, 'normal', NULL, 'M', 'compta', 'bilite', NULL, NULL, NULL, NULL, NULL, NULL, 'compta@cleodis.com', NULL, NULL, NULL, NULL, NULL, 'oui', '1', NULL, 'non', 'non', NULL, NULL);

INSERT INTO `constante` (`id_constante`, `constante`, `valeur`)
VALUES
    (NULL, '__NOTIFIE_PASSAGE_ARRETEE_AFFAIRE__', 'lhochart'),
    (NULL, '__NOTIFIE_DATE_RESTITUTION_DEPASSEE__', 'lhochart'),
    (NULL, '__NOTIFIE_STOP_CONTRAT__', 'lhochart'),
    (NULL, '__NOTIFIE_AVIS_CREDIT_SOCIETE_UPDATE__', 'lhochart,jlesueur')
    (NULL, '__NOTIFIE_CREATE_TACHE_PARTENAIRE__', 'jvasut,mmysoet,jloison,lhochart'),
    (NULL, '__NOTIFIE_VALIDATION_COMITE__', 'jvasut,mmysoet,egerard,btronquit,pcaminel,lhochart')
    (NULL, '__NOTIFIE_COMMENTAIRE_AFFAIRE_PARTENAIRE__', 'tdelattre,jvasut,egerard,mmysoet'),
    (NULL, '__SUIVI_NOTIFIE_MEP__', 'compta'),
    (NULL, '__SUIVI_NOTIFIE_UPLOAD_FACTURE_PARTENAIRE__', 'compta'),
    (NULL, '__NOTIFIE_UPLOAD_PJ_PARTENAIRE__', 'lhochart,jvasut,mmysoet');

