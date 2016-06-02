<?
define("__BYPASS__", true);
$_SERVER["argv "][1] ="cresus";
include(dirname(__FILE__)."/../../global.inc.php");

$query ='SELECT COUNT( * ) AS nbr_doublon,  `num_dossier` 
		 FROM adherent
		 GROUP BY num_dossier
		 HAVING COUNT( * ) >2
		 ORDER BY  `adherent`.`num_dossier` DESC ';
		 //LIMIT 1, 100';
$result = ATF::db()->sql2array($query);

foreach ($result as $key => $value) {
	log::logger($value , "mfleurquin");
	ATF::adherent()->q->reset()->where("num_dossier", $value["num_dossier"]);
	$adherents = ATF::adherent()->select_all();
	foreach ($adherents as $k => $v) {
		if($k > 0){
			$dossier = explode("-", $v["num_dossier"]);
			$dossier[1] = $dossier[1] + $k;
			
			ATF::adherent()->u(array("id_adherent" => $v["id_adherent"], "num_dossier" => $dossier[0]."-".$dossier[1]."-".$dossier[2] ));
		}
		
	}
	

}

?>