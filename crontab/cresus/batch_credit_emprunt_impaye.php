<?
define("__BYPASS__", true);
$_SERVER["argv "][1] ="cresus";
include(dirname(__FILE__)."/../../global.inc.php");

//Ressources_ charges TABLE 154 
echo "batch_Valenciennes
";
batch_Va();

echo "batch_Roubaix
";
batch_Roubaix();





function batch_Va(){
	$query ='SELECT  `COL 2` as id_res , `COL 3` as id_adherent
			 FROM  `TABLE 154`';
	$result = ATF::db()->sql2array($query);

	foreach ($result as $key => $value) {
		if($key > 1){		
			$id_adherent = $value["id_adherent"];
			$id_ress = $value["id_res"];

			//On recupere le num_dossier
			$q ="SELECT id_adherent FROM `adherent` WHERE `num_dossier_old` = ".$id_adherent." AND `id_site_accueil` = 2";
			$adherent = ATF::db()->sql2array($q);		
			$adherent = $adherent[0]["id_adherent"];

			$q ="SELECT * FROM `TABLE 159` WHERE `COL 2` = ".$id_adherent;
			$ad = ATF::db()->sql2array($q);
		
			if($adherent && ATF::adherent()->select($adherent , "nom") == strtoupper(trim(rtrim($ad[0]["COL 4"])))){
				
				//ON RECUPERE l'id res_cha pour le lien avec adherent
				$id_adh = $ad[0]["COL 2"];
				$q ="SELECT * FROM `TABLE 154` WHERE `COL 3` = ".$id_adh;
				$id_res_cha = ATF::db()->sql2array($q);
				$id_res_cha = $id_res_cha[0]["COL 2"];

				//EMPRUNTS -- TABLE 151 (fichier CH_VAR.XLS)
				 $delete = "DELETE FROM emprunt WHERE id_adherent = ".$adherent;
				 ATF::db()->sql2array($delete); 

				 $query ="SELECT * FROM `TABLE 151` WHERE `COL 11` = ".$id_res_cha;
				 $emprunt = ATF::db()->sql2array($query);
				 if($emprunt){	 	
					 foreach ($emprunt as $k => $v) {
					 	$empruntInsert = array(
								"id_adherent"=> $adherent,
								"organisme" => $v["COL 3"],
								"adresse" => $v["COL 4"],
								"cp" => $v["COL 6"],
								"ville" => $v["COL 5"],
								"date_debut" => ReturnDate($v["COL 9"]),
								"date_fin" => ReturnDate($v["COL 8"]),
								"mensualite" => number_format($v["COL 7"], 2, ',', ''),
								"montant" => number_format($v["COL 10"], 2, ',', ''),
								"impaye" => number_format($v["COL 12"], 2, ',', '') 
						);
						if($v["COL 7"]!= "0,00" || $v["COL 10"]!= "0,00" || $v["COL 12"]!= "0,00"){
							ATF::emprunt()->insert($empruntInsert);
						}
					 }
				 }

				  //CREDITS - TABLE 152  (fichier CH_VAR1.XLS)
				 $delete = "DELETE FROM credit WHERE id_adherent = ".$adherent;
				 ATF::db()->sql2array($delete); 

				 $query ="SELECT * FROM `TABLE 152` WHERE `COL 11` = ".$id_res_cha;
				 $credit = ATF::db()->sql2array($query);
				 if($credit){		 	
					 foreach ($credit as $k => $v) {
					 	$creditInsert = array(
								"id_adherent"=> $adherent,
								"organisme" => $v["COL 3"],
								"adresse" => $v["COL 4"],
								"cp" => $v["COL 6"],
								"ville" => $v["COL 5"],
								"date_debut" => ReturnDate($v["COL 9"]),
								"date_fin" => ReturnDate($v["COL 8"]),
								"mensualite" => number_format($v["COL 7"], 2, ',', ''),
								"montant" => number_format($v["COL 10"], 2, ',', ''),
								"impaye" => number_format($v["COL 12"], 2, ',', '') 
						);
						if($v["COL 7"]!= "0,00" || $v["COL 10"]!= "0,00" || $v["COL 12"]!= "0,00"){
							ATF::credit()->insert($creditInsert);
						}
					 }
				 }

				 //IMPAYE - TABLE 153 (fichier CH_VAR2.XLS)
				 $delete = "DELETE FROM impaye WHERE id_adherent = ".$adherent;
				 ATF::db()->sql2array($delete);

				 $query ="SELECT * FROM `TABLE 153` WHERE  `COL 11` = ".$id_res_cha;
				 $impaye = ATF::db()->sql2array($query);
				 if($impaye){		 	
					 foreach ($impaye as $k => $v) {
					 	$impayeInsert = array(
								"id_adherent"=> $adherent,
								"organisme" => $v["COL 3"],
								"adresse" => $v["COL 4"],
								"cp" => $v["COL 6"],
								"ville" => $v["COL 5"],
								"date_debut" => ReturnDate($v["COL 9"]),
								"date_fin" => ReturnDate($v["COL 8"]),
								"mensualite" => number_format($v["COL 7"], 2, ',', ''),
								"montant" => number_format($v["COL 10"], 2, ',', ''),
								"impaye" => number_format($v["COL 12"], 2, ',', '') 
						);

						if($v["COL 7"]!= "0,00" || $v["COL 10"]!= "0,00" || $v["COL 12"]!= "0,00"){					
							ATF::impaye()->insert($impayeInsert);
						}
					 }
				 }
				
			}else{
				log::logger(ATF::adherent()->select($adherent , "nom")." != ".strtoupper(trim(rtrim($ad[0]["COL 4"]))) , "mfleurquin");
			}
		}
	}
}


