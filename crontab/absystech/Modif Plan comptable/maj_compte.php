
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
	ATF::compte_absystech()->q->reset()->where("compte_absystech", trim(ATF::db()->real_escape_string($compte)));
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
	$non_trouve = array();
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
						log::logger("|".trim($ligne[6])."| non trouvé en base" , "erreur_maj_compte_".$_SERVER["argv"][1]);
						$non_trouve[trim($ligne[6])] = true;
					}else{
						ATF::facture_ligne()->u($traitement);

						echo $ligne[0].";".$ligne[1].";".$ligne[2].";".$ligne[9].";".$ligne[6].";".get_compte_absystech($ligne[6], "code").";".$ligne[11]."\n";

						$change[] = $ligne;
					}
				}
			}
		}
		log::logger($non_trouve  , "erreur_maj_compte_".$_SERVER["argv"][1]);
		echo "Lignes modifiées : ".count($change)."\n";

	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		echo "Ligne : ".$lines_count." ERREUR\n";
		throw $e;
	}

}

