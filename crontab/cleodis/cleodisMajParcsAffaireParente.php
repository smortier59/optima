<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

/*Permet de mettre à jour les lignes de commande ayant une affaire provenance mais pour laquelle il n'y a ni parc dans l'affaire courante ni dans l'affaire parente*/

ATF::db()->begin_transaction();


$query="SELECT * 
		FROM  `commande_ligne` 
		WHERE  `id_affaire_provenance` IS NOT NULL 
		AND  `serial` IS NULL
		ORDER BY  `commande_ligne`.`id_commande_ligne` DESC  ";

$commande_ligne=ATF::db()->sql2array($query);

$pc=$bpbq=$bpmq=$pcprov=0;
		
foreach ($commande_ligne as $item) {
	$query="SELECT * 
			FROM  `commande` 
			WHERE  `id_commande` =".$item["id_commande"];
	
	$commande=ATF::db()->sql2array($query);

	$query="SELECT * 
			FROM  `affaire` 
			WHERE  `id_affaire` =".$commande[0]["id_affaire"];
	
	$affaire=ATF::db()->sql2array($query);

	
	$query="SELECT * 
			FROM  `parc` 
			WHERE `provenance` =".$item["id_affaire_provenance"]."
			AND  `ref` = '".$item["ref"]."'";
	   
	$parcs=ATF::db()->sql2array($query);
			
	if(!$parcs){
		$query="SELECT * 
				FROM  `parc` 
				WHERE `id_affaire` =".$item["id_affaire_provenance"]."
				AND  `ref` = '".$item["ref"]."'";
		   
		$parcs_prov=ATF::db()->sql2array($query);
		if(!$parcs_prov){
			$pc++;
		}else{
			$i=1;
			foreach($parcs_prov as $it){
				if($i<=$item["quantite"]){
					$new_parc=$it;
					unset($new_parc["id_parc"]);
					$new_parc["id_affaire"]=$commande[0]["id_affaire"];
					$new_parc["provenance"]=$item["id_affaire_provenance"];
					$new_parc["id_societe"]=$commande[0]["id_societe"];
					$new_parc["date"]=$commande[0]["date_debut"];

					if($commande[0]["etat"]=="non_loyer"){
						$new_parc["existence"]="inactif";
					}else{
						$new_parc["existence"]="actif";
						$it["existence"]="inactif";
						$it["date_inactif"]=$commande[0]["date_debut"];
					}

					if($affaire[0]["nature"]=="avenant"){
						$new_parc["etat"]="broke";
					}elseif($affaire[0]["nature"]=="AR"){
						$new_parc["etat"]="reloue";
					}elseif($affaire[0]["nature"]=="vente"){
						$new_parc["etat"]="vendu";
					}
print_r($new_parc);
					ATF::parc()->i($new_parc);
print_r($it);
					ATF::parc()->u($it);
print_r(array("id_commande_ligne"=>$item["id_commande_ligne"],"serial"=>$new_parc["serial"]));
					ATF::commande_ligne()->u(array("id_commande_ligne"=>$item["id_commande_ligne"],"serial"=>$new_parc["serial"]));
					$i++;
				}
			}
			$pcprov++;
		}
	}else{
		$i=1;
		$bpbq++;
		foreach($parcs as $i){
			if($i<=$item["quantite"]){
print_r(array("id_commande_ligne"=>$item["id_commande_ligne"],"serial"=>$i["serial"]));
				ATF::commande_ligne()->u(array("id_commande_ligne"=>$item["id_commande_ligne"],"serial"=>$i["serial"]));
				$i++;
			}
		}
	}
}

		print_r("\n Pas de parcs ".$pc);
		print_r("\n Bon parc bonne quantité ".$bpbq);
		print_r("\n Bon parc mauvaise quantité ".$bpmq);
		print_r("\n Bon parc provenance ".$pcprov);

$query="SELECT * 
		FROM  `commande` 
		WHERE  `etat` =  'arreter'";

$commandes=ATF::db()->sql2array($query);

foreach ($commandes as $item) {
	ATF::commande()->stopCommande($item);
}



ATF::db()->commit_transaction();
?>