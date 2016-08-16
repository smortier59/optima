<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

/*Permet de mettre à jour les lignes de commande ayant une affaire provenance mais pour laquelle il n'y a ni parc dans l'affaire courante ni dans l'affaire parente*/

$query="SELECT * 
		FROM  `commande` 
		WHERE (
		`date_EVOLUTION` LIKE  '%15'
		AND  `date_debut` LIKE  '%15'
		)
		OR (
		`date_EVOLUTION` LIKE  '%01'
		AND  `date_debut` LIKE  '%01'
		)";

$commande=ATF::db()->sql2array($query);
ATF::user()->setDB("main");

$count=count($commande);
foreach ($commande as $key=>$item) {
	print_r("\n\n ".$key."/".$count."    ".ATF::db()->numberTransaction());
	print_r("\n ".$item["ref"]." ".$item["date_debut"]." ".$item["date_evolution"]);
	$infos['value']=$item["date_debut"];
	$infos['field']="date_debut"; 
	$infos['id_commande']=$item["id_commande"]; 
	try{
		ATF::commande()->updateDate($infos);
	} catch(errorATF $e) {
		ATF::commande()->u(array("id_commande"=>$item["id_commande"],"date_evolution"=>date("Y-m-d H:i:s",strtotime($item["date_evolution"]."- 1 day"))));
	}
	$com=ATF::commande()->select($item["id_commande"]);
	print_r("\n ".$com["ref"]." ".$com["date_debut"]." ".$com["date_evolution"]);
}

?>
