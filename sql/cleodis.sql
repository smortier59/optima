ALTER TABLE `produit`
ADD `assurance` FLOAT(6,3) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `loyer`,
ADD `frais_de_gestion` FLOAT(6,3) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `assurance`,
ADD `serenite` FLOAT(6,3) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `frais_de_gestion`,
ADD `maintenance` FLOAT(6,3) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `serenite`,
ADD `hotline` FLOAT(6,3) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `maintenance`,
ADD `supervision` FLOAT(6,3) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `hotline`,
ADD `support` FLOAT(6,3) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `supervision`;

ALTER TABLE `produit` DROP `loyer_sans_tva`;