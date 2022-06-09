ALTER TABLE `loyer_kilometrage` ADD `echeance` INT NOT NULL AFTER `id_affaire`, ADD `montant_ht` FLOAT(8,2) NOT NULL AFTER `echeance`;

CREATE TABLE `restitution_anticipee` (
  `id_loyer_kilometrage` mediumint(8) UNSIGNED NOT NULL,
  `loyer` float(8,2) NOT NULL,
  `kilometrage` mediumint(9) NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `echeance` int(11) NOT NULL,
  `montant_ht` float(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

ALTER TABLE `panier` DROP FOREIGN KEY `panier_ibfk_2`; ALTER TABLE `panier` ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`id_affaire`) REFERENCES `affaire`(`id_affaire`) ON DELETE SET NULL ON UPDATE CASCADE;