<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

//echo "\nDevis :\n";
//util::mkdir($dataPath."/devis");
//chmod($dataPath."/devis",0755);
//chown($dataPath."/devis","apache");
//chgrp($dataPath."/devis","apache");
//ATF::devis()->q->reset();
//foreach (ATF::devis()->sa() as $item) {
//	$oldPath = $dataPath."fichier_devis/".$item["id_devis"].".pdf";
//	$newPath = $dataPath."devis/".$item["id_devis"].".fichier_joint";
//	$oldPathScan = $dataPath."fichier_devis/".$item["id_devis"]."_scan.pdf";
//	$newPathScan = $dataPath."devis/".$item["id_devis"].".retourBPA";
//	if (file_exists($oldPath)) {
//		if (!file_exists($newPath)) {
//			rename($oldPath,$newPath);
//			echo "\nid_devis=".$item["id_devis"]."=>".$item["id_devis"]." ok...";
//		} else {
//			echo "\n".$newPath." existe déjà !";
//		}
//		chmod($newPath,0755);
//		chown($newPath,"apache");
//		chgrp($newPath,"apache");
//	} else {
//		echo "\nManque ".$oldPath." !";
//	}
//	if (file_exists($oldPathScan)) {
//		if (!file_exists($newPathScan)) {
//			rename($oldPathScan,$newPathScan);
//			echo "\nid_devisScan=".$item["id_devis"]."=>".$item["id_devis"]." ok...";
//		} else {
//			echo "\n".$newPathScan." existe déjà !";
//		}
//		chmod($newPathScan,0755);
//		chown($newPathScan,"apache");
//		chgrp($newPathScan,"apache");
//	}
//}
//
//echo "\ndemande_refi :\n";
//util::mkdir($dataPath."/demande_refi");
//chmod($dataPath."/demande_refi",0755);
//chown($dataPath."/demande_refi","apache");
//chgrp($dataPath."/demande_refi","apache");
//ATF::demande_refi()->q->reset();
//foreach (ATF::demande_refi()->sa() as $item) {
//	$oldPath = $dataPath."fichier_demande_refi/".$item["id_demande_refi"].".pdf";
//	$newPath = $dataPath."demande_refi/".$item["id_demande_refi"].".fichier_joint";
//	$oldPathScan = $dataPath."fichier_demande_refi/".$item["id_demande_refi"]."_scan.pdf";
//	$newPathScan = $dataPath."demande_refi/".$item["id_demande_refi"].".retourDR";
//	if (file_exists($oldPath)) {
//		if (!file_exists($newPath)) {
//			rename($oldPath,$newPath);
//			echo "\nid_demande_refi=".$item["id_demande_refi"]."=>".$item["id_demande_refi"]." ok...";
//		} else {
//			echo "\n".$newPath." existe déjà !";
//		}
//		chmod($newPath,0755);
//		chown($newPath,"apache");
//		chgrp($newPath,"apache");
//	} else {
//		echo "\nManque ".$oldPath." !";
//	}
//	if (file_exists($oldPathScan)) {
//		if (!file_exists($newPathScan)) {
//			rename($oldPathScan,$newPathScan);
//			echo "\nid_demande_refiScan=".$item["id_demande_refi"]."=>".$item["id_demande_refi"]." ok...";
//		} else {
//			echo "\n".$newPathScan." existe déjà !";
//		}
//		chmod($newPathScan,0755);
//		chown($newPathScan,"apache");
//		chgrp($newPathScan,"apache");
//	}
//}
//
//echo "\nbon_de_commande :\n";
//util::mkdir($dataPath."/bon_de_commande");
//chmod($dataPath."/bon_de_commande",0755);
//chown($dataPath."/bon_de_commande","apache");
//chgrp($dataPath."/bon_de_commande","apache");
//ATF::bon_de_commande()->q->reset();
//foreach (ATF::bon_de_commande()->sa() as $item) {
//	$oldPath = $dataPath."fichier_bon_de_commande/".$item["id_bon_de_commande"].".pdf";
//	$newPath = $dataPath."bon_de_commande/".$item["id_bon_de_commande"].".fichier_joint";
//	$oldPathScan = $dataPath."fichier_bon_de_commande/".$item["id_bon_de_commande"]."_scan.pdf";
//	$newPathScan = $dataPath."bon_de_commande/".$item["id_bon_de_commande"].".pdf";
//	if (file_exists($oldPath)) {
//		if (!file_exists($newPath)) {
//			rename($oldPath,$newPath);
//			echo "\nid_bon_de_commande=".$item["id_bon_de_commande"]."=>".$item["id_bon_de_commande"]." ok...";
//		} else {
//			echo "\n".$newPath." existe déjà !";
//		}
//		chmod($newPath,0755);
//		chown($newPath,"apache");
//		chgrp($newPath,"apache");
//	} else {
//		echo "\nManque ".$oldPath." !";
//	}
//	if (file_exists($oldPathScan)) {
//		if (!file_exists($newPathScan)) {
//			rename($oldPathScan,$newPathScan);
//			echo "\nid_bon_de_commandeScan=".$item["id_bon_de_commande"]."=>".$item["id_bon_de_commande"]." ok...";
//		} else {
//			echo "\n".$newPathScan." existe déjà !";
//		}
//		chmod($newPathScan,0755);
//		chown($newPathScan,"apache");
//		chgrp($newPathScan,"apache");
//	}
//}
//
//
//echo "\nprolongation :\n";
//util::mkdir($dataPath."/prolongation");
//chmod($dataPath."/prolongation",0755);
//chown($dataPath."/prolongation","apache");
//chgrp($dataPath."/prolongation","apache");
//ATF::prolongation()->q->reset();
//foreach (ATF::prolongation()->sa() as $item) {
//	$oldPath = $dataPath."fichier_prolongation/".$item["id_prolongation"].".pdf";
//	$newPath = $dataPath."prolongation/".$item["id_prolongation"].".fichier_joint";
//	if (file_exists($oldPath)) {
//		if (!file_exists($newPath)) {
//			rename($oldPath,$newPath);
//			echo "\nid_prolongation=".$item["id_prolongation"]."=>".$item["id_prolongation"]." ok...";
//		} else {
//			echo "\n".$newPath." existe déjà !";
//		}
//		chmod($newPath,0755);
//		chown($newPath,"apache");
//		chgrp($newPath,"apache");
//	} else {
//		echo "\nManque ".$oldPath." !";
//	}
//}
//
//
//echo "\naffaire :\n";
//util::mkdir($dataPath."/affaire");
//chmod($dataPath."/affaire",0755);
//chown($dataPath."/affaire","apache");
//chgrp($dataPath."/affaire","apache");
//ATF::affaire()->q->reset();
//foreach (ATF::affaire()->sa() as $item) {
//	$oldPath = $dataPath."fichier_facturation/".$item["id_affaire"].".pdf";
//	$newPath = $dataPath."affaire/".$item["id_affaire"].".facturation";
//	if (file_exists($oldPath)) {
//		if (!file_exists($newPath)) {
//			rename($oldPath,$newPath);
//			echo "\nid_affaire=".$item["id_affaire"]."=>".$item["id_affaire"]." ok...";
//		} else {
//			echo "\n".$newPath." existe déjà !";
//		}
//		chmod($newPath,0755);
//		chown($newPath,"apache");
//		chgrp($newPath,"apache");
//	} else {
//		echo "\nManque ".$oldPath." !";
//	}
//}

