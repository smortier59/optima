<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$q = "SELECT id_societe, `particulier_nom`, `particulier_prenom`, `tel`, `particulier_portable`
FROM `societe`
WHERE id_famille = 9 AND (tel != particulier_portable OR (tel IS NULL AND particulier_portable IS NOT NULL))";


$data = ATF::db()->sql2array($q);


foreach ($data as $key => $value) {
	ATF::societe()->u(array("id_societe"=> $value["id_societe"], "tel"=> $value["particulier_portable"]));
	echo "Societe ".$value["id_societe"]." ".$value["particulier_nom"]." ".$value["particulier_prenom"]." Tel pro (Avant / Apres) ".$value["tel"]." / ".$value["particulier_portable"]."\n";
}