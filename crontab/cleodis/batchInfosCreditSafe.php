<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


ATF::societe()->q->reset()->where("date", "2016-01-25%", "AND", null, "LIKE" )
						  ->whereIsNotNull("siret");
$societes = ATF::societe()->select_all();

foreach ($societes as $key => $value) {
	if(strlen(str_replace(" ", "", $value["siret"])) == 14){
		$data = ATF::societe()->getInfosFromCREDITSAFE(array("siret"=>$value["siret"]));
		
		foreach ($data as $k => $v) {
			$value[$k] = $v;
		}
		if($value["cs_score"] == "Note non disponible") unset($value["cs_score"]);
		if($value["cs_avis_credit"] == "Limite de crédit non applicable") unset($value["cs_avis_credit"]);
		unset($value["nb_employe"]);
		
		try {
			ATF::societe()->u($value);
		} catch (Error $e) {
			echo $value["societe"]." : ".$e->getMessage()."\n";	
		}
	}else{
		echo "La société ".$value["societe"]." n'a pas de SIRET correct !\n";		
	}


}
