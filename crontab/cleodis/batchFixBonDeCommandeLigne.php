<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

ATF::db()->begin_transaction();

echo "\ncommande :\n";
ATF::bon_de_commande_ligne()->q->reset()->whereIsNull("id_commande_ligne");
foreach (ATF::bon_de_commande_ligne()->sa() as $item) {
	$ligne = NULL;
	$id_commande = ATF::bon_de_commande()->select($item["id_bon_de_commande"],"id_commande");
	$query = "SELECT * FROM commande_ligne WHERE ref LIKE '%".ATF::db()->real_escape_string($item["ref"])."%' AND id_commande=".$id_commande;
	if ($cl = ATF::db()->sql2array($query)) {
		$ligne = $cl[0];
		echo "\nRef exacte : ";
	} else {
		$query = "SELECT * FROM commande_ligne WHERE (ref LIKE '%PREST%' OR ref LIKE '%LOG%' OR (ref IS NULL AND (produit LIKE '%nom de dom%' OR produit LIKE '%garant%'))) AND id_commande=".$id_commande;
		if ($cl = ATF::db()->sql2array($query)) {
			$ligne = $cl[0];
			echo "\nRef choisie : ";
		} else {
			$query = "SELECT * FROM commande_ligne WHERE id_commande=".$id_commande;
			if ($cl = ATF::db()->sql2array($query)) {
//				$ligne = $cl[0];
			echo "\nAucune correspondance ! ";
//print_r($item);
//print_r($cl);
			} else {
				echo "\nerror";
			}
		}
	}
	
	echo $item["id_bon_de_commande_ligne"]." ".$item["ref"]." => ".$ligne["ref"]." (".$ligne["produit"].")";
	if ($ligne) {
		ATF::bon_de_commande_ligne()->update(array(
			"id_bon_de_commande_ligne"=>$item["id_bon_de_commande_ligne"]
			,"id_commande_ligne"=>$ligne["id_commande_ligne"]
		));
	}
}

ATF::db()->commit_transaction();

?>