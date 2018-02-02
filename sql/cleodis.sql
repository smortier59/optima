#17390 - Ajout message sur la facture
ALTER TABLE `affaire` ADD `commentaire_facture` TEXT NULL COMMENT 'Commentaire qui sera afficher sur les factures clients' AFTER `id_partenaire`;



#DEVIS CLEODIS V2
ALTER TABLE `devis_ligne` ADD `options` ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER `commentaire`;
ALTER TABLE `devis` ADD `commentaire_offre_partenaire` TEXT NULL DEFAULT NULL AFTER `raison_refus`;
ALTER TABLE `devis` ADD `offre_partenaire` varchar(80) NULL DEFAULT NULL AFTER `raison_refus`;



ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;ALTER TABLE `affaire` CHANGE `provenance` `provenance` ENUM('toshiba','cleodis','vendeur','partenaire') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `affaire` ADD `id_partenaire` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL AFTER `date_verification`;
ALTER TABLE `affaire` ADD FOREIGN KEY (`id_partenaire`) REFERENCES `societe`(`id_societe`) ON DELETE RESTRICT ON UPDATE CASCADE;



# - FAQ portail partenaire Toshiba
ALTER TABLE `site_menu` ADD `site_web` ENUM('location_evolutive','portail_toshiba') NOT NULL AFTER `url`;


INSERT INTO `site_menu` (`id_site_menu`, `titre_menu`, `visible`, `url`, `site_web`) VALUES (5, 'FAQ', 'oui', NULL, 'location_evolutive');

INSERT INTO `site_article` (`id_site_article`, `titre`, `id_site_menu`, `position`, `visible`) VALUES
(21, 'Qui est Cleodis Location Systèmes ?  Le partenaire des TPE, PME et professions libérales depuis 10 ans', 5, 7, 'oui'),
(22, 'Pourquoi la location évolutive s’impose-t-elle dans les entreprises ?', 5, 1, 'oui'),
(23, 'Notre vocation ? Vous faire profiter de la technologie en vous apportant SIMPLICITE, TRANQUILLITE et LIBERTE', 5, 2, 'oui'),
(24, 'Comment commander ?', 5, 3, 'oui'),
(25, 'Quelles sont les pièces à fournir ?', 5, 3, 'oui'),
(26, 'Pourquoi louer avec Cleodis : tout est simple, contactez-nous !', 5, 4, 'oui'),
(27, 'Les activités du groupe CLEODIS au service de votre trésorerie', 5, 8, 'oui');

