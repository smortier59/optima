<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cap";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$i = "SELECT * FROM `TABLE 196`;";
$liste = ATF::db()->sql2array($i);

$i = 0;

foreach ($liste as $key => $value) {
	$i++;
	echo $i."/".count($liste)." ";	


	ATF::societe()->q->reset()->where("societe","%".str_replace("'", "\'", $value["COL 1"]),"OR",false,"LIKE")
							  ->where("societe",str_replace("'", "\'", $value["COL 1"]),"OR",false,"=");
	$soc = ATF::societe()->select_row();



	if($soc){
		$societe = array("id_societe"=>$soc["id_societe"],
					 "siret"=>$value["COL 21"],
					 "nom_commercial"=>$value["COL 1"],
					 "tel"=>$value["COL 42"],
					 "fax"=>$value["COL 43"]
					);

	
		ATF::societe()->u($societe);

		if($value["COL 46"] && $value["COL 47"]){
			ATF::contact()->q->reset()->where("id_societe",$soc["id_societe"])
									  ->where("contact.nom",str_replace("'", "\'",$value["COL 47"]),"AND","contacts")
									  ->where("contact.prenom",str_replace("'", "\'",$value["COL 46"]),"AND","contacts");
			
			if(!ATF::contact()->select_row()){
				if($value["tel"]) $value["tel"] = "0".$value["tel"];
				if($value["Portable"]) $value["Portable"] = "0".$value["Portable"];
					$contact = array("id_societe"=>$soc["id_societe"],
										"nom"=> $value["COL 47"],
										"prenom"=> $value["COL 46"],
										"fonction"=> $value["COL 49"],
										"tel"=> $value["COL 42"]
									);
					ATF::contact()->i($contact);
			}
		}

		if($value["COL 21"]){
			$data = ATF::societe()->getInfosFromCREDITSAFE(array("siret"=>$value["COL 21"]));			
			unset($data["adresse"],$data["adresse_2"],$data["cp"],$data["ville"],$data["nb_employe"]);

			$data["id_societe"] = $soc["id_societe"];
			if($data["cs_score"] == "Note non disponible") unset($data["cs_score"]);
			if($data["cs_avis_credit"] == "Limite de crédit non applicable") unset($data["cs_avis_credit"]);

			try{
				ATF::societe()->u($data);	
				echo "\n";						
			}catch(errorATF $e){
				echo $e->getMessage()."\n";
			}
			
		}
	}else{
		ATF::societe()->q->setToString();
		log::logger(ATF::societe()->select_row() , "mfleurquin");

		echo $value["COL 1"]." introuvable !\n";
	}
	
}


$sqlD = "DROP TABLE `TABLE 196`";
ATF::db()->sql2array($sqlD);


