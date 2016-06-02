<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::bon_de_commande_ligne()->q->reset();
$bon_de_commande_ligne=ATF::bon_de_commande_ligne()->sa();
$inf1=0;
$eg1=0;
$sup1=0;
$supsup1=0;
$infinf1=0;
foreach($bon_de_commande_ligne as $key=>$item){
	$enr=true;
	ATF::commande_ligne()->q->reset()->addCondition("id_commande",ATF::bon_de_commande()->select($item["id_bon_de_commande"],"id_commande"))
									 ->setCount();
									 
	if($item["ref"]){
		ATF::commande_ligne()->q->addCondition("ref",$item["ref"]);
	}else{
		ATF::commande_ligne()->q->addCondition("upper(produit)",addslashes($item["produit"]));
	}
	
	$commande_ligne=ATF::commande_ligne()->sa();
	if($commande_ligne["count"]<1){
		$inf1++;
	}elseif($commande_ligne["count"]==1){
		$eg1++;
		ATF::bon_de_commande_ligne()->u(array("id_bon_de_commande_ligne"=>$item["id_bon_de_commande_ligne"],"id_commande_ligne"=>$commande_ligne["data"][0]["id_commande_ligne"]));
	}else{
		foreach($commande_ligne["data"] as $k=>$i){
			if($enr){
				ATF::bon_de_commande_ligne()->q->reset()->addCondition("id_commande_ligne",$i["id_commande_ligne"])->setCount();
				$bon_de_commande_ligne_sup=ATF::bon_de_commande_ligne()->sa();
				if($bon_de_commande_ligne_sup["count"]<=$i["quantite"]){
					$eg1++;
					ATF::bon_de_commande_ligne()->u(array("id_bon_de_commande_ligne"=>$item["id_bon_de_commande_ligne"],"id_commande_ligne"=>$i["id_commande_ligne"]));
					$enr=false;
				}
			}
		}
	}
}

		print_r("\n Aucune commande_ligne existante ".$inf1);
		print_r("\n Une commande_ligne existante ".$eg1);

?>