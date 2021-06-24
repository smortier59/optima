
<?php
define("__BYPASS__", true);
// Définition du codename

// Import du fichier de config d'Optima
include(dirname(__FILE__) . "/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite", false);

// On verifie que les parametres et environnements sont OK
check_config();

$path = dirname(__FILE__) . "/" . $_SERVER["argv"][2];




// Matrice de type
$type = array(
	"Fixe" => "fixe",
	"portable" => "portable",
	"fans objet" => "sans_objet",
	"immateriel" => "immateriel"
);

$produits = $packs = array();

// Début de transaction SQL
ATF::db()->begin_transaction();
try {

	// On effectue le nettoyages des fichiers pour exclures les produits existants (clé ref et fournisseur déja présent)
	// Retirer les packs et lignes concernés par les produits déja existants
	$data = nettoyage_pack_produit_ligne($path);


	echo "========= DEBUT DE SCRIPT =========\n";
	// Gestion des produits
	$produits = import_produit($data["produits_ok"]);

	// Gestion des packs
	$packs = import_pack($data["packs_ok"]);

	// Ajout des liaison entre les deux
	import_ligne($data["lignes_ok"], $packs, $produits);
} catch (errorATF $e) {
	ATF::db()->rollback_transaction();
	throw $e;
}
ATF::db()->commit_transaction();



echo "========= FIN DE SCRIPT =========\n";

/**
 * Importe des produits depuis un fichier excel
 * @return array Produits insérés
 */

function import_produit($data)
{
	$produits = array();
	try {
		$lines_count = 0;
		$processed_lines = 0;

		foreach ($data as $k => $ligne) {
			$lines_count++;

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
			$ean = /*$ligne[16]*/ NULL;
			$url_image = $ligne[19];
			$eco_tax = $ligne[6];
			$eco_mob = $ligne[7];
			$document_contrat = $ligne[18];
			$visible = strtolower($ligne[15]);

			ATF::produit()->q->reset()->where("ref", $ref, "AND")->where("id_fournisseur", get_fournisseur($raw_Fournisseur));
			$alreadyExistsFromRefFournisseur = ATF::produit()->select_row();
			if ($alreadyExistsFromRefFournisseur) {
				log::logger("Produit " . $ref . "/" . $raw_Fournisseur . " non traité car déjà présent dans la BDD.", "import_" . $_SERVER["argv"][2] . "_escape_product");
			} else {
				$produit = array(
					"site_associe" => $_SERVER["argv"][2],
					"produit" => $product,
					"type"=> mapping_type_produit($rawType),
					"ref" => $ref,
					"ean" => $ean,
					"id_fournisseur" => get_fournisseur($raw_Fournisseur),
					"prix_achat" => $buying_price,
					"taxe_ecotaxe" => $eco_tax,
					"taxe_ecomob" => $eco_mob,
					"etat" => $state,
					"id_fabriquant" => get_fabriquant($raw_vendor),
					"id_sous_categorie" => get_sous_categorie($sub_category, $category),
					"description" => $description,
					"commentaire" => $commentaire,
					"loyer" => $rate,
					"duree" => $term,
					"url_image" => $url_image,
					"visible_sur_site" => $visible
				);
				if ($produit['type'] == "sans objet") $produit['type'] = "sans_objet";
				if ($ligne[0] == "GARANTIE") $produit['description'] = NULL;

				if ($document_contrat) {
					ATF::document_contrat()->q->reset()->where("document_contrat", $document_contrat, false, "LIKE");
					if ($r = ATF::document_contrat()->select_row()) {
						$produit['id_document_contrat'] = $r['id_document_contrat'];
					} else {
						echo "Produit - document contrat (ref : " . $ref . ") NON TROUVE : " . $document_contrat . " \n";
					}
				}

				$produit["id_produit"] = ATF::produit()->i($produit);
				$ligne[0] = $produit["id_produit"];
				$produits[] = $ligne;
				echo "Produit insert (name : " . $product . ", type: " . $produit['type'] . ", ref:" . $ref . ", fournisseur:" . $raw_Fournisseur . ") \n";

				if (strtolower($ligne[0]) == "livraison") {
					util::copy(__DIR__ . "/Livraison01.png", ATF::produit()->filepath($produit["id_produit"], "photo"));
				} else if (strtolower($ligne[0]) == "garantie") {
					util::copy(__DIR__ . "/Garantie01.png", ATF::produit()->filepath($produit["id_produit"], "photo"));
				}

				$processed_lines++;
			}
		}


		log::logger("#####Produits imports",  $_SERVER["argv"][2] . "_migration");
		log::logger("total: $lines_count",  $_SERVER["argv"][2] . "_migration");
		log::logger("imported: $processed_lines",  $_SERVER["argv"][2] . "_migration");

		return $produits;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo "Produit EAN : " . $ref . "/" . $raw_Fournisseur . " ERREUR\n";
		throw $e;
	}
}

