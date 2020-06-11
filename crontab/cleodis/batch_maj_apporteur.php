<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

ATF::$usr->set('id_user',16);
ATF::$usr->set('id_agence',1);

ATF::affaire()->q->reset()->whereIsNull("id_partenaire");
$affs = ATF::affaire()->sa();

log::logger("Ref affaire;Affaire;Ancien partenaire;Nom ancien partenaire;Nouvel apporteur;Nom nouvel apporteur;" , "mfleurquin");

foreach ($affs as $ka => $va) {
	$app = ATF::societe()->select($va["id_societe"]);
	if($app){
		log::logger($va["ref"].";".$va["affaire"].";".$va["id_partenaire"].";".ATF::societe()->select($va["id_partenaire"], "societe").";".$app["id_societe"].";".$app["societe"].";" , "mfleurquin");
	}


}

