<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$log_file = "bdomplus/controle_affaire_magasin_facture".date("Ymd");


log::logger("============================================================", $log_file);
log::logger("======  Début du script par le controle des factures  ======", $log_file);
log::logger("============================================================", $log_file);

try{
	ATF::db()->begin_transaction();
	echo "-----------------------------------------------------------------\n";
	echo "Retrouve les logs dans /log/bdomplus/controle_affaire_magasin_facture suivi de la date\n";
	echo "Update des status de facture magasin suite à l'import du fichier des factures magasin\n";
	echo "-----------------------------------------------------------------\n";

	ATF::facture_magasin()->check_statut_facture($log_file);

	echo "-----------------------------------------------------------------\n";
	echo "Fin du script des status des factures magasins\n";
	echo "-----------------------------------------------------------------\n";



	log::logger(" ", $log_file);
	log::logger(" ", $log_file);
	log::logger("============================================================", $log_file);
	log::logger("====== Controle des factures terminé, on passe aux affaires magasin ===========", $log_file);



	echo "-----------------------------------------------------------------\n";
	echo "Mise à jour des affaires par rapport au factures magasins recues\n";
	echo "-----------------------------------------------------------------\n";

	ATF::souscription()->check_affaires_magasin();

	echo "-----------------------------------------------------------------\n";
	echo "Fin du script\n";
	echo "-----------------------------------------------------------------\n";

	ATF::db()->commit_transaction();
} catch (errorATF $e) {
	ATF::db()->rollback_transaction();
	print_r($e);
}
log::logger("================================== Fin du script  =====================", $log_file);

?>
