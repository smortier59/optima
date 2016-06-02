<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

	
ATF::bon_de_commande_ligne()->q->reset()->addJointure("bon_de_commande_ligne","id_bon_de_commande","bon_de_commande","id_bon_de_commande",NULL,NULL,NULL,NULL,"left")
										->addCondition("bon_de_commande.id_bon_de_commande",NULL,'AND',false,'IS NULL');
		
if($data=ATF::bon_de_commande_ligne()->sa()){
	foreach($data as $k=>$v){
		ATF::bon_de_commande_ligne()->d($v["id_bon_de_commande_ligne"]);
	}
}

?>