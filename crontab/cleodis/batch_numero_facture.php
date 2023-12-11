<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT DISTINCT(date)
	FROM `facture`
	WHERE numero IS NULL";
$data = ATF::db()->sql2array($q);

foreach ($data as $v) {
    ATF::facture()->setNumero($v["date"]);
}