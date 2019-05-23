<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

echo "-----------------------------------------------------------------\n";
echo "Mise à jour des affaires par rapport au factures magasins recues\n";
echo "-----------------------------------------------------------------\n";

ATF::souscription()->check_affaires_magasin();

echo "-----------------------------------------------------------------\n";
echo "Fin du script\n";
echo "-----------------------------------------------------------------\n";


?>
