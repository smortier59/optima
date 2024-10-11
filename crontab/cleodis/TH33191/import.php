<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);
ATF::$usr->set('id_user',16);

echo "========= DEBUT DE SCRIPT =========\n";

$fichier = $path == '' ? "./produits.csv" : $path;
$f = fopen($fichier, 'rb');
$lines_count = 0;
$processed_lines = 0;
$update = 0;
$erreur = 0;

while (($ligne = fgetcsv($f, 0, ';'))) {
    if ($lines_count === 0) {
        $lines_count++;
    } else {

        try{
            $produit = [];
            $produit = [
                "id_sous_categorie" => findOrCreateSousCategorie($ligne[2]),
                "ref" => ATF::db()->real_escape_string($ligne[0]),
                "produit" => ATF::db()->real_escape_string($ligne[1]),
                "id_fabriquant" => findOrCreateFabriquant($ligne[3]),
                "commentaire" => ATF::db()->real_escape_string($ligne[4]),
                "prix_achat" => $ligne[5],
                "id_fournisseur" => findSociete($ligne[6])
            ];
            $id_produit = existProduct($produit["ref"], $produit["id_fabriquant"]);
            if ($id_produit !== null) {
                $produit["id_produit"] = $id_produit;
                ATF::produit()->u($produit);
                $update++;
            } else {
                ATF::produit()->i($produit);
                $processed_lines++;
            }
        } catch(errorATF $e) {
            $erreur++;
            echo "-------------------\n";
            print_r($ligne);
            echo $e->getMessage()."\n";
        }
        $lines_count++;
    }
}

echo "Produits insérés : ".$processed_lines." - Mis à jour : ".$update." Total de lignes: ".$lines_count."\n";

echo "========= FIN DE SCRIPT =========\n";

function findOrCreateFabriquant($fabriquant) {
    ATF::fabriquant()->q->reset()->where("fabriquant", $fabriquant);
    $res = ATF::fabriquant()->select_row();
    if ($res) {
        return $res["id_fabriquant"];
    } else {
        return ATF::fabriquant()->i(["fabriquant" => $fabriquant]);
    }
}

function findOrCreateSousCategorie($sousCat) {
    ATF::sous_categorie()->q->reset()->where("sous_categorie", $sousCat);
    $res = ATF::sous_categorie()->select_row();
    if ($res) {
        return $res["id_sous_categorie"];
    } else {
        return ATF::sous_categorie()->i(["id_categorie" => 80, "sous_categorie" => $sousCat]);
    }
}

function existProduct($ref, $fabriquant) {
    ATF::produit()->q->reset()->where("ref", $ref)->where("id_fabriquant", $fabriquant);
    if ($p = ATF::produit()->select_row()) {
        return $p["id_produit"];
    }
    return null;
}

function findSociete($societe) {
    ATF::societe()->q->reset()->where("nom_commercial", $societe);
    $res = ATF::societe()->sa();
    if (count($res) > 0) {
        return $res[0]["id_societe"];
    }
    return null;
}