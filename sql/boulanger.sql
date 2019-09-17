ALTER TABLE `produit` ADD `url_image` VARCHAR(500) NULL AFTER `id_document_contrat`, ADD `livreur` MEDIUMINT(8) UNSIGNED NULL AFTER `url_image`, ADD `frais_livraison` FLOAT(6,3) NULL AFTER `livreur`, ADD `ref_garantie` VARCHAR(15) NULL AFTER `frais_livraison`;
ALTER TABLE `produit` CHANGE `id_licence_type` `id_licence_type` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL;
