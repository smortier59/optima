ALTER TABLE `contact` DROP `langue`;

ALTER TABLE `contact` ADD `langue` ENUM('FR','NL') NOT NULL DEFAULT 'FR' AFTER `id_owner`;
ALTER TABLE `societe` ADD `langue` ENUM('FR','NL') NOT NULL DEFAULT 'FR' AFTER `id_pays`;


ALTER TABLE `affaire` CHANGE `type_affaire` `type_affaire` ENUM('normal','2SI') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'normal';
ALTER TABLE `affaire` ADD `langue` ENUM('FR','NL') NOT NULL DEFAULT 'FR' AFTER type_affaire;