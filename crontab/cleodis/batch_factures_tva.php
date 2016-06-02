<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

/*ATF::facture()->q->reset()->where("facture.tva", "1.200")
					      ->where("facture.date_periode_debut", "2013-12-31","AND",false, "<=")
					      ->where("facture.etat", "payee", "AND", false , "!=");
$factures = ATF::facture()->select_all();*/

ATF::facture()->q->reset()->where("facture.id_facture", 96160, "OR" )
						  ->where("facture.id_facture", 96139, "OR" )
						  ->where("facture.id_facture", 96156, "OR" );
$factures = ATF::facture()->select_all();

batch($factures);


function batch($factures){
	foreach ($factures as $key => $value) {
		$facture = array();
		$values_facture["produits"] = array();	


		$facture = ATF::facture()->select($value["facture.id_facture"]);

		ATF::facture_ligne()->q->reset()->where("id_facture" , $value["facture.id_facture"]);
		$facture_ligne = ATF::facture_ligne()->select_all();

		ATF::facturation()->q->reset()->where("id_facture" , $value["facture.id_facture"]);
		$facturation = ATF::facturation()->select_all();
			if($facture["etat"] == "payee"){
				ATF::facture()->u(array("id_facture"=>$value["facture.id_facture"] , "etat"=> "impayee"));
			}

			ATF::facture()->delete($value["facture.id_facture"]);
			unset($facture["id_facture"]);
			if($facture["type_facture"] == "libre"){
				$facture["prix_libre"] = $facture["prix"];
				$facture["date_periode_debut_libre"] = $facture["date_periode_debut"];
				$facture["date_periode_fin_libre"] = $facture["date_periode_fin"];
			}
			foreach ($facture_ligne as $k => $v) {				
				unset($v["id_facture_ligne"],$v["id_facture"]);
				$d = array();
				foreach ($v as $cle => $valeur) {				
					if(strpos($cle,'id') !== false){
						if($cle !==  'id_affaire_provenance'){
							$cle = $cle."_fk";		
						}								
					}				
					$cle = "facture_ligne__dot__".$cle;
					$d[$cle] = $valeur;
				}
				$values_facture["produits"][] = $d;
			}
			$facture["batchtva"] = 1.20;
			$facture["batch"] = true;
			$values_facture["produits"] = json_encode($values_facture["produits"]);
			//log::logger($values_facture["produits"] , "mfleurquin");
			//log::logger($facture , "mfleurquin");

			$id = ATF::facture()->insert(array("facture"=>$facture , "values_facture"=> $values_facture));
			echo "Mise à jour Facture ref : ".$facture["ref"]."\n";	
		//ATF::db()->commit_transaction(true);

	}
}


?>