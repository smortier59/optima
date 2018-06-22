<?php

	define("__BYPASS__",true);
	$_SERVER["argv"][1] = "cleodis";
	include(dirname(__FILE__)."/../../../global.inc.php");
	ATF::define("tracabilite",false);

	// RCT
	$matriceFRS = array(
		"DECATHLON"=>20622,
		"OGEA"=>29021,
	);
	$matriceFAB = array(
		"DECATHLON"=>538,
		"OGEA"=>539,
	);
	$matriceSCAT = array(
		"VELO ENFANT"=>202,
		"ASSURANCE"=>36
	);

	// PROD
	// $matriceFRS = array(
	// 	"DECATHLON"=>20622,
	// 	"OGEA"=>29031,
	// );
	// $matriceFAB = array(
	// 	"DECATHLON"=>541,
	// 	"OGEA"=>542
	// );
	// $matriceSCAT = array(
	// 	"VELO ENFANT"=>202,
	// 	"ASSURANCE"=>'NONE'
	// );




	ATF::db()->begin_transaction();
	$fileProduit = "./products-utf8.csv";

	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr);

	$ctInsertProduct=0;
	$ctUpdateProduct=0;
	while ($ligne = fgetcsv($fpr)) {
		if (!$ligne[0]) continue; // pas d'ID pas de chocolat

		$ean = "0".$ligne[4];

		$produit = array(
			"produit"=>$ligne[1],
			"type"=>$ligne[2],
			"ref"=>$ligne[3],
			"ean"=>$ean,
			"id_fournisseur"=>$matriceFRS[$ligne[5]],
			"prix_achat"=>$ligne[6],
			"etat"=>$ligne[7],
			"obsolete"=>$ligne[8],
			"id_fabriquant"=>$matriceFAB[$ligne[9]],
			// "id_categorie"=>$ligne[10],
			"id_sous_categorie"=>$matriceSCAT[$ligne[11]],
			"description"=>$ligne[12],
			"site_associe"=>$ligne[13],
			"loyer"=>$ligne[14],
			"duree"=>$ligne[15],
			"visible_sur_site"=>$ligne[16]
		);

		ATF::produit()->q->reset()->addField('id_produit')->where('ean', $ean);
		if ($id_produit = ATF::produit()->select_cell()) {
			$produit['id_produit'] = $id_produit;
		}

		try {
			if ($id_produit) {
				ATF::produit()->u($produit);
				echo "Produit EAN : ".$produit['ean']." updated\n";
				$ctUpdateProduct++;
			} else {
				$id_produit = ATF::produit()->i($produit);
				echo "Produit EAN : ".$produit['ean']." inserted\n";
				$ctInsertProduct++;
			}

			$listProduit[$ligne[0]] = $id_produit;
		} catch (errorATF $e) {
			ATF::db()->rollback_transaction();
			print_r($produit);
			echo "Produit EAN : ".$produit['ean']."/".$id_produit." ERREUR\n";
			throw $e;
		}

		// print_r($listProduit);
	}
	echo $ctInsertProduct." Produit insérés\n";
	echo $ctUpdateProduct." Produit modifiés\n";

	$filePack = "./packs-utf8.csv";

	$fpa = fopen($filePack, 'rb');
	$entete = fgetcsv($fpa);

	$ctInsertPack=0;
	$ctUpdatePack=0;

	while ($ligne = fgetcsv($fpa)) {

		$pack_produit = array(
			"nom"=>$ligne[1],
			"site_associe"=>$ligne[2],
			"visible_sur_site"=>$ligne[3],
			"frequence"=>$ligne[4],
			"etat"=>$ligne[5],
			// "visible_sur_site"=>$ligne[6],
			"description"=>$ligne[7]
		);

		ATF::pack_produit()->q->reset()->addField('id_pack_produit')->where('nom', addslashes($ligne[1]))->where('site_associe', $ligne[2]);
		if ($id_pack_produit = ATF::pack_produit()->select_cell()) {
			$pack_produit['id_pack_produit'] = $id_pack_produit;
		}

		try {
			if ($id_pack_produit) {
				ATF::pack_produit()->u($pack_produit);
				echo "Pack Produit : ".$pack_produit['nom']." updated\n";
				$ctUpdatePack++;
			} else {
				$id_pack_produit = ATF::pack_produit()->i($pack_produit);
				echo "Pack Produit : ".$pack_produit['ean']." inserted\n";
				$ctInsertPack++;
			}

			$listPack[$ligne[0]] = $id_pack_produit;
		} catch (errorATF $e) {
			ATF::db()->rollback_transaction();
			print_r($pack_produit);
			echo "Pack : ".$pack_produit['nom']."/".$id_pack_produit." ERREUR\n";
			throw $e;
		}

	}

	echo $ctInsertPack." Pack Produit insérés\n";
	echo $ctUpdatePack." Pack Produit modifiés\n";

	$filePackLigne = "./packs-lignes-utf8.csv";

	$fppa = fopen($filePackLigne, 'rb');
	$entete = fgetcsv($fppa);
	$ctInsertPackL=0;
	$ctUpdatePackL=0;

	while ($ligne = fgetcsv($fppa)) {
		$id_pack = $listPack[$ligne[0]];
		$id_produit = $listProduit[$ligne[2]];

		$pack_produit_ligne = array(
			"id_pack_produit"=>$id_pack,
			"id_produit"=>$id_produit,
			"produit"=>$ligne[3],
			"quantite"=>$ligne[4],
			"min"=>$ligne[5],
			"max"=>$ligne[6],
			"option_incluse"=>$ligne[7],
			"option_incluse_obligatoire"=>$ligne[8],
			"visible_sur_pdf"=>$ligne[9],
			"ordre"=>$ligne[10],
			"ref"=>$ligne[11],
			"id_fournisseur"=>$matriceFRS[$ligne[12]],
			"prix_achat"=>$ligne[13],
			"visibilite_prix"=>$ligne[14] == "non" ? "invisible" : "visible"
		);

		ATF::pack_produit_ligne()->q->reset()->addField('id_pack_produit_ligne')->where('id_produit', $id_produit)->where('id_pack_produit', $id_pack);
		if ($id_pack_produit_ligne = ATF::pack_produit_ligne()->select_cell()) {
			$pack_produit_ligne['id_pack_produit_ligne'] = $id_pack_produit_ligne;
		}

		try {
			if ($id_pack_produit_ligne) {
				ATF::pack_produit_ligne()->u($pack_produit_ligne);
				echo "Pack Produit Ligne : ".$pack_produit_ligne['produit']." updated\n";
				$ctUpdatePackL++;
			} else {
				$id_pack_produit_ligne = ATF::pack_produit_ligne()->i($pack_produit_ligne);
				echo "Pack Produit Ligne : ".$pack_produit_ligne['produit']." inserted\n";
				$ctInsertPackL++;
			}

			$listPack[$ligne[0]] = $id_pack_produit_ligne;
		} catch (errorATF $e) {
			ATF::db()->rollback_transaction();
			print_r($pack_produit_ligne);
			echo "Pack : ".$pack_produit_ligne['produit']."/".$id_pack_produit_ligne." ERREUR\n";
			throw $e;
		}


	}
	echo $ctInsertPackL." Pack Produit Ligne insérés\n";
	echo $ctUpdatePackL." Pack Produit Ligne modifiés\n";
	ATF::db()->commit_transaction();
?>
