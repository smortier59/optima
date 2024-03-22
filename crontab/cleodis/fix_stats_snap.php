<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

$q = 'SELECT * FROM `stat_snap` WHERE `nb` = 0 AND `stat_concerne` LIKE "mep-%" and id_agence=1 ORDER BY `id_stat_snap` ASC';
$data = ATF::db()->sql2array($q);

foreach ($data as $key => $value) {

  	$end =  date("Y-m-d" , strtotime(date("Y-m-01" , strtotime($value["date"]." +1 month"))." -1 day"));
    ATF::db()->begin_transaction();
	try{

		ATF::commande()->q->reset()
			->addField("COUNT(*)","nb")
			->setStrict()
			->addJointure("commande","id_societe","societe","id_societe")
			->addJointure("commande","id_affaire","affaire","id_affaire")
			->addJointure("societe","id_owner","user","id_user")
			->where("user.id_agence",$value["id_agence"])

			->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")
			->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL")

			->addCondition("commande.etat","prolongation" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","AR" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","arreter" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","vente" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","mis_loyer_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","prolongation_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution_contentieux" ,"AND", "conditiondevis", "NOT LIKE")

			->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
			->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

			->addCondition("commande.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

			->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%Y')","year")
			->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%m')","month")

			->addGroup("year")->addGroup("month")
			->addOrder("year")->addOrder("month")

			->addCondition("`commande`.`mise_en_place`",$value["date"],"AND",false,">=")
			->addCondition("`commande`.`mise_en_place`",$end,"AND",false,"<=");


		$result= ATF::commande()->select_row();

		if(empty($result)){
			$result["nb"] = 0;
		}

		ATF::stat_snap()->u(array("id_stat_snap"=>$value["id_stat_snap"], "nb"=>$result["nb"]));

		ATF::db()->commit_transaction();
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo $e->getMessage();
	}


	ATF::db()->begin_transaction();
	try{
		ATF::commande()->q->reset()
			->addField("COUNT(*)","nb")
			->setStrict()
			->addJointure("commande","id_societe","societe","id_societe")
			->addJointure("commande","id_affaire","affaire","id_affaire")
			->addJointure("societe","id_owner","user","id_user")
			->where("user.id_agence",$value["id_agence"])

			->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")
			->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL")

			->addCondition("commande.etat","prolongation" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","AR" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","arreter" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","vente" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","mis_loyer_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","prolongation_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution_contentieux" ,"AND", "conditiondevis", "NOT LIKE")

			->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
			->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

			->addCondition("commande.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

			->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%Y')","year")
			->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%m')","month")

			->addGroup("year")->addGroup("month")
			->addOrder("year")->addOrder("month")

			->addCondition("`commande`.`mise_en_place`",$value["date"],"AND",false,">=")
			->addCondition("`commande`.`mise_en_place`",$end,"AND",false,"<=");

		$result= ATF::commande()->select_row();
		if(empty($result)){
			$result["nb"] = 0;
		}
		ATF::stat_snap()->u(array("id_stat_snap"=>$value["id_stat_snap"], "nb"=>$result["nb"]));

		ATF::db()->commit_transaction();
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo $e->getMessage();
	}

}