<?
/**
* Vérifie que le report a bien été créé
*/
include(dirname(__FILE__)."/../global.inc.php");

$devs = "dev@absystech.fr";
$temoin = "/tmp/last_test_".$_SERVER["argv"][1]."-".$_SERVER["argv"][2];

if (filemtime($temoin) > time()-3600) {
	echo "\nReport modified.";
	echo "\n";
} elseif (!$_SERVER["argv"][2]) {
	echo "\nProbleme lors de l'execution des test. Un email est envoye a ".$devs;
	echo "\n";
	mail($devs,"Tests unitaires erreur fatale pour une raison inconnue (".ATF::$codename.") !",file_get_contents(dirname(__FILE__)."/../log/tu_".ATF::$codename.".log"),"From: Tests Optima <tu@absystech.fr>");
} else {
	echo "\nLe test ne s'est pas terminé correctement pour une raison inconnue.";
	echo "\n";
}
?>