<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";

include(dirname(__FILE__)."/../../../global.inc.php");

echo dirname(__FILE__)."/../../../global.inc.php\n";

ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."bdomplus/";

echo $dataPath."\n";

ATF::facture()->q->reset()->where("facture.date","2020-09-25");
$factures = ATF::facture()->select_all();

log::logger($factures , "mfleurquin");

echo count($factures)."\n";


$q = "SELECT * FROM `facture` WHERE facture.date < '2020-09-25' ORDER BY facture.id_facture desc LIMIT 0,1";
$data = ATF::db()->sql2array($q);
log::logger($data , "mfleurquin");

$last_fact = $data[0];



log::logger("-----------------".$last_fact["ref_externe"] , "mfleurquin");
$last_ref = str_replace("F930C005", "", $last_fact["ref_externe"]);



$ref_externe = "F930C005";
$last_ref ++;


foreach ($factures as $key => $value) {
	log::logger("F930C005".$last_ref, "mfleurquin");
	ATF::facture()->u(array("id_facture"=> $value["facture.id_facture"], "ref_externe"=> "F930C005".$last_ref));
	ATF::facture()->move_files($value["facture.id_facture"]);
	$last_ref++;
}

?>