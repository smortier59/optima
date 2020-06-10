
<?php
define("__BYPASS__",true);
// Définition du codename
$_SERVER["argv"][1] = "cleodis";
// Import du fichier de config d'Optima
include(dirname(__FILE__)."/../../../global.inc.php");
// Désactivation de la traçabilité
ATF::define("tracabilite",false);

echo "========= DEBUT DE SCRIPT =========\n";

// Début de transaction SQL
ATF::db()->begin_transaction();


// If path is not supplied, then get default path
$fileSociete = $path == '' ? "./societe_import.csv" : $path;
$fpr = fopen($fileSociete, 'rb');
$entete = fgetcsv($fpr);
$societes = $campagnes = array();


try {

	$lines_count = 0;
	$processed_lines = 0;

	while ($ligne = fgetcsv($fpr, 0, ';')) {

		$lines_count++;

		if (!$ligne[0]) continue; // pas d'ID pas de chocolat

		ATF::campagne()->q->reset()->where("campagne", $ligne[3]);
		$campagne = ATF::campagne()->select_row();

		if($campagne){

			$campagnes[$ligne[3]] = $campagne["id_campagne"];
		} else {
			$campagnes[$ligne[3]] = ATF::campagne()->insert(array("campagne"=> $ligne[3]));
		}

		$societe = array(
			"siret"=> $ligne[0],
			"societe" => $ligne[1],
			"web" => $ligne[2],
			"id_campagne"=> $campagnes[$ligne[3]]
		);

		$id = ATF::societe()->insert($societe);

		if($ligne[4] && $ligne[4] != " "){
			$contact = array("nom"=>$ligne[4], "id_societe"=>$id);
			ATF::contact()->i($contact);
		}
		if($ligne[5] && $ligne[5] != " "){
			$contact = array("nom"=>$ligne[5], "id_societe"=>$id);
			ATF::contact()->i($contact);
		}
		if($ligne[6] && $ligne[6] != " "){
			$contact = array("nom"=>$ligne[6], "id_societe"=>$id);
			ATF::contact()->i($contact);
		}

	}

} catch (errorATF $e) {
	ATF::db()->rollback_transaction();
	echo "Societe  : ".$ligne[1]."/".$ligne[0]." ERREUR\n";
	throw $e;
}




// Rollback la transaction
//ATF::db()->rollback_transaction();
// Valide la trnasaction
ATF::db()->commit_transaction();
echo "========= FIN DE SCRIPT =========\n";