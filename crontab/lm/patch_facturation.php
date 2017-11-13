<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



ATF::commande()->q->reset()->whereIsNotNull("date_debut");
$commandes = ATF::commande()->select_all();

foreach ($commandes as $key => $value) {
	$id_affaire = $value["commande.id_affaire_fk"];
	$affaire = ATF::affaire()->select($id_affaire);

	ATF::loyer()->q->reset()->where("id_affaire", $id_affaire);
	$loyers = ATF::loyer()->select_all();

	$nbLoyer = 0;

	foreach ($loyers as $kl => $vl) {
		$nbLoyer += $vl["duree"];
	}

	ATF::facturation()->q->reset()->where("id_affaire", $id_affaire);
	$facturations = ATF::facturation()->select_all();


	if($nbLoyer > count($facturations)){
		$date_debut=ATF::commande()->select($value["commande.id_commande"], "date_debut");
		foreach ($loyers as $kl => $item) {
			$frequence=1;
			//Pour chaque échéance d'une période
			for($j=1;$j<=$item['duree'];$j++){
				//log::logger($date_debut , "mfleurquin");

				$date_fin=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
				$date_fin=date("Y-m-d",strtotime($date_fin."-1 day"));
				$echeance = array(
					"id_societe"=>$affaire["id_societe"],
					"id_affaire"=>$affaire["id_affaire"],
					"montant"=>$item['loyer'],
					"assurance"=>$item['assurance'],
					"frais_de_gestion"=>$item['frais_de_gestion'],
					"date_periode_fin"=>$date_fin,
					"date_periode_debut"=>$date_debut,
					"type"=>"contrat",
					"nature"=>$item["nature"]
				);

				$spy = 0;
				foreach ($facturations as $kf => $vf) {
					//Si la facturation existe déja, il ne faut pas la réintégrer
					if(date("Y-m-d", strtotime($echeance["date_periode_debut"])) == date("Y-m-d", strtotime($vf["date_periode_debut"]))
					&& date("Y-m-d", strtotime($echeance["date_periode_fin"])) == date("Y-m-d", strtotime($vf["date_periode_fin"]))){
						$spy = 1;
					}
				}

				//Si la facturation n'existe pas, on l'ajoute à l'échancier
				if(!$spy){
					$id_facturation = ATF::facturation()->i($echeance);
				}
				$date_debut=date("Y-m-d H:i:s",strtotime($date_debut."+".$frequence." month"));
			}
		}
	}
	echo '.';

}



