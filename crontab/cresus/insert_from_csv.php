<?
define("__BYPASS__", true);
$_SERVER["argv "][1] ="cresus";
include(dirname(__FILE__)."/../../global.inc.php");


/************************************************************************
* AVANT TOUT :
* 			REMPLACER LES COLONNES DE CHIFFRES DANS LES FICHIERS XLS CAR ILS SONT ECRIT 1 000,0000 ET LORS DE L'INSERT 
* 			IL INSERT 1 !!!
*/


//delete();



$query ='SELECT  * , STR_TO_DATE(  `COL 113` ,  "%d/%m/%Y" ) AS DATE
		 FROM  `TABLE 160` 
		 
		 WHERE `COL 2` >1
		 AND `COL 113` != ""
		 ORDER BY  `date` ASC';
		 //LIMIT 1, 100';
$result = ATF::db()->sql2array($query);

echo "---------------------------------------------------------------------------------- 
";

echo "INSERTION DES ADHERENTS 
";
echo "---------------------------------------------------------------------------------- 
";
$i = 1;
foreach ($result as $key => $value) {
	if($value["COL 4"] && $value["COL 5"]){
		
		
		if(!$value["COL 113"]){
			$value["COL 113"] = date("d/m/Y");
		}

		$date = explode("-", ReturnDate($value["COL 113"]));
		
		$q ="SELECT MAX(num_dossier) as max FROM adherent where num_dossier LIKE '".$date[0]."%-".getLettre(getEnum($value["COL 124"], "pole_accueil"))."'";
		$res = ATF::db()->sql2array($q);
		

		
		
		if($res[0]["max"]){
			$num = explode("-", $res[0]["max"]);
			$num = $num[1]+1;				
			$num_dossier = $date[0].$date[1]."-".str_pad($num, 4, "0", STR_PAD_LEFT)."-".getLettre(getEnum($value["COL 124"], "pole_accueil"));
			
		}else{
			$num_dossier = $date[0].$date[1]."-0001-".getLettre(getEnum($value["COL 124"], "pole_accueil"));
		}


									
		$adherent = array( 				 
			"date_entree"=> ReturnDate($value["COL 113"]) , 				 
			"id_site_accueil"=> getEnum($value["COL 120"] , "site_accueil"),
			"id_pole_accueil"=> getEnum($value["COL 124"], "pole_accueil") , 
			"id_orientation"=> getEnum($value["COL 78"] , "orientation"), 
			"date_cloture"=> ReturnDate($value["COL 119"]) , 
			"archive"=> ReturnBool($value["COL 125"]) , 
			"num_dossier"=> $num_dossier , 
			"num_dossier_old"=> intval($value["COL 2"]), 
			"civilite"=> civilite($value["COL 3"]) , 
			"nom"=> strtoupper($value["COL 4"]) , 
			"prenom"=> ucfirst(strtolower($value["COL 5"])) , 
			"ville_naissance"=> $value["COL 9"] , 
			"nom_jeune_fille"=> strtoupper($value["COL 6"]) , 
			"date_naissance"=> ReturnDate($value["COL 8"]) , 
			"tranche_age"=> age($value["COL 13"])  , 
			"pays_naissance"=> $value["COL 10"] , 
			"sexe"=> sexe($value["COL 7"]) , 
			"nationalite"=> nationalite($value["COL 11"]) , 
			"nationalite2"=> $value["COL 12"] , 
			"personne_a_charge"=> $value["COL 38"] , 
			"fixe"=> $value["COL 33"] , 
			"mobile"=> $value["COL 34"] , 
			"situation_familiale"=> situation_fam($value["COL 36"]) , 
			"habitation"=> habitation($value["COL 37"]) , 
			"adresse_perso"=> $value["COL 28"] , 
			"adresse_perso_2"=> $value["COL 29"] , 
			"zone_geo"=> $value["COL 32"] , 
			"cp"=> $value["COL 30"] , 
			"id_zonegeo"=> getEnum($value["COL 31"], "zonegeo", $value["COL 30"] ) , 
			"surface_habitable"=> $value["COL 121"] , 
			"mail"=> $value["COL 104"] , 
			"securite_sociale"=> $value["COL 14"] , 
			"caf"=> $value["COL 15"] , 
			"cmu"=> ReturnBool($value["COL 123"]) , 
			"assurance"=> ReturnBool($value["COL 74"]) , 
			"nom_assurance"=> $value["COL 75"] , 
			"mutuelle"=> ReturnBool($value["COL 76"]) , 
			"nom_mutuelle"=> $value["COL 77"] , 
			"profession"=> $value["COL 16"] , 
			"csp"=> csp($value["COL 17"]) , 
			"demandeur_emploi"=> DemandeurEmploi($value["COL 18"]) , 
			"qualif_pro"=> $value["COL 105"] , 
			"niveau"=> niveau($value["COL 112"]) , 
			"employeur"=> $value["COL 20"] , 
			"tel_employeur"=> $value["COL 24"] , 
			"adresse_employeur"=> $value["COL 21"] , 
			"fax"=> $value["COL 25"] , 
			"cp_employeur"=> $value["COL 22"] , 
			"ville_employeur"=> $value["COL 23"] , 
			"ce"=> ReturnBool($value["COL 26"]) , 
			"adresse_ce"=> $value["COL 27"] , 
			"nom_conjoint"=> strtoupper($value["COL 59"]) , 
			"prenom_conjoint"=> ucfirst($value["COL 60"]) , 
			"nom_jf_conjoint"=> strtoupper($value["COL 61"]) , 
			"sexe_conjoint"=> sexe($value["COL 62"]) , 
			"date_naiss_conjoint"=> ReturnDate($value["COL 63"]) , 
			"ville_naissance_conjoint"=> $value["COL 64"] , 
			"pays_naissance_conjoint"=> $value["COL 65"] , 
			"nationalite_conjoint"=> $value["COL 66"] , 
			"secu_conjoint"=> $value["COL 67"] , 
			"profession_conjoint"=> $value["COL 111"] , 
			"employeur_conjoint"=> $value["COL 68"] , 
			"adresse_employeur_conjoint"=> $value["COL 69"] , 
			"tel_employeur_conjoint"=> $value["COL 70"] , 
			"fax_employeur_conjoint"=> $value["COL 71"] , 
			"ce_conjoint"=> ReturnBool($value["COL 72"]) , 
			"adresse_ce_conjoint"=> $value["COL 73"] , 
			"commentaire"=> $value["COL 114"] , 
			"id_padd"=> padd($value["COL 126"]) , 
			"id_impaye_energie"=> impaye($value["COL 127"]) , 
			"id_precarite_habitat"=> precarite($value["COL 128"]) , 
			"procedure_en_cours"=> ReturnBool($value["COL 115"]) , 
			"coupure_edf_eau"=> ReturnBool($value["COL 116"]) , 
			"saisie_cpt_banc"=> ReturnBool($value["COL 117"]) , 
			"abis_vente_huissier"=> ReturnBool($value["COL 118"]) , 
			"ficp"=> ReturnBool($value["COL 122"])
		);
		
		
			
			
		$id = ATF::adherent()->i($adherent);			
		echo "ADHERENT : ".$id."\n";
		 //ENFANTS 
		 for($i=39; $i <49; $i++){
		 	if($value["COL ".$i]){
		 		$j = $i+10;
		 		$enfant = array("id_adherent" => $id, "prenom"=> ucfirst($value["COL ".$i]), "date_naissance" => ReturnDate($value["COL ".$j]));
				ATF::adherent_enfant()->i($enfant);
		 	}
		 }
		 
		 //EMPRUNTS -- TABLE 155 (fichier CH_VAR.XLS)
		 $query ="SELECT * FROM `TABLE 155` WHERE `COL 3` = ".$value["COL 2"];
		 $emprunt = ATF::db()->sql2array($query);
		 if($emprunt){		 	
			 foreach ($emprunt as $k => $v) {
			 	$empruntInsert = array(
						"id_adherent"=> $id,
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
		 $query ="SELECT * FROM `TABLE 156` WHERE `COL 3` = ".$value["COL 2"];
		 $credit = ATF::db()->sql2array($query);
		 if($credit){		 	
			 foreach ($credit as $k => $v) {
			 	$creditInsert = array(
						"id_adherent"=> $id,
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
		 $query ="SELECT * FROM `TABLE 157` WHERE `COL 3` = ".$value["COL 2"];
		 $impaye = ATF::db()->sql2array($query);
		 if($impaye){		 	
			 foreach ($impaye as $k => $v) {
			 	$impayeInsert = array(
						"id_adherent"=> $id,
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
		
		
		 //RESSOURCES - CHARGES  -- TABLE 158 (RES_CHA.XLS)	 
		 $query ="SELECT * FROM `TABLE 158` WHERE `COL 3` = ".$value["COL 2"];
		 $res_charge = ATF::db()->sql2array($query);
		 		 
		 $ressources = array(
			  "id_adherent" =>$id,
			  "salaire" => charge_ressource($res_charge[0]["COL 4"]) ,
			  "pension" => charge_ressource($res_charge[0]["COL 6"]) ,
			  "indemnite"=> charge_ressource($res_charge[0]["COL 8"]) ,
			  "ijss" => charge_ressource($res_charge[0]["COL 10"]) ,
			  "assedic" => charge_ressource($res_charge[0]["COL 12"]) ,
			  "rmi" => charge_ressource($res_charge[0]["COL 14"]) ,
			  "primes" => charge_ressource($res_charge[0]["COL 16"]) ,
			  "retraite" => charge_ressource($res_charge[0]["COL 18"]) ,
			  "rsa" => charge_ressource($res_charge[0]["COL 20"]) ,
			  "autre" => charge_ressource($res_charge[0]["COL 22"]) ,
			  "alloc_fam" => charge_ressource($res_charge[0]["COL 24"]) ,
			  "alloc_log" => charge_ressource($res_charge[0]["COL 26"]) ,
			  "apl" => charge_ressource($res_charge[0]["COL 28"]) ,
			  "alloc_parent_iso" => charge_ressource($res_charge[0]["COL 30"]) ,
			  "alloc_adulte_handi" => charge_ressource($res_charge[0]["COL 32"]) ,
			  "pension_ali" => charge_ressource($res_charge[0]["COL 34"]) ,
			  "presta_comp" => charge_ressource($res_charge[0]["COL 36"]) ,
			  "autre_revenu" => charge_ressource($res_charge[0]["COL 38"]) ,
			  "salaire_conjoint" => charge_ressource($res_charge[0]["COL 5"]) ,
			  "pension_conjoint" => charge_ressource($res_charge[0]["COL 7"]) ,
			  "indemnite_conjoint" => charge_ressource($res_charge[0]["COL 9"]) ,
			  "ijss_conjoint" => charge_ressource($res_charge[0]["COL 11"]) ,
			  "assedic_conjoint" => charge_ressource($res_charge[0]["COL 13"]) ,
			  "rmi_conjoint" => charge_ressource($res_charge[0]["COL 15"]) ,
			  "primes_conjoint" => charge_ressource($res_charge[0]["COL 17"]) ,
			  "retraite_conjoint" => charge_ressource($res_charge[0]["COL 19"]) ,
			  "rsa_conjoint" => charge_ressource($res_charge[0]["COL 21"]) ,
			  "autre_conjoint" => charge_ressource($res_charge[0]["COL 23"]) ,
			  "alloc_fam_conjoint" => charge_ressource($res_charge[0]["COL 25"]) ,
			  "alloc_log_conjoint" => charge_ressource($res_charge[0]["COL 27"]) ,
			  "apl_conjoint" => charge_ressource($res_charge[0]["COL 29"]) ,
			  "alloc_parent_iso_conjoint" => charge_ressource($res_charge[0]["COL 31"]) ,
			  "alloc_adulte_handi_conjoint" => charge_ressource($res_charge[0]["COL 33"]) ,
			  "pension_ali_conjoint" => charge_ressource($res_charge[0]["COL 35"]) ,
			  "presta_comp_conjoint" => charge_ressource($res_charge[0]["COL 37"]) ,
			  "autre_revenu_conjoint" => charge_ressource($res_charge[0]["COL 39"]) ,
			  "tpn" => ReturnBool(charge_ressource($res_charge[0]["COL 161"])) ,
			  "fsl" => ReturnBool(charge_ressource($res_charge[0]["COL 162"])) , 
			  "autre_droit" => charge_ressource($res_charge[0]["COL 163"]) 
		 );	
		 ATF::adherent_ressource()->i($ressources);
		 
		 
		 $charges = array(
		 	 "id_adherent" => $id,
			  "impot" => charge_ressource($res_charge[0]["COL 44"]),  "impot_conjoint" => charge_ressource($res_charge[0]["COL 45"]),  "impot_charge" =>charge_ressource($res_charge[0]["COL "]),
			  "taxe_fonciere" =>charge_ressource($res_charge[0]["COL 47"]),  
			  "taxe_fonciere_conjoint" =>charge_ressource($res_charge[0]["COL 48"]), 
			  "taxe_fonciere_charge" =>charge_ressource($res_charge[0]["COL 49"]),
			  "taxe_habitation" =>charge_ressource($res_charge[0]["COL 50"]), 
			  "taxe_habitation_conjoint" =>charge_ressource($res_charge[0]["COL 51"]), 
			  "taxe_habitation_charge" =>charge_ressource($res_charge[0]["COL 52"]),
			  "redevance" =>charge_ressource($res_charge[0]["COL 156"]), 
			  "redevance_charge" =>charge_ressource($res_charge[0]["COL 157"]),
			  "indus" =>charge_ressource($res_charge[0]["COL 158"]), 
			  "indus_conjoint" =>charge_ressource($res_charge[0]["COL 159"]),  
			  "indus_charge" =>charge_ressource($res_charge[0]["COL 160"]),
			  "loyer" =>charge_ressource($res_charge[0]["COL 53"]), 
			  "loyer_conjoint" =>charge_ressource($res_charge[0]["COL 54"]), 
			  "loyer_charge" =>charge_ressource($res_charge[0]["COL 55"]),
			  "remb_pret_immo" =>charge_ressource($res_charge[0]["COL 56"]), 
			  "remb_pret_immo_conjoint" =>charge_ressource($res_charge[0]["COL 57"]), 
			  "remb_pret_immo_charge" =>charge_ressource($res_charge[0]["COL 58"]),
			  "electricite" =>charge_ressource($res_charge[0]["COL 59"]),  
			  "electricite_conjoint" =>charge_ressource($res_charge[0]["COL 60"]), 
			  "electricite_charge" =>charge_ressource($res_charge[0]["COL 61"]),
			  "gaz" =>charge_ressource($res_charge[0]["COL 62"]), 
			  "gaz_conjoint" =>charge_ressource($res_charge[0]["COL 63"]), 
			  "gaz_charge" =>charge_ressource($res_charge[0]["COL 64"]),
			  "autre_chauffage" =>charge_ressource($res_charge[0]["COL 65"]), 
			  "autre_chauffage_conjoint" =>charge_ressource($res_charge[0]["COL 66"]), 
			  "autre_chauffage_charge" =>charge_ressource($res_charge[0]["COL 67"]),
			  "eau" =>charge_ressource($res_charge[0]["COL 68"]),  
			  "eau_conjoint" =>charge_ressource($res_charge[0]["COL 69"]), "eau_charge" =>charge_ressource($res_charge[0]["COL 70"]),
			  "assu_logement" =>charge_ressource($res_charge[0]["COL 71"]), "assu_logement_conjoint" =>charge_ressource($res_charge[0]["COL 72"]), "assu_logement_charge" =>charge_ressource($res_charge[0]["COL 73"]),
			  "internet" =>charge_ressource($res_charge[0]["COL 74"]), "internet_conjoint" =>charge_ressource($res_charge[0]["COL 75"]),  "internet_charge" =>charge_ressource($res_charge[0]["COL 76"]),
			  "autre_habi" =>charge_ressource($res_charge[0]["COL 77"]), "autre_habi_conjoint" =>charge_ressource($res_charge[0]["COL 78"]), "autre_habi_charge" =>charge_ressource($res_charge[0]["COL 79"]),
			  "assu_auto" =>charge_ressource($res_charge[0]["COL 80"]), "assu_auto_conjoint" =>charge_ressource($res_charge[0]["COL 81"]),  "assu_auto_charge" =>charge_ressource($res_charge[0]["COL 82"]),
			  "entretien_carbu" =>charge_ressource($res_charge[0]["COL 83"]), "entretien_carbu_conjoint" =>charge_ressource($res_charge[0]["COL 84"]),  "entretien_carbu_charge" =>charge_ressource($res_charge[0]["COL 85"]),
			  "abo_tec" =>charge_ressource($res_charge[0]["COL 86"]), "abo_tec_conjoint" =>charge_ressource($res_charge[0]["COL 87"]), "abo_tec_charge" =>charge_ressource($res_charge[0]["COL 88"]),
			  "autre_dep" =>charge_ressource($res_charge[0]["COL 89"]), "autre_dep_conjoint" =>charge_ressource($res_charge[0]["COL 90"]), "autre_dep_charge" =>charge_ressource($res_charge[0]["COL 91"]),
			  "frais_gestion" =>charge_ressource($res_charge[0]["COL 92"]),"frais_gestion_conjoint" =>charge_ressource($res_charge[0]["COL 93"]),  "frais_gestion_charge" =>charge_ressource($res_charge[0]["COL 94"]),
			  "acces_compte" =>charge_ressource($res_charge[0]["COL 95"]),  "acces_compte_conjoint" =>charge_ressource($res_charge[0]["COL 96"]), "acces_compte_charge" =>charge_ressource($res_charge[0]["COL 97"]),
			  "mutuelle" =>charge_ressource($res_charge[0]["COL 98"]), "mutuelle_conjoint" =>charge_ressource($res_charge[0]["COL 99"]),  "mutuelle_charge" =>charge_ressource($res_charge[0]["COL 100"]),
			  "mobile" =>charge_ressource($res_charge[0]["COL 101"]), "mobile_conjoint" =>charge_ressource($res_charge[0]["COL 102"]), "mobile_charge" =>charge_ressource($res_charge[0]["COL 103"]),
			  "pel" =>charge_ressource($res_charge[0]["COL 107"]), "pel_conjoint" =>charge_ressource($res_charge[0]["COL 108"]), "pel_charge" =>charge_ressource($res_charge[0]["COL 109"]),
			  "assurance_vie" =>charge_ressource($res_charge[0]["COL 110"]), "assurance_vie_conjoint" =>charge_ressource($res_charge[0]["COL 111"]), "assurance_vie_charge" =>charge_ressource($res_charge[0]["COL 112"]),
			  "livret_epargne" =>charge_ressource($res_charge[0]["COL 113"]), "livret_epargne_conjoint" =>charge_ressource($res_charge[0]["COL 114"]), "livret_epargne_charge" =>charge_ressource($res_charge[0]["COL 115"]),
			  "contrat_obseque" =>charge_ressource($res_charge[0]["COL 116"]), "contrat_obseque_conjoint" =>charge_ressource($res_charge[0]["COL 117"]), "contrat_obseque_charge" =>charge_ressource($res_charge[0]["COL 118"]),
			  "autre_banque" =>charge_ressource($res_charge[0]["COL 119"]), "autre_banque_conjoint" =>charge_ressource($res_charge[0]["COL 120"]),  "autre_banque_charge" =>charge_ressource($res_charge[0]["COL 121"]),
			  "pension_ali" =>charge_ressource($res_charge[0]["COL 122"]), "pension_ali_conjoint" =>charge_ressource($res_charge[0]["COL 123"]), "pension_ali_charge" =>charge_ressource($res_charge[0]["COL 124"]),
			  "courses" =>charge_ressource($res_charge[0]["COL 125"]), "courses_conjoint" =>charge_ressource($res_charge[0]["COL 126"]), "courses_charge" =>charge_ressource($res_charge[0]["COL 127"]),
			  "habillement" =>charge_ressource($res_charge[0]["COL 128"]),  "habillement_conjoint" =>charge_ressource($res_charge[0]["COL 129"]), "habillement_charge" =>charge_ressource($res_charge[0]["COL 130"]),
			  "transports" =>charge_ressource($res_charge[0]["COL 131"]), "transports_conjoint" =>charge_ressource($res_charge[0]["COL 132"]), "transports_charge" =>charge_ressource($res_charge[0]["COL 133"]),
			  "cantine" =>charge_ressource($res_charge[0]["COL 134"]), "cantine_conjoint" =>charge_ressource($res_charge[0]["COL 135"]), "cantine_charge" =>charge_ressource($res_charge[0]["COL 136"]),
			  "soins" =>charge_ressource($res_charge[0]["COL 137"]), "soins_conjoint" =>charge_ressource($res_charge[0]["COL 138"]), "soins_charge" =>charge_ressource($res_charge[0]["COL 139"]),
			  "cigarette" =>charge_ressource($res_charge[0]["COL 140"]), "cigarette_conjoint" =>charge_ressource($res_charge[0]["COL 141"]), "cigarette_charge" =>charge_ressource($res_charge[0]["COL 142"]),
			  "presse" =>charge_ressource($res_charge[0]["COL 143"]), "presse_conjoint" =>charge_ressource($res_charge[0]["COL 144"]), "presse_charge" =>charge_ressource($res_charge[0]["COL 145"]),
			  "amende" =>charge_ressource($res_charge[0]["COL 156"]),			  
			  "conso_elec" =>charge_ressource($res_charge[0]["COL 165"]),
			  "conso_gaz" =>charge_ressource($res_charge[0]["COL 167"]),
			  "conso_eau" =>charge_ressource($res_charge[0]["COL 166"])		 
		 );
		  ATF::adherent_charge()->i($charges);
		 
		 
		 
		 //COTISATIONS
		 //COL 79 - 83 -> date_adhesion
		 //COL 84 - 88 -> detail		 
		 //COL 89 - 93 -> montant
		 //COL 94 - 98 -> montant_regle
		 //COL 106 - 110 -> solde
		 //COL 99 - 103 -> date_reglement
		 for($i =0; $i<5; $i++){
		 	$a = 79+$i;
			$b = 84+$i;
			$c = 89+$i;
			$d = 94+$i;
			$e = 106+$i;
			$f = 99+$i;
			
			if($value["COL ".$a] || $value["COL ".$b] || $value["COL ".$c]!= "0,000000" || $value["COL ".$d]!= "0,000000" || $value["COL ".$e]!= "0,000000" || $value["COL ".$a]){
			  	ATF::adherent_cotisation()->insert(array("id_adherent" => $id,
													 "date_adhesion" => ReturnDate($value["COL ".$a]),
													 "detail" => $value["COL ".$b],
													 "montant" => $value["COL ".$c],
													 "montant_regle" => $value["COL ".$d],
													 "solde" => $value["COL ".$e],
													 "date_reglement" => ReturnDate($value["COL ".$f])
											   ));	
			 }
			
					
		 }
		 //RDV - TABLE 162  (fichier RDV_JURI.XLS) 
		 $query ="SELECT * FROM `TABLE 162` WHERE `COL 3` = ".$value["COL 2"];
		 $rdv = ATF::db()->sql2array($query);
		 if($rdv){
		 	foreach($rdv as $k=>$v){
		 		if($v["COL 18"]){		 			
		 			$v["COL 18"] = getEnum($value["COL 78"] , "orientation");					
		 		}else{
		 			$v["COL 18"] = NULL;
		 		}	
				$date = NULL;				
				if($v["COL 4"]){
					$date = ReturnDate($v["COL 4"])." ".$v["COL 6"].":00";
				}
				if($v["COL 18"]){
					$v["COL 18"] = getEnum($v["COL 18"], "pole_accueil");
				}else{
					$v["COL 18"] = NULL;
				}
					
				$rdvInsert =array(
				 	 "date_contact" => ReturnDate($v["COL 4"]), 
				 	 "type_contact" => typeContact($v["COL 7"]), 
					 "date_rdv" => $date, 
					 "type_rdv" => typeRDV($v["COL 5"]), 
					 "id_pole_accueil" => getEnum($v["COL 17"], "pole_accueil"), 
					 "objet_rdv" => $v["COL 9"], 
					 "presence" => presence($v["COL 10"]), 
					 "commentaire" => $v["COL 12"], 
					 "procedure_en_cours" => ReturnBool($v["COL 13"]), 
					 "observation_procedure" => $v["COL 15"], 
					 "commentaire_procedure" => $v["COL 14"], 
					 "orientation_interne" => $v["COL 18"], 
					 "orientation_externe" => orient_ext($v["COL 19"]), 
					 "orient_autre" => $v["COL 20"], 
					 "id_action_propose" => getEnum($v["COL 16"] , "action_propose"), 
					 "autre_action" => $v["COL 21"] , 
					 "id_adherent" => $id, 
					 "id_demande_conseil" => getEnum($v["COL 11"], "demande_conseil"), 
					 "id_type_accompagnement"  => getEnum($v["COL 22"] , "type_accompagnement")
				 );				 
				 ATF::rdv()->i($rdvInsert);
			}			
		 	
		 }	
	 }	
}
recupvilleparcp();	

	function recupvilleparcp(){
		$query ="SELECT * FROM adherent where id_zonegeo IS NULL";
		$result = ATF::db()->sql2array($query);
		foreach($result as $k=>$v){		
			if($v["cp"] && strlen($v["cp"]) == 5){				
				$query ="SELECT * FROM zonegeo where zonegeo.cp = ".$v["cp"]." limit 0,1";
				$res = ATF::db()->sql2array($query);	
				if($res){					
					ATF::adherent()->u(array("id_adherent"=> $v["id_adherent"], "id_zonegeo"=> $res[0]["id_zonegeo"]));
				}
				
			}
		}
	}
	
	function civilite($infos){
		if(($infos == "Mr") || ($infos == "Mme") || ($infos == "Melle")){
			return $infos;
		}else{
			return "Mr";
		}
	}	
	
	function sexe($infos){
		if(($infos == "F") || ($infos == "Féminin")){
			return "F";
		}else{
			return "M";
		}
	}
	
	function ReturnDate($infos){
		if($infos != ""){
			$res = explode("/",$infos);	
			return $res["2"]."-".$res["1"]."-".$res["0"];	
		}
		return NULL;		
	}
	
	function ReturnBool($infos){
		if($infos == 0){
			return "non";
		}else{
			return "oui";
		}	
	}
	
	function DemandeurEmploi($infos){		
		if($infos){
			if($infos == "0"){
				return "non";
			}elseif($infos == "-1" || $infos == "-1 an"){
				return "-1an";
			}elseif($infos == "-1"){
				return "+1 an";
			}elseif($infos == "+2 ans"){
				return "+2ans";
			}	
		}else{			
			return "non";
		}	
		return "non";
	}
	
	function getEnum($infos , $table, $cp = false){	
		
		if($table == "zonegeo"){
			if($cp && $infos){
				$query ='SELECT id_'.$table.' FROM `'.$table.'`
				 	 WHERE LOWER('.$table.') LIKE LOWER("%'.$infos.'%")
				 	 OR cp = '.$cp;
					 
			}else{
				return NULL;
			}
			
		}else{
			$query ='SELECT id_'.$table.' FROM `'.$table.'`
				 WHERE LOWER('.$table.') LIKE LOWER("%'.$infos.'%")';
		}		 
		$result = ATF::db()->sql2array($query);
		
		if(!$result[0]["id_".$table]){
			if($table === "orientation"){
				return "13";
			}						
		}	
		
		if($table == "zonegeo"){
			$infos = enleveaccents($infos);
			if(strtoupper($infos) !==  strtoupper(enleveaccents(ATF::zonegeo()->select($res[$key]["id_".$table] , "zonegeo")))){
				$q = 'SELECT id_'.$table.' FROM `'.$table.'`
				 	      WHERE LOWER('.$table.') LIKE LOWER("%'.$infos.'%")';
				$res = ATF::db()->sql2array($q);
				
				if($res){
					$esp = 0;
					foreach ($res as $key => $value) {
						if(strtoupper($infos) ==  strtoupper(enleveaccents(ATF::zonegeo()->select($res[$key]["id_".$table] , "zonegeo")))){							
							$result[0] = $res[$key];
							$esp = 1;
						}
					}
					if($esp ==0){
						return NULL;
					}											
				}else{
					return NULL;
				}	     
				
			}
			
		}
		
		//log::logger($query , "mfleurquin");
		return $result[0]["id_".$table];	
		
		
			
		
	}

	function enleveaccents($chaine)
    {
     		$chaine = str_replace("é", "E", $chaine);
			$chaine = str_replace("è", "E", $chaine);
			$chaine = str_replace("ê", "E", $chaine);
 
     return $chaine;
    }

	function situation_fam($infos){			
		switch($infos){
			case "Célibatair" : return "celibataire";
			case "célibatair" : return "celibataire";
			case "CELIBATAI" : return "celibataire";
			case "Marié(e)" : return "marie";
			case "Concubinag" : return "concubinage";
			case "Séparé(e)" : return "separe";
			case "Séparé" : return "separe";
			case " séparée" : return "separe";
			case "Veuf(ve)" : return "veuf";
			case "Veuve" : return "veuf";
			case "Pacs" : return "pacs";
			case "Union libr" : return "union_libre";
			case "union libr" : return "union_libre";
			case "Autres" : return "autre";
			case "Divorcée" : return "divorce";	
			case "Divorcé" : return "divorce";
			default : return "inconnu";			
		}
	}

	function habitation($infos){		
		switch($infos){
			case "Locataire" : return "locataire";
			case "LOCATAIRE" : return "locataire";
			case "Hébergement Famille" : return "heber_famille";
			case "Accession à la propriété" : return "accession_propriete";
			case "Propriétaire" : return "proprietaire";
			case "Hébergement Ami" : return "heber_ami";
			case "Hébergement Foyer" : return "heber_foyer";
			case "Foyer" : return "heber_foyer";
			case "Logement de fonction" : return "logement_fonction";
			case "LOGEMENT FONCTION" : return "logement_fonction";
			case " proprietaire en SCI" : return "pro_en_sci";
			case "sdf" : return "sdf";
			case "SDF" : return "sdf";
			default : return "inconnu";
		}
	}
	
	function csp($infos){		
		switch($infos){
			case "Agriculteur" : return 'agriculteur';
			case "Artisan, Commerçant, Chef d'entreprise" : return 'artisant_commercant_chef';
			case "LOGISTICIEN" : return 'logisticien';
			
			case "Employé" : return 'employe';
			case "EMPLOYE": return 'employe';
			case "Employée" : return 'employe';
			
			case "Ouvrier" : return 'ouvrier';
			
			case "Retraité" : return 'retraite';
			case "Retraitée" : return 'retraite';
			case "retraité" : return 'retraite';
			
			case "Inactif" : return 'inactif';
			case "Cadre" : return 'cadre';
			case "Cadre Contremaître de chantier": return 'cadre';
			
			case "Etudiant" : return 'etudiant';
			case "Sans activité professionnelle" : return 'sans_activite';
			
			case "Autre" : return "autre";
			case "AUTRE" : return "autre";
			case "AUTRES" : return "autre";
			default : return "inconnu";
		}
	}

	function padd($infos){		
			switch($infos){			
				
				case "a. Recevabilité dossier BDF" : return "3";
				case strncmp($infos, "a. R", 4) : return "3";
					
				case "b. Dettes apurées" : return "2";
				case "c. Rétablissement personnel" : return "4";
				case "d. Respect des échéanciers et des engagements" : return "5";
				case "e. Evolution dans la gestion budgétaire" : return "6";
				case "f. Orientation liquidation judiciaire" : return "7";
				
				case "g. Assistance aux audiences" : return "8";	
				case strncmp($infos, "g. A", 4) : return "8";
				
				case "h. Répartition du disponible au marc le franc." : return "9";	
				
				case "i. Négociation aboutée." : return "10";
				case "i. Négociation aboutie" : return "10";	
				
				case "j. Classement sans suite pour défaut d’implication" : return "11";	
				case "k. Abandon adhérent." : return "12";
				
				case "ARCHIVAGE ADMINISTRATIF" : return "14";
				case "solutionné" : return "17";
				
				case "l. Rejets de crédit."	 : return "16";
				case "rachat de crédit" : return "16";
				case "rachat de credit" : return "16";
				
				case "irrecevable" : return "18";
				case "irrecevalbilité" : return "18";
				case "irrecevabilité" : return "18";
				
				case "nouvelle demande" : return "19";
				case "NOUVELLE DEMANDE" : return "19";
				
				case "PAS D'EVALUATION" : return "1";
				 
				case strncmp($infos, "m. Autre", 8) : return ;					
				default : return "1";
			}
		
				
		
	}
	
	function impaye($infos){
		
		switch($infos){
			case "a. Médiations distributeurs d’énergie abouties" : return "11";			
			case "b. Orientation PADD" : return "3";
			case "c. Orientation ECOGAZ" : return "2";
			case "d. Problématique liée au comportement" : return "4";
			case "e. Problématique liée à l’habitat" : return "5";
			case "f. Problématique liée à une mauvaise gestion du budget" : return "6";
			case "g. Respect des engagements" : return "7";
			case "h. Abandon adhérent." : return "8";
			case "i. Sans solutions" : return "9";			
			case "j. Classement sans suite pour défaut d’implication" : return "10";
			case "" : return "1";
			default : return "1";
			
		}	
	}
	function precarite($infos){
		
		switch($infos){
			case "a. Micro Crédits travaux habitat accordés" : return "2";			
			case "b. Travaux économies énergie réalisés" : return "3";
			case "c. Suivi des consommations réduites Objectif atteint" : return "4";
			case "d. Suivi des consommations réduites Objectif pas atteint" : return "5";
			case "" : return "1";
			default : return "1";			
		}		
	}

	function nationalite($infos){
		if($infos){
			if($infos == "Française"){
				return "francaise";
			}
			if($infos == "Etrangère"){
				return "etranger";
			}
		}
		return "";		
	}

	function niveau($infos){		
		switch ($infos) {
			case '0':	return "70";
			case '1':	return "60";
			case '2':	return "50";
			case '3':	return "51";
			case '4':	return "40";
			case '5':	return "30";
			case '6':	return "20";
			case '7':	return "10";
			default :	return "-";			
		}
	}

	function age($age){		
		if($age && (strlen($age)> 4) && ($age !== "F : NC")){
			if(($age == "s)D : 41-60 ans") || ($age == " 41-60 ans")){
				return "D : 41-60 ans";
			}
			if($age == "C : 26-40 "){				
				return "C : 26-40 ans";
			}
			return $age;
		}else{
			return "NC";
		}
		
	}
	
	function typeContact($infos){
		if($infos){
			switch($infos){
				case "Téléphonique" : return "telephonique";				
				case "Téléphonique & courrier" : return "tel_cour";
				case "Téléphonique et courrier" : return "tel_cour";
				case "Courrier et téléphone" : return "tel_cour";
				case "Téléphonique + courrier" : return "tel_cour";
				case "Téléphonique + courrier" : return "tel_cour";
				case "Téléphonique et courrier." : return "tel_cour";
				case "Courrier & téléphone" : return "tel_cour";				
				case "Accueil" : return "accueil";
				case "Mail" : return "mail";
				case "Courrier" : return "courrier";
				case "FAX" : return "fax";
				case "Fax" : return "fax";
				default : return NULL;
			}
		}else{
			return NULL;
		}
	}
	
	function presence($infos){
		if($infos){
			switch($infos){
				case "Présent(e)"	: return "present";				
				case "Absent excusé(e)"	: return "absent_excuse";
				case "Absent(e) excusé(e)"	: return "absent_excuse";
				case "Absent(e) non excusé(e)"	: return "absent_non_excuse";							
				default : return NULL;
			}
		}else{
			return NULL;
		}
	}
	
	function typeRDV($infos){
		if($infos){
			switch($infos){
				case "Diagnostic Micro-Crédit"	: return "diagnostique";
				case "Diagnostic"	: return "diagnostique";			
				case "Réunion 1er accueil"	: return "reunion_d_accueil";
				case "Accompagnement"	: return "accompagnement";
				case "A Accompagnement"	: return "accompagnement";
				case "Atelier"	: return "atelier";
				case "Conseil juridique"	: return "conseil_juridique";
				case "Permanence PAD" : return "perm_pad";							
				default : return NULL;
			}
		}else{
			return NULL;
		}
	}
	
	function orient_ext($infos){
		if($infos){
			switch($infos){
				case "Services sociaux"	: return "services_sociaux";
				case "CCAS"	: return "ccas";
				case "Autres à préciser"	: return "autre";								
				default :  return NULL;
			}
		}else{
			return NULL;
		}
	}
	
	function charge_ressource($infos){
		if($infos == "0,00" || $infos == "0,000000" || $infos == "0"){			
			return NULL;
		}else{
			return $infos;
		}
	}
	
	function getLettre($infos){			
		switch($infos){
			case "1" : return "P";
			case "2" : return "E";
			case "3" : return "M";
			case "4" : return "H";
		}
	}
	
	function delete(){
		$query ="SELECT * FROM adherent";
		$result = ATF::db()->sql2array($query);
		foreach($result as $k=>$v){
			echo "SUPPRESSION DE l'ADHERENT N° ".$v["id_adherent"]."
";
			ATF::adherent()->delete($v["id_adherent"]);
		}	
		
		$query ="ALTER TABLE  `adherent` AUTO_INCREMENT =1";
		ATF::db()->sql2array($query);
		$query ="ALTER TABLE  `adherent_enfant` AUTO_INCREMENT =1";
		ATF::db()->sql2array($query);
		$query ="ALTER TABLE  `adherent_cotisation` AUTO_INCREMENT =1";
		ATF::db()->sql2array($query);
		$query ="ALTER TABLE  `adherent_ressource` AUTO_INCREMENT =1";
		ATF::db()->sql2array($query);
		$query ="ALTER TABLE  `adherent_charge` AUTO_INCREMENT =1";
		ATF::db()->sql2array($query);
		$query ="ALTER TABLE  `rdv` AUTO_INCREMENT =1";
		ATF::db()->sql2array($query);		
	}