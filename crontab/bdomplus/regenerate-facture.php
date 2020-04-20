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
ATF::facture()->q->where("prix", "0", "OR", 'k1', "<");
// Facture avec un taux de TVA a 1
// ATF::facture()->q->where("tva", "1.000", "OR", 'k1');
ATF::facture()->q->where("id_facture",'2125',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2127',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2128',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2129',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2130',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2131',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2132',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2133',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2134',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2135',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2136',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2137',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2138',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2139',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2140',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2141',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2142',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2143',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2144',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2145',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2146',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2147',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2148',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2149',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2150',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2151',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2152',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2153',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2154',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2155',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2156',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2157',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2158',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2159',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2160',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2161',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2162',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2163',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2164',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2165',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2166',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2167',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2168',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2193',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2194',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2195',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2196',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2197',"OR","k1","=");
ATF::facture()->q->where("id_facture",'2198',"OR","k1","=");


ATF::facture()->q->setToString();
log::logger(ATF::facture()->sa(), $logFile);
echo ATF::facture()->sa()."\n";
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