function batch_Roubaix(){
	$query ='SELECT  `COL 2` as id_res , `COL 3` as id_adherent
			 FROM  `TABLE 158`';
	$result = ATF::db()->sql2array($query);

	foreach ($result as $key => $value) {
		if($key > 1){	

			$id_adherent = $value["id_adherent"];
			$id_ress = $value["id_res"];

			//On recupere le num_dossier
			$q ="SELECT id_adherent FROM `adherent` WHERE `num_dossier_old` = ".$id_adherent." AND `id_site_accueil` = 1";
			$adherent = ATF::db()->sql2array($q);		
			$adherent = $adherent[0]["id_adherent"];

			$q ="SELECT * FROM `TABLE 160` WHERE `COL 2` = ".$id_adherent;
			$ad = ATF::db()->sql2array($q);

			if($adherent && ATF::adherent()->select($adherent , "nom") == strtoupper(trim(rtrim($ad[0]["COL 4"])))){
				
				//ON RECUPERE l'id res_cha pour le lien avec adherent
				$id_adh = $ad[0]["COL 2"];
				$q ="SELECT * FROM `TABLE 158` WHERE `COL 3` = ".$id_adh;
				$id_res_cha = ATF::db()->sql2array($q);
				$id_res_cha = $id_res_cha[0]["COL 2"];

				//EMPRUNTS -- TABLE 155 (fichier CH_VAR.XLS)
				 $delete = "DELETE FROM emprunt WHERE id_adherent = ".$adherent;
				 ATF::db()->sql2array($delete); 

				 $query ="SELECT * FROM `TABLE 155` WHERE `COL 11` = ".$id_res_cha;
				 $emprunt = ATF::db()->sql2array($query);
				 if($emprunt){	 	
					 foreach ($emprunt as $k => $v) {
					 	$empruntInsert = array(
								"id_adherent"=> $adherent,
								"organisme" => $v["COL 3"],
								"adresse" => $v["COL 4"],
								"cp" => $v["COL 6"],
								"ville" => $v["COL 5"],
								"date_debut" => ReturnDate($v["COL 9"]),
								"date_fin" => ReturnDate($v["COL 8"]),
								"mensualite" => number_format($v["COL 7"], 2, ',', ''),
								"montant" => number_format($v["COL 10"], 2, ',', ''),
								"impaye" => number_format($v["COL 12"], 2, ',', '') 
						);
						if($v["COL 7"]!= "0,00" || $v["COL 10"]!= "0,00" || $v["COL 12"]!= "0,00"){
							ATF::emprunt()->insert($empruntInsert);
						}
					 }
				 }

				  //CREDITS - TABLE 156  (fichier CH_VAR1.XLS)
				 $delete = "DELETE FROM credit WHERE id_adherent = ".$adherent;
				 ATF::db()->sql2array($delete); 

				 $query ="SELECT * FROM `TABLE 156` WHERE `COL 11` = ".$id_res_cha;
				 $credit = ATF::db()->sql2array($query);
				 if($credit){		 	
					 foreach ($credit as $k => $v) {
					 	$creditInsert = array(
								"id_adherent"=> $adherent,
								"organisme" => $v["COL 3"],
								"adresse" => $v["COL 4"],
								"cp" => $v["COL 6"],
								"ville" => $v["COL 5"],
								"date_debut" => ReturnDate($v["COL 9"]),
								"date_fin" => ReturnDate($v["COL 8"]),
								"mensualite" => number_format($v["COL 7"], 2, ',', ''),
								"montant" => number_format($v["COL 10"], 2, ',', ''),
								"impaye" => number_format($v["COL 12"], 2, ',', '') 
						);
						if($v["COL 7"]!= "0,00" || $v["COL 10"]!= "0,00" || $v["COL 12"]!= "0,00"){
							ATF::credit()->insert($creditInsert);
						}
					 }
				 }

				 //IMPAYE - TABLE 157 (fichier CH_VAR2.XLS)
				 $delete = "DELETE FROM impaye WHERE id_adherent = ".$adherent;
				 ATF::db()->sql2array($delete);

				 $query ="SELECT * FROM `TABLE 157` WHERE  `COL 11` = ".$id_res_cha;
				 $impaye = ATF::db()->sql2array($query);
				 if($impaye){		 	
					 foreach ($impaye as $k => $v) {
					 	$impayeInsert = array(
								"id_adherent"=> $adherent,
								"organisme" => $v["COL 3"],
								"adresse" => $v["COL 4"],
								"cp" => $v["COL 6"],
								"ville" => $v["COL 5"],
								"date_debut" => ReturnDate($v["COL 9"]),
								"date_fin" => ReturnDate($v["COL 8"]),
								"mensualite" => number_format($v["COL 7"], 2, ',', ''),
								"montant" => number_format($v["COL 10"], 2, ',', ''),
								"impaye" => number_format($v["COL 12"], 2, ',', '') 
						);

						if($v["COL 7"]!= "0,00" || $v["COL 10"]!= "0,00" || $v["COL 12"]!= "0,00"){					
							ATF::impaye()->insert($impayeInsert);
						}
					 }
				 }
				
			}else{
				if(ATF::adherent()->select($adherent , "nom")){
					log::logger(ATF::adherent()->select($adherent , "nom")." != ".strtoupper(trim(rtrim($ad[0]["COL 4"]))) , "mfleurquin");	
				}
							
			}
		}
	}
}



function ReturnDate($infos){
	if($infos != ""){
		$res = explode("/",$infos);	
		return $res["2"]."-".$res["1"]."-".$res["0"];	
	}
	return NULL;		
}