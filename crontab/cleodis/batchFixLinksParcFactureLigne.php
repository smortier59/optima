<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);
ATF::parc()->q->reset()->whereIsNull("id_produit");
foreach (ATF::parc()->select_all() as $item) {
	if ($item["id_produit"]) {
		continue;
	}
	$serial = trim($item["serial"]);
	$fl = ATF::facture_ligne();
	$fl->q->reset()
		->from("facture_ligne","id_facture","facture","id_facture")
//		->andWhere("id_affaire",$item["id_affaire"])
		->whereIsNotNull("facture_ligne.id_produit")
		->andWhere("serial",$serial,"serial")
		
		// Sinon le serial est parfois de 3 lettres, donc il y auarit des faux positifs
		->orWhere("serial","%,".$serial,"serial","LIKE")
		->orWhere("serial","%, ".$serial,"serial","LIKE")
		->orWhere("serial",$serial.",%","serial","LIKE")
		->orWhere("serial",$serial.", %","serial","LIKE")
		->orWhere("serial","%,".$serial.",%","serial","LIKE")
		->orWhere("serial","%,".$serial.", %","serial","LIKE")
		->orWhere("serial","%, ".$serial.", %","serial","LIKE")
		->orWhere("serial","%, ".$serial.",%","serial","LIKE")
		
		->addField("id_produit")
		->setDimension('cell');
	$id_produit = $fl->select_all();
	echo "\n".$serial." => ".$id_produit;
	
	if (!$id_produit) {
		echo "Produit non trouve  id_parc=".$item["id_parc"]."!";
	} else {
		echo "...Ecriture...";
		ATF::parc()->update(array(
			"id_parc"=>$item["id_parc"]
			,"id_produit"=>$id_produit
		));
	}
}
?>