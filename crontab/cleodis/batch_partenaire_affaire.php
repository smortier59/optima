<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT *
	FROM `societe`
	WHERE `id_apporteur` IS NOT NULL";
$societes = ATF::db()->sql2array($q);




foreach ($societes as $key => $value) {

	$q2= "SELECT *
		FROM `affaire`
		WHERE `id_societe` = ".$value['id_societe']."
		AND affaire.id_partenaire IS NULL";
	$affaires = ATF::db()->sql2array($q2);

	if($affaires){
		foreach ($affaires as $kaff => $vaff) {
			ATF::affaire()->u(array("id_affaire"=> $vaff["id_affaire"], "id_partenaire"=>$value["id_apporteur"]));
		}
	}

	echo '.';
}