/**
 * Importe des packs depuis un fichier excel
 * @return array Packs insérés
 */
function import_pack($data)
{
	$packs = array();
	$lines_count = 0;
	$processed_lines = 0;

	try {

		foreach ($data as $k => $ligne) {
			$lines_count++;

			$nom = $ligne[1];
			$etat = $ligne[2];
			$associated_site = $_SERVER["argv"][2];
			$publicly_visible = $ligne[4];
			$description = $ligne[5];

			ATF::pack_produit()->q->reset()->where("nom", ATF::db()->real_escape_string($nom));
			$p = ATF::pack_produit()->select_row();

			$pack = array(
				"nom" => $nom,
				"etat" => strtolower($etat),
				"site_associe" => $associated_site,
				"visible_sur_site" => strtolower($publicly_visible),
				"description" => $description,
			);

			if ($p) {
				$pack["id_pack_produit"] = $p["id_pack_produit"];
				ATF::pack_produit()->u($pack);
				$packs[$ligne[0]] = array("id_pack_produit" => $p["id_pack_produit"], "raw" => $ligne);
				echo "Pack mis à jour (N° : " . $ligne[0] . ") \n";
			} else {
				$packs[$ligne[0]] = array("id_pack_produit" => ATF::pack_produit()->i($pack), "raw" => $ligne);
				echo "Pack inseré (N° : " . $ligne[0] . ") \n";
			}

			$processed_lines++;
		}

		log::logger("#####Packs imports",  $_SERVER["argv"][2] . "_migration");
		log::logger("total: $lines_count",  $_SERVER["argv"][2] . "_migration");
		log::logger("imported: $processed_lines",  $_SERVER["argv"][2] . "_migration");

		return $packs;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		log::logger($pack, "import_" . $_SERVER["argv"][2]);
		echo "Pack N° : " . $ligne[0] . " ERREUR\n";
		throw $e;
	}
}

/**
 * Importe les liaisons entre les packs et les produits depuis un fichier excel
 * @return array Packs insérés
 */
