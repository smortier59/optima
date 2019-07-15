<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



try{
	echo "-----------------------------------------------------------------\n";
	echo "Retrouve les logs dans /log/controle_affaire_magasin_facture\n";
	echo "Update des status de facture magasin suite à l'import du fichier des factures magasin\n";
	echo "-----------------------------------------------------------------\n";

	ATF::facture_magasin()->check_statut_facture();

	echo "-----------------------------------------------------------------\n";
	echo "Fin du script des status des factures magasins\n";
	echo "-----------------------------------------------------------------\n";


	echo "-----------------------------------------------------------------\n";
	echo "Mise à jour des affaires par rapport au factures magasins recues\n";
	echo "-----------------------------------------------------------------\n";

	if($_SERVER["argv"][2]) $day = $_SERVER["argv"][2];
	else $day = 1;

	ATF::souscription()->check_affaires_magasin($day);

	echo "-----------------------------------------------------------------\n";
	echo "Fin du script\n";
	echo "-----------------------------------------------------------------\n";
} catch (errorATF $e) {
	print_r($e);
}

?>
