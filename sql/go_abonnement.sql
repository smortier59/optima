ALTER TABLE `affaire` ADD `num_chassis` VARCHAR(255) NULL DEFAULT NULL AFTER `id_type_affaire`;
UPDATE societe SET ref = REPLACE(ref, 'GO', '0G');