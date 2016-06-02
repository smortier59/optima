<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$i = 'SELECT societe.id_societe , `societe`, count(affaire.id_societe) as nb_affaire, id_fournisseur
	  FROM `societe`, `affaire`
	  WHERE societe.id_societe = affaire.id_societe
	  AND societe.etat !=  "inactif"
	  GROUP BY societe.societe
	  HAVING nb_affaire > 1';

$societes = ATF::db()->sql2array($i);

foreach ($societes as $key => $value) {


	ATF::societe()->u(array("id_societe"=> $value["id_societe"] , "id_apporteur"=> $value["id_fournisseur"]));

	ATF::commande()->q->reset()->where("commande.id_societe", $value["id_societe"]);
	$commandes = ATF::commande()->select_all();

	$fournisseur = NULL;
	foreach ($commandes as $k => $v) {
		ATF::commande_ligne()->q->reset()->addConditionNotNull("id_fournisseur")->where("commande_ligne.id_commande", $v["commande.id_commande"])->setLimit(1);
		$commande_ligne = ATF::commande_ligne()->select_row();
		
		$fournisseur[] = $commande_ligne["id_fournisseur"];
	}

	$fournisseur = array_map("unserialize", array_unique(array_map("serialize", $fournisseur)));



	if($fournisseur[1]){		

		$q = 'SELECT `COL 3` as fournisseur
	  	FROM `TABLE 159` as tfournisseur
	  	WHERE tfournisseur.`COL 1` = "'.$value["societe"].'"';

		$fourn = ATF::db()->sql2array($q);		

		if($fourn[0]["fournisseur"]){			
			if($fourn[0]["fournisseur"] == "N'CO SERVICES"){
				$fourn["id_societe"] = 2118;
			}else{
				ATF::societe()->q->reset()->where("societe" , $fourn[0]["fournisseur"]);
				$fourn = ATF::societe()->select_row();

				log::logger($fourn , "mfleurquin");
			}
			ATF::societe()->u(array("id_societe" => $value["id_societe"] , "id_fournisseur"=> $fourn["id_societe"]));
		}
	}else{
		ATF::societe()->u(array("id_societe" => $value["id_societe"] , "id_fournisseur"=> $fournisseur[0]));
	}
}

?>