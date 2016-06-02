<?

/** Script qui permet de faire le ménage dans la table emailing contact pour repartir sur de bonne base
* @author Quentin JANON <qjanon@absystech.fr>
*/
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

// Pour voir les doublons de mails
// SELECT COUNT(*) AS t,`email`,`prenom`,`nom` FROM `emailing_contact` GROUP BY `email` ORDER BY t DESC

// Suppression des email NULL
// DELETE FROM emailing_contact WHERE email IS NULL
// SUpprimer les email contenant des apostrophes
// DELETE FROM emailing_contact WHERE email LIKE "%'%"

/* Exécuter ca pour nettoyer les doublons d'emails, quand le script n'affiche plus rien dans le shell on peut couper. */
//ATF::emailing_contact()->q->reset()
//									->addField("COUNT(*)","ct")
//									->addField("email")
//									->addGroup("email")
//									->addOrder("ct","DESC");
//foreach (ATF::emailing_contact()->select_all() as $k=>$i) {
//	unset($save);
//	ATF::emailing_contact()->q->reset()->addCondition("email",$i['email']);
//	$data = ATF::emailing_contact()->select_all();
//	$nb = count($data);
//	if ($nb==1) continue;
//	foreach ($data as $c=>$contact) {
//		if (!$c) {
//			$save = $contact;
//			continue;
//		} else {
//			$save['sollicitation'] += $contact['sollicitation'];
//			$save['tracking'] += $contact['tracking'];
//			$save['erreur'] += $contact['erreur'];
//			ATF::emailing_contact()->delete($contact['id_emailing_contact']);
//		}
//	}
//	ATF::emailing_contact()->update($save);
//	echo "=====> ".$nb." x ".$save['email']."(".$save['id_emailing_contact'].") OK \n";
//}

/* Exécuter ca pour nettoyer les emails foireux, ne pas le lancer a la sutie du premier foreach car ca risque de prendre enormément de temps 
foreach (ATF::emailing_contact()->select_all() as $k=>$i) {
	if (!util::isEmail($i['email'])) {
		echo "=====> ".$i['email']." supprimé \n";
		ATF::emailing_contact()->delete($i['id_emailing_contact']);
	}
}*/

?>