function import_ligne($lignes_ok, $packs, $produits)
{
	$pack_produit_ligne = array();
	try {

		$lines_count = 0;
		$processed_lines = 0;

		foreach ($lignes_ok as $k => $ligne) {

			$lines_count++;

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
			$fournisseur = $ligne[14];

			$id_pack_produit = $packs[$id]["id_pack_produit"];
			//$id_produit = $produits[$ligne[1]];

			ATF::produit()->q->reset()
				->select('id_produit')
				->select('id_fournisseur')
				->where("ref", ATF::db()->real_escape_string($reference))
				->where("id_fournisseur", get_fournisseur($fournisseur));
			$produit = ATF::produit()->select_row();




			if (!$produit) {
				var_dump($ligne);
				var_dump($produit);
				echo "Produit non trouve non plus dans \$produit ! " . $reference . " => Pack n  " . $ligne[1] . " abandonn  \n";

				ATF::produit()->q->setToString();
				echo ATF::produit()->select_row();

				continue;
				//throw new errorATF("Produit non trouve non plus dans \$produit ! " . $id." => Pack n  ".$ligne[0]." abandonn  \n");
			}


			ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $id_pack_produit)
												 ->where("id_produit", $produit["id_produit"])
												 ->where("id_fournisseur", $produit["id_fournisseur"]);
			$l = ATF::pack_produit_ligne()->select_row();

			// N° Pack;Réf Produit;Quantité;Min;Max;option_incluse;option_incluse_obligatoire;Afficher sur le site;Ordre;Visible;Px achat
			$pack_produit_ligne = array(
				"principal" => $principal,
				"id_pack_produit" => $id_pack_produit,
				"id_produit" => $produit["id_produit"],
				"produit" => ATF::produit()->select($produit["id_produit"], "produit"),
				"quantite" => $quantity,
				"min" => $min,
				"max" => $max,
				"option_incluse" => $are_options_included,
				"option_incluse_obligatoire" => $are_options_included_mandatory,
				"ref" => $reference,
				"prix_achat" => $buying_price,
				"id_fournisseur" => $produit["id_fournisseur"],
				"visible" => $publicly_visible,
				"visible_sur_pdf" => $visible_on_pdf_file,
				"ordre" => $order
			);

			if ($pack_produit_ligne['visible'] == "Lignes de produits") $pack_produit_ligne['visible'] = "oui";
			if ($pack_produit_ligne['visible'] == "Lignes de produits non visible") $pack_produit_ligne['visible'] = "non";

			if ($l) {
				$pack_produit_ligne["id_pack_produit_ligne"] = $l["id_pack_produit_ligne"];
				ATF::pack_produit_ligne()->u($pack_produit_ligne);
				echo "Ligne mise à jour \n";
			} else {
				ATF::pack_produit_ligne()->i($pack_produit_ligne);
				echo "Ligne inserée \n";
			}

			$processed_lines++;
		}

		log::logger("#####Lignes imports",   $_SERVER["argv"][2] . "_migration");
		log::logger("total: $lines_count",   $_SERVER["argv"][2] . "_migration");
		log::logger("imported: $processed_lines",   $_SERVER["argv"][2] . "_migration");
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		print_r($pack);
		echo "Ligne Pack N° : " . $ligne[0] . " ERREUR\n";
		print_r($ligne);
		print_r($pack_produit_ligne);
		log::logger($e, "import_" . $_SERVER["argv"][2]);
		throw $e;
	}
}

/**
 * Permet de gérer le mapping des types de produits, enlève notamment les accent et passe tout en minuscule pour coller aux possibilités de l'ENUM en BDD
 * @param  String $rawType Valeur du type issu du CSV
 * @return String Le type formaté.
 */
function mapping_type_produit($rawType)
{
	$code=htmlentities("$rawType");
	$rawType = preg_replace('/&(.)(.*?);/', '$1', $code);
	return mb_strtolower($rawType, 'UTF-8');
}

/**
 * Récupère le fournisseur depuis un nom
 * @param  String $fournisseur Nom du fournisseur
 * @return Integer|String              ID du fournisseur si existant où un message d'information
 */
function get_fournisseur($ref_fournisseur)
{
	ATF::societe()->q->reset()->where("ref", ATF::db()->real_escape_string($ref_fournisseur), "AND");
	$f = ATF::societe()->select_row();

	if ($f) {
		return $f["id_societe"];
	} else {
		echo "Il faut créer le fournisseur pour la ref" . $ref_fournisseur . "\n";
	}
}

/**
 * Récupère le fabriquant depuis un nom
 * @param  String $fabriquant Nom du fabriquant
 * @return Integer|String              ID du fabriquant si existant où un message d'information
 */

function get_fabriquant($fabriquant)
{
	ATF::fabriquant()->q->reset()->where("fabriquant", ATF::db()->real_escape_string($fabriquant), "AND", false, "LIKE");
	$f = ATF::fabriquant()->select_row();

	if ($f) {
		return $f["id_fabriquant"];
	} else {
		return ATF::fabriquant()->i(array("fabriquant" => $fabriquant));
	}
}

/**
 * Récupère le categorie depuis un nom
 * @param  String $categorie Nom du categorie
 * @return Integer|String              ID du categorie si existant où un message d'information
 */
