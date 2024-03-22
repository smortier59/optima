<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

$q = 'SELECT DISTINCT(id_agence) FROM `user` WHERE `etat` = "normal" AND id_agence IS NOT NULL';
$agences = ATF::db()->sql2array($q);


foreach ($agences as $key => $value) {
	$date = date("Y-m-01");
	$dateMoisPrec = date("Y-m-01" , strtotime($date." -1 month"));

	log::logger($date, "mfleurquin");
	log::logger($dateMoisPrec, "mfleurquin");

/*
	ATF::db()->begin_transaction();
	try{

		ATF::devis()->q->reset()
							->addField("COUNT(*)","nb")
							->setStrict()
							->addJointure("devis","id_societe","societe","id_societe")
							->addJointure("devis","id_affaire","affaire","id_affaire")
							->addJointure("societe","id_owner","user","id_user")
							->where("user.id_agence",$value["id_agence"])
							->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")
							->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL")
							->addCondition("devis.devis","%lcd%" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%avenant%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%vente%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%AVT%","AND", "conditiondevis", "NOT LIKE")
							->addCondition("devis.devis","%MIPOS%","AND", "conditiondevis", "NOT LIKE")

							->addCondition("devis.type_contrat","vente","AND", "conditiondevis", "!=")
							->addCondition("devis.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

							->addCondition("devis.etat",'gagne',"AND","conditiondevis","=")
							->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
							->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

							->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%Y')","year")
							->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%m')","month")

							->addGroup("year")->addGroup("month")
							->addOrder("year")->addOrder("month")

							->addCondition("`devis`.`first_date_accord`",$dateMoisPrec,"AND",false,">=")
							->addCondition("`devis`.`first_date_accord`",$date,"AND",false,"<");

		$result= ATF::devis()->select_row();

		if(empty($result)){
			$result["nb"] = 0;
		}

		ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"devis-reseau", "id_agence"=>$value["id_agence"]));

		ATF::db()->commit_transaction();

	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo $e->getMessage();
	}


	ATF::db()->begin_transaction();
	try{
		ATF::devis()->q->reset()
			->addField("COUNT(*)","nb")
			->setStrict()
			->addJointure("devis","id_societe","societe","id_societe")
			->addJointure("devis","id_affaire","affaire","id_affaire")
			->addJointure("societe","id_owner","user","id_user")
			->where("user.id_agence",$value["id_agence"])
			->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")
			->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL")
			->addCondition("devis.devis","%lcd%" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%avenant%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%vente%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%AVT%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%MIPOS%","AND", "conditiondevis", "NOT LIKE")

			->addCondition("devis.type_contrat","vente","AND", "conditiondevis", "!=")
			->addCondition("devis.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

			->addCondition("devis.etat",'gagne',"AND","conditiondevis","=")
			->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
			->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

			->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%Y')","year")
			->addField("DATE_FORMAT(`devis`.`first_date_accord`,'%m')","month")

			->addGroup("year")->addGroup("month")
			->addOrder("year")->addOrder("month")

			->addCondition("`devis`.`first_date_accord`",$dateMoisPrec,"AND",false,">=")
			->addCondition("`devis`.`first_date_accord`",$date,"AND",false,"<");

		$result= ATF::devis()->select_row();

		if(empty($result)){
			$result["nb"] = 0;
		}
		ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"devis-autre", "id_agence"=>$value["id_agence"]));


		ATF::db()->commit_transaction();
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo $e->getMessage();
	}

*/
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

			->addCondition("`commande`.`mise_en_place`",$dateMoisPrec,"AND",false,">=")
			->addCondition("`commande`.`mise_en_place`",$date,"AND",false,"<");


		$result= ATF::commande()->select_row();

		if(empty($result)){
			$result["nb"] = 0;
		}

		ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"mep-reseau", "id_agence"=>$value["id_agence"]));

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

			->addCondition("`commande`.`mise_en_place`",$dateMoisPrec,"AND",false,">=")
			->addCondition("`commande`.`mise_en_place`",$date,"AND",false,"<");

		$result= ATF::commande()->select_row();
		if(empty($result)){
			$result["nb"] = 0;
		}
		ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"mep-autre", "id_agence"=>$value["id_agence"]));

		ATF::db()->commit_transaction();
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo $e->getMessage();
	}
}