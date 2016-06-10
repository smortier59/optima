<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



$q = "SELECT *  FROM description_produit";
$produits = ATF::db()->sql2array($q);	

foreach ($produits as $key => $value){
	if($value["id_produit"]){
		ATF::produit()->u(array("id_produit"=>$value["id_produit"],
							"produit"=>$value["produit"],
							"description"=>$value["description"]
					));
	}	
}
