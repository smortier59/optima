
<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cleodis";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);

echo "========= DEBUT DE SCRIPT =========\n";

// Matrice de type
$type = array(
	"fixe" => "fixe",
	"portable" => "portable",
	"fans objet" => "sans_objet",
	"immateriel" => "immateriel"
);

$produits = $packs = array();

// Début de transaction SQL
ATF::db()->begin_transaction();
// Gestion des produits
$produits = import_produit();

// Gestion des packs
$packs = import_pack();

// Ajout des liaison entre les deux
import_ligne($packs, $produits);

// Rollback la transaction
//ATF::db()->rollback_transaction();
// Valide la trnasaction
ATF::db()->commit_transaction();
echo "========= FIN DE SCRIPT =========\n";

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
				log::logger('Skipping EAN/REF found : ' . print_r($alreadyExistsFromRef,true) ." || ". print_r($alreadyExistsFromEan,true), "import_hexamed_escape_product");
				log::logger("Produit ".$ref."/".$ean." non traité car déjà présent dans la BDD.", "import_hexamed_escape_product");
				continue;
			}

			if($ean === "") ATF::produit()->q->reset()->where("ref", $ref);
			else ATF::produit()->q->reset()->where("ean", $ean,"AND")->where("ref", $ref);

			$p = ATF::produit()->select_row();

			$produit = array(
				"site_associe" => 'hexamed',
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


		log::logger("#####Produits imports",  "hexamed_migration");
		log::logger("total: $lines_count",  "hexamed_migration");
		log::logger("imported: $processed_lines",  "hexamed_migration");

		return $produits;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo "Produit EAN : ".$produit['ean']."/".$ligne[0]." ERREUR\n";
		throw $e;
	}
}

/**
 * Importe des packs depuis un fichier excel
 * @return array Packs insérés
 */
function import_pack(){
	$fileProduit = "./pack.csv";
	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr);
	$packs = array();

	$lines_count = 0;
	$processed_lines = 0;

	try {

		while ($ligne = fgetcsv($fpr, 0 ,';')) {
			$lines_count++;

			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			$nom = $ligne[1];
			$etat = $ligne[2];
			$associated_site = $ligne[3];
			$publicly_visible = $ligne[4];
			$description = $ligne[5];

			ATF::pack_produit()->q->reset()->where("nom", ATF::db()->real_escape_string($nom));
			$p = ATF::pack_produit()->select_row();

			$pack = array(
				"nom"=>$nom,
				"etat"=>strtolower($etat),
				"site_associe"=>$associated_site,
				"visible_sur_site"=>strtolower($publicly_visible),
				"description"=>$description,
			);

			if($p){
				$pack["id_pack_produit"] = $p["id_pack_produit"];
				ATF::pack_produit()->u($pack);
				$packs[$ligne[0]] = array("id_pack_produit"=>$p["id_pack_produit"], "raw"=>$ligne);
				echo "Pack mis à jour (N° : ".$ligne[0].") \n";
			}else{
				$packs[$ligne[0]] = array("id_pack_produit"=>ATF::pack_produit()->i($pack), "raw"=>$ligne);
				echo "Pack inseré (N° : ".$ligne[0].") \n";
			}

			$processed_lines++;
		}

		log::logger("#####Packs imports",  "hexamed_migration");
		log::logger("total: $lines_count",  "hexamed_migration");
		log::logger("imported: $processed_lines",  "hexamed_migration");

		return $packs;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		print_r($pack);
		log::logger($pack, "import_hexamed");
		echo "Pack N° : ".$ligne[0]." ERREUR\n";
		throw $e;
	}
}

/**
 * Importe les liaisons entre les packs et les produits depuis un fichier excel
 * @return array Packs insérés
 */
