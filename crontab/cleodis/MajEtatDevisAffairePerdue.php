<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

ATF::affaire()->q->reset()->where("affaire.etat","devis");
$contrat = ATF::affaire()->sa();

foreach ($contrat as $key => $value) {
	ATF::devis()->q->reset()->where("devis.id_affaire", $value["id_affaire"]);
	$res = ATF::devis()->sa();
	$esp = false;
	foreach ($res as $k => $v) {
		if($v["etat"] != "perdu"){
			$esp = true;
		}
	}
		
	if($esp == false){
echo $value["id_affaire"]." perdue !\n";
		ATF::affaire()->u(array("id_affaire" => $value["id_affaire"] , "etat" => "perdue"));
	}		
}


?>
