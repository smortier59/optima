<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../../../global.inc.php");
ATF::define("tracabilite",false);


$type = array(
	"Fixe"=>"fixe",
	"portable"=>"portable",
	"Portable"=>"portable",
	"Sans objet"=>"sans_objet",
	"Immateriel"=>"immateriel"
);

$societes = $contacts = $magasins =  array();


ATF::db()->begin_transaction();

$societes = import_societe();
import_contacts($societes);
import_magasins($societes);

$directory = dirname(__FILE__)."/";



function import_societe(){
	$fileSociete = "societe.csv";
	$fpr = fopen($fileSociete, 'rb');
	$entete = fgetcsv($fs,0 ,";");
	$societes = array();
	try {
		while ($ligne = fgetcsv($fpr,0 ,";")) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat
			$ligne = array_map("utf8_encode", $ligne);

			ATF::societe()->q->reset()->where("siret", $ligne[11]);
			$s = ATF::societe()->select_row();

			$societe = array(
				"societe"=>$ligne[0],
				"nom_commercial"=>$ligne[1],
				"etat"=>strtolower($ligne[2]),
				"adresse"=>$ligne[3],
				"adresse_2" => $ligne[4],
				"cp"=>$ligne[5],
				"ville"=>$ligne[6],
				"latitude"=>$ligne[7],
				"longitude"=>$ligne[8],
				"id_famille"=>2,
				"siren"=>$ligne[10],
				"siret"=>$ligne[11],
				"relation"=>strtolower($ligne[12])
			);



			if($s){
				$societe["id_societe"] = $s["id_societe"];
				ATF::societe()->u($societe);
				$societes[$ligne[11]] = $s["id_societe"];
				echo "Societe mise à jour (societe : ".$ligne[1].") \n";
			}else{
				$societes[$ligne[11]] = ATF::societe()->i($societe);
				echo "Societe inserée (societe : ".$ligne[1].") \n";
			}

		}
		return $societes;
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		//print_r($produit);
		throw $e;
	}
}

function import_contacts($societes){
	$fileContact = "contacts.csv";
	$fpr = fopen($fileContact, 'rb');
	$entete = fgetcsv($fs,0 ,";");
	$contacts = array();
	try {
		while ($ligne = fgetcsv($fpr,0 ,";")) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat
			$ligne = array_map("utf8_encode", $ligne);


			ATF::contact()->q->reset()->where("nom", str_replace("'", "\'", $ligne[1]), "AND")->where("id_societe",$societes[$ligne[0]]);
			$c = ATF::contact()->select_row();

			$contact = array(
				"nom"=>$ligne[1],
				"fonction"=>$ligne[2],
				"etat"=>strtolower($ligne[3]),
				"login"=>$ligne[4],
				"pwd" => hash('sha256',$ligne[5]),
				"id_societe" => $societes[$ligne[0]]
			);


			if($c){
				$contact["id_contact"] = $c["id_contact"];
				ATF::contact()->u($contact);
				echo "Contact mise à jour (contact : ".$ligne[0].") \n";
			}else{
				ATF::contact()->i($contact);
				echo "Contact inserée (contact : ".$ligne[0].") \n";
			}
		}
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		//print_r($produit);
		throw $e;
	}

}

function import_magasins($societes){
	$fileMagasin = "magasins.csv";
	$fpr = fopen($fileMagasin, 'rb');
	$entete = fgetcsv($fs,0 ,";");
	$magasins = array();
	try {
		while ($ligne = fgetcsv($fpr,0 ,";")) {
			if (!$ligne[0]) continue; // pas d'ID pas de chocolat
			$ligne = array_map("utf8_encode", $ligne);


			ATF::magasin()->q->reset()->where("id_societe",$societes[$ligne[0]]);
			$m = ATF::magasin()->select_row();

			$magasin = array(
				"magasin"=>$ligne[1],
				"code"=>$ligne[2],
				"site_associe"=>"bdomplus",
				"id_societe" => $societes[$ligne[0]],
				"statut" => strtolower($ligne[3])
			);

			if($m){
				$magasin["id_magasin"] = $c["id_magasin"];
				ATF::magasin()->u($magasin);
				echo "Magasin mis à jour (magasin : ".$ligne[1].") \n";
			}else{
				ATF::magasin()->i($magasin);
				echo "Magasin inseré (magasin : ".$ligne[1].") \n";
			}
		}
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		//print_r($produit);
		throw $e;
	}

}


ATF::db()->commit_transaction();