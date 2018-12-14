<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);




/*
$type = array(
	"Fixe"=>"fixe",
	"portable"=>"portable",
	"Sans objet"=>"sans_objet",
	"Immateriel"=>"immateriel"
);

$produits = $packs = array();



ATF::db()->begin_transaction();
	$produits = import_produit();
	$packs = import_pack();
	import_ligne($packs, $produits);

ATF::db()->commit_transaction();


$directory = dirname(__FILE__)."/";
$folder_cleodis = dirname(__FILE__)."/../../../../data/cleodis/";

//Copie des images de produit
foreach ($produits as $key => $value) {
    $images = glob($directory . "/produit/".$key.".*");
    if($images[0]){
        if( !copy($images[0], $folder_cleodis."produit/".$value.".photo")){
            echo "Echec de copy de l'image du produit ".$key."\n";
        }
    }
}

//Copie des images de pack
foreach ($packs as $key => $value) {
    $images = glob($directory . "/pack/".$key.".*");
    if($images[0]){
        copy($images[0], $folder_cleodis."pack_produit/".$value.".photo");
    }
}

function import_produit(){
	$fileProduit = "./produit.csv";
	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr);
	$produits = array();
	try {

		while ($ligne = fgetcsv($fpr)) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			$ean = $ligne[13];

			if($ean === "") ATF::produit()->q->reset()->where("ref", $ligne[0]);
			else ATF::produit()->q->reset()->where("ean", $ean,"AND")->where("ref", $ligne[0]);

			$p = ATF::produit()->select_row();

			// Référence;Désignation;Etat;Commentaire;Prix d'achat;Type;Fournisseur;Fabriquant;Catégorie;Sous Catégorie;Loyer;Durée;Visible sur le site;EAN;Description;TYPE

			$produit = array(
				"produit"=>$ligne[1],
				"type"=>$type[$ligne[5]],
				"ref"=>$ligne[0],
				"ean"=>$ean,
				"id_fournisseur"=> get_fournisseur($ligne[6]),
				"prix_achat"=>$ligne[4],
				"etat"=>$ligne[2],
				"id_fabriquant"=>get_fabriquant($ligne[7]),
				//"id_categorie"=>get_categorie($ligne[8]),
				"id_sous_categorie"=>get_sous_categorie($ligne[9], get_categorie($ligne[8])),
				"description"=>$ligne[14],
				"loyer"=>$ligne[10],
				"duree"=>$ligne[11],
				"visible_sur_site"=>"oui"
			);

			// Image spécifique
			$folder_cleodis = __DIR__."/../../../../data/cleodis/";
			if ($ligne[15] == "LIVRAISON") {
		        if( !copy(__DIR__."/Livraison01.png", __DIR__."/produit/".$p["id_produit"].".jpg")){
		            echo "Echec de copy de l'image garantie du produit ".$p["id_produit"]."\n";
		        }
			}

			if ($ligne[15] == "EXTENSION GARANTIE") {
		        if( !copy(__DIR__."/Garantie01.png", __DIR__."/produit/".$p["id_produit"].".jpg")){
		            echo "Echec de copy de l'image garantie du produit ".$p["id_produit"]."\n";
		        }
			}

			if($p){
				$produit["id_produit"] = $p["id_produit"];
				ATF::produit()->u($produit);
				$produits[$ligne[0]] = $p["id_produit"];
				echo "Produit mis à jour (ref : ".$ligne[0].") \n";
			}else{
				$produits[$ligne[0]] = ATF::produit()->i($produit);
				echo "Produit inseré (ref : ".$ligne[0].") \n";
			}


		}

		return $produits;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		//print_r($produit);
		echo "Produit EAN : ".$produit['ean']."/".$ligne[0]." ERREUR\n";
		throw $e;
	}
}

function import_pack(){
	$fileProduit = "./pack.csv";
	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr);
	$packs = array();

	try {

		while ($ligne = fgetcsv($fpr)) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat


			ATF::pack_produit()->q->reset()->where("nom", $ligne[2]);
			$p = ATF::pack_produit()->select_row();


			$pack = array(
				"nom"=>$ligne[2],
				"etat"=>strtolower($ligne[3]),
				"site_associe"=>$ligne[4],
				"visible_sur_site"=>strtolower($ligne[5]),
				"description"=>$ligne[6]
			);

			if($p){
				$pack["id_pack_produit"] = $p["id_pack_produit"];
				ATF::pack_produit()->u($pack);
				$packs[$ligne[0]] = $p["id_pack_produit"];
				echo "Pack mis à jour (N° : ".$ligne[0].") \n";
			}else{
				$produits[$ligne[0]] = ATF::pack_produit()->i($pack);
				echo "Pack inseré (N° : ".$ligne[0].") \n";
			}
		}
		return $packs;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		print_r($pack);
		echo "Pack N° : ".$ligne[0]." ERREUR\n";
		throw $e;
	}
}

function import_ligne($packs, $produits){
	$filePackLigne = "./ligne.csv";

	$fppa = fopen($filePackLigne, 'rb');
	$entete = fgetcsv($fppa);
	try {
		while ($ligne = fgetcsv($fppa)) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			$id_pack_produit = $packs[$ligne[0]];
			$id_produit = $produits[$ligne[1]];

			ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $id_pack_produit)
												 ->where("id_produit", $id_produit);
			$l = ATF::pack_produit_ligne()->select_row();

			// N° Pack;Réf Produit;Quantité;Min;Max;option_incluse;option_incluse_obligatoire;Afficher sur le site;Ordre;Visible;Px achat
			$pack_produit_ligne = array(
				"id_pack_produit"=>$id_pack_produit,
				"id_produit"=>$id_produit,
				"produit"=>ATF::produit()->select($id_produit , "produit"),
				"quantite"=>$ligne[2],
				"min"=>$ligne[3],
				"max"=>$ligne[4],
				"option_incluse"=>$ligne[5],
				"option_incluse_obligatoire"=>$ligne[6],
				"ref"=>$ligne[1],
				"prix_achat"=> $ligne[10],
				"visible"=> $ligne[9],
				"ordre" => $ligne[8]
			);

			if($l){
				$pack_produit_ligne["id_pack_produit_ligne"] = $l["id_pack_produit_ligne"];
				ATF::pack_produit_ligne()->u($pack_produit_ligne);
				echo "Ligne mise à jour \n";
			}else{
				ATF::pack_produit_ligne()->i($pack_produit_ligne);
				echo "Ligne inserée \n";
			}

		}
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		print_r($pack);
		echo "Pack N° : ".$ligne[0]." ERREUR\n";
		throw $e;
	}

}



function get_fournisseur($fournisseur){
	ATF::societe()->q->reset()->where("societe", $fournisseur, "AND", false, "LIKE");
	$f = ATF::societe()->select_row();

	if($f){
		return $f["id_societe"];
	}else{
		echo "Il faut créer le fournisseur ".$fournisseur."\n";
	}
}

function get_fabriquant($fabriquant){
	ATF::fabriquant()->q->reset()->where("fabriquant", $fabriquant, "AND", false, "LIKE");
	$f = ATF::fabriquant()->select_row();

	if($f){
		return $f["id_fabriquant"];
	}else{
		return ATF::fabriquant()->i(array("fabriquant"=>$fabriquant));
	}
}

function get_categorie($categorie){
	ATF::categorie()->q->reset()->where("categorie", $categorie, "AND", false, "LIKE");
	$f = ATF::categorie()->select_row();

	if($f){
		return $f["id_categorie"];
	}else{
		return ATF::categorie()->i(array("categorie"=>$categorie));
	}
}

function get_sous_categorie($sous_categorie, $categorie){
	ATF::sous_categorie()->q->reset()->where("sous_categorie", $sous_categorie, "AND", false, "LIKE");
	$f = ATF::sous_categorie()->select_row();

	if($f){
		return $f["id_sous_categorie"];
	}else{
		print_r(array("sous_categorie"=>$sous_categorie, "id_categorie"=>$categorie));
		return ATF::sous_categorie()->i(array("sous_categorie"=>$sous_categorie, "id_categorie"=>$categorie));
	}
}

$fileProduit = "./produit.csv";
$fpr = fopen($fileProduit, 'rb');
$entete = fgetcsv($fpr);
$produits = array();
try {

	while ($ligne = fgetcsv($fpr)) {
		if (!$ligne[0]) continue; // pas d'ID pas de chocolat

		$ean = $ligne[13];

		if($ean === "") ATF::produit()->q->reset()->where("ref", $ligne[0]);
		else ATF::produit()->q->reset()->where("ean", $ean,"AND")->where("ref", $ligne[0]);

		$p = ATF::produit()->select_row();

		ATF::produit()->u(array("id_produit"=>$p["id_produit"], "commentaire"=>$ligne[3]));

	}

} catch (errorATF $e) {
	ATF::db()->rollback_transaction();
	//print_r($produit);
	echo "Produit EAN : ".$produit['ean']."/".$ligne[0]." ERREUR\n";
	throw $e;
}
*/


