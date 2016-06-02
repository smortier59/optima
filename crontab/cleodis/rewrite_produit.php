<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

echo "Parcours des produits ";

foreach (ATF::produit()->select_all() as $key => $value) {
	if(!$value["url"]){
		ATF::produit()->u(array("id_produit"=>$value["id_produit"], "url"=>util::mod_rewrite($value["produit"]) ));
		echo ".";
	}
}

echo "Parcours des packs produit ";

foreach (ATF::pack_produit()->select_all() as $key => $value) {
	if(!$value["url"]){
		ATF::pack_produit()->u(array("id_pack_produit"=>$value["id_pack_produit"], "url"=>util::mod_rewrite($value["nom"]) ));
		echo ".";
	}
}


?>