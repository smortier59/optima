<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

$contrat = ATF::commande()->select_all();

foreach ($contrat as $key => $value) {
	ATF::facture()->q->reset()->setLimit(1)
							  ->where("facture.id_commande", $value["commande.id_commande"])
							  ->addField("facture.date")
							  ->addField("facture.type_facture")
							  ->addField("facture.ref")
							  ->addOrder("facture.date" , "asc")
							  ->addCondition("facture.type_facture","facture","OR")
							  ->addCondition("facture.type_facture","libre","OR")
							  ->addCondition("facture.type_facture","refi","OR");
	$factures = ATF::facture()->select_row();	
	log::logger("Ref contrat : ".$value["commande.id_commande"] ,  basename(__FILE__).".log");
	log::logger("Facture : ".$factures["facture.ref"] , basename(__FILE__).".log");		
	if($factures != array()){
		$data = array("id_commande"=> $value["commande.id_commande"] , "mise_en_place" => $factures["facture.date"]);					
		//Faire l'update du coup		
		ATF::commande()->u($data);
		log::logger("Contrat : ".$value["commande.id_commande"]." date de mise en place : ".$factures["facture.date"] ,  basename(__FILE__).".log");
	}else{
		$data = array("id_commande"=> $value["commande.id_commande"] , "mise_en_place" => NULL);
		ATF::commande()->u($data);
		log::logger("Contrat : ".$value["commande.id_commande"]." date de mise en place : NULL" ,  basename(__FILE__).".log");
	}
	log::logger("-------------------" , basename(__FILE__).".log");
}


?>