echo "\ncommande :\n";
util::mkdir($dataPath."/commande");
chmod($dataPath."/commande",0755);
chown($dataPath."/commande","apache");
chgrp($dataPath."/commande","apache");
ATF::commande()->q->reset();
foreach (ATF::commande()->sa() as $item) {
	$oldPathScan = $dataPath."fichier_commande/".$item["id_commande"]."_scan.pdf";
	$newPathScan = $dataPath."commande/".$item["id_commande"].".retour";
	$oldPathScanPV = $dataPath."fichier_commande/".$item["id_commande"]."_pv_scan.pdf";
	$newPathScanPV = $dataPath."commande/".$item["id_commande"].".retourPV";

	if (file_exists($oldPathScanPV)) {
		if (!file_exists($newPathScanPV)) {
			rename($oldPathScanPV,$newPathScanPV);
			echo "\nid_commande=".$item["id_commande"]."=>".$item["id_commande"]." ok...";
		} else {
			echo "\n".$newPathScanPV." existe déjà !";
		}
		chmod($newPathScanPV,0755);
		chown($newPathScanPV,"apache");
		chgrp($newPathScanPV,"apache");
	}
	if (file_exists($oldPathScan)) {
		if (!file_exists($newPathScan)) {
			rename($oldPathScan,$newPathScan);
			echo "\nid_commandeScan=".$item["id_commande"]."=>".$item["id_commande"]." ok...";
		} else {
			echo "\n".$newPathScan." existe déjà !";
		}
		chmod($newPathScan,0755);
		chown($newPathScan,"apache");
		chgrp($newPathScan,"apache");
	}
}


?>