<?
/** 
* Crontab gérant l'envoi des jobs d'emailing
* @author Quentin JANON <qjanon@absystech.fr>
* @date 03-11-2010
*/

define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");
if ($_SERVER["argv"][1]==="-force") {
	unset($_SERVER["argv"][1]);
	$force = true;
}

/* Check si un job est en cours d'envoi */
if (ATF::constante()->getValue("__LAST_SPEEDMAIL_SENDER__")!="" && !$_SERVER["argv"][2]) {
	if ($force) {
		ATF::constante()->setValue("__LAST_SPEEDMAIL_SENDER__","");
	} else {
		mail("debug@absystech.fr","Speedmail sender blocked...","Occurence du ".ATF::constante()->getValue("__LAST_SPEEDMAIL_SENDER__")." non finie...","From: Speedmail <debug@absystech.fr>");
		die("\nPrecedente occurence non terminee !".ATF::constante()->getValue("__LAST_SPEEDMAIL_SENDER__"));
	}
}


ATF::constante()->setValue("__LAST_SPEEDMAIL_SENDER__",date("Y-m-d H:i:s",time()));

/* DEBUT SECTION CRITIQUE */
$_GET["debug"]=1;
// Séléction de toute les BDD optima_*
$bases = ATF::db()->sql2array("SHOW DATABASES WHERE `Database` LIKE 'optima_%'");

// Commende de lancement du script d'envoi de mail
$cmd['sender'] = "/usr/bin/php ".__DIR__."/speedmail_sender.php";
$db_to_send = NULL;
foreach ($bases as $k => $i) {
	if ($tableExist = ATF::db()->sql2array("SHOW TABLES FROM `".$i['Database']."` LIKE 'emailing_%'")) {
		$db = explode("optima_",$i['Database']); $db=$db[1];
		$strCmd = $cmd['sender']." ".$db." toSent";
		echo "\nExecution de la commande : ".$strCmd." ";
	    $r = `$strCmd`;
		if (is_numeric($r)) {
			echo $r." mail(s) a envoyer sur ".$i['Database']."\n";
			$db_to_send[$db]=$r;
		} else {
		    echo $r;
		}
		    
	}
}
foreach ($db_to_send as $k => $i) {
	echo "\nEXECUTION : ".$cmd['sender']." ".$k." ".floor(50/count($db_to_send))."\n";
	system($cmd['sender']." ".$k." ".floor(50/count($db_to_send)));
}

/* FIN SECTION CRITIQUE */
echo "\nReset constante\n";
ATF::constante()->setValue("__LAST_SPEEDMAIL_SENDER__","");
?>