UPDATE `user` SET civilite= 'Mme' WHERE civilite = 'Mlle';
ALTER TABLE `user` CHANGE `civilite` `civilite` ENUM('M','Mme') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'M';