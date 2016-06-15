<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);
 
$faq = array(
		array(21,"Qu'est-ce que Leroy Merlin Abonnements?","Leroy Merlin Abonnement est une société appartenant à 100% à Leroy Merlin France. Elle gère ses dossiers d'abonnement. Vous bénéficiez donc de la qualité de service de Leroy Merlin."),
		array(21,"Si je m'abonne, est ce que je bénéficie des points habituels sur ma carte Maison?","Non, ce n'est pas possible. Il s'agit effectivement d'une offre complète complémentaire à notre offre de produits."),
		array(21,"Je bénéficie d'une réduction grâce à ma carte maison. Est-ce que c'est applicable sur vos abonnements?","Non, il n'est pas possible d'obtenir de remises sur nos offres d'abonnements."),
		array(21,"Pourquoi ne puisse je pas utiliser mon compte Leroy Merlin pour accéder à mes dossiers d'abonnements?","Leroy Merlin Abonnement étant une société dédiée aux abonnements, nous avons choisi de créer des comptes spécifiques pour vous proposer des fonctionnalités innovantes. Cependant, nous réfléchissons à n'avoir qu'un seul compte client pour gérer l'intégralité de votre relation avec Leroy Merlin."),
		array(22,"Pourquoi choisir les offres d'abonnement de Leroy Merlin?","Nous sélectionnons les meilleurs produits pour répondre à votre besoin et traitons avec des entreprises spécialisées dans chacun des domaines d'intervention. Nous vous garantissons ainsi une très grande qualité de service. Et, de toute façon, si vous n'êtes pas satisfait, vous pouvez résiliez votre contrat quand vous voulez après la période d'engagement."),
		array(22,"Qui peut bénéficier des offres d'abonnement?","Aujourd'hui, seuls les particuliers peuvent s'abonner aux offres de Leroy Merlin."),
		array(22,"Pourquoi s'abonner plutôt qu'acheter?","Les offres que nous proposons sont complètes et vous propose une réponse complète à votre besoin. Il s'agit d'un ensemble de services adaptés aux besoins de nos clients. Nous garantissons un usage et non plus une vente. Par exemple, nous proposons pour certaines offres des garanties de matériels pendant 15 ans ! Nous sommes capables de proposer ce type de garanties car nous choisissons avec vous les produits les plus adaptés et garantissons l'entretien."),
		array(22,"S'agit-il d'un crédit ou d'une location?","Non, il ne s'agit absolument pas d'un crédit ni d'une location. Aucune banque n'intervient dans la gestion de nos offres "," c'est Leroy Merlin qui gèrera l'intégralité de la relation afin de vous garantir une relation privilégiée tout au long de la vie de votre abonnement."),
		array(22,"Pourquoi ne pas proposer de la location?","Certaines enseignes choisissent de proposer des offres de location de matériel "," Leroy Merlin préfère proposer des offres d'abonnement pour laisser plus de liberté à ses clients. Passé la période d'engagement, vous pouvez soit continuer si vous êtes satisfait de nos services ou résilier pour bénéficier d'une offre plus récente ou nous quitter. Notre objectif : Vous offrir toujours une offre de qualité !"),
		array(22,"Puisse je acheter les produits s'ils me plaisent?","Non, il ne vous est pas possible d'acheter le produit après la période d'engagement "," la règlementation nous l'interdit. En fait, nos offres d'abonnements sont bien plus qu'un produit mais vraiment une offre complète de services parfaitement adaptée, vous n'achetez pas un produit mais un service complet."),
		array(22,"Suis-je propriétaire des accessoires livrés et ou posés avec le produit?","Non, Leroy Merlin reste propriétaire de tous les accessoires excepté ceux éventuellement facturés à part."),
		array(22,"S'abonner coûte-il plus cher qu'acheter?","Dans certains cas, nos offres d'abonnements peuvent coûter plus chères que l'achat. Mais, pour faire une vraie comparaison, il ne faut pas oublier d'inclure les entretiens, les éventuelles pannes de matériel et, ce sur toute la durée garantie. Vu que tout est inclus dans nos offres d'abonnement, il n'y a pas de mauvaise surprise et pas de frais non justifiés..."),
		array(22,"Puisse-je donner ou prêter le produit en abonnement?","Non, nos formules d'abonnement étant adaptées à votre besoin, il n'est pas possible de prêter, louer ou vendre le matériel. Il ne serait plus assuré ni garanti !"),
		array(22,"La loi Châtel s'applique-t-elle sur nos offres d'abonnement?","La loi Châtel règlemente la vente à distance, nos offres d'abonnement y sont donc soumises. Vous disposez donc d'un droit de rétractation légal de 7 jours francs. Néanmoins, la loi Châtel est plus connue dans le secteur des télécoms qui permet de résilier un contrat de manière anticipée en ne payant qu'une fraction des montants restant dus ou de limiter l'engagement à 24 mois. Nos offres d'abonnement ne sont pas concernées par cette règlementation."),
		array(22,"Puisse-je bénéficier du crédit d'impôts et de la TVA réduite?","Non, à ce jour, la règlementation française n'est pas adaptée à nos offres innovantes. Effectivement, il faut être propriétaire du matériel pour pouvoir bénéficiez du crédit d'impôts et de la TVA réduite."),
		array(22,"A quels produits puisse-je m'abonner?","Nous avons choisi de tisser des liens forts avec quelques partenaires pour vous garantir la meilleure garantie de service. C'est pourquoi, nous ne proposons que quelques références de produits pour chaque solution
		Nous complétons régulièrement nos offres d'abonnement afin de répondre rapidement au maximum de besoins."),
		array(22,"Comment puisse-je connaître toutes les offres d'abonnement disponibles chez Leroy Merlin?","Il suffit de choisir le produit que vous souhaitez et un bouton spécifique apparaîtra si une offre d'abonnement est disponible."),
		array(22,"Les produits disponibles en abonnement sont-ils neufs?","Oui, nous ne proposons, aujourd'hui, que des produits neufs en abonnement."),
		array(22,"Quelles sont les conditions générales d'abonnement?","Chaque offre étant spécifique, les conditions générales diffèrent d'une offre à une autre. Vous pouvez y accéder au moment de la souscription et devrez en prendre connaissance avant de les signer.
		De plus, votre contrat vous sera envoyé par mail et il sera toujours disponible sur votre compte client."),
		array(22,"Comment puisse-je connaître le montant de mes prélèvements","Le montant des prélèvements est indiqué dans votre contrat d'abonnement."),
		array(22,"Quelle est la durée d'engagement?","Cela dépend des offres et des produits. Nous adaptons la durée en fonction de l'usage qui est fait du matériel et du besoin ou non de changer de matériel."),
		array(22,"Quels services proposez-vous dans les offres?","Cela dépend de chaque offre ! Effectivement, chaque produit répond à un besoin et nécessite donc des services différents. Un service de pose est-elle indispensable pour un produit à poser sur un meuble? Tous les détails sont disponibles dans chaque offre."),
		array(22,"S'abonner est-elle une solution durable?","Oui. Lorsque nous récupérons un produit, nous le remettons en état si cela est possible et pouvons le reproposer en abonnement ou le revendre d'occasion. Ceci permet d'allonger sensiblement la durée de vie des produits et ainsi réduire sensiblement nos déchets. De même, nous travaillons en étroite collaboration avec nos fournisseurs pour optimiser la durée de vie de nos produits mis en abonnement."),
		array(25,"Comment peut-on souscrire à une offre d'abonnement?","Vous pouvez souscrire à une offre directement sur notre site internet ou dans certains de nos magasins."),
		array(25,"Comment se passe la souscription et l'installation?","La souscription à une offre d'abonnement Leroy Merlin se fait sur Internet. Cependant, certaines offres peuvent aussi être proposées en magasin. Pour bénéficier de nos offres, 1/ vous choisissez tout d'abord l'offre la plus adaptée, vous pouvez aussi ajouter des services et des produits optionnels complémentaires à l'offre d'abonnement. 2/ Vous renseignerez vos coordonnées. 3/ Vous renseignez vos coordonnées bancaires, vous signez votre contrat et le mandat de prélèvement et réglez par carte bancaire votre première mensualité.
		Si vous bénéficiez de l'installation du matériel, nous prendrons ensuite contact avec vous pour vérifier ensemble l'adéquation de l'offre choisie avec votre besoin et convenons d'un rendez-vous pour la pose. 4/ Vous profitez de votre solution"),
		array(25,"Les offres sont-elles disponibles en magasin?","Certaines offres sont effectivement proposées dans quelques magasins."),
		array(25,"Où peut on souscrire les offres?","La plupart de nos offres sont accessibles sur notre site internet. Mais, il peut y avoir aussi des offres locales adaptées à la région proposées en magasin."),
		array(25,"Les offres sont elles accessibles partout?","Non, par défaut, nos offres d'abonnement sont disponibles en France métropolitaine exclusivement. Mais, certaines ne le sont que dans certains départements. Souhaitant proposer un service d'excellence, nous étoffons régulièrement notre réseau de partenaires afin de proposer nos offres le plus largement possible."),
		array(25,"Est-ce que je peux souscrire par téléphone ou mail?","Non, la souscription à nos offres se fait sur notre site internet ou, pour certaines, en magasin."),
		array(25,"Comment être sûr que l'offre choisie correspond à mon besoin?","Pas d'inquiétude, un conseiller vous appellera avant l'envoi des produits afin de bien valider la correspondance de l'offre avec votre besoin."),
		array(25,"Quelles informations demandez vous pour bénéficier d'une offre d'abonnement?","Afin de bénéficier d'une offre d'abonnement Leroy Merlin, nous vous demandons vos coordonnées pour l'installation et/ou la livraison du matériel, votre RIB pour les prélèvements mensuels et un paiement par carte bleue de la première mensualité et … c'est tout !"),
		array(25,"Est-ce que vous pouvez refuser mon dossier?","Oui, nous nous gardons la possibilité de refuser votre dossier si l'offre de répond pas à votre besoin et ne pouvons vous garantir la qualité du service ou s'il y a un problème avec votre dossier ou en cas de présence d'un litige."),
		array(25,"Pendant ma souscription, j'ai un message d'erreur, comment faire?","Si vous avez rencontré une erreur pendant la souscription à une offre, vous pouvez nous laisser un message via le formulaire de contacts. Nous vous contacterons dans les plus brefs délais."),
		array(25,"Le stockage de mes données bancaires est-il vraiment sécurisé?","Nous ne stockons jamais vos données bancaires. Elles sont stockées chez notre partenaire bancaire qui dispose des accréditations nécessaires."),
		array(26,"Comment puisse-je suivre ma commande?","Nous vous envoyons régulièrement des mails d'informations mais vous pouvez aussi suivre l'avancement de votre dossier sur votre compte client."),
		array(26,"Comment faire pour décaler le rendez-vous de pose?","Si vous ne pouvez finalement pas être présent aux date et heure programmées, vous pouvez nous joindre depuis votre compte client et demander un nouveau rendez-vous."),
		array(26,"Comment faire pour changer l'adresse d'installation ou de livraison?","Vous pouvez me faire directement sur votre espace client mais nous vous recommandons aussi de nous envoyer un message en précisant le changement d'adresse si la livraison n'a pas encore eu lieu."),
		array(27,"Comment puisse-je me rétracter?","Il est bien entendu possible de vous rétracter. Pour cela, accédez à votre compte client et suivez les instructions. Vous disposez d'un délai de 14 jours ouvrés pour vous rétracter si vous avez souscrit sur Internet et 7 jours si vous avez souscrit en magasin. Cependant des frais pourront vous être facturés pour la désinstallation du matériel et son transport."),
		array(27,"Comment puisse-je créer mon compte Leroy Merlin Abonnement?","Votre compte Leroy Merlin Abonnement sera automatiquement créé lorsque vous souscrirez à une de nos offres. Vous recevrez immédiatement un mail contenant les éléments vous permettant de vous connecter."),
		array(27,"Où puis-je trouver mon identifiant et mot de passe ?","L'identifiant de votre compte est votre adresse mail et c'est vous qui choisissez votre mot de passe. En cas d'oubli, vous pourrez toujours le réinitialiser directement depuis le site client dédié."),
		array(27,"J'ai une question sur mon abonnement. Comment faire?","Tout d'abord, regarder dans notre FAQ que nous complétons régulièrement. Si vous ne trouvez pas la réponse à votre question, vous pouvez nous poser votre question à partir de la rubrique Contactez-nous accessible depuis votre espace Client."),
		array(27,"Comment joindre un conseiller en cas de difficulté?","Tout dépend du problème que vous rencontrez. Les différents contacts sont indiqués dans votre contrat d'abonnement. Et, vous pouvez toujours nous contacter à partir de votre espace Clients."),
		array(27,"Comment faire pour modifier mes données personnelles (coordonnées postales, bancaires, code personnel, …)?","Il vous suffit d'accéder à votre compte personnel, de choisir la bonne rubrique et de suivre les instructions."),
		array(27,"Vous me demandez des informations personnelles. A quoi vont-elles vous servir?","Vos données personnelles sont traitées avec prudence et confidentialité. Nous nous engageons à ne pas les diffuser à des tiers sans votre accord sauf, bien entendu à nos partenaires qui permettent l'exécution des services inclus dans votre offre d'abonnement. Conformément à la Loi Informatique et Liberté du 06/01/1978, vous disposez d’un droit d’accès, de rectification et de suppression des données vous concernant et d’opposition à leur traitement. Si vous souhaitez l’exercer, vous pouvez écrire à Leroy Merlin Abonnement - Rue Chanzy - Lezennes - 59 712 LILLE CEDEX 9, en indiquant votre nom, prénom, adresse et email. Vous pouvez consulter notre politique de données personnelles sur notre site internet."),
		array(27,"Est-ce que je peux ajouter ou supprimer une option pendant mon contrat?","Oui, certaines options peuvent être ajoutées ou enlevées pendant la vie de votre contrat. Pour cela, vous pouvez accéder à votre espace client et effectuer ces modifications."),
		array(27,"Que se passe-t-il si je dois déménager?","Si vous déménagez, il y a quatre possibilités : 1/ Vous pouvez résilier votre contrat sans frais si la période d'engagement est passée, ou avec frais sinon , 2/ Si vous souhaitez garder le matériel pour votre nouveau logement, et si cela est possible (prévu dans l'offre et zone couverte par nos services), nous organisons la désinstallation et la réinstallation avec éventuellement des frais associés, 3/ le nouvel occupant peut continuer le contrat avec les mêmes garanties, 4/ Nous pouvons étudier la vente du matériel au nouvel occupant."),
		array(27,"Comment prolonger mon contrat?","Pour prolonger votre contrat, vous n'avez rien à faire "," il est à tacite reconduction. Mais, bien entendu, vous pouvez l'arrêter à tout moment après la période d'engagement avec un délai de prévenance prévu dans le contrat."),
		array(27,"A quelle date suis-je prélevé?","Nous prélevons les mensualités le 5 du mois. Cependant, vous pouvez demander un changement de date de prélèvement sur simple demande en vous connectant sur votre compte client. Le mois suivant, nous recalculons la mensualité en tenant compte du changement."),
		array(27,"Comment peut-on résilier l'abonnement?","Vous pouvez résilier votre contrat à tout moment dès la période d'engagement passée. Avant cela, vous pouvez le faire mais devrez payer les mensualités restant à courir. Pour le faire, conntectez vous à votre espace client et suivez les instructions."),
		array(27,"Que se passe-t-il si mon produit en abonnement est cassé ou volé?","Les produits que nous proposons n'ont pas vocation à bouger sensiblement de place, le risque de casse ou de vol est donc extrêmement faible. Cependant, votre signature du bon de livraison équivaut au transfert de responsabilité. Ainsi, si vous cassez un produit ou vous le faites voler, votre responsabilité sera mise en cause et vous serez redevable à Leroy Merlin Abonnement de la valeur des réparations en cas de casse, ou de la valeur du produit (prenant en compte son ancienneté) en cas de vol. C'est pourquoi, nous vous recommandons fortement de bien vérifier que votre assurance vous couvre contre ces situations, ce qui est majoritairement le cas au travers de votre responsabilité civile ou de votre assurance habitation. Dans le cas d’une casse ou d’un vol, nous vous invitons à vous rapprocher de votre compagnie d'assurance afin d’ouvrir un dossier de sinistre et de faire en sorte que les frais de réparation soient pris en charge par celle-ci. Si vous n’êtes pas couvert pour ce type de sinistre, vous serez contraint de procéder à vos frais, à la réparation ou au remplacement du produit."),
		array(27,"Que se passe-t-il si le produit pris en abonnement n'est pas réparable?","Si le produit n'est pas réparable pendant la période d'engagement, nous le remplacerons gratuitement. Une fois la période d'engagement passée, nous déterminons la meilleure solution : soit nous le remplaçons par le même modèle soit le contrat s'arrête et nous vous proposons un nouveau matériel plus récent avec un nouveau contrat."),
		array(27,"Que se passe-t-il si une de mes mensualités est impayée?","Il peut y avoir différentes origines à un impayé. S'il s'agit d'un problème technique, nous mettrons tout en œuvre pour corriger l'anomalie et renouveler le prélèvement impayé. S'il s'agit d'un impayé pour solde insuffisant, nous vous contacterons rapidement pour régulariser la situation et trouver une solution ensemble.")
	);

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
	$data = array("id_parent"=>$value[0],
				  "titre"=>$value[1],
				  "id_site_menu"=>5,
				  "position"=>$position,
				  "visible"=>"oui"
				);
	
	$id_titre = ATF::site_article()->i($data);

	ATF::site_article_contenu()->i(array("id_site_article"=>$id_titre,"texte"=>$value[2]));
}