function get_categorie($categorie)
{
	ATF::categorie()->q->reset()->where("categorie", ATF::db()->real_escape_string($categorie), "AND", false, "LIKE");
	$f = ATF::categorie()->select_row();

	if ($f) {
		return $f["id_categorie"];
	} else {
		return ATF::categorie()->i(array("categorie" => ATF::db()->real_escape_string($categorie)));
	}
}

/**
 * Récupère le sous catégorie depuis un nom
 * @param  String $sous catégorie Nom du sous catégorie
 * @return Integer|String              ID du sous catégorie si existant où un message d'information
 */
function get_sous_categorie($sous_categorie, $categorie)
{
	ATF::sous_categorie()->q->reset()->where("sous_categorie", ATF::db()->real_escape_string($sous_categorie), "AND", false, "LIKE")
		->where("id_categorie", ATF::db()->real_escape_string($categorie), "AND", false);
	$f = ATF::sous_categorie()->select_row();

	if ($f) {
		return $f["id_sous_categorie"];
	} else {
		print_r(array("sous_categorie" => $sous_categorie, "id_categorie" => $categorie));
		return ATF::sous_categorie()->i(array("sous_categorie" => ATF::db()->real_escape_string($sous_categorie), "id_categorie" => $categorie));
	}
}


function clean_produit_existant($path)
{
	$fileProduit = $path;
	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr);
	$produits = array();
	try {
		$lines_count = 0;
		$processed_lines = 0;

		while ($ligne = fgetcsv($fpr, 0, ';')) {
			$lines_count++;

			if (!$ligne[1]) continue; // pas d'ID pas de chocolat

			ATF::produit()->q->reset()->where("ref", $ligne[1], "AND")->where("id_fournisseur", get_fournisseur($ligne[9]));
			$alreadyExistsFromRef = ATF::produit()->select_row();

			if ($alreadyExistsFromRef) {
				echo "Produit Ref --> " . $ligne[1] . " -- Fournisseur --> " . $ligne[9] . " déja présent, on exclu le produit, pack et ligne\n";
				$produits["nok"][$ligne[1]] = true;
			} else {
				$produits["ok"][] = $ligne;
			}
		}

		return $produits;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo "Produit REF/Fournisseur : " . $ligne[1] . "/" . $ligne[9] . " ERREUR\n";
		throw $e;
	}
}


function check_config()
{

	echo "------------------------------------------------------------------------------------\n";
	echo "                     VERIFICATION DE LA CONFIGURATION \n";
	echo "\n\n";

	if (!$_SERVER["argv"][1] || $_SERVER["argv"][1] != "cleodis") {
		echo "\n\n      ##########################################\n";
		echo "      ##                  ERROR    	        ##\n";
		echo "      ##########################################\n\n";
		echo "      Schema " . $_SERVER["argv"][1] . " incorrect, merci de lancer le script php import_pack_tunnel.php cleodis [site_associe]\n\n";
		return;
	} else {
		echo "      CODENAME utilisé pour l'import : " . $_SERVER["argv"][1] . "\n";
	}
	if (!$_SERVER["argv"][2]) {
		echo "      ##########################################\n";
		echo "      ##                  ERROR    	        ##\n";
		echo "      ##########################################\n\n";
		echo "      Parametre site_associe manquant " . $_SERVER["argv"][1] . " incorrect, merci de lancer le script php import_pack_tunnel.php cleodis [site_associe]\n\n";
		return;
	} else {
		echo "      Site Associé utilisé pour l'import : " . $_SERVER["argv"][2] . "\n";
	}



	$path = dirname(__FILE__) . "/" . $_SERVER["argv"][2];

	if (!is_dir($path)) {
		echo "      ##########################################\n";
		echo "      ##                  ERROR    	        ##\n";
		echo "      ##########################################\n\n";
		echo "      Le dossier " . $path . "/ n existe pas !!\n\n";
		return;
	} else {
		echo "      Dossier contenant les fichiers trouvé : " . $path . "/\n\n";

		if ((!file_exists($path . "/produit.csv")) || (!file_exists($path . "/pack.csv")) || (!file_exists($path . "/ligne.csv"))) {

			echo "      ##########################################\n";
			echo "      ##                  ERROR    	        ##\n";
			echo "      ##########################################\n\n";


			if (!file_exists($path . "/produits.csv")) echo "      -- Le fichier des produits n'existe pas " . $path . "/produit.csv\n";
			if (!file_exists($path . "/packs.csv")) echo "      Le fichier des packs n'existe pas " . $path . "/pack.csv\n";
			if (!file_exists($path . "/lignes.csv")) echo "      Le fichier des lignes de pack n'existe pas " . $path . "/ligne.csv\n";
			return;
		} else {
			echo "      Le fichier des produits existe " . $path . "/produit.csv\n";
			echo "      Le fichier des packs existe " . $path . "/pack.csv\n";
			echo "      Le fichier des lignes de pack existe " . $path . "/ligne.csv\n";
		}
	}

	echo "\n";
	echo "                     VERIFICATION DE LA CONFIGURATION  TERMINEE\n";
	echo "------------------------------------------------------------------------------------\n\n";
}


