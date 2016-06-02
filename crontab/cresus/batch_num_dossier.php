<?
define("__BYPASS__", true);
$_SERVER["argv "][1] ="cresus";
include(dirname(__FILE__)."/../../global.inc.php");




$adherents = ATF::adherent()->select_all();

foreach ($adherents as $key => $value) {	
	$dossier1 = $value["num_dossier"];
	$dossier = explode("-", $dossier1);
	if(strlen($dossier[1]) < 4){			
		if(strlen($dossier[1]) == 1){
			$num_dossier = $dossier[0]."-000".$dossier[1]."-".$dossier[2];
		}elseif(strlen($dossier[1]) == 2){
			$num_dossier = $dossier[0]."-00".$dossier[1]."-".$dossier[2];
		}elseif (strlen($dossier[1]) == 3) {
			$num_dossier = $dossier[0]."-0".$dossier[1]."-".$dossier[2];
		}
		ATF::adherent()->u(array("id_adherent" => $value["id_adherent"] , "num_dossier" => $num_dossier));
	}
}


?>