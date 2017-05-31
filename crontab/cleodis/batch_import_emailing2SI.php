<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT `contact`.*
	FROM  `contact`, `societe`
	WHERE `societe`.id_societe = `contact`.id_societe
	AND contact.email IS NOT NULL
	AND id_campagne = 1";
$data = ATF::db()->sql2array($q);

ATF::db()->begin_transaction();

foreach ($data as $key => $value) {
	log::logger($value , "mfleurquin");
	try{
		ATF::emailing_contact()->i(array( "civilite"=> $value["civilite"],
										   "nom"=> $value["nom"],
										   "prenom"=> $value["prenom"],
										   "societe" => $value["societe"],
										   "email"=> $value["email"],
										   "id_emailing_source"=>1
										 ));
	}catch(errorATF $e){
		echo $e->getMessage()."\n";
	}

}
ATF::db()->commit_transaction();

?>