ALTER TABLE `produit`
ADD `assurance` FLOAT(6,3) NULL AFTER `loyer`,
ADD `frais_de_gestion` FLOAT(6,3) NULL AFTER `assurance`,
ADD `serenite` FLOAT(6,3) NULL AFTER `frais_de_gestion`,
ADD `maintenance` FLOAT(6,3) NULL AFTER `serenite`,
ADD `hotline` FLOAT(6,3) NULL AFTER `maintenance`,
ADD `supervision` FLOAT(6,3) NULL AFTER `hotline`,
ADD `support` FLOAT(6,3) NULL AFTER `supervision`;



ALTER TABLE `produit` DROP `loyer_sans_tva`;