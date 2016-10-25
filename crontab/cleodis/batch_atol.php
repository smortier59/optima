<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT * 
	FROM `societe` 
	WHERE `date` LIKE '%2016-10-25 09:08%%'";
$data = ATF::db()->sql2array($q);	

foreach ($data as $key => $value) {
	ATF::contact()->q->reset()->where("id_societe",$value["id_societe"]);
	$res = ATF::contact()->select_row();

	if($res){
		ATF::societe()->u(array("id_societe"=>$value["id_societe"],
								"id_contact_facturation"=>$res["id_contact"],
								"id_contact_signataire"=>$res["id_contact"]
								)
						);
	}
}