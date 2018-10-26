<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

/*
$q = "SELECT *
	  FROM  `TABLE 145`";

$rums = ATF::db()->sql2array($q);

foreach ($rums as $key => $value) {
	if($key > 0){
		ATF::societe()->q->reset()->where("code_client" , $value["COL 1"]);
		$societe = ATF::societe()->select_row();

		if($societe){
			ATF::societe()->u(array("id_societe" => $societe["id_societe"] , "rum" => $value["COL 3"]));
		}
	}
}


ATF::societe()->q->reset()->whereIsNotNull("RUM");
$res = ATF::societe()->select_all();

foreach ($res as $key => $value) {
	$rum = $value["rum"];
	ATF::affaire()->q->reset()->where("affaire.id_societe", $value["id_societe"]);
	$affaires = ATF::affaire()->select_all();
	foreach ($affaires as $k => $v) {
		ATF::affaire()->u(array("id_affaire" => $v["affaire.id_affaire"], "RUM" => $rum));
		echo "Mise à jour de l'affaire ".ATF::affaire()->select($v["affaire.id_affaire"], "ref")."(SOCIETE ".$value["societe"].") RUM : ".$rum."\n";
	}
}

$q = "ALTER TABLE `societe` DROP  `rum`";
ATF::db()->sql2array($q);



ATF::affaire()->q->reset()->addField("affaire.RIB", "RIB")
						  ->addField("affaire.RUM", "RUM")
						  ->addField("affaire.IBAN", "IBAN")
						  ->addField("affaire.BIC", "BIC")
						  ->whereIsNotNull("RUM")
						  ->whereIsNotNull("RIB");
$affairesRUM = ATF::affaire()->select_all();

foreach ($affairesRUM as $key => $value) {
	ATF::affaire()->q->reset()->where("RIB",$value["RIB"]);
	$affaires = ATF::affaire()->select_all();
	foreach ($affaires as $k => $v) {
		ATF::affaire()->u(array("id_affaire" => $v["affaire.id_affaire"],
								"RIB" => $value["RIB"],
								"IBAN" => $value["IBAN"],
								"BIC" => $value["BIC"],
								"RUM" => $value["RUM"]
								)
						);
		echo "Mise à jour de l'affaire ".ATF::affaire()->select($v["affaire.id_affaire"] , "ref")."\n";
	}
}
*/


$q= "SELECT *
	FROM `societe`
	WHERE RUM IS NULL";
$societe_sans_rum = ATF::db()->sql2array($q);

foreach ($societe_sans_rum as $key => $value) {
	ATF::affaire()->q->reset()->addField("RUM")
							  ->where("affaire.id_societe", $value["id_societe"])
							  ->whereIsNotNull("RUM")
							  ->addOrder("affaire.id_affaire", "DESC");
	$affaire = ATF::affaire()->select_row();

	if($affaire){
		ATF::societe()->u(array("id_societe"=> $value["id_societe"], "RUM"=>$affaire["RUM"]));
		echo $value["ref"]." - ".$value["societe"]." ---> RUM : ".$affaire["RUM"]."\n";
	}
}

?>