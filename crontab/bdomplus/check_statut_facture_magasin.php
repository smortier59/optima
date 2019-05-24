<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

echo "-----------------------------------------------------------------\n";
echo "Update des status de facture magasin suite à l'import du fichier des factures magasin\n";
echo "-----------------------------------------------------------------\n";

ATF::facture_magasin()->check_statut_facture();

echo "-----------------------------------------------------------------\n";
echo "Fin du script\n";
echo "-----------------------------------------------------------------\n";


?>
