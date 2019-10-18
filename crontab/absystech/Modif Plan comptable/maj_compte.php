
<?php
define("__BYPASS__",true);
// Définition du codename
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);


if(!$_SERVER["argv"][1]){
	echo "Il faut un codename sur lequel executer le script";
}else{
	echo "========= DEBUT DE SCRIPT =========\n";


	// Début de transaction SQL
	ATF::db()->begin_transaction();
	maj_ligne_facture();


	//Rollback la transaction
	//ATF::db()->rollback_transaction();
	// Valide la trnasaction
	ATF::db()->commit_transaction();
	echo "========= FIN DE SCRIPT =========\n";
}


function get_compte_absystech($compte, $champ){
	ATF::compte_absystech()->q->reset()->where("compte_absystech", ATF::db()->real_escape_string($compte));
	$res = ATF::compte_absystech()->select_row();

	return $res[$champ];

}

function maj_ligne_facture(string $path = ''){
	$fileLigne = $path == '' ? "./".$_SERVER["argv"][1].".csv" : $path;
	$fpr = fopen($fileLigne, 'rb');
	$entete = fgetcsv($fpr);
	$lignes = array();
	$change= array();
	echo "societe;ref;date;prix;compte;code;type_facture\n";
	try{
		$lines_count = 0;
		$processed_lines = 0;
		while ($ligne = fgetcsv($fpr, 0, ';')) {
			$lines_count++;

			if (!$ligne[0]) continue; // pas d'ID pas de chocolat


			if(get_compte_absystech($ligne[5], "id_compte_absystech") != get_compte_absystech($ligne[6], "id_compte_absystech")){

				if($ligne[6]){

					$traitement = array("id_facture_ligne" => $ligne[10], "id_compte_absystech"=> get_compte_absystech($ligne[6], "id_compte_absystech") );
					if(!$traitement["id_compte_absystech"]){
						log::logger("|".$ligne[6]."| non trouvé en base" , "erreur_maj_compte_".$_SERVER["argv"][1]);
					}else{
						log::logger($traitement , "erreur_maj_compte_".$_SERVER["argv"][1]);

						ATF::facture_ligne()->u($traitement);

						echo $ligne[0].";".$ligne[1].";".$ligne[2].";".$ligne[9].";".$ligne[6].";".get_compte_absystech($ligne[6], "code").";".$ligne[11]."\n";

						$change[] = $ligne;
					}

				}




			}
		}

		echo "Lignes modifiées : ".count($change)."\n";

	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo "Ligne : ".$lines_count." ERREUR\n";
		throw $e;
	}

}





/**
 * Importe des produits depuis un fichier excel
 * @return array Produits insérés
 */

