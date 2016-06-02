<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

/*Permet de mettre à jour les affaires qui sont de nature affaires et qui ont pourtant des lignes de commande avec une affaire_parente*/

ATF::db()->begin_transaction();


$query="SELECT  `affaire` . * , `commande`.`id_commande`
		FROM  `commande_ligne` 
		INNER JOIN  `commande` ON  `commande`.`id_commande` =  `commande_ligne`.`id_commande` 
		INNER JOIN  `affaire` ON  `affaire`.`id_affaire` =  `commande`.`id_affaire` 
		WHERE  `id_affaire_provenance` IS NOT NULL 
		AND  `affaire`.`nature` =  'affaire'
		GROUP BY  `affaire`.`id_affaire` ";

$affaires=ATF::db()->sql2array($query);
		
foreach ($affaires as $item) {
	ATF::affaire()->u(array("id_affaire"=>$item["id_affaire"],"nature"=>"AR"));
	ATF::commande_ligne()->q->reset()->addCondition("id_commande",$item["id_commande"]);
	$commande_lignes=ATF::commande_ligne()->sa();
	unset($tab_com);
	foreach($commande_lignes as $i){
		if($i["id_affaire_provenance"] && !$tab_com[$i["id_affaire_provenance"]]){
			$tab_com[$i["id_affaire_provenance"]]=$i["id_affaire_provenance"];
		}
	}
	foreach($tab_com as $i){
		ATF::affaire()->u(array("id_affaire"=>$i,"id_fille"=>$item["id_affaire"]));
	}
}



ATF::db()->commit_transaction();
?>