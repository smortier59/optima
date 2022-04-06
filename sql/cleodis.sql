ALTER TABLE `facture` ADD `date_envoi` DATE NULL DEFAULT NULL AFTER `envoye_mail`;
ALTER TABLE `facture` ADD `envoye` ENUM('oui','non','erreur') NULL DEFAULT 'non' AFTER `date_envoi`;