function import_produit(string $path = ''){
	// If path is not supplied, then get default path
	$fileProduit = $path == '' ? "./produit.csv" : $path;
	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr);
	$produits = array();

	try {

		$lines_count = 0;
		$processed_lines = 0;

		while ($ligne = fgetcsv($fpr, 0, ';')) {

			$lines_count++;

			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			// TYPE;Référence;Désignation;Etat;Commentaire;Prix d'achat dont ecotaxe;EcoTaxe;Eco Mobilier;Type;Fournisseur;Fabriquant;Catégorie;Sous Catégorie;Loyer;Durée;Visible sur le site;EAN;Description;id_document_contrat :;url image


			$ref = $ligne[1];
			$product = $ligne[2];
			$rawType = $ligne[8];
			$raw_Fournisseur = $ligne[9];
			$raw_vendor = $ligne[10];
			$buying_price = $ligne[5];
			$state = $ligne[3];
			$category = get_categorie($ligne[11]);
			$sub_category = $ligne[12];
			$commentaire = $ligne[4];
			$description = $ligne[17];
			$rate = $ligne[13];
			$term = $ligne[14];
			$ean = $ligne[16];
			$url_image = $ligne[19];
			$eco_tax = $ligne[6];
			$eco_mob = $ligne[7];
			$document_contrat = $ligne[18];
			$visible = strtolower($ligne[15]);


			// Check if a given product ref already exists in database
			ATF::produit()->q->reset()->where("ref", $ref);
			$alreadyExistsFromRef = ATF::produit()->select_row();
			// Check if a given product ean already exists in database
			ATF::produit()->q->reset()->where("ean", $ean);
			$alreadyExistsFromEan = ATF::produit()->select_row();

			if ($alreadyExistsFromRef || $alreadyExistsFromEan) {
				log::logger('Skipping EAN/REF found : ' . print_r($alreadyExistsFromRef,true) ." || ". print_r($alreadyExistsFromEan,true), "import_boulangerpro_escape_product");
				log::logger("Produit ".$ref."/".$ean." non traité car déjà présent dans la BDD.", "import_boulangerpro_escape_product");
				continue;
			}

			if($ean === "") ATF::produit()->q->reset()->where("ref", $ref);
			else ATF::produit()->q->reset()->where("ean", $ean,"AND")->where("ref", $ref);

			$p = ATF::produit()->select_row();

			$produit = array(
				"site_associe" => 'boulangerpro',
				"produit"=> $product,
				"type"=> mb_strtolower($rawType, 'UTF-8'),
				"ref"=> $ref,
				"ean"=> $ean,
				"id_fournisseur"=> get_fournisseur($raw_Fournisseur),
				"prix_achat"=> $buying_price,
				"taxe_ecotaxe"=> $eco_tax,
				"taxe_ecomob"=> $eco_mob,
				"etat"=> $state,
				"id_fabriquant"=> get_fabriquant($raw_vendor),
				"id_sous_categorie"=> get_sous_categorie($sub_category, $category),
				"description"=> $description,
				"commentaire"=>$commentaire,
				"loyer"=> $rate,
				"duree"=> $term,
				"url_image"=> $url_image,
				"visible_sur_site"=> $visible
			);

			if ($produit['type']== "sans objet") $produit['type']= "sans_objet";

			if ($ligne[0] == "GARANTIE") {
				$produit['description'] = NULL;
			}

			if ($document_contrat) {
				ATF::document_contrat()->q->reset()->where("document_contrat", $document_contrat, false, "LIKE");
				if ($r = ATF::document_contrat()->select_row()) {
					$produit['id_document_contrat'] = $r['id_document_contrat'];
				} else {
					echo "Produit - document contrat (ref : ".$ref.") NON TROUVE : ".$document_contrat." \n";
				}
			}

			if($p){
				$produit["id_produit"] = $p["id_produit"];
				ATF::produit()->u($produit);
				$produits[$ligne[0]] = $p["id_produit"];
				echo "Produit mis a jour (ref : ".$ligne[0].") \n";
			}else{
				$produit["id_produit"] = ATF::produit()->i($produit);
				$produits[$ligne[0]] = $produit["id_produit"];
				echo "Produit insert (name : ".$product.", type: ".$produit['type'].", ref:".$ref.") \n";
			}

			// Image spécifique
			if (strtolower($ligne[0]) == "livraison") {
				util::copy(__DIR__."/Livraison01.png", ATF::produit()->filepath($produit["id_produit"],"photo"));
			} else if (strtolower($ligne[0]) == "garantie") {
				util::copy(__DIR__."/Garantie01.png", ATF::produit()->filepath($produit["id_produit"],"photo"));
			}
			$processed_lines++;
		}


		log::logger("#####Produits imports",  "boulangerpro_migration");
		log::logger("total: $lines_count",  "boulangerpro_migration");
		log::logger("imported: $processed_lines",  "boulangerpro_migration");

		return $produits;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo "Produit EAN : ".$produit['ean']."/".$ligne[0]." ERREUR\n";
		throw $e;
	}
}
