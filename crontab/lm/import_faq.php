<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");

echo dirname(__FILE__)."/../../global.inc.php";

ATF::define("tracabilite",false);
 
$faq = array(
	array(21,"Qu'est-ce que Leroy Merlin Abonnements ?","Leroy Merlin Abonnements est la structure qui s\'occupe de tous les dossiers d\'abonnements de Leroy Merlin. Comme elle fait pleinement partie de Leroy Merlin, vous bénéficiez évidemment de la qualité de services de Leroy Merlin. ")
	,array(21,"Si je m'abonne, puis-je bénéficier des points habituels sur ma carte Maison ?","Non, les abonnements ne permettent pas de cumuler des points sur la carte Maison. Il s\'agit d\'une offre de services complémentaires à notre offre de produits. Mais pour tous vos achats de produits Leroy Melrin, vous continuez de cumuler des points sur votre carte Maison. ")
	,array(21,"Je bénéficie d\'une réduction grâce à ma carte maison. Puis-je utiliser ma réduction sur les abonnements ? ","Non, les réductions de la carte Maison ne s\'appliquent pas aux offres d\'abonnements Leroy Merlin. En revanche, vous pouvez utiliser vos réductions sur tous les produits Leroy Merlin. ")
	,array(21,"Pourquoi mes dossiers d\'abonnements ne sont-ils pas accessibles depuis mon compte Leroy Merlin ?","Pour pouvoir vous proposer des fonctionnalités innovantes, nous avons fait le choix d\'un module dédié aux abonnements depuis un compte spécifique. Toutefois, nous comprenons qu'un compte unique puisse être plus simple pour vous et nous étudions cette possibilité pour l\'avenir. ")
	,array(22,"Pourquoi choisir les offres d\'abonnements de Leroy Merlin ?","Parce que vous allez vous simplifier la vie en trouvant l\'offre qui correspond exactement à vos besoins ! En effet, avec l\'offre d\'abonnement, Leroy Merlin vous propose de profiter d\'un usage et pas seulement d\'un produit. Par exemple, nous proposons des garanties de matériels pendant 15 ans sur certains équipements ! Nous avons confiance dans les produits que nous vous proposons et nous pouvons vous garantir la mise en fonction optimale et l\'entretien. Pour vous, pas de mauvaise surprise : prix garanti et sérénité assurée.")
	,array(22,"Qui peut bénéficier des offres d\'abonnements ?","Aujourd\'hui, tous les particuliers peuvent s\'abonner aux offres de Leroy Merlin mais les abonnements ne sont pas accessibles aux professionnels. ")
	,array(22,"Pourquoi s\'abonner plutôt qu'acheter ?","Leroy Merlin vous propose une réponse complète à vos besoins, dans la durée. Ainsi, quel que soit votre besoin (boire de l\'eau de qualité, vous protéger des intrusions, surveiller votre jardin...), Leroy Merlin y répond mois après mois en vous proposant un ensemble complet qui comprend l\'installation, les produits, leur entretien...   Nous allons jusqu'à garantir certains matériels pendant 15 ans ! Pour vous, pas de mauvaise surprise : prix fixe garanti et sérénité assurée. ")
	,array(22,"Les offres d\'abonnements Leroy Merlin sont-elles des crédits ou des locations ?","A l\'inverse de certaines enseignes, nous avons choisi de proposer des offres d\'abonnements qui vous laissent plus de liberté qu'une location. En effet, passé la période d\'engagement, vous pouvez continuer votre abonnement, le résilier pour bénéficier d\'une offre plus récente ou suspendre complètement votre abonnement. Notre objectif ? Répondre parfaitement à vos besoins, même s\'ils évoluent dans le temps. ")
	,array(22,"Puis-je acheter les produits s\'ils me plaisent ?","La réglementation ne nous permet pas de vous proposer d\'acheter un produit. A travers les abonnements, nous vous proposons une réponse complète à vos besoins (installation, produit, entretien...) qui va bien au-delà d\'un simple achat.")
	,array(22,"Suis-je propriétaire des accessoires livrés et ou posés avec le produit ?","Leroy Merlin reste propriétaire du matériel et de tous les accessoires sauf ceux éventuellement facturés à part. Vous êtes ainsi assuré de pouvoir attendre le fonctionnement optimal de vos produits tout au long de votre abonnement. ")
	,array(22,"S\'abonner coûte-il plus cher qu'acheter ?","Parfois, nos offres d\'abonnements peuvent coûter plus cher que l\'achat d\'un produit seul. Mais l\'abonnement vous assure de nombreux services supplémentaires (l\'installation, l\'entretien, ...) et vous garantit le bon fonctionnement des équipements tout au long de votre abonnement sans surcoût, même en cas de panne.  Dans votre abonnement, tout est inclus ! Pas de mauvaise surprise : prix garanti, sérénité assurée ! ")
	,array(22,"Puis-je donner ou prêter le produit en abonnement ?","Nos formules d\'abonnements sont adaptées à votre besoin et pas forcément à votre voisin. Il n\'est pas possible de prêter, de louer ou de vendre le matériel proposé dans votre abonnement. Il ne serait plus assuré ni garanti !")
	,array(22,"La loi Châtel s\'applique-t-elle sur nos offres d\'abonnements ?","La loi Châtel règlemente la vente à distance et nos offres d\'abonnements y sont donc soumises. Vous disposez donc d\'un droit de rétractation légal de 7 jours francs. Néanmoins, la loi Châtel est plus connue dans le secteur des télécoms qui permet de résilier un contrat de manière anticipée en ne payant qu'une fraction des montants restant dus ou de limiter l\'engagement à 24 mois. Nos offres d\'abonnements ne sont pas concernées par cette disposition particulière de la règlementation.")
	,array(22,"Puisse-je bénéficier du crédit d\'impôts et de la TVA réduite sur les offres d\'abonnements ?","La règlementation française n\'est pas encore adaptée à nos offres innovantes d\'abonnements. En effet, vous devez être propriétaire du matériel pour pouvoir bénéficiez du crédit d\'impôts et de la TVA réduite et vous ne pouvez donc pas en profiter avec nos offres d\'abonnements. ")
	,array(22,"A quels produits puis-je m'abonner ?","Vous trouverez l\'ensemble des produits accessibles sous forme d\'abonnements Leroy Merlin <a href='http://www.leroymerlin.fr/v3/p/les-abonnements-leroy-merlin-l1500114731' target='_blank'>ici</a> . Vous pouvez aussi accéder à la fiche des produits et choisir S\'abonner. Nous avons volontairement choisi de proposer l\'abonnement sur des produits parfaitement fiables et avec des partenaires en qui nous avons toute confiance. C\'est pourquoi, nous préférons vous proposer une offre resserrée de produits disponibles sous forme d\'abonnement. Nous complèterons cette offre au fil du temps pour répondre au mieux à vos besoins. Vous pouvez aussi accéder à la fiche des produits et choisir S\'abonner.")
	,array(22,"Les produits disponibles sous forme d\'abonnement sont-ils neufs ?","Les produits disponibles sous forme d\'abonnement sont évidemment neufs pour vous offrir un usage optimal. Nous pouvons aussi être amenés à vous proposer des abonnements sur des produits d\'occasion mais, dans ce cas, nous l\'indiquerons très clairement.")
	,array(22,"Quelles sont les conditions générales d\'abonnement ?","Chaque offre est spécifique et les conditions générales varient donc selon les cas. Vous les découvrirez au cours de l\'inscription et vous pourrez bien sûr les lire attentivement avant de les signer. De plus, votre contrat vous sera envoyé par mail et il sera disponible à tout instant sur votre compte client.")
	,array(22,"Comment puis-je connaître le montant de mes prélèvements ?","Le montant des prélèvements est indiqué dans votre contrat d\'abonnement et vous y aurez accès à tout moment sur votre compte client.")
	,array(22,"Quelle est la durée d\'engagement ?","Là encore, la durée d\'abonnement dépend des offres et des produits. Nous concevons les abonnements pour que la durée corresponde à l\'usage du matériel et du besoin éventuel de changer de matériel. Par exemple, pour les abonnements d\'alarme, vous vous engagez sur 1 an.")
	,array(22,"Quels services proposez-vous dans les offres ?","Cela dépend de chaque offre ! Effectivement, chaque produit répond à un besoin et nécessite donc des services différents. Pour certains, il s\'agit de l\'installation, pour d\'autres d\'une révision régulière. Vous trouverez le détail des services dans chaque offre.")
	,array(22,"S\'abonner est-elle une solution durable ?","Bien sûr ! Lorsque nous récupérons un produit, nous le remettons en état si cela est possible et pouvons le reproposer en abonnement ou le revendre d\'occasion. Ceci permet d\'allonger la durée de vie des produits et ainsi de réduire sensiblement nos déchets. De même, nous travaillons en étroite collaboration avec nos fournisseurs pour optimiser la durée de vie de nos produits mis en abonnement.")
	,array(25,"Comment et où peut-on souscrire à une offre d\'abonnement ?","Vous pouvez souscrire à une offre directement sur notre site internet ou dans certains de nos magasins.<br>
Nos offres d\'abonnements sont en général disponibles en France métropolitaine mais certaines le sont dans certains départements uniquement. Nous souhaitons bien évidemment vous proposer un service d\'excellence et nous étoffons régulièrement notre réseau de partenaires pour  développer nos offres le plus largement possible.<br>
Pour bénéficier de nos offres :
<ol><li>Nous vous demandons vos coordonnées ainsi que l’adresse d’installation.</li>
<li>Vous saisissez votre numéro de carte bleue pour régler l’acompte de votre première mensualité ainsi que votre RIB pour les règlements mensuels suivants.</li>
<li>Nous vous proposons de signer électroniquement votre contrat ainsi que votre mandat de prélèvements.</li>
<li>Dans la plupart des cas, un de nos conseillers vous appelle dans les 24h qui suivront l\'enregistrement de votre demande et valide avec vous l\'adéquation de votre configuration avec votre habitat.</li>
<li>Ce même conseiller convient alors avec vous de la date d’installation, sous 10 jours maximum.</li>
<li>Votre contrat commence à partir de l’installation ou de la livraison.</li></ol>")
	,array(25,"Comment être sûr que l\'offre choisie correspond à mon besoin ?","La sérénité fait partie de l\'abonnement ! Si vous souscrivez sur Internet, un conseiller vous appelle avant l\'envoi des produits pour vérifier avec vous la correspondance de l\'offre avec votre besoin. Et, si vous l\'avez fait en magasin, vous avez bénéficié du savoir faire de nos conseillers de vente...")
	,array(25,"Quelles informations demandez-vous pour bénéficier d\'une offre d\'abonnement ?","Pour profiter d\'une offre d\'abonnement Leroy Merlin, c\'est tout simple ! Nous vous demandons vos coordonnées pour l\'installation et/ou la livraison du matériel, votre RIB pour les prélèvements mensuels et un paiement par carte bleue de la première mensualité. Et c\'est tout ! ")
	,array(25,"Pendant ma souscription, j\'ai un message d\'erreur, comment faire ?","Si vous avez rencontré un message d\'erreur pendant la souscription à une offre, vous pouvez nous laisser un message via le formulaire de contact. Nous vous contacterons dans les plus brefs délais pour régler la situation. ")
	,array(25,"Le stockage de mes données bancaires est-il vraiment sécurisé ?","Nous ne stockons jamais vos données bancaires. Elles sont stockées chez notre partenaire bancaire qui dispose des accréditations nécessaires pour une sécurité parfaite.")
	,array(26,"Comment puis-je suivre ma commande ?","A chaque étape de votre commande, nous vous envoyons des mails d\'information. Si vous le souhaitez, vous pouvez aussi suivre l\'avancement de votre dossier sur votre compte client.")
	,array(26,"Comment faire pour décaler le rendez-vous de pose ou changer l\'adresse d\'installation?","Si vous ne pouvez finalement pas être présent aux date et heure programmées, vous pouvez nous joindre depuis votre compte client et demander un nouveau rendez-vous ou contacter directement notre partenaire qui vous aura laissé ses coorodonnées.")
	,array(27,"Comment puis-je me rétracter ?","Avec Leroy Merlin, vous pouvez bien sûr changer d\'avis et vous rétracter. Pour cela, il vous suffit d\'accéder à votre compte client et de suivre les instructions. Vous disposez d\'un délai de 14 jours ouvrés pour vous rétracter si vous avez souscrit sur Internet et 7 jours si vous avez souscrit en magasin. Cependant, si le matériel a déjà été livré, des frais pourront vous être facturés pour la désinstallation du matériel et son transport.")
	,array(27,"Comment puis-je créer mon compte Leroy Merlin Abonnement ?","C\'est tout simple ! Votre compte Leroy Merlin Abonnement sera automatiquement créé lorsque vous souscrirez à une de nos offres. Vous recevrez immédiatement un mail contenant les éléments vous permettant de vous connecter.")
	,array(27,"Où puis-je trouver mes identifiant et mot de passe ?","L\'identifiant de votre compte est votre adresse mail et c\'est vous qui choisissez votre mot de passe. En cas d\'oubli, vous pouvez  toujours réinitialiser le mot de passe directement depuis votre site Client.")
	,array(27,"J\'ai une question sur mon abonnement. Comment faire ?","Tout d\'abord, nous vous conseillons de lire la FAQ (Foire Aux Questions) que nous complétons régulièrement. Si vous ne trouvez pas la réponse à votre question, vous pouvez nous poser votre question à partir de la rubrique Contactez-nous accessible depuis votre espace client.")
	,array(27,"Comment joindre un conseiller en cas de difficulté ?","Le bon interlocuteur dépend de votre question. Les différents contacts sont indiqués dans votre contrat d\'abonnement. En cas de doute, vous pouvez nous contacter à partir de votre espace clients et nous vous répondrons dans les plus brefs délais. ")
	,array(27,"Comment faire pour modifier mes données personnelles (coordonnées postales, bancaires, code personnel...) ?","Pour modifier vos données, il vous suffit de vous connecter à votre compte personnel. Sélectionnez la rubrique qui vous concerne et suivez les instructions ! ")
	,array(27," A quoi vont servir les informations personnelles que vous me demandez ?","Vos données personnelles sont traitées avec respect, prudence et confidentialité. Nous nous engageons en effet à ne pas les diffuser à des tiers sans votre accord sauf bien sûr à nos partenaires qui permettent l\'exécution des services inclus dans votre offre d\'abonnement. Conformément à la Loi Informatique et Liberté du 06/01/1978, vous disposez d’un droit d’accès, de rectification et de suppression des données vous concernant et d’opposition à leur traitement. Si vous souhaitez l’exercer, vous pouvez écrire à Leroy Merlin Abonnement - Rue Chanzy - Lezennes - 59 712 LILLE CEDEX 9, en indiquant votre nom, prénom, adresse et email. Vous pouvez bien évidemment consulter notre politique de données personnelles sur notre site internet.")
	,array(27,"Est-ce que je peux ajouter ou supprimer une option pendant mon contrat ?","Oui, certaines options peuvent être ajoutées ou enlevées pendant la vie de votre contrat. Pour cela, vous pouvez accéder à votre espace client et effectuer ces modifications.")
	,array(27,"Que se passe-t-il si je dois déménager ?","Si vous déménagez, il y a quatre possibilités : 
<ol><li>Vous pouvez résilier votre contrat sans frais si la période d\'engagement est passée ou avec frais sinon. </li>
<li>Si vous souhaitez garder le matériel pour votre nouveau logement et si cela est possible (prévu dans l\'offre et zone couverte par nos services), nous organisons la désinstallation et la réinstallation avec éventuellement des frais associés. </li>
<li>Le nouvel occupant peut continuer le contrat avec les mêmes garanties. </li>
<li>Nous pouvons étudier la vente du matériel au nouvel occupant.</li></ol>")
	,array(27,"Comment prolonger mon contrat ?","Pour prolonger votre contrat, vous n\'avez rien à faire : il est reconduit automatiquement par défaut (reconduction tacite). Mais vous pouvez bien sûr l\'arrêter à tout moment après la période d\'engagement en nous prévenant à l\'avance selon les délais prévus dans le contrat.")
	,array(27,"A quelle date suis-je prélevé ?","Nous prélevons les mensualités le 5 du mois. Mais vous pouvez choisir une autre date de prélèvement sur simple demande en vous connectant sur votre compte client. Le mois suivant, nous recalculons la mensualité en tenant compte du changement.")
	,array(27,"Comment peut-on résilier l\'abonnement ?","Vous pouvez résilier votre contrat à tout moment dès la période d\'engagement passée. Avant cela, vous pouvez le faire mais vous devrez payer les mensualités restant à courir. Pour le faire, il vous suffit de vous connecter à votre espace client et de suivre les instructions.")
	,array(27,"Que se passe-t-il si mon produit en abonnement est cassé ou volé ?","Les produits que nous proposons n\'ont pas vocation à être déplacés. Le risque de casse ou de vol est donc extrêmement faible. Cependant, votre signature du bon de livraison équivaut au transfert de responsabilité. Ainsi, si un produit est cassé ou volé, vous êtes responsable et vous serez redevable à Leroy Merlin Abonnement de la valeur des réparations en cas de casse ou de la valeur du produit (prenant en compte son ancienneté) en cas de vol. C\'est pourquoi, nous vous recommandons de vérifier auprès de votre assureur que vous êtes couvert, ce qui est très souvent le cas avec la responsabilité civile ou votre assurance habitation. Dans le cas d’une casse ou d’un vol, nous vous invitons à vous rapprocher de votre compagnie d\'assurance afin d’ouvrir un dossier de sinistre et de faire en sorte que les frais de réparation soient pris en charge par celle-ci. Si vous n’êtes pas couvert pour ce type de sinistre, vous devrez procéder à vos frais, à la réparation ou au remplacement du produit.")
	,array(27,"Que se passe-t-il si le produit pris en abonnement n\'est pas réparable ?","Si le produit n\'est pas réparable pendant la période d\'engagement, nous le remplacerons gratuitement. Une fois la période d\'engagement passée, nous déterminons la meilleure solution : soit nous le remplaçons par le même modèle soit le contrat s\'arrête et nous vous proposons un nouveau matériel plus récent et adapté à vos besoins avec un nouveau contrat.")
	,array(27,"Que se passe-t-il si l\'une de mes mensualités est impayée ?","S\'il s\'agit d\'un impayé d\'origine technique, nous mettrons bien sûr tout en œuvre pour corriger l\'anomalie et renouveler le prélèvement impayé. S\'il s\'agit d\'un impayé pour solde insuffisant, nous vous contacterons rapidement pour régulariser la situation et trouver une solution ensemble."));

$parent = NULL;
$position = 1;

foreach ($faq as $key => $value) {
	if($parent == NULL){
		$parent = $value[0];
		$position = 1;
	}

	if($parent == $value[0]){
		$position = $position + 10;
	}else{
		$parent = $value[0];
		$position = 1;
	} 
	$data =	 array("id_parent"=>$value[0],
				  "titre"=>$value[1],
				  "id_site_menu"=>5,
				  "position"=>$position,
				  "visible"=>"oui"
				);
	
	$id_titre = ATF::site_article()->i($data);

	ATF::site_article_contenu()->i(array("id_site_article"=>$id_titre,"texte"=>$value[2]));
}



