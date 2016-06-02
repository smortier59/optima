<?
define("__BYPASS__", true);
$_SERVER["argv "][1] ="cresus";
include(dirname(__FILE__)."/../../global.inc.php");



$query ='SELECT  * 
		 FROM  `TABLE 166`';
$result = ATF::db()->sql2array($query);

foreach($result as $k=> $v){
	if($k > 0){	
		$q = "SELECT id_adherent
			  FROM adherent
			  WHERE num_dossier_old = ".$v["COL 3"]."
			  AND id_site_accueil = 2";
		$adherent = ATF::db()->sql2array($q);
		if($adherent){
			$id_adherent = $adherent[0]["id_adherent"];
			$q2 = "SELECT *
				   FROM rdv
				   WHERE id_adherent = ".$id_adherent."
				   AND date_contact = '".ReturnDate($v["COL 4"])."'";
			$rdv2 = ATF::db()->sql2array($q2);
			
			if($rdv2){					
				$date = NULL;				
				if($v["COL 4"]){
					$date = ReturnDate($v["COL 4"])." ".$v["COL 6"].":00";
				}	


				if(!$v["COL 15"]){ $v["COL 15"] = "";}
				if(!$v["COL 14"]){ $v["COL 14"] = "";}
				if(!$v["COL 20"]){ $v["COL 20"] = "";}
				if(!$v["COL 21"]){ $v["COL 21"] = "";}
				/*log::logger($rdvInsert =array(
						 "id_rdv" => $rdv2[0]["id_rdv"],
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
						 "id_adherent" => $id_adherent, 
						 "id_demande_conseil" => getEnum($v["COL 11"], "demande_conseil"), 
						 "id_type_accompagnement"  => getEnum($v["COL 22"] , "type_accompagnement")
					 ) , "mfleurquin");*/
				$u = 'UPDATE `rdv` SET date_contact = "'.ReturnDate($v["COL 4"]).'", type_contact = "'.typeContact($v["COL 7"]).'",  date_rdv = "'.$date.'", type_rdv = "'.typeRDV($v["COL 5"]).'", id_pole_accueil = '.getEnum($v["COL 17"], "pole_accueil").', objet_rdv = "'.$v["COL 9"].'",  presence = "'.presence($v["COL 10"]).'", commentaire = "'.str_replace('"' , '', $v["COL 12"]).'", procedure_en_cours = "'.ReturnBool($v["COL 13"]).'",observation_procedure = "'.$v["COL 15"].'",  commentaire_procedure = "'.$v["COL 14"].'", orientation_interne = '.getEnum($v["COL 18"], "pole_accueil").', orient_autre = "'.$v["COL 20"].'",  id_action_propose = '.getEnum($v["COL 16"] , "action_propose").',  autre_action = "'.$v["COL 21"].'" , id_demande_conseil = '.getEnum($v["COL 11"], "demande_conseil").', id_type_accompagnement = '.getEnum($v["COL 22"] , "type_accompagnement");				

				if(orient_ext($v["COL 19"])){
					$u .=  ', orientation_externe = "'.orient_ext($v["COL 19"]).'"';
				}
				$u .= ' WHERE id_rdv = '.$rdv2[0]["id_rdv"].';
';

				if($rdv2[0]["id_rdv"] == 5522){
					log::logger($v , "mfleurquin");
				}

				echo $u;
			}
		}
		
		
	}

}

function ReturnBool($infos){
	if($infos == 0){
		return "non";
	}else{
		return "oui";
	}	
}

function ReturnDate($infos){
	if($infos != ""){
		$res = explode("/",$infos);	
		return $res["2"]."-".$res["1"]."-".$res["0"];	
	}
	return "NULL";		
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
			default : return "NULL";
		}
	}else{
		return "NULL";
	}
}

function presence($infos){
	if($infos){
		switch($infos){
			case "Présent(e)"	: return "present";				
			case "Absent excusé(e)"	: return "absent_excuse";
			case "Absent(e) excusé(e)"	: return "absent_excuse";
			case "Absent(e) non excusé(e)"	: return "absent_non_excuse";							
			default : return "NULL";
		}
	}else{
		return "NULL";
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
			default : return "NULL";
		}
	}else{
		return "NULL";
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

function getEnum($infos , $table, $cp = false){	
	
	
	$query ='SELECT id_'.$table.' FROM `'.$table.'`
		 WHERE LOWER('.$table.') LIKE LOWER("%'.$infos.'%")';
			 
	$result = ATF::db()->sql2array($query);
	
	if(!$result[0]["id_".$table]){
		if($table === "orientation"){
			return "13";
		}	
		if(($table == "demande_conseil") || ($table =="type_accompagnement") || ($table =="action_propose")){
			return 1;
		}	
		if($table == "pole_accueil"){
			return NULL;
		}				
	}

	//log::logger($query , "mfleurquin");
	return $result[0]["id_".$table];	
	
	
		
	
}	