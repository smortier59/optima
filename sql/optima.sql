CREATE TABLE `etat_imprimante` (
  `id_etat_imprimante` mediumint(8) UNSIGNED NOT NULL,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `color` enum('other','unknow','cyan','magenta','yellow','black') DEFAULT NULL,
  `current` bigint(20) NOT NULL,
  `max` int(11) DEFAULT NULL,
  `type` enum('toner','copie_noir','copie_couleur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `etat_imprimante`
  ADD PRIMARY KEY (`id_etat_imprimante`),
  ADD KEY `id_stock` (`id_stock`);

ALTER TABLE `etat_imprimante`
  MODIFY `id_etat_imprimante` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1258;

ALTER TABLE `etat_imprimante`
  ADD CONSTRAINT `etat_imprimante_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;



CREATE TABLE `etat_consommable_imprimante` (
  `id_etat_consommable_imprimante` mediumint(8) UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `id_consommable_imprimante` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `etat_consommable_imprimante`
  ADD PRIMARY KEY (`id_etat_consommable_imprimante`),
  ADD KEY `id_stock` (`id_stock`),
  ADD KEY `id_consommable` (`id_consommable_imprimante`);

ALTER TABLE `etat_consommable_imprimante`
  MODIFY `id_etat_consommable_imprimante` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `etat_consommable_imprimante`
  ADD CONSTRAINT `etat_consommable_imprimante_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `etat_consommable_imprimante_ibfk_2` FOREIGN KEY (`id_consommable_imprimante`) REFERENCES `consommable_imprimante` (`id_consommable_imprimante`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE `alerte_imprimante` (
  `id_alerte_imprimante` mediumint(8) NOT NULL,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notification` enum('oui','non') NOT NULL DEFAULT 'oui',
  `date_cloture` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `alerte_imprimante`
  ADD PRIMARY KEY (`id_alerte_imprimante`),
  ADD KEY `id_stock` (`id_stock`),
  ADD KEY `date_cloture` (`date_cloture`);

ALTER TABLE `alerte_imprimante`
  MODIFY `id_alerte_imprimante` mediumint(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1901;

ALTER TABLE `alerte_imprimante`
  ADD CONSTRAINT `alerte_imprimante_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE `consommable_imprimante` (
  `id_consommable_imprimante` mediumint(8) UNSIGNED NOT NULL,
  `designation` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `duree` int(10) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `ref_imprimante` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `consommable_imprimante`
  ADD PRIMARY KEY (`id_consommable_imprimante`),
  ADD KEY `consommable_imprimante_ibfk_1` (`ref_imprimante`);

ALTER TABLE `consommable_imprimante`
  MODIFY `id_consommable_imprimante` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `consommable_imprimante`
  ADD CONSTRAINT `consommable_imprimante_ibfk_1` FOREIGN KEY (`ref_imprimante`) REFERENCES `stock` (`ref`) ON DELETE CASCADE ON UPDATE CASCADE;




ALTER TABLE `consommable_imprimante` ADD `couleur_consommable` ENUM('noir','cyan','magenta','jaune') NULL AFTER `ref_imprimante`;


