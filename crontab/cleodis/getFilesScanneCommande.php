<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");

$fromDir = "/home/www/absystech/extranet_v3_cleodis/www/fichier_commande/";
$toDir = "/home/optima/data/cleodis/commande/";

foreach (scandir($fromDir) as $k=>$i) {
	if ($i=="." || $i=="..") continue;
	preg_match_all("/([0-9]{1,5})_(scan|pv_scan)\.pdf/",$i,$m);

	$id = $m[1][0];
	$field = $m[2][0];
	if (!$id || !$field) continue;
	
	$zipfile = new ZipArchive();
	touch($toDir.$id.".".$field);

	if ($zipfile->open($toDir.$id.".".$field) === TRUE) {
		echo $i."<=====>".$fromDir.$i."\n";
		if (!$zipfile->addFile($fromDir.$i, $i)) {
		   die("Problème avec l'ajout du fichier dans le zip : '".$i."'-'".$fromDir.$i."'\n");
		}
		
		$zipfile->close();
	} else {
	   die("Problème avec l'ouverture du zip\n");
	}
	
//	if (copy($fromDir.$i,$toDir.$id.".".$field)) {
//		echo "File : ".$fromDir.$i." copié vers ".$toDir.$id.".".$field."\n";
//	} else {
//		echo "ERREUR File : ".$fromDir.$i."\n";
//	}
}
echo "THIS IS THE END\n";
?>