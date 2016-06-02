<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



echo '	 Demarage du script .......
	 ';

echo 'Demarage de l insertion des accompagnateurs  ......
	 ';
$query ="SELECT  `COL 1` 
		FROM  `TABLE 145`
		WHERE `COL 1` <> 'REGION'
		GROUP BY  `COL 1`";
		 //LIMIT 1, 200";
$accompagnateur = ATF::db()->sql2array($query);

//On crée les accompagnateurs
foreach ($accompagnateur as $key => $value) {	
	if($value["COL 1"]){		
		ATF::accompagnateur()->insert(array("accompagnateur" => $value["COL 1"], "portail_associe" => "Norauto"));		
	}
}

echo 'Insertion des accompagnateurs terminée .....
	 ';

$query ="SELECT  * 
		 FROM  `TABLE 145`";
		 //LIMIT 1, 200";
$agences = ATF::db()->sql2array($query);
unset($agences[0]);

echo 'Insertion des société et de leur(s) contact(s) !
	 ';
foreach ($agences as $key => $value) {
	if($value["COL 1"] && $value["COL 1"] !== "Region"){
		//log::logger($value , "mfleurquin");
		ATF::accompagnateur()->q->reset()->where("accompagnateur", $value["COL 1"]);	
		$id_accompagnateur = ATF::accompagnateur()->select_row();
		
		
		//Check existance socete
		
		ATF::societe()->q->reset()->where("societe", str_replace("'", " ",$value["COL 9"]));
		if(ATF::societe()->select_all()){
			$value["COL 9"] = $value["COL 9"]." 2";
		}	
		if($value["COL 3"] == "Filiale NORAUTO FRANCHISE"){
			$value["COL 3"] = "FRANCHISE NORAUTO F";
		}
		
		$agence = array(
		    "id_owner" => 35,		
			"siret" => $value["COL 10"],
			"siren" => substr($value["COL 10"], 0 , 9),
			"societe" => str_replace("'", " ",$value["COL 9"]),
			"nom_commercial" => str_replace("'", " ", $value["COL 3"]),
			"adresse" => $value["COL 13"],
			"adresse_2" => $value["COL 14"],
			"cp" => $value["COL 15"].$value["COL 16"],
			"ville" => $value["COL 17"],
			"reference_tva" => $value["COL 4"],
			"latitude" => $value["COL 11"],
			"longitude" => $value["COL 12"],
			"code_client" => "F00".$value["COL 6"],
			"id_accompagnateur" => $id_accompagnateur["id_accompagnateur"],
			"divers_3" => "Norauto"
		);
		
		ATF::societe()->i($agence);
		
		ATF::societe()->q->reset()->where("societe", str_replace("'", " ",$value["COL 9"]));
		$id_soc =  ATF::societe()->select_row();
		$id_soc = $id_soc["id_societe"];
		
		//On insere le/les contacts
		if($value["COL 18"] == "Madame"){
			$value["COL 18"] = "Mme";
		}else{
			$value["COL 18"] = "M";
		}
		if($value["COL 19"] === "Thierry Allessandra et Lionel Challis" || $value["COL 20"] === "Thierry Allessandra et Lionel Challis"){
			ATF::contact()->i(array("civilite" =>$value["COL 18"],
								"nom" => "Allessandra",
								"prenom" => "Thierry",
								"id_societe" => $id_soc));
			ATF::contact()->i(array("civilite" =>$value["COL 18"],
								"nom" => "Challis",
								"prenom" => "Lionel",
								"id_societe" => $id_soc));					
		}else{
			ATF::contact()->i(array("civilite" =>$value["COL 18"],
								"nom" => $value["COL 20"],
								"prenom" => $value["COL 19"],
								"id_societe" => $id_soc));
		}
	}
}

echo 'Batch terminé !!! Il faut désormais supprimer la TABLE 145 !
	 ';




?>