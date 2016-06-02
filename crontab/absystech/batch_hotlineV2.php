<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

ATF::hotline_interaction()->q->reset()->setlimit();

foreach(ATF::hotline_interaction()->select_all() as $key=>$value){

	$data = array();
	$minutes = $credit_presta = 0;


	if($value["temps"] != "00:00:00"){		
		$minutes = strtotime("1970-01-01 ".$value['temps']." UTC") /60;		
		$data["credit_presta"] = $minutes/60;
	}


	if($value["temps_passe"] != "00:00:00"){
		$data["duree_presta"] = $value["temps_passe"];
		$minutes = strtotime("1970-01-01 ".$value['temps']." UTC") /60;		
		$credit_presta = $minutes/60;
		if($data["credit_presta"] < $credit_presta) $data["credit_presta"] = $credit_presta;
	}

	if(!empty($data)){
		if(ATF::hotline()->select($value['id_hotline'], "facturation_ticket") == "oui" && ATF::hotline()->select($value['id_hotline'], "charge") == "intervention"){
		}else{
			$data["credit_presta"] = 0;
		}

		$data["id_hotline_interaction"] = $value["id_hotline_interaction"];
		ATF::hotline_interaction()->u($data);
	}

	

}