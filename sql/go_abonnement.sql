ALTER TABLE `parc` ADD `immatriculation` VARCHAR(25) NULL DEFAULT NULL AFTER `id_bon_de_commande`;
ALTER TABLE `type_affaire` ADD `contrat_template` VARCHAR(255) NOT NULL DEFAULT 'contrat' AFTER `devis_template`;

CREATE TABLE `tva` (
  `id_tva` mediumint(8) UNSIGNED NOT NULL,
  `text` varchar(50) NOT NULL,
  `taux` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




INSERT INTO `tva` (`id_tva`, `text`, `taux`) VALUES
(1, 'N/A', 0.00),
(2, '20,00%', 20.00),
(3, '0,00%', 0.00);


ALTER TABLE `tva`  ADD PRIMARY KEY (`id_tva`);
ALTER TABLE `tva`
  MODIFY `id_tva` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

ALTER TABLE `produit` ADD `id_tva` mediumint(9) UNSIGNED NOT NULL DEFAULT 2;
ALTER TABLE `produit` ADD KEY `id_tva` (`id_tva`) USING BTREE;
ALTER TABLE `produit` ADD CONSTRAINT `produit_ibfk_44` FOREIGN KEY (`id_tva`) REFERENCES `tva` (`id_tva`) ON UPDATE CASCADE;


ALTER TABLE `affaire`
ADD `kilometrage_max` INT NULL DEFAULT NULL COMMENT 'Kilometrage maximum autorisé' AFTER `num_chassis`,
ADD `montant_kilometrage_max_depasse` FLOAT(11,2) NULL DEFAULT NULL COMMENT 'Montant depassement KM max' AFTER `kilometrage_max`,
ADD `franchise` INT NULL DEFAULT NULL COMMENT 'Montant franchise assurance' AFTER `montant_kilometrage_max_depasse`;