$fileProduit = "./produit.csv";
$fpr = fopen($fileProduit, 'rb');
$entete = fgetcsv($fpr);
$produits = array();
try {

	while ($ligne = fgetcsv($fpr)) {
		if (!$ligne[0]) continue; // pas d'ID pas de chocolat

		$ean = $ligne[13];

		if($ean === "") ATF::produit()->q->reset()->where("ref", $ligne[0]);
		else ATF::produit()->q->reset()->where("ean", $ean,"AND")->where("ref", $ligne[0]);
		$p = ATF::produit()->select_row();

		$id_categorie = get_categorie($ligne[8]);
		$id_sous_categorie = get_sous_categorie($ligne[9], $id_categorie);

		$produit = array("id_produit"=>$p["id_produit"],
						 "id_sous_categorie"=>$id_sous_categorie);

		ATF::produit()->u($produit);

	}

} catch (errorATF $e) {
	print_r($produit);
	echo "Produit EAN : ".$ligne[0]." ERREUR\n";
	print_r($e);
	throw $e;
}


function get_categorie($categorie){
	ATF::categorie()->q->reset()->where("categorie", ATF::db()->real_escape_string($categorie), "AND", false, "LIKE");
	$f = ATF::categorie()->select_row();

	if($f){
		return $f["id_categorie"];
	}else{
		return ATF::categorie()->i(array("categorie"=>ATF::db()->real_escape_string($categorie)));
	}
}

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
?>
