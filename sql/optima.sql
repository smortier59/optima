ALTER TABLE `stat_snap` ADD `data` VARCHAR(2048) NULL DEFAULT NULL AFTER `valeur`;

ALTER TABLE `stat_snap` CHANGE `valeur` `valeur` FLOAT NULL;