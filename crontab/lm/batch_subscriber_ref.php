<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

ATF::affaire()->q->reset()->addField("id_parent")
						  ->addField("subscriber_reference")
						  ->whereIsNull("affaire.subscriber_reference")
						  ->addOrder("affaire.id_affaire");
$affaire_sans_ref_subscriber = ATF::affaire()->select_all();

foreach ($affaire_sans_ref_subscriber as $key => $value) {

	//On recupère le ref sub de l'affaire parente
	$subscriber_reference = ATF::affaire()->select($value["id_parent"], "subscriber_reference");

	if($subscriber_reference){
		echo ATF::affaire()->select($value["affaire.id_affaire"], "ref")." --> ".$subscriber_reference."\n";
		ATF::affaire()->u(array("id_affaire"=>$value["affaire.id_affaire"], "subscriber_reference"=> $subscriber_reference));
	}


}