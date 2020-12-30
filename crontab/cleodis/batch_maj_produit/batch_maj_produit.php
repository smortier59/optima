<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cleodis";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);

$return = ATF::import()->maj_infos_produit(array(), array(), "./maj_produit.csv");
log::logger($return , "mfleurquin");


?>