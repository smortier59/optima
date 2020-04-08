ALTER TABLE `print_consommable` CHANGE `couleur` `couleur` ENUM('noir','cyan','magenta','jaune','autre') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
-- Crée la régle de mot de passe
INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES (NULL, '__REGLE_MDP__', '(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(.{8,})*');
-- Crée le système de status de compte de type ENUM
ALTER TABLE `user` ADD `status` ENUM('blocked','active') NOT NULL DEFAULT 'active' AFTER `api_key`; 