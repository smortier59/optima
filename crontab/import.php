<?
define("__BYPASS__",true);
if (!$_SERVER['argv'][1]) {
	$_SERVER['argv'][1]="absystech";
}	
include(dirname(__FILE__)."/../global.inc.php");

if (ATF::$codename == "absystech") {
	$cmd = 'ps -efa | egrep -e "[0-9] php '.__FILE__.'"';
	$result = explode("\n",trim(`$cmd`));
	if (count($result)>1) {
		echo "Programme déjà en cours d'exécution  !";
		exit(-1);
	}
}

// Lancement des imports

echo "\n...vérification des imports pour ".$_SERVER["argv"][1]."...";
try {
	$retour=ATF::importer()->importMassif();
} catch (error $e) {
	echo $e->getMessage();

	$retour = "Une erreur s'est déclenchée";
}
if($retour==2){
	echo "\naucun import en cours pour ".ATF::$codename."\n";
}elseif($retour===true){
	echo "\nimport reussi pour ".ATF::$codename."\n";
}else{
	echo "\nimport echoue pour ".ATF::$codename."\n";
	print_r($retour);
}
	
// Si le codename n'est pas absystech, alors c'est qu'on doit éxécuter uniquement les imports des autres DB
// Si on est absystech, alors on fait notre import, puis on rappel ce même script en lui passant le codename	

if (!$_SERVER['argv'][1]) {
	$bases = ATF::db()->sql2array("SHOW DATABASES WHERE `Database` LIKE 'extranet_v3_%'");
	foreach ($bases as $k => $i) {
		if ($k == "extranet_v3_absystech") continue;
		if ($tableExist = ATF::db()->sql2array("SHOW TABLES FROM `".$i['Database']."` LIKE 'emailing_%'")) {
			$cmd = "php ".__FILE__." ".substr($i['Database'],12) ;
			echo "\n\n".date("d-m-Y H:i:s")." \nLancement de la commande : '".$cmd."'";
			passthru($cmd,$r);
		}
	}
}


?>