<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

ATF::$usr->set('id_user',16);
ATF::$usr->set('id_agence',1);

$i = "SELECT * FROM `TABLE 144`;";
$liste = ATF::db()->sql2array($i);

$i = 0;

foreach ($liste as $key => $value) {
	$soc = array("siret"=>$value["SIRET"]
				,"societe"=>$value["Raison sociale ou enseigne"]
				,"adresse"=>$value["DC_ADDRESS1"]
				,"adresse_2"=>$value["DC_ADDRESS2"]
				,"cp"=>$value["DC_POSTCODE"]
				,"ville"=>$value["DC_CITY"]
				,"nom_commercial"=>"SUBWAY"
				,"tel"=>$value["DC_PHONE"]
				,"id_owner"=>16
				);
	ATF::societe()->q->reset()->where("siret",$value["SIRET"]);
	if(ATF::societe()->select_all()){
		$res = ATF::societe()->select_all();		
		$soc["id_societe"] = $res[0]["id_societe"];
		ATF::societe()->u($soc);
		$id_societe = $res[0]["id_societe"];

	}else{
		$id_societe = ATF::societe()->insert(array("societe"=>$soc));
	}
	if($value["DC_NOM"]){
		$contact = array("civilite"=> $value["DC_CIVILITE"],
					 "nom"=> $value["DC_NOM"],
					 "prenom"=> $value["DC_PRENOM"],
					 "id_societe"=> $id_societe,
					 "fonction"=> $value["DC_LIBELLE_FONCTION"] ,
					);

		ATF::contact()->i($contact);
	}
	
	$data = ATF::societe()->getInfosFromCREDITSAFE(array("siret"=>$value["SIRET"]));			
	unset($data["adresse"],$data["adresse_2"],$data["cp"],$data["ville"],$data["nb_employe"]);

	$data["id_societe"] = $id_societe;
	if($data["cs_score"] == "Note non disponible") unset($data["cs_score"]);
	if($data["cs_avis_credit"] == "Limite de crédit non applicable") unset($data["cs_avis_credit"]);

	try{
		ATF::societe()->u($data);	
		echo ".";						
	}catch(error $e){
		echo "\n".$e->getMessage()."\n";
	}

}


$sqlD = "DROP TABLE `TABLE 144`";
ATF::db()->sql2array($sqlD);