INSERT INTO `site_article_contenu` (`id_site_article_contenu`, `id_site_article`, `texte`) VALUES
(NULL, 21, '<p style=\"margin: 0px 0px 10px; color: rgb(53, 53, 53); font-family: Arial;\">Créée en 2004 à Lille, CLEODIS Location Systèmes s’est rapidement imposée auprès des&nbsp;<strong>PME, TPE, artisans et professions libérales</strong>&nbsp;comme un acteur incontournable de la Location Longue&nbsp;Durée Evolutive.<span style=\"font-size: 1.5em;\">&nbsp;</span></p><p style=\"margin: 0px 0px 10px; color: rgb(53, 53, 53); font-family: Arial;\">Présents dans&nbsp;<strong>plus de 3 000 entreprises</strong>&nbsp;sur l’ensemble du territoire français, nous intervenons en partenariat avec notre réseau de distributeurs et SSII pour vous apporter des&nbsp;<strong>solutions complètes et proches de chez vous.</strong></p>'),
(NULL, 23, '<span style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">Cléodis est présent à vos côtés pour :</span><div class=\"block-green\" style=\"margin: 10px auto; padding: 10px 30px; color: rgb(255, 255, 255); border-radius: 5px; font-family: Arial; line-height: 16.2000007629395px; background: rgb(121, 163, 29);\"><ol style=\"margin: 0px 0px 1em; padding: 0px 0px 0px 1.5em; list-style-position: outside;\"><li style=\"margin: 0px; padding: 0px;\">Vous&nbsp;<span style=\"margin: 0px; padding: 0px; color: rgb(0, 50, 93);\">conseiller</span>&nbsp;sur le matériel le plus adapté à votre besoin</li><li style=\"margin: 0px; padding: 0px;\">Faire installer le matériel par un professionnel membre de notre réseau de partenaires, sélectionné pour sa&nbsp;<span style=\"margin: 0px; padding: 0px; color: rgb(0, 50, 93);\">compétence et sa proximité ;</span></li><li style=\"margin: 0px; padding: 0px;\">Maintenir le matériel en état au travers d’une&nbsp;<span style=\"margin: 0px; padding: 0px; color: rgb(0, 50, 93);\">garantie</span>&nbsp;et un&nbsp;<span style=\"margin: 0px; padding: 0px; color: rgb(0, 50, 93);\">SAV efficace</span>&nbsp;durant toute la durée de votre contrat.</li></ol></div><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">Avec Cléodis, vous disposez de&nbsp;<strong style=\"margin: 0px; padding: 0px;\">matériels toujours&nbsp;</strong><strong style=\"margin: 0px; padding: 0px;\">en adéquation avec vos besoins</strong>&nbsp;et pouvant évoluer en cours de contrat. &nbsp;</p><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">En fin de contrat, nous renouvelons vos équipements par de nouveaux afin de toujours vous garantir un bon niveau de performance et une maintenance sur site.</p><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">&nbsp;</p><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><strong style=\"margin: 0px; padding: 0px;\">Cléodis fait partie du groupe CST</strong>&nbsp;- Conseils en Solutions de Trésorerie -<strong style=\"margin: 0px; padding: 0px;\">&nbsp;spécialiste de l\'optimisation de la trésorerie</strong>&nbsp;auprès des TPE, PME et professions libérales. &nbsp;</p><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">Pour en savoir plus sur<strong style=\"margin: 0px; padding: 0px;\">&nbsp;nos solutions d\'optimisations, contactez-nous au 03 28 140 200.</strong></p><div style=\"margin: 0px; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><strong style=\"margin: 0px; padding: 0px;\"><br style=\"margin: 0px; padding: 0px;\"></strong></div><p class=\"font16\" style=\"margin: 0px 0px 1em; padding: 0px; font-size: 16px; color: rgb(53, 53, 53); font-family: Arial;\"><strong style=\"margin: 0px; padding: 0px;\">Avec Cléodis, Louez, vous avez tout compris !</strong></p><p class=\"font16\" style=\"margin: 0px 0px 1em; padding: 0px; font-size: 16px; color: rgb(53, 53, 53); font-family: Arial;\"><span style=\"margin: 0px; padding: 0px; font-size: small;\"><strong style=\"margin: 0px; padding: 0px; font-size: 16px;\">Suivez toute notre actualité sur notre blog :&nbsp;<a href=\"http://location-evolutive.over-blog.com/tag/qui%20peut%20louer/\" style=\"margin: 0px; padding: 0px; color: rgb(53, 53, 53); text-decoration: none;\">http://location-evolutive.over-blog.com/tag/qui%20peut%20louer/</a></strong></span></p>'),
(NULL, 26, '<span style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">C’est sans engagement et vous pourrez ainsi :</span><ul style=\"margin: 0px 0px 1em; padding: 0px 0px 0px 1.5em; list-style-position: outside; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><li style=\"margin: 0px; padding: 0px;\"><strong style=\"margin: 0px; padding: 0px;\">gagner du temps</strong></li><li style=\"margin: 0px; padding: 0px;\">bénéficier d’un&nbsp;<strong style=\"margin: 0px; padding: 0px;\">conseil indépendant</strong>&nbsp;(des marques, des éditeurs et des prestataires)</li><li style=\"margin: 0px; padding: 0px;\">vous équiper de<strong style=\"margin: 0px; padding: 0px;\">&nbsp;solutions tout compris</strong>&nbsp;validées par nos experts techniques</li><li style=\"margin: 0px; padding: 0px;\">recourir à des<strong style=\"margin: 0px; padding: 0px;\">&nbsp;installateurs reconnus</strong>&nbsp;pour leur compétence et leur professionnalisme</li></ul><p class=\"font16\" style=\"margin: 0px 0px 1em; padding: 0px; font-size: 16px; color: rgb(53, 53, 53); font-family: Arial;\"><strong style=\"margin: 0px; padding: 0px;\">Avec Cléodis, louez ! Vous avez tout compris !</strong></p>'),
(NULL, 25, '<span style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">C\'est simple ! nous n’avons besoin que de quelques éléments pour étudier votre dossier :</span><ul style=\"margin: 0px 0px 1em; padding: 0px 0px 0px 1.5em; list-style-position: outside; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><li style=\"margin: 0px; padding: 0px;\">vos noms, prénoms, fonctions, téléphone et courriels (pour transmission de l’offre)</li><li style=\"margin: 0px; padding: 0px;\">les cordonnées complète de l’entreprise ou son code SIREN/SIRET</li></ul>'),
(NULL, 24, '<strong style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px; margin: 0px; padding: 0px;\">1</strong><span style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">. vous avez trouvé le produit qui vous convient parmi nos</span><span style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">&nbsp;</span><strong style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px; margin: 0px; padding: 0px;\">solutions packagées</strong><span style=\"color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">&nbsp;en ligne?</span><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><span style=\"margin: 0px; padding: 0px; color: rgb(51, 51, 51);\"><strong style=\"margin: 0px; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; font-size: small;\">&gt; Valider votre panier et nous vous contacterons pour organiser la livraison et l\'installation.</span></strong></span></p><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><strong style=\"margin: 0px; padding: 0px;\">2. vous avez un besoin</strong>&nbsp;mais n’êtes pas certain de la meilleure façon de l’appréhender ?<br style=\"margin: 0px; padding: 0px;\"><strong style=\"margin: 0px; padding: 0px;\">&gt; nous réalisons un audit GRATUIT et vous proposons l\'offre la mieux adaptée.</strong><br style=\"margin: 0px; padding: 0px;\"><strong style=\"margin: 0px; padding: 0px;\"></strong></p><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><strong style=\"margin: 0px; padding: 0px;\">3. vous savez exactement ce que vous cherchez</strong>&nbsp;et/ou vous avez déjà effectué la démarche auprès de votre partenaire technique ;&nbsp;<br style=\"margin: 0px; padding: 0px;\"><strong style=\"margin: 0px; padding: 0px;\">i</strong>l ne vous manque plus que la partie locative ?<br style=\"margin: 0px; padding: 0px;\"><strong style=\"margin: 0px; padding: 0px;\">&gt; nous vous bâtissons une offre locative sur mesure en 24h maximum.</strong></p>'),
(NULL, 22, '<ol style=\"margin: 0px 0px 1em; padding: 0px 0px 0px 1.5em; list-style-position: outside; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><li style=\"margin: 0px; padding: 0px;\">La location convient parfaitement aux&nbsp;<strong style=\"margin: 0px; padding: 0px;\">équipements à durée de vie courte</strong>, à obsolescence et dépréciation rapide, 3 spécificités remarquables de l’informatique, la&nbsp;bureautique et la téléphonie.</li><li style=\"margin: 0px; padding: 0px;\">L’évolution des besoins de l’entreprise dans ces domaines étant difficile à planifier, la location est à ce jour le meilleur&nbsp;<strong style=\"margin: 0px; padding: 0px;\">support financier capable d’apporter la souplesse</strong>nécessaire à ces évolutions : une simple&nbsp;modification du contrat et voilà votre système mis à niveau ! Plus de contraintes comptables liées à l’amortissement, les plus ou moins values, la gestion des reprises ou du recyclage des anciens matériels : CLEODIS s’occupe de tout !</li><li style=\"margin: 0px; padding: 0px;\">La location permet d’intégrer dans&nbsp;<strong style=\"margin: 0px; padding: 0px;\">un même contrat</strong>:<ul style=\"margin: 0.5em 0px; padding: 0px 0px 0px 1.5em; list-style: disc outside;\"><li style=\"margin: 0px; padding: 0px;\">des matériels</li><li style=\"margin: 0px; padding: 0px;\">des logiciels</li><li style=\"margin: 0px; padding: 0px;\">des prestations de service</li></ul></li></ol><div class=\"block-green\" style=\"margin: 10px auto; padding: 10px 30px; color: rgb(255, 255, 255); border-radius: 5px; font-family: Arial; line-height: 16.2000007629395px; background: rgb(121, 163, 29);\"><p style=\"margin: 0px 0px 1em; padding: 0px;\"><span style=\"margin: 0px; padding: 0px; color: rgb(0, 50, 93);\">Témoignage client : Hughes ROBIDEZ : Expert Comptable / Commissaire aux Compte chez Grant Thornton</span></p><p style=\"margin: 0px 0px 1em; padding: 0px;\">\"Les économistes s’accordent sur le fait que l’utilisation des fonds propres de l’entreprise doit être réservée aux éléments directement liés à l’activité de l’entreprise, comme l’appareil productif par exemple.<br style=\"margin: 0px; padding: 0px;\">Si le système d’information joue une part prépondérante dans la gestion et la productivité de l’entreprise, il ne représente pas son cœur d’activité. Dès lors, recourir à une solution de financement souple et adaptée comme la location évolutive représente un choix de gestion pertinent.\"</p></div><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><strong style=\"margin: 0px; padding: 0px;\">Avant de choisir un nouvel équipement, posez-vous les 4 questions suivantes :</strong></p><ul style=\"margin: 0px 0px 1em; padding: 0px 0px 0px 1.5em; list-style-position: outside; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\"><li style=\"margin: 0px; padding: 0px;\">ai-je les compétences en interne pour définir mes besoins ?</li><li style=\"margin: 0px; padding: 0px;\">ai-je la maîtrise des aspects techniques pour choisir les équipements les mieux adaptés ?</li><li style=\"margin: 0px; padding: 0px;\">ma connaissance des prix de marché est-elle suffisante pour être sûr d’obtenir les meilleurs prix ?</li><li style=\"margin: 0px; padding: 0px;\">ai-je beaucoup de temps à y consacrer ?</li></ul><p style=\"margin: 0px 0px 1em; padding: 0px; color: rgb(53, 53, 53); font-family: Arial; line-height: 16.2000007629395px;\">Si vous n’avez pas répondu «oui» à ces 4 questions, nous vous invitons à nous contacter.</p>'),
(NULL, 27, '<h2 class=\"bleu\" style=\"color: rgb(27, 45, 77); font-family: \'Open Sans\', sans-serif; font-size: 22px; box-sizing: border-box; line-height: 1.2; margin: 36px 0px 12px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; clear: both;\">Créé en 2004 à Lille, le Groupe Cléodis est un expert en externalisation et optimisation de trésorerie auprès des TPE, PME, professions libérales et réseaux de franchise.</h2><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\"><img class=\"alignleft wp-image-511 size-medium\" src=\"http://cleodis.ravendt.net/wp-content/uploads/2015/06/shutterstock_125338145-300x200.jpg\" alt=\"Externalisation et optimisation de trésorerie par Cléodis - Informatique RH et Recouvrement\" width=\"300\" height=\"200\" style=\"box-sizing: border-box; border: 0px; vertical-align: middle; width: inherit; height: auto; max-width: 100%; text-align: left; float: left; margin-right: 20px; margin-bottom: 12px;\">Si vous souhaitez vous affranchir de toutes les contraintes liées à la gestion tout en optimisant et renforçant votre trésorerie, faites appel à nous ! Nous sommes en quelque sorte&nbsp;<span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">« Une PME au service des PME »</span>.</p><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">Les partenaires avec lesquels nous travaillons, sont triés sur le volet et sélectionnés pour leurs compétences et leur proximité (nous pouvons intervenir&nbsp;<span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">partout en France</span>).</p><h3 class=\"bleu\" style=\"color: rgb(27, 45, 77); font-family: \'Open Sans\', sans-serif; font-size: 20px; box-sizing: border-box; line-height: 1; margin: 36px 0px 12px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; clear: both;\">Les atouts et les trois métiers du Groupe Cléodis</h3><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">Nous fonctionnons selon un processus bien défini et qui a fait ses preuves :</p><ul style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px 20px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; list-style-image: url(http://www.cleodis.com/wp-content/themes/meris.1.1.0/meris/puce.png); list-style-position: initial; text-align: justify;\"><li style=\"box-sizing: border-box; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\"><span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Une approche individualisée</span>&nbsp;de l’entreprise cliente (même dans le cas des créations d’entreprise)</li><li style=\"box-sizing: border-box; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\"><span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Une supervision et une gestion centralisées</span>&nbsp;intégrant un reporting périodique</li></ul><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">Axé sur l’externalisation et l’optimisation de Trésorerie, le Groupe Cléodis a développé ses offres et ses services en les organisant en trois filiales :</p><ul style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px 20px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; list-style-image: url(http://www.cleodis.com/wp-content/themes/meris.1.1.0/meris/puce.png); list-style-position: initial; text-align: justify;\"><li style=\"box-sizing: border-box; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\"><span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Cléodis Location Systèmes</span>&nbsp;(Location Evolutive IT)</li><li style=\"box-sizing: border-box; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\"><span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Cléodis Gestion Facturation</span>&nbsp;(Poste Client -&nbsp;Recouvrement)</li><li style=\"box-sizing: border-box; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\"><span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Cléodis Gestion Abonnement pour compte</span>&nbsp;(Prestation de gestion de Poste Client par abonnement)<span style=\"font-family: inherit; font-style: inherit; box-sizing: border-box; font-weight: 700; border: 0px; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\"><br><br><br></span><br></li></ul><blockquote style=\"color: rgb(118, 118, 118); font-family: \'Open Sans\', sans-serif; font-size: 19px; box-sizing: border-box; padding: 0px; margin: 0px 0px 24px; border: 0px; font-style: italic; outline: 0px; vertical-align: baseline; -webkit-hyphens: none; quotes: none; line-height: 1.2631578947;\"><p style=\"box-sizing: border-box; margin: 0px; border: 0px; font-family: inherit; font-size: 17.5px; font-style: inherit; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify; line-height: 1.25;\">Cléodis est un membre du&nbsp;<span style=\"box-sizing: border-box; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Groupe CST</span>&nbsp;– Conseils en Solutions de Trésorerie –&nbsp;<span style=\"box-sizing: border-box; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">spécialiste de l’optimisation de trésorerie</span>&nbsp;auprès des TPE, PME et professions libérales.</p></blockquote><h3 class=\"bleu\" style=\"color: rgb(27, 45, 77); font-family: \'Open Sans\', sans-serif; font-size: 20px; box-sizing: border-box; line-height: 1; margin: 36px 0px 12px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; clear: both;\">Les dates et chiffres clefs du Groupe Cléodis</h3><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">2004 Création de&nbsp;<span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Cléodis Location Systèmes</span></p><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">2010 Création du&nbsp;<span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Groupe Cléodis</span>&nbsp;et de la filiale&nbsp;<span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">CLEOFI</span></p><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">2013 Le Groupe Cléodis est élu <b>«&nbsp;partenaire de l’année&nbsp;» auprès du réseau Midas </b>!</p><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">2014 La société <b>Cap Recouvrement </b>rejoint le Groupe en tant que Filiale (<span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">Cléodis Gestion Facturation</span>)</p><span style=\"text-align: justify;\"><font color=\"#525252\" face=\"Open Sans, sans-serif\"><span style=\"font-size: 14px;\">2016&nbsp;Lancement de l\'offre de&nbsp;</span></font></span><span style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; text-align: justify;\"><b>Prestation de gestion d\'abonnement/location pour compte (IT et Poste client)</b></span><br><br><p style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; box-sizing: border-box; margin: 0px 0px 24px; border: 0px; outline: 0px; padding: 0px; vertical-align: baseline; text-align: justify;\">Aujourd’hui, le Groupe Cléodis, c’est plus de 7 M€ de CA et plus de&nbsp;<span style=\"box-sizing: border-box; font-weight: 700; border: 0px; font-family: inherit; font-style: inherit; margin: 0px; outline: 0px; padding: 0px; vertical-align: baseline;\">3 000 clients</span>&nbsp;en France.</p><span style=\"color: rgb(82, 82, 82); font-family: \'Open Sans\', sans-serif; font-size: 14px; text-align: justify;\">Découvrez nos solutions sur <a href=\"http://www.cleodis.com\">www.cleodis.com</a></span><br><br>');
