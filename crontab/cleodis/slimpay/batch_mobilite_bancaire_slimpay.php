<?php
define("__BYPASS__",true);
// $_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

$BmnPath = realpath("../../../bmn");

ATF::slimpay()->updateAllBankMobilityEntites($BmnPath);
?>