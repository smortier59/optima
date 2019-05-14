UPDATE `licence` SET `id_commande_ligne` = NULL;

CREATE TABLE `export_facture` ( `id_export_facture` mediumint(8) UNSIGNED NOT NULL,
								 `id_facture` mediumint(8) UNSIGNED NOT NULL,
								 `date_export` timestamp NOT NULL DEFAULT current_timestamp(),
								 `fichier_export` enum('flux_vente') NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `export_facture` ADD PRIMARY KEY (`id_export_facture`), ADD KEY `id_facture` (`id_facture`);
ALTER TABLE `export_facture` MODIFY `id_export_facture` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;