<?
define("__BYPASS__",true);
include("../global.inc.php");

$modules = array('devis','commande','facture','suivi');
foreach ($modules as $m) {
	$c = ATF::getClass($m);
	foreach ($c->select_all() as $e) {
		if ($c->file_exists($e["id_".$m],'fichier_joint')) {
			$c->fetch_from_nebula($e["id_".$m],'fichier_joint');
		}
	}
}

?>