function import_ligne($packs, $produits){

	$filePackLigne = "./ligne.csv";
	$pack_produit_ligne = array();
	$fppa = fopen($filePackLigne, 'rb');
	$entete = fgetcsv($fppa);

	try {

		$lines_count = 0;
		$processed_lines = 0;

		while ($ligne = fgetcsv($fppa, 0, ';')) {

			$lines_count++;

			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			//$principal = $ligne[0]=="PRODUIT PRINCIPAL" ? "oui" : "non";
			$id = $ligne[1];
			$reference = $ligne[2];
			$principal = $ligne[3];
			$quantity = $ligne[4];
			$min = $ligne[5];
			$max = $ligne[6];
			$are_options_included = $ligne[7];
			$are_options_included_mandatory = $ligne[8];
			$publicly_visible = $ligne[9];
			$order = $ligne[10];
			$product_line_visible = $ligne[11];
			$visible_on_pdf_file = $ligne[12];
			$buying_price = $ligne[13];

			$id_pack_produit = $packs[$id]["id_pack_produit"];
			$id_produit = $produits[$ligne[1]];

			ATF::produit()->q->reset()
				->select('id_produit')
				->select('id_fournisseur')
				->where("ref", ATF::db()->real_escape_string($reference));
			$produit = ATF::produit()->select_row();

			if (!$produit) {
				var_dump($ligne);
				var_dump($produit);
				echo "Produit non trouve non plus dans \$produit ! " . $id." => Pack n  ".$ligne[0]." abandonn  \n";
				continue;
				//throw new errorATF("Produit non trouve non plus dans \$produit ! " . $id." => Pack n  ".$ligne[0]." abandonn  \n");
			}

			if (!$id_produit) {
				$id_produit = $produit["id_produit"];
				echo "Produit non trouve ! " . $id." => Pack n°".$ligne[0].", du coup on prend le id_produit=".$id_produit."\n";
				//continue;
			}

			ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $id_pack_produit)
												 ->where("id_produit", $id_produit);
			$l = ATF::pack_produit_ligne()->select_row();

			// N° Pack;Réf Produit;Quantité;Min;Max;option_incluse;option_incluse_obligatoire;Afficher sur le site;Ordre;Visible;Px achat
			$pack_produit_ligne = array(
				"principal"=>$principal,
				"id_pack_produit"=>$id_pack_produit,
				"id_produit"=>$id_produit,
				"produit"=>ATF::produit()->select($id_produit , "produit"),
				"quantite"=>$quantity,
				"min"=>$min,
				"max"=>$max,
				"option_incluse"=>$are_options_included,
				"option_incluse_obligatoire"=>$are_options_included_mandatory,
				"ref"=>$reference,
				"prix_achat"=> $buying_price,
				"id_fournisseur"=> $produit["id_fournisseur"],
				"visible"=> $publicly_visible,
				"visible_sur_pdf"=> $visible_on_pdf_file,
				"ordre" => $order
			);

			if ($pack_produit_ligne['visible']=="Lignes de produits") $pack_produit_ligne['visible']="oui";
			if ($pack_produit_ligne['visible']=="Lignes de produits non visible") $pack_produit_ligne['visible']="non";

			if($l){
				$pack_produit_ligne["id_pack_produit_ligne"] = $l["id_pack_produit_ligne"];
				ATF::pack_produit_ligne()->u($pack_produit_ligne);
				echo "Ligne mise à jour \n";
			}else{
				ATF::pack_produit_ligne()->i($pack_produit_ligne);
				echo "Ligne inserée \n";
			}

			$processed_lines++;
		}

		log::logger("#####Lignes imports",  "hexamed_migration");
		log::logger("total: $lines_count",  "hexamed_migration");
		log::logger("imported: $processed_lines",  "hexamed_migration");

	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		print_r($pack);
		echo "Ligne Pack N° : ".$ligne[0]." ERREUR\n";
		print_r($ligne);
		print_r($pack_produit_ligne);
		log::logger($e, "import_hexamed");
		throw $e;
	}

}

/**
 * Récupère le fournisseur depuis un nom
 * @param  String $fournisseur Nom du fournisseur
 * @return Integer|String              ID du fournisseur si existant où un message d'information
 */
function get_fournisseur($fournisseur){
	ATF::societe()->q->reset()->where("societe", ATF::db()->real_escape_string($fournisseur), "AND", false, "LIKE");
	$f = ATF::societe()->select_row();

	if($f){
		return $f["id_societe"];
	}else{
		echo "Il faut créer le fournisseur ".$fournisseur."\n";
	}
}

/**
 * Récupère le fabriquant depuis un nom
 * @param  String $fabriquant Nom du fabriquant
 * @return Integer|String              ID du fabriquant si existant où un message d'information
 */

function get_fabriquant($fabriquant){
	ATF::fabriquant()->q->reset()->where("fabriquant", ATF::db()->real_escape_string($fabriquant), "AND", false, "LIKE");
	$f = ATF::fabriquant()->select_row();

	if($f){
		return $f["id_fabriquant"];
	}else{
		return ATF::fabriquant()->i(array("fabriquant"=>$fabriquant));
	}
}

/**
 * Récupère le categorie depuis un nom
 * @param  String $categorie Nom du categorie
 * @return Integer|String              ID du categorie si existant où un message d'information
 */
function get_categorie($categorie){
	ATF::categorie()->q->reset()->where("categorie", ATF::db()->real_escape_string($categorie), "AND", false, "LIKE");
	$f = ATF::categorie()->select_row();

	if($f){
		return $f["id_categorie"];
	}else{
		return ATF::categorie()->i(array("categorie"=>ATF::db()->real_escape_string($categorie)));
	}
}

/**
 * Récupère le sous catégorie depuis un nom
 * @param  String $sous catégorie Nom du sous catégorie
 * @return Integer|String              ID du sous catégorie si existant où un message d'information
 */
function get_sous_categorie($sous_categorie, $categorie){
	ATF::sous_categorie()->q->reset()->where("sous_categorie", ATF::db()->real_escape_string($sous_categorie), "AND", false, "LIKE")
									 ->where("id_categorie", ATF::db()->real_escape_string($categorie), "AND", false);
	$f = ATF::sous_categorie()->select_row();

	if($f){
		return $f["id_sous_categorie"];
	}else{
		print_r(array("sous_categorie"=>$sous_categorie, "id_categorie"=>$categorie));
		return ATF::sous_categorie()->i(array("sous_categorie"=>ATF::db()->real_escape_string($sous_categorie), "id_categorie"=>$categorie));
	}
}
