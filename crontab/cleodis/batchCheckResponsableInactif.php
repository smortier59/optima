<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

ATF::user()->q->reset()->where("id_societe", 246)
					   ->where("etat", "inactif");
$usersInactifCleodis = ATF::user()->select_all();

foreach ($usersInactifCleodis as $key => $value) {
	ATF::societe()->q->reset()->where("id_owner", $value["id_user"]);
	$soc = ATF::societe()->select_all();
	
	log::logger("------------------------------------------------------------------------------------------------------------------", "BASENAME__FILE__");
	log::logger("User ".$value["login"]." Superieur -> : ".ATF::user()->select($value["id_superieur"], "login") , "BASENAME__FILE__");	
	if($value["id_superieur"]){
		$infos = array("id_owner" => $value["id_superieur"]);
	}else{
		//Si pas de responsable on met Jerome Loison
		$infos = array("id_owner" => 16);
	}		
	foreach ($soc as $k => $v) {		
		$infos["id_societe"] = $v["id_societe"];
		ATF::societe()->u($infos);
		log::logger("Societe : ".$v["societe"]." Ancien responsable User : ".$value["login"]." nouveau responsable -> : ".ATF::user()->select($value["id_superieur"], "login") , "BASENAME__FILE__");
	}
	log::logger("------------------------------------------------------------------------------------------------------------------", "BASENAME__FILE__");
}
			   

?>