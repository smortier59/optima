<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$ref_mandate = "A001063000001";
$amount = 25;
$libelle = "Test prelevement";
$date = date(Y-m-d);


ATF::slimpay()->createDebit($affaire, $files, $contact);


?>