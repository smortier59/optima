ALTER TABLE `print_consommable` CHANGE `couleur` `couleur` ENUM('noir','cyan','magenta','jaune','autre') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
-- Crée la régle de mot de passe
INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES (NULL, '__REGLE_MDP__', '(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(.{8,})*');
-- Crée le système de status de compte de type ENUM
ALTER TABLE `user` ADD `status` ENUM('blocked','active') NOT NULL DEFAULT 'active' AFTER `api_key`;



INSERT INTO `constante`
    (`id_constante`, `constante`, `valeur`)
VALUES
    (NULL, '__API_CREDIT_SAFE_USERNAME__', NULL),
    (NULL, '__API_CREDIT_SAFE_PASSWORD__', NULL),
    (NULL, '__API_CREDIT_SAFE_BASEURL__', 'https://connect.sandbox.creditsafe.com/v1');



ALTER TABLE `telescope` ADD `home_events` ENUM('oui','non') NOT NULL DEFAULT 'non' COMMENT 'Afficher le calendrier sur la page d\'accueil' AFTER `actif`;
UPDATE `telescope` SET `home_events` = 'non' WHERE `telescope`.`id_telescope` = 1;
UPDATE `telescope` SET `home_events` = 'non' WHERE `telescope`.`id_telescope` = 2;
UPDATE `telescope` SET `home_events` = 'non' WHERE `telescope`.`id_telescope` = 3;
UPDATE `telescope` SET `home_events` = 'non' WHERE `telescope`.`id_telescope` = 4;
UPDATE `facture` SET exporte = 'oui';
ALTER TABLE `facture` ADD exporte  enum('oui','non') DEFAULT "non";




INSERT INTO `constante` (`id_constante`, `constante`, `valeur`)
VALUES
(NULL, '__MS_GRAPH_CLIENT_ID__', 'd1f6e0a0-a23c-4611-91b7-a9eff8385414'),
(NULL, '__MS_GRAPH_CLIENT_SECRET__', '8SFGwR4K.k7qfB~l.T.5ItUBj44Uic02~5'),
(NULL, '__MS_GRAPH_TENANT_ID__', '92435d98-99f6-449b-b313-528fba7ad851');