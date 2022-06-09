ALTER TABLE `loyer_kilometrage` ADD `echeance` INT NOT NULL AFTER `id_affaire`, ADD `montant_ht` FLOAT(8,2) NOT NULL AFTER `echeance`;

CREATE TABLE `restitution_anticipee` (
  `id_loyer_kilometrage` mediumint(8) UNSIGNED NOT NULL,
  `loyer` float(8,2) NOT NULL,
  `kilometrage` mediumint(9) NOT NULL,
  `id_affaire` mediumint(8) UNSIGNED NOT NULL,
  `echeance` int(11) NOT NULL,
  `montant_ht` float(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

ALTER TABLE `restitution_anticipee`
  ADD PRIMARY KEY (`id_loyer_kilometrage`),
  ADD KEY `id_affaire` (`id_affaire`);

ALTER TABLE `restitution_anticipee`
  DROP `loyer`,
  DROP `kilometrage`;

ALTER TABLE `restitution_anticipee` MODIFY `id_loyer_kilometrage` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `restitution_anticipee` ADD CONSTRAINT `restitution_anticipee_ibfk_1` FOREIGN KEY (`id_affaire`) REFERENCES `affaire` (`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;