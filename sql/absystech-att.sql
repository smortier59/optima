ALTER TABLE `societe` ADD `id_contact_signataire` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `id_contact_facturation`, ADD INDEX (`id_contact_signataire`);
ALTER TABLE `societe` ADD FOREIGN KEY (`id_contact_signataire`) REFERENCES `contact`(`id_contact`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `devis` ADD `id_contrat_sell_and_sign` INT NULL COMMENT 'ID du contrat coté sell And Sign' AFTER `duree_contrat_cout_copie`;
ALTER TABLE `devis` ADD `date_signature` DATE NULL COMMENT 'Date de la signature du document via portail Sign' AFTER `id_contrat_sell_and_sign`;

INSERT INTO `constante` (`id_constante`, `constante`, `valeur`) VALUES
(NULL, '__OODRIVE_HOST__', 'https://cloud.sellandsign.com'),
(NULL, '__OODRIVE_JTOKEN__', 'TSDSSBX|14D1TPTfDjl3unie8OwZ5HHNAtTEfGFSSyHLd68IpiY=')


CREATE TABLE code_projet_ec (
	id_code_projet_ec INT auto_increment NOT NULL,
	code_projet_ec varchar(100) NOT NULL,
	CONSTRAINT code_projet_ec_PK PRIMARY KEY (id_code_projet_ec)
);

ALTER TABLE affaire ADD id_code_projet_ec INT NULL, ADD INDEX (id_code_projet_ec);
ALTER TABLE affaire ADD CONSTRAINT affaire_FK FOREIGN KEY (id_code_projet_ec) REFERENCES code_projet_ec(id_code_projet_ec) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE jalon ADD id_code_projet_ec INT NULL, ADD INDEX (id_code_projet_ec);
ALTER TABLE jalon ADD CONSTRAINT jalon_FK FOREIGN KEY (id_code_projet_ec) REFERENCES code_projet_ec(id_code_projet_ec) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE jalon ADD icone varchar(100) DEFAULT "pli-box-open" NOT NULL;
ALTER TABLE jalon ADD classname varchar(100) DEFAULT "default" NOT NULL;



INSERT INTO jalon VALUES
(NULL, "Mail envoyé au magasin", "affaire", "absystech", 1),
(NULL, "Colis Expédié", "affaire", "absystech", 1),
(NULL, "Colis livré sur site", "affaire", "absystech", 1),
(NULL, "Site contacté – rdv fixé" , "affaire", "absystech", 1),
(NULL, "Rdv replanifié" , "affaire", "absystech", 1),
(NULL, "Remplacement réalisé", "affaire", "absystech", 1),
(NULL, "Enlèvement programmé" , "affaire", "absystech", 1),
(NULL, "Colis retour reçu", "affaire", "absystech", 1);