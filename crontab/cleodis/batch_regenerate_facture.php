<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodisbe";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT *
	FROM `facture`
	WHERE `date` LIKE '2016-%'";
$data = ATF::db()->sql2array($q);



foreach ($data as $key => $value) {
	ATF::facture_cleodisbe()->generatePDF(array("id"=>$value["id_facture"]));
}