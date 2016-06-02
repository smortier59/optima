<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::db()->begin_transaction();



/*MAJ DES VENTES*/
$query="SELECT a.`id_affaire` AS affaire, b.`id_affaire` AS parent
		FROM  `affaire` AS a
		INNER JOIN  `affaire` AS b ON b.`id_fille` = a.`id_affaire` 
		WHERE a.nature =  'vente'";
		
$nb=0;
$affaires=ATF::db()->sql2array($query);
foreach ($affaires as $i) {
	ATF::affaire()->u(array("id_affaire"=>$i["affaire"],"id_parent"=>$i["parent"]));
	ATF::affaire()->u(array("id_affaire"=>$i["parent"],"id_fille"=>NULL));
}

///*MAJ DES AR*/
//$query="SELECT * 
//		FROM  `affaire` 
//		WHERE  `nature` =  'AR'";
//		
//$nb=0;
//$affaires=ATF::db()->sql2array($query);
//foreach ($affaires as $i) {
//	ATF::affaire()->q->reset()->addCondition("id_fille",$i["id_affaire"]);
//	$affaireParent=ATF::affaire()->sa();
//	if(count($affaireParent)>1){
////		print_r("\n Plus d'une");
////		print_r($i);
////		print_r($affaireParent);
//	}elseif(!$affaireParent){
//		print_r("\n Aucun");
//		print_r($i["ref"]);
//	}
////	print_r();
////	foreach($affaireParent as $item){
////		
////	}
//	
//}


$query="SELECT * 
		FROM  `affaire` 
		WHERE  `ref` LIKE  '0805052'
		OR  `ref` LIKE  '0805010'
		OR  `ref` LIKE  '0802059'
		OR  `ref` LIKE  '0711005'
		OR  `ref` LIKE  '0701002'
		OR  `ref` LIKE  '0701077'
		OR  `ref` LIKE  '0707047'
		OR  `ref` LIKE  '0708012'
		OR  `ref` LIKE  '0707080'
		OR  `ref` LIKE  '0709027AVT2'
		OR  `ref` LIKE  '1007004'
		OR  `ref` LIKE  '0706018AVT1'
		OR  `ref` LIKE  '0807060AVT1'
		OR  `ref` LIKE  '0705019AVT1'
		OR  `ref` LIKE  '0701057'
		OR  `ref` LIKE  '0801059'
		OR  `ref` LIKE  '0707047AVT1'
		OR  `ref` LIKE  '0804035AVT1'
		OR  `ref` LIKE  '0806066AVT1'
		OR  `ref` LIKE  '0806093AVT1'
		OR  `ref` LIKE  '0806098AVT1'
		OR  `ref` LIKE  '1011017'
		OR  `ref` LIKE  '0701034'
		OR  `ref` LIKE  '0807123AVT2'
		OR  `ref` LIKE  '0907018AVT1'
		OR  `ref` LIKE  '0810056AVt1'
		OR  `ref` LIKE  '0812040AVT1'
		
		OR  `ref` LIKE  '0603004'
		OR  `ref` LIKE  '0706015AVT1'
		OR  `ref` LIKE  '0760015'
		OR  `ref` LIKE  '0604001'
		OR  `ref` LIKE  '0701024'
		OR  `ref` LIKE  '0806104'
		OR  `ref` LIKE  '0512002'
		
		OR  `ref` LIKE  '0901028'
		OR  `ref` LIKE  '0807035'
		OR  `ref` LIKE  '0802052AVT2'
		OR  `ref` LIKE  '0807065'
		";
$nb=0;
$affaires=ATF::db()->sql2array($query);
foreach ($affaires as $i) {
	$nb++;
	print_r("\n".$nb." / ".count($affaires));

	$query = "	SELECT `facture`.`id_facture` , `facture`.`id_affaire` , `id_facturation` , `facture`.`ref`
				FROM `facture`
				INNER JOIN `facturation` ON `facture`.`id_facture` = `facturation`.`id_facture`
				WHERE `date` >= '2011-09-01'
				AND `facture`.`id_affaire` = ".$i["id_affaire"];
				
	$factures=ATF::db()->sql2array($query);
	
	foreach ($factures as $item) {
		$objAffaire = new affaire_cleodis($item['id_affaire']);
		
		print_r("\nSuppression facturation pourrie ".$item['ref']);
		$facturation=ATF::facturation()->select($item["id_facturation"]);
		
		ATF::facture()->delete($item["id_facture"]);
		ATF::facturation()->delete($facturation["id_prolongation"]);
	}
}
			
ATF::db()->commit_transaction();

?>