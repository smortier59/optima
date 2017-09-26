<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



$q = "SELECT *  FROM devis, affaire WHERE devis.id_affaire = affaire.id_affaire AND id_pack_produit IS NULL";
$devis = ATF::db()->sql2array($q);



foreach ($devis as $key => $value){
	$q2 = "SELECT id_produit FROM devis_ligne WHERE id_devis = ".$value["id_devis"]." LIMIT 0,1";
	$produit = ATF::db()->sql2array($q2);

	ATF::affaire()->u(array("id_affaire"=>$value["id_affaire"], "id_pack_produit"=>ATF::produit()->select($produit[0]["id_produit"] , "id_pack_produit")));
}
