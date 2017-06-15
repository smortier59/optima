<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");


ATF::societe()->q->reset()->where("fournisseur", "non","AND")
						  ->whereIsNull("id_contact_facturation");

$client = ATF::societe()->select_all();


foreach ($client as $key => $value) {
	ATF::contact()->q->reset()->where("id_societe", $value["id_societe"])
							  ->addOrder("id_contact","DESC");
	if($contact = ATF::contact()->select_all()){
		ATF::societe()->u(array("id_societe"=>$value["id_societe"], "id_contact_facturation"=>$contact[0]["id_contact"]));
	}

}