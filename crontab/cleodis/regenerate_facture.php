<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodisbe";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodisbe/";


/*
* Regénérer les factures des affaire 1404040 et 1407020 avec comme refinanceur CLEODIS BE


	ATF::facture()->q->reset()->where("facture.id_affaire",11175,"OR","factures","=")
							  ->where("facture.id_affaire",11360,"OR","factures","=");
	$factures = ATF::facture()->select_all();

	ATF::affaire()->u(array("id_affaire"=> 11175, "id_filiale"=>4225));
	ATF::affaire()->u(array("id_affaire"=> 11360, "id_filiale"=>4225));



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
			$facture["batchtva"] = 1.21;
			$facture["batch"] = true;
			$values_facture["produits"] = json_encode($values_facture["produits"]);

			$id = ATF::facture()->insert(array("facture"=>$facture , "values_facture"=> $values_facture));
			echo "Mise à jour Facture ref : ".$facture["ref"]."\n";



	}
*/


ATF::facture()->q->reset()->where("facture.date","2016-01-01","AND",false,">=");
$factures = ATF::facture()->select_all();

//echo count($factures);
foreach ($factures as $key => $value) {

	ATF::facture()->move_files($value["facture.id_facture"]);
}
?>