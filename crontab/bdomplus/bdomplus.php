<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$type = array(
	"Fixe"=>"fixe",
	"portable"=>"portable",
	"Portable"=>"portable",
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
$folder_cleodis = "/home/data/bdomplus/";

//Copie des images de produit
foreach ($produits as $key => $value) {
	echo "produit image : ".$directory . "import/Produits/".$key.".*\n";
    $images = glob($directory . "import/Produits/".$key.".*");
    if($images[0]){
        if( !copy($images[0], $folder_cleodis."produit/".$value.".photo")){
            echo "Echec de copy de l'image du produit ".$key." ".$folder_cleodis."produit/".$value.".photo\n";
        } else $folder_cleodis."produit/".$value.".photo OK\n";
    }
}

//Copie des images de pack
foreach ($packs as $key => $value) {
	echo "pack image : ".$directory . "import/Packs/".$key.".*\n";
    $images = glob($directory . "import/Packs/".$value["raw"][1].".*");
    if($images[0]){
        if (!copy($images[0], $folder_cleodis."pack_produit/".$value["id_pack_produit"].".photo")) {
            echo "Echec de copy de l'image du produit ".$key." ".$folder_cleodis."pack_produit/".$value["id_pack_produit"].".photo\n";
        } else echo $folder_cleodis."pack_produit/".$value["id_pack_produit"].".photo OK\n";
    }
}


function import_produit(){
	$fileProduit = "./import/produits.csv";
	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr,0 ,";");
	$produits = array();
	try {
		while ($ligne = fgetcsv($fpr,0 ,";")) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			ATF::produit()->q->reset()->where("ref", $ligne[0]);
			$p = ATF::produit()->select_row();



			if ($ligne[7]=="Immatériel") $ligne[7]="immateriel";
			elseif ($ligne[7]=="Sans objet") $ligne[7]="sans_objet";
			else $ligne[7] = $type[$ligne[7]];

			$produit = array(
				"ref"=>$ligne[0],
				"produit"=>$ligne[1],
				"etat"=>$ligne[2],
				"commentaire"=>$ligne[3],
				"prix_achat"=>$ligne[4],
				"taxe_ecotaxe"=>$ligne[5],
				"taxe_ecomob"=>$ligne[6],
				"type"=>strtolower($ligne[7]),
				"id_fournisseur"=> get_fournisseur($ligne[8]),
				"id_fabriquant"=>get_fabriquant($ligne[9]),
				//"id_categorie"=>get_categorie($ligne[10]),
				"id_sous_categorie"=>get_sous_categorie($ligne[11], get_categorie($ligne[10])),
				"loyer"=>$ligne[12],
				"duree"=>$ligne[13],
				"visible_sur_site"=>$ligne[14],
				"description"=>$ligne[15]
			);

			if($p){
				$produit["id_produit"] = $p["id_produit"];
				ATF::produit()->u($produit);
				$produits[$ligne[0]] = $p["id_produit"];
				echo "Produit mis à jour (ref : ".$ligne[0].") \n";
			}else{
				$produits[$ligne[0]] = ATF::produit()->i($produit);
				echo "Produit inseré (ref : ".$ligne[0].", type: ".$produit['type'].") \n";
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
	$fileProduit = "import/packs.csv";
	$fpr = fopen($fileProduit, 'rb');
	$entete = fgetcsv($fpr,0 ,";");
	$packs = array();

	try {
		while ($ligne = fgetcsv($fpr,0 ,";")) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			$frequence = array("mensuel"=> "mois", "annuel"=> "an");

			ATF::pack_produit()->q->reset()
				->where("nom", ATF::db()->real_escape_string($ligne[2]))
				->where("frequence", ATF::db()->real_escape_string($frequence[$ligne[7]]))
				->where("prolongation", ATF::db()->real_escape_string($ligne[8]));
			$p = ATF::pack_produit()->select_row();


			$pack = array(
				"nom"=>$ligne[2],
				"etat"=>strtolower($ligne[3]),
				"site_associe"=>$ligne[4],
				"visible_sur_site"=>strtolower($ligne[5]),
				"description"=>$ligne[6],
				"frequence"=>$frequence[$ligne[7]],
				"prolongation"=>$ligne[8]
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

	$filePackLigne = "import/lignes.csv";
	$pack_produit_ligne = array();

	$fppa = fopen($filePackLigne, 'rb');

	$entete = fgetcsv($fppa,0 ,";");

	try {
		while ($ligne = fgetcsv($fppa,0 ,";")) {


			if (!$ligne[0]) continue; // pas d'ID pas de chocolat

			$id_pack_produit = $packs[$ligne[0]]["id_pack_produit"];
			$id_produit = $produits[$ligne[1]];

			ATF::produit()->q->reset()
				->select('id_produit')
				->select('id_fournisseur')
				->where("ref", ATF::db()->real_escape_string($ligne[1]));
			$produit = ATF::produit()->select_row();

			if (!$id_produit) {
				echo "Produit non trouve ! " . $ligne[1]." => Pack n°".$ligne[0]." abandonné\n";
				$id_produit = $produit["id_produit"];
				//continue;
			}

			ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $id_pack_produit)
												 ->where("id_produit", $id_produit);
			$l = ATF::pack_produit_ligne()->select_row();


			log::logger($ligne , "mfleurquin");

			// N° Pack;Réf Produit;Quantité;Min;Max;option_incluse;option_incluse_obligatoire;Afficher sur le site;Ordre;Visible;Px achat
			$pack_produit_ligne = array(
				"id_pack_produit"=>$id_pack_produit,
				"id_produit"=>$id_produit,
				"produit"=>ATF::produit()->select($id_produit , "produit"),
				"id_fournisseur"=> $produit["id_fournisseur"],
				"ref"=>$ligne[1],
				"quantite"=>$ligne[2],
				"min"=>$ligne[3],
				"max"=>$ligne[4],
				"option_incluse"=>$ligne[5],
				"option_incluse_obligatoire"=>$ligne[6],
				"afficher_sur_site"=> $ligne[7],
				"ordre" => $ligne[8],
				"visible"=> $ligne[9],
				"visible_sur_pdf"=> $ligne[10]
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

		}
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		print_r($pack);
		echo "Ligne Pack N° : ".$ligne[0]." ERREUR\n";
		print_r($ligne);
		print_r($pack_produit_ligne);
		throw $e;
	}

}





function get_fournisseur($fournisseur){
	ATF::societe()->q->reset()->where("societe", ATF::db()->real_escape_string($fournisseur), "AND", false, "LIKE");
	$f = ATF::societe()->select_row();

	if($f){
		return $f["id_societe"];
	}else{
		echo "Il faut créer le fournisseur ".$fournisseur."\n";
	}
}

function get_fabriquant($fabriquant){
	ATF::fabriquant()->q->reset()->where("fabriquant", ATF::db()->real_escape_string($fabriquant), "AND", false, "LIKE");
	$f = ATF::fabriquant()->select_row();

	if($f){
		return $f["id_fabriquant"];
	}else{
		return ATF::fabriquant()->i(array("fabriquant"=>$fabriquant));
	}
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