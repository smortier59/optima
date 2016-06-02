<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

/*Permet de mettre à jour les lignes de commande ayant une affaire provenance mais pour laquelle il n'y a ni parc dans l'affaire courante ni dans l'affaire parente*/
ATF::db()->begin_transaction();

$query="SELECT * 
		FROM  `commande_ligne` 
		WHERE  `id_affaire_provenance` IS NOT NULL 
		AND  `serial` IS NULL 
		ORDER BY  `commande_ligne`.`id_commande_ligne` ASC ";

$commande_ligne=ATF::db()->sql2array($query);
ATF::user()->setDB("main");

$count=count($commande_ligne);
$i=0;
foreach ($commande_ligne as $key=>$item) {
	print_r("\n\n ".$key."/".$count);
	ATF::devis()->q->reset()->addCondition("id_affaire",$item["id_affaire_provenance"])->setDimension("row");
	$devis=ATF::devis()->sa();
	
	$trouv=false;
	$tab_trouv=array();
	while($trouv==false){
		ATF::devis_ligne()->q->reset()->addCondition("id_devis",$devis["id_devis"]);
		
		if($item["ref"]){
			ATF::devis_ligne()->q->addCondition("ref",$item["ref"]);
		}
									  
		if($item["id_produit"]){
			ATF::devis_ligne()->q->addCondition("id_produit",$item["id_produit"]);
		}
									  
		
		ATF::devis_ligne()->q->addHavingConditionNotNull("serial")
						     ->setDimension("row");
									  
		if($tab_trouv){
			foreach($tab_trouv as $i){
				ATF::devis_ligne()->q->addCondition("id_devis_ligne",$i,"AND",1,"!=");
			}
		}
		
		if($devis_ligne=ATF::devis_ligne()->sa()){
			$tab_trouv[]=$devis_ligne["id_devis_ligne"];
			ATF::commande_ligne()->q->reset()->addCondition("id_commande",$item["id_commande"])
											 ->addCondition("serial",$devis_ligne["serial"]);
			
			if(!$commande_ligne=ATF::commande_ligne()->sa()){
				ATF::commande_ligne()->u(array("id_commande_ligne"=>$item["id_commande_ligne"],"serial"=>$devis_ligne["serial"]));
print_r(array("id_commande_ligne"=>$item["id_commande_ligne"],"serial"=>$devis_ligne["serial"]));
				$trouv=true;
				$i+=1;
			}
		}else{
			$trouv=true;
		}
	}
}
	print_r("\n\n ".$i."/".$count);

ATF::db()->commit_transaction();

?>
