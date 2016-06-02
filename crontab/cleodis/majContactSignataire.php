<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::parc()->q->reset()->addCondition("provenance",NULL,"OR",false,"IS NOT NULL");
$parc=ATF::parc()->sa();
foreach($parc as $key=>$item){
	ATF::affaire()->u(array("id_affaire"=>$item["id_affaire"],"nature"=>"AR"));
}
ATF::societe()->q->reset();
$societe = ATF::societe()->sa();
foreach($societe as $key=>$item){
	unset($contact);
	//On test en premier un contact avec un mail
	ATF::contact()->q->reset()->addCondition("id_societe",$item["id_societe"])->addCondition("email",NULL,"AND",false,"IS NOT NULL");
	$contact=ATF::contact()->sa();
	if($contact[0]["id_contact"]){
		ATF::societe()->u(array("id_societe"=>$item["id_societe"],"id_contact_signataire"=>$contact[0]["id_contact"]));
	}else{
		unset($contact);
		//Sinon on test s'il y a un contact
		ATF::contact()->q->reset()->addCondition("id_societe",$item["id_societe"]);
		$contact=ATF::contact()->sa();
		if($contact[0]["id_contact"]){
			ATF::societe()->u(array("id_societe"=>$item["id_societe"],"id_contact_signataire"=>$contact[0]["id_contact"]));
		}
	}
}

?>