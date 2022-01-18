<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

$q = 'SELECT DISTINCT(id_agence) FROM `user` WHERE `etat` = "normal"';


$agence = ATF::db()->sql2array($q);

$type= array("reseau", 'autre');

$data = array(
			"2016" => array("08","09","10","11","12"),
			"2017" => array("01","02","03","04","05","06","07","08","09","10","11","12"),
			"2018" => array("01","02","03","04","05","06","07","08","09","10","11","12"),
			"2019" => array("01")
		);



foreach ($agence as $key => $value) {
	if($value["id_agence"]){
		foreach ($data as $ka => $va) {
			foreach ($va as $km => $vm) {

				foreach ($type as $kt => $vt) {
					ATF::devis()->q->reset()
							->addField("COUNT(*)","nb")
							->setStrict()
							->addJointure("devis","id_societe","societe","id_societe")
							->addJointure("devis","id_affaire","affaire","id_affaire")
							->addJointure("societe","id_owner","user","id_user")
							->where("user.id_agence",$value["id_agence"])

							->addCondition("devis.devis","%lcd%" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%avenant%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%vente%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%AVT%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%MIPOS%","AND", "conditiondevis", "NOT LIKE")

							->addCondition("devis.type_contrat","vente","AND", "conditiondevis", "!=")
							->addCondition("devis.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

							->addCondition("devis.etat",'gagne',"AND","conditiondevis","=")

							->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%Y')","year")
							->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%m')","month")

							->addCondition("`devis`.`first_date_accord`", $ka."-".$vm."-01","AND",false,">=")
							->addCondition("`devis`.`first_date_accord`", $ka."-".$vm."-31","AND",false,"<=");

					if($type == "reseau"){
						ATF::devis()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")
						               ->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL");
					}else{
						ATF::devis()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")
						               ->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL");
					}

					$result = ATF::devis()->select_row();
					echo "devis-".$vt." ".$ka."-".$vm."-01 ".$value["id_agence"]." --> ".$result["nb"]." \n";
					ATF::stat_snap()->i(array("date"=>$ka."-".$vm."-01", "nb"=>$result["nb"], "stat_concerne"=>"devis-".$vt, "id_agence"=>$value["id_agence"]));



					ATF::commande()->q->reset()
							->addField("COUNT(*)","nb")
							->setStrict()
							->addJointure("commande","id_societe","societe","id_societe")
							->addJointure("commande","id_affaire","affaire","id_affaire")
							->addJointure("societe","id_owner","user","id_user")
							->where("user.id_agence",$value["id_agence"])

							->addCondition("commande.etat","prolongation" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","AR" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","arreter" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","vente" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","restitution" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","mis_loyer_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","prolongation_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","restitution_contentieux" ,"AND", "conditiondevis", "NOT LIKE")

							->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")
							->addCondition("commande.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

							->addCondition("commande.date_arret",NULL,"AND","conditiondevisEtat","IS NULL")
							->addCondition("`commande`.`date_arret`", $ka."-".$vm."-01","OR","conditiondevisEtat",">=")
							->addCondition("`commande`.`date_arret`", $ka."-".$vm."-31","OR","conditiondevisEtat","<=")

							->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%Y')","year")
							->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%m')","month")

							->addCondition("`commande`.`mise_en_place`", $ka."-".$vm."-01","AND",false,">=")
							->addCondition("`commande`.`mise_en_place`", $ka."-".$vm."-31","AND",false,"<=");


					if($type == "reseau"){
						ATF::commande()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL");
					}else{
						ATF::commande()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL");
					}

					$result= ATF::commande()->select_row();

					echo "mep-".$vt." ".$ka."-".$vm."-01 ".$value["id_agence"]." --> ".$result["nb"]." \n";
					ATF::stat_snap()->i(array("date"=>$ka."-".$vm."-01", "nb"=>$result["nb"], "stat_concerne"=>"mep-".$vt, "id_agence"=>$value["id_agence"]));

				}
			}
		}
	}
}

?>