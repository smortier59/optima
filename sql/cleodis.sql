ALTER TABLE `affaire` CHANGE `type_affaire` `type_affaire` ENUM('normal','2SI','Boulanger Pro',
'Consommables_com',
'DIB',
'Dyadem',
'FLEXFUEL',
'Instore',
'LAFI',
'Manganelli',
'NRC',
'OLISYS - Ma Solution IT',
'Proxi Pause',
'Trekk',
'ZENCONNECT – ZEN PACK') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'normal';



