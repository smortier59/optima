<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

// Déplacer tous les fichiers de facture autre, refi et ap ({ID}.pdf) vers /data/factures/{NEW_ID}.fichier_joint
$dataPath = __DATA_PATH__."cleodis/";
util::mkdir($dataPath."/facture");
chmod($dataPath."/facture",0755);
chown($dataPath."/facture","apache");
chgrp($dataPath."/facture","apache");

// Factures Refi => /fichier_facture_refi/
echo "\nFactures REFI :\n";
ATF::facture_refi()->q->reset()->addField("id_facture_refi")->addField("id_facture_new");
foreach (ATF::facture_refi()->select_all() as $item) {
	$oldPath = $dataPath."fichier_facture_refi/".$item["id_facture_refi"].".pdf";
	$newPath = $dataPath."facture/".$item["id_facture_new"].".fichier_joint";
	if (file_exists($oldPath)) {
		if (!file_exists($newPath)) {
			rename($oldPath,$newPath);
			echo "id_refi=".$item["id_facture_refi"]."=>".$item["id_facture_new"]." ok...";
		} else {
			echo "\n".$newPath." existe déjà !";
		}
		chmod($newPath,0755);
		chown($newPath,"apache");
		chgrp($newPath,"apache");
	} else {
		echo "\nManque ".$oldPath." !";
	}
}

echo "\nFactures AP :\n";
// Factures AP => /fichier_facture/
$query = "SELECT id_facture,id_facture_new FROM facture_ap";
foreach (ATF::db()->sql2array($query) as $item) {
	$oldPath = $dataPath."fichier_facture/".$item["id_facture"].".pdf";
	$newPath = $dataPath."facture/".$item["id_facture_new"].".fichier_joint";
	if (file_exists($oldPath)) {
		if (!file_exists($newPath)) {
			rename($oldPath,$newPath);
			echo "id_ap=".$item["id_facture"]."=>".$item["id_facture_new"]." ok...";
		} else {
			echo "\n".$newPath." existe déjà !";
		}
		chmod($newPath,0755);
		chown($newPath,"apache");
		chgrp($newPath,"apache");
	} else {
		echo "\nManque ".$oldPath." !";
	}
}

echo "\nFactures Normales :\n";
// Factures normales => /fichier_facture_autre/
ATF::facture()->q->reset()->addField("id_facture");
foreach (ATF::facture()->select_all() as $item) {
	$oldPath = $dataPath."fichier_facture_autre/".$item["id_facture"].".pdf";
	$newPath = $dataPath."facture/".$item["id_facture"].".fichier_joint";
	if (file_exists($oldPath)) {
		if (!file_exists($newPath)) {
			rename($oldPath,$newPath);
			echo "id=".$item["id_facture"]." ok...";
		} else {
			echo "\n".$newPath." existe déjà !";
		}
		chmod($newPath,0755);
		chown($newPath,"apache");
		chgrp($newPath,"apache");
	} else {
		echo "\nManque ".$oldPath." !";
	}
}

?>