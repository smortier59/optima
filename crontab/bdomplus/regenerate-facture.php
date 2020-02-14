<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");

echo "Include global : ".dirname(__FILE__)."/../../global.inc.php \n";

$logFile = "bdomplus-regenerate-facture-2002-003";

log::logger("================DEBUT DE BATCH================", $logFile);
ATF::facture()->q->reset();
// ATF::facture()->where("date", "2019-12-01", "AND", null, "<");
// Uniquement les avoirs
ATF::facture()->q->where("prix", "0", "OR", null, "<");
// Facture avec un taux de TVA a 1
ATF::facture()->q->where("tva", "1.000");
ATF::facture()->q->setToString();
log::logger(ATF::facture()->sa(), $logFile);
ATF::facture()->q->unsetToString();
$factures = ATF::facture()->sa();

log::logger("====== Nombre de factures à traiter : ".count($factures), $logFile);

foreach ($factures as $facture) {
	log::logger("====== Facture REF ".$facture['ref']." en cours de traitement", $logFile);
	if ($facture['prix'] <= 0) log::logger("====== Facture AVOIR, montant négatif", $logFile);
	if ($facture['tva'] == '1.000') log::logger("====== Facture avec taux de TVA à 1", $logFile);


	$path = ATF::facture()->filepath($facture["id_facture"],"fichier_joint");
	log::logger("====== PATH ".$path, $logFile);
	if (file_exists($path)) {
		log::logger("====== -------> ALREADY EXIST ", $logFile);
		log::logger("copy ".$path." into ".$path."_copy", $logFile);
		// $r = util::rename($path, $path."_copy");
		log::logger("result ".$r, $logFile);
	} else {
		log::logger("====== -------> DON'T EXIST ", $logFile);
	}
	log::logger("====== MOVE FILES ", $logFile);
	// $data = ATF::pdf()->generic("facture",$facture["id_facture"],true,$s,false);
	// ATF::facture()->store($s,$facture["id_facture"],"fichier_joint",$data,false);
	
}


log::logger("================FIN DE BATCH================", $logFile);
