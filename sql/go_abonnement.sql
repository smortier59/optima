CREATE TABLE `tva` (
  `id_tva` mediumint(8) UNSIGNED NOT NULL,
  `text` varchar(50) NOT NULL,
  `taux` float(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `tva` ADD PRIMARY KEY (`id_tva`);

  INSERT INTO `tva` (`id_tva`, `text`, `taux`) VALUES
(1, 'N/A', 0.00),
(2, '20', 20.00),
(3, '0', 0.00);

ALTER TABLE `tva`
  MODIFY `id_tva` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;


ALTER TABLE `produit` ADD `id_tva` MEDIUMINT UNSIGNED NOT NULL DEFAULT '2' AFTER `increment`, ADD INDEX (`tva`);
ALTER TABLE `produit` ADD FOREIGN KEY (`id_tva`) REFERENCES `tva`(`id_tva`) ON DELETE RESTRICT ON UPDATE CASCADE;