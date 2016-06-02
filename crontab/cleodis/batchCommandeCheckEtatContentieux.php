<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

/*
ATF::commande()->q->reset()
						   ->addCondition("commande.etat","prolongation","OR",NULL,"=")
						   ->addCondition("commande.etat","restitution","OR",NULL,"=")
						   ->addCondition("commande.etat","mis_loyer","OR",NULL,"=");

$contrat = ATF::commande()->sa();

foreach ($contrat as $key => $value) {
	ATF::facture()->q->reset()->where("id_commande", $value["id_commande"])->addField("facture.rejet");
	$res = ATF::facture()->select_all();
	$esp = 0;	
		foreach($res as $k=>$v){
			//log::logger($v["facture.rejet"] , "mfleurquin");
			if(($v["facture.rejet"] !== "non_rejet") && ($v["facture.rejet"] !== "non_preleve_mandat")){
				$esp = 1;
			}
		}
		if($esp === 1){
			ATF::commande()->u(array("id_commande" => $value["id_commande"] , "etat"=> $value["etat"]."_contentieux"));			
		}	

}*/


$q =   'SELECT *
		FROM affaire, commande
		WHERE affaire.id_affaire = commande.`id_affaire` 
		AND affaire.etat =  "terminee"
		AND commande.etat !=  "arreter"
		AND commande.etat !=  "AR"
		AND commande.date_evolution <  "2013-09-01"
		AND affaire.id_affaire NOT 
		IN (		
		SELECT prolongation.id_affaire
		FROM prolongation
		)';
		
$contrat = ATF::db()->sql2array($q);	
		foreach ($contrat as $key=>$item) {
			ATF::commande()->u(array("id_commande" => $item["id_commande"], "etat" => "arreter"));
		}
?>