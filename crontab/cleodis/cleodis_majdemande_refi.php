<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

	$demande_refi=ATF::demande_refi()->sa();
	
	foreach($demande_refi as $key=>$item){
		$infos["id_affaire"]=$item["id_affaire"];
		$infos["taux"]=$item["taux"];
		$infos["vr"]=$item["valeur_residuelle"];
		$loyers=ATF::affaire()->getCompteTLoyerActualise($infos);
		if($item["loyer_actualise"]!=$loyers){
			ATF::demande_refi()->u(array("id_demande_refi"=>$item["id_demande_refi"],"loyer_actualise"=>$loyers));
			print_r(array("id_demande_refi"=>$item["id_demande_refi"],"id_affaire"=>$item["id_affaire"],"loyer_actualise"=>$loyers,"ancien_loyer"=>$item["loyer_actualise"]));
		}
	}

?>