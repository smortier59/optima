<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


ATF::facture()->q->reset()->addAllFields("facture")
						  ->whereIsNull("DATE_EXPORT_VTE","AND")
						  ->where("facture.date",date("Y-m-d"),"AND",false,"<=");

$infos = array();
if($factures = ATF::facture()->sa()){
	$file = ATF::facture()->export_GL_LM($infos,$factures);
}
