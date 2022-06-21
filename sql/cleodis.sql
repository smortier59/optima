ALTER TABLE `produit`
ADD `assurance` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `loyer`,
ADD `frais_de_gestion` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `assurance`,
ADD `serenite` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `frais_de_gestion`,
ADD `maintenance` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `serenite`,
ADD `hotline` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `maintenance`,
ADD `supervision` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `hotline`,
ADD `support` DECIMAL(6,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `supervision`;

ALTER TABLE `produit` DROP `loyer_sans_tva`;