<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

$q = 'SELECT DISTINCT(id_agence) FROM `user` WHERE `etat` = "normal"';


$agence = ATF::db()->sql2array($q);




foreach ($agence as $key => $value) {
	$date = date("Y-m-01");
	$dateMoisPrec = date("Y-m-01" , strtotime($date." -1 month"));

	ATF::devis()->q->reset()							
			->addField("COUNT(*)","nb")					
			->setStrict()
			->addJointure("devis","id_societe","societe","id_societe")	
			->addJointure("societe","id_owner","user","id_user")
			->addCondition("devis.etat",'gagne',"AND")
			->addCondition("user.id_agence",$value["id_agence"])
			
			->addCondition("societe.code_client",'%S%',"OR","nonFinie","NOT LIKE")
			->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NOT NULL")

			->addCondition("devis.devis","%lcd%" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%avenant%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%vente%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%AVT%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%MIPOS%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.type_contrat","%vente%","AND", "conditiondevis", "!=")
			->addCondition("devis.ref","%avt%","AND", "conditiondevis", "NOT LIKE")			
			->addCondition("`devis`.`first_date_accord`",$dateMoisPrec,"AND",false,">=")
			->addCondition("`devis`.`first_date_accord`",$date,"AND",false,"<");	

	$result= ATF::devis()->select_row();

	ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"devis-reseau", "id_agence"=>$value["id_agence"]));


	ATF::devis()->q->reset()							
			->addField("COUNT(*)","nb")					
			->setStrict()
			->addJointure("devis","id_societe","societe","id_societe")		
			->addCondition("devis.etat",'gagne',"AND")
			->addJointure("societe","id_owner","user","id_user")
			->addCondition("user.id_agence",$value["id_agence"])

			->addCondition("societe.code_client",'%S%',"OR","nonFinie","LIKE")
			->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL")
			
			->addCondition("devis.devis","%lcd%" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%avenant%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%vente%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%AVT%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.devis","%MIPOS%","AND", "conditiondevis", "NOT LIKE")
			->addCondition("devis.type_contrat","%vente%","AND", "conditiondevis", "!=")
			->addCondition("devis.ref","%avt%","AND", "conditiondevis", "NOT LIKE")			
			->addCondition("`devis`.`first_date_accord`",$dateMoisPrec,"AND",false,">=")
			->addCondition("`devis`.`first_date_accord`",$date,"AND",false,"<");	

	$result= ATF::devis()->select_row();

	ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"devis-autre", "id_agence"=>$value["id_agence"]));


	ATF::commande()->q->reset()							
			->addField("COUNT(*)","nb")					
			->setStrict()
			->addJointure("commande","id_societe","societe","id_societe")
			->addJointure("commande","id_affaire","affaire","id_affaire")
			->addJointure("societe","id_owner","user","id_user")
			->addCondition("user.id_agence",$value["id_agence"])

			->addCondition("societe.code_client",'%S%',"OR","nonFinie","NOT LIKE")
			->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NOT NULL")	

			->addCondition("commande.etat","prolongation" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","AR" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","arreter" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","vente" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","mis_loyer_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","prolongation_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution_contentieux" ,"AND", "conditiondevis", "NOT LIKE")		
			->addCondition("affaire.affaire","%transfert%" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.ref","%avt%","AND", "conditiondevis", "NOT LIKE")									
			

			->addCondition("`commande`.`mise_en_place`",$dateMoisPrec,"AND",false,">=")
			->addCondition("`commande`.`mise_en_place`",$date,"AND",false,"<");
				

	$result= ATF::commande()->select_row();
	ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"mep-reseau", "id_agence"=>$value["id_agence"]));



		
	ATF::commande()->q->reset()							
			->addField("COUNT(*)","nb")					
			->setStrict()
			->addJointure("commande","id_societe","societe","id_societe")
			->addJointure("commande","id_affaire","affaire","id_affaire")
			->addJointure("societe","id_owner","user","id_user")
			->addCondition("user.id_agence",$value["id_agence"])

			->addCondition("societe.code_client",'%S%',"OR","nonFinie","LIKE")
			->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL")

			->addCondition("commande.etat","prolongation" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","AR" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","arreter" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","vente" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","mis_loyer_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","prolongation_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.etat","restitution_contentieux" ,"AND", "conditiondevis", "NOT LIKE")		
			->addCondition("affaire.affaire","%transfert%" ,"AND", "conditiondevis", "NOT LIKE")
			->addCondition("commande.ref","%avt%","AND", "conditiondevis", "NOT LIKE")									
		

			->addCondition("`commande`.`mise_en_place`",$dateMoisPrec,"AND",false,">=")
			->addCondition("`commande`.`mise_en_place`",$date,"AND",false,"<");

	$result= ATF::commande()->select_row();
	ATF::stat_snap()->i(array("date"=>$dateMoisPrec, "nb"=>$result["nb"], "stat_concerne"=>"mep-autre", "id_agence"=>$value["id_agence"]));
}



?>