function nettoyage_pack_produit_ligne($path)
{

	echo "************************************************************************************\n";
	echo "                     NETTOYAGE DES FICHIERS \n";
	echo "\n\n";

	$produits_ok_nok = clean_produit_existant($path . "/produit.csv");


	$pack_to_exclude = pack_to_exclude($path."/ligne.csv", $produits_ok_nok);

	$lignes_ok = clean_ligne($path."/ligne.csv" , $pack_to_exclude);
	$packs_ok = clean_pack($path."/pack.csv" , $pack_to_exclude);

	$pack_to_exclude = pack_to_exclude($path . "/ligne.csv", $produits_ok_nok);

	$lignes_ok = clean_ligne($path . "/ligne.csv", $pack_to_exclude);
	$packs_ok = clean_pack($path . "/pack.csv", $pack_to_exclude);


	echo "\n                     NETTOYAGE DES FICHIERS TERMINEE \n";
	echo "************************************************************************************\n";
	echo "\n\n";

	return array(
		"produits_ok" => $produits_ok_nok["ok"],
		"packs_ok" => $packs_ok,
		"lignes_ok" => $lignes_ok
	);
}

function pack_to_exclude($path, $produits_ok_nok)
{
	$pack_to_exclude = $lignes_ok = array();
	$fppa = fopen($path, 'rb');
	$entete = fgetcsv($fppa);

	try {
		$lines_count = 0;
		while ($ligne = fgetcsv($fppa, 0, ';')) {
			$lines_count++;
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			if (isset($produits_ok_nok["nok"][$ligne[2]])) {
				// pack à exclure
				$pack_to_exclude[$ligne[1]] = $produits_ok_nok["nok"][$ligne[2]];
			}
		}

		return $pack_to_exclude;
	} catch (errorATF $e) {
		throw $e;
	}
}

function clean_ligne($path, $pack_to_exclude)
{
	$lignes_ok = array();
	$fppa = fopen($path, 'rb');
	$entete = fgetcsv($fppa);
	try {
		$lines_count = 0;
		while ($ligne = fgetcsv($fppa, 0, ';')) {
			$lines_count++;
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			if (!empty($pack_to_exclude)) {
				if (!array_key_exists($ligne[1], $pack_to_exclude)) {
					$lignes_ok[] = $ligne;
				}
			} else {
				$lignes_ok[] = $ligne;
			}
		}

		return $lignes_ok;
	} catch (errorATF $e) {
		throw $e;
	}
}

function clean_pack($path, $pack_to_exclude)
{
	$pack_ok = array();

	$fppa = fopen($path, 'rb');
	$entete = fgetcsv($fppa);
	try {
		$lines_count = 0;
		while ($ligne = fgetcsv($fppa, 0, ';')) {
			$lines_count++;
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			if (!array_key_exists($ligne[0], $pack_to_exclude)) {
				$pack_ok[] = $ligne;
			} else {
				echo "Le pack " . $ligne[0] . " " . $ligne[1] . " est exclu car produit ref " . $pack_to_exclude[$ligne[0]] . " déja présent\n";
			}
		}

		return $pack_ok;
	} catch (errorATF $e) {
		throw $e;
	}
}
