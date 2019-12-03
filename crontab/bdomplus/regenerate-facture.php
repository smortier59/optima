<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");

echo "Include global : ".dirname(__FILE__)."/../../global.inc.php \n";

$logFile = "bdomplus-regenerate-facture";

log::logger("================DEBUT DE BATCH================", $logFile);
ATF::facture()->q->reset()->where("date", "2019-12-01", "AND", null, "<");
ATF::facture()->q->setToString();
log::logger(ATF::facture()->sa(), $logFile);
ATF::facture()->q->unsetToString();
$factures = ATF::facture()->sa();

foreach ($factures as $facture) {
	log::logger("====== Facture REF ".$facture['ref']." en cours de traitement", $logFile);
	
	$path = ATF::facture()->filepath($facture["id_facture"],"fichier_joint");
	log::logger("====== PATH ".$path, $logFile);
	if (file_exists($path)) {
		log::logger("====== -------> ALREADY EXIST ", $logFile);
		log::logger("copy ".$path." into ".$path."_copy", $logFile);
		$r = util::rename($path, $path."_copy");
		log::logger("result ".$r, $logFile);
	} else {
		log::logger("====== -------> DON'T EXIST ", $logFile);
	}
	log::logger("====== MOVE FILES ", $logFile);
	$data = ATF::pdf()->generic("facture",$facture["id_facture"],true,$s,false);
	ATF::facture()->store($s,$facture["id_facture"],"fichier_joint",$data,false);
	
}


log::logger("================FIN DE BATCH================", $logFile);
