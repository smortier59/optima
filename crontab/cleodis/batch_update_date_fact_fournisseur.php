<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

/*
	COL 1 doit etre Date	
	COL 3 Libelle (Ref/societe)
*/

$q = "SELECT * 
	  FROM  `batch_facture`";

$factures = ATF::db()->sql2array($q);	  

foreach ($factures as $key => $value) {	
		$lib = $value["ref"];
		$date = $value["date"];

		$date = explode("/", $date);
		$date = $date[2]."-".$date["1"]."-".$date[0];
		//$date = date("Y-m-d", strtotime($date));
		$lib = explode("/",$lib);
		$lib = $lib[0];		

		ATF::facture_fournisseur()->q->reset()->where("ref",'%'.$lib.'%',"OR",null,"LIKE");
		$fac = ATF::facture_fournisseur()->select_row();
		if(empty($fac)){			
			echo "Aucune facture fournisseur avec la ref: ".$lib."\n";
		}else{
			ATF::facture_fournisseur()->u(array("id_facture_fournisseur" => $fac["id_facture_fournisseur"], 
												"date_paiement"=>$date,
												"etat"=>"payee"));
		}
}


?>