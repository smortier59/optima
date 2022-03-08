CREATE TABLE `dev_optima_cleodis`.`loyer_kilometrage` (
    `id_loyer_kilometrage` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
    `loyer` FLOAT(8,2) NOT NULL , `kilometrage` MEDIUMINT NOT NULL ,
    `id_affaire` MEDIUMINT UNSIGNED NOT NULL ,
    PRIMARY KEY (`id_loyer_kilometrage`), INDEX (`id_affaire`)
) ENGINE = InnoDB;

ALTER TABLE `loyer_kilometrage` ADD FOREIGN KEY (`id_affaire`) REFERENCES `dev_optima_go_abonnement`.`affaire`(`id_affaire`) ON DELETE CASCADE ON UPDATE CASCADE;