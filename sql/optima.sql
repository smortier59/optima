ALTER TABLE `facture` ADD `id_echeancier` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `id_facture_parente`, ADD INDEX (`id_echeancier`);
ALTER TABLE `facture` ADD CONSTRAINT `echeancier` FOREIGN KEY (`id_echeancier`) REFERENCES `echeancier`(`id_echeancier`) ON DELETE CASCADE ON UPDATE CASCADE;
