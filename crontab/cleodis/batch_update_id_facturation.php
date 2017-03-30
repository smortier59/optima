<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


ATF::commande()->q->reset()->where("commande.etat", "mis_loyer","OR")
						   ->where("commande.etat", "mis_loyer_contentieux","OR")
						   ->where("commande.etat", "prolongation_contentieux","OR")
						   ->addGroup("id_societe");

$commandes = ATF::commande()->sa();


foreach ($commandes as $key => $value) {
	$societe = ATF::societe()->select($value["id_societe"]);

	if(!$societe["id_contact_facturation"]){
		ATF::contact()->q->reset()->where("id_societe" , $value["id_societe"]);
		$contact = ATF::contact()->select_row();
		if($contact){
			ATF::societe()->u(array("id_societe"=>$value["id_societe"] , "id_contact_facturation"=>$contact["id_contact"]));
		}
		
	}
}
?>