<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$affaire = "1471";
$files = array("contrat" => array( "function" => "mandatSellAndSign",  "value" => 1471));
$contact = array(
    "id_contact" => 1062,
    "email" => "mfleurquin@absystech.fr",
    "email_perso" => "mfleurquin@absystech.fr"
);


ATF::souscription()->sendContrat($affaire, $files, $contact);


?>