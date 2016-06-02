<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");


/************************************************/

$fichier='/home/absystech/optima.absystech.net/core/log/cleodis_statut.log';
$contenu = fread(fopen($fichier, "r"), filesize($fichier));
preg_match_all("/2012-02-2[345679].+arreter!=/",$contenu,$enr);
ATF::db()->begin_transaction();

	foreach($enr as $i){
		foreach($i as $item){
			preg_match_all("/[0-9]{7}(AVT[0-9]?)?/",$item,$gnac);
			print_r($gnac);
			ATF::commande()->q->reset()->addCondition("ref",$gnac[0][0])->setDimension("row");
			if($commande=ATF::commande()->sa()){
				$commande_exec[$commande["id_commande"]]=$commande;
			}
		}
	}
	
	foreach($commande_exec as $item){
			ATF::commande()->stopCommande($item);
	}
	
ATF::db()->commit_transaction();
?>