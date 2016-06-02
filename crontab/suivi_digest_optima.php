<?
/** Routine permetant d'envoyer les mails de digest sur tous les codenames
* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
*/
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

$bases = ATF::db()->sql2array("SHOW DATABASES WHERE `Database` LIKE 'extranet_v3_%'");

// Commende de lancement du script d'envoi de digest
$cmd['sender'] = "/usr/bin/php suivi_digest.php";
foreach ($bases as $k => $i) {
	$strCmd = $cmd['sender']." ".substr($i['Database'],12);
	echo "\nExecution de la commande : ".$strCmd." ";
	if (`$strCmd`) {
		echo " mails digest de ".$i['Database']." envoyé\n";
	}
}

?>