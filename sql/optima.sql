CREATE TABLE `alerte_imprimante` (
  `id_alerte_imprimante` mediumint(8) NOT NULL,
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `message` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notification` enum('oui','non') NOT NULL DEFAULT 'oui'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `alerte_imprimante`
  ADD PRIMARY KEY (`id_alerte_imprimante`),
  ADD KEY `id_stock` (`id_stock`);

ALTER TABLE `alerte_imprimante`
  MODIFY `id_alerte_imprimante` mediumint(8) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE `alerte_imprimante`
  ADD CONSTRAINT `alerte_imprimante_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock`) ON DELETE CASCADE ON UPDATE CASCADE;


CREATE TABLE `etat_imprimante` (
  `id_stock` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `color` enum('other','unknow','cyan','magenta','yellow','black') DEFAULT NULL,
  `current` bigint(20) NOT NULL,
  `max` int(11) DEFAULT NULL,
  `type` enum('toner','copie_noir','copie_couleur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `etat_imprimante`
  ADD PRIMARY KEY (`id_stock`,`name`,`date`);

ALTER TABLE `etat_imprimante`
  ADD CONSTRAINT `etat_imprimante_ibfk_1` FOREIGN KEY (`id_stock`) REFERENCES `stock` (`id_stock` ON DELETE CASCADE ON UPDATE CASCADE;);









  CREATE TABLE `extranet_v3_absystech`.`consommable_imprimante` (
  `id_consommable_imprimante` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT , 
  `designation` VARCHAR(255) NOT NULL ,
  `code` VARCHAR(50) NOT NULL , 
  `duree` INT(10) NOT NULL ,
  `prix` DECIMAL(10,2) NOT NULL , 
  `ref_imprimante` VARCHAR(32) NOT NULL ,  
  PRIMARY KEY (`id_consommable_imprimante`)) ENGINE = InnoDB;
  
  ALTER TABLE `consommable_imprimante` DROP FOREIGN KEY `consommable_imprimante_ibfk_1`; ALTER TABLE `consommable_imprimante` 
  ADD CONSTRAINT `consommable_imprimante_ibfk_1` 
  FOREIGN KEY (`ref_imprimante`) 
  REFERENCES `stock`(`ref`) ON DELETE CASCADE ON UPDATE CASCADE;