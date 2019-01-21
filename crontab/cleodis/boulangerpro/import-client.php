<?php
	define("__BYPASS__",true);
	$_SERVER["argv"][1] = "cleodis";
	include(dirname(__FILE__)."/../../../global.inc.php");
	ATF::define("tracabilite",false);



	$fileClient = "./client/client-utf8.csv";

	$fpr = fopen($fileClient, 'rb');
	$entete = fgetcsv($fpr);

	ATF::societe()->q->reset()->where("societe", "BOULANGER PRO");
	$apporteur = ATF::societe()->select_row();
	$id_apporteur = $apporteur["id_societe"];

	while ($ligne = fgetcsv($fpr)) {
		if (!$ligne[0]) continue; // pas d'ID pas de chocolat
		try {
			ATF::db()->begin_transaction();
			ATF::societe()->q->reset()->where("SIRET", $ligne[0]);
			if($soc = ATF::societe()->select_row()){
				log::logger("SIRET existant ".$ligne[0]." ( ".$soc["societe"]." )", "soc_existante_import_client");
			}else{
				$cs = ATF::societe()->getInfosFromCREDITSAFE(array("siret"=>$ligne[0]));
				$cs["code_client"] = ATF::societe()->getCodeClient("boulangerpro", "BP");
				$cs["code_client_partenaire"] = $ligne[2];
				$cs["email"] = $ligne[4];
				$cs["id_apporteur"] = $apporteur["id_societe"];
				$cs["siret"] = $ligne[0];

				unset($cs["nb_employe"],$cs["resultat_exploitation"],$cs["capitaux_propres"],$cs["dettes_financieres"],$cs["capital_social"]);

				if($cs["cs_avis_credit"] == "Limite de crédit non applicable") unset($cs["cs_avis_credit"]);
				if($cs["cs_score"] == "Note non disponible") unset($cs["cs_score"]);


				if($cs["gerant"]){
					$gerant = $cs["gerant"];
					unset($cs["gerant"]);
				}
				echo ".";
				$id_soc = ATF::societe()->insert($cs);

				foreach ($gerant as $key => $value) {
					$value["est_dirigeant"] = "oui";
					$value["id_societe"] = $id_societe;
					ATF::contact()->insert($value);
				}

			}
			ATF::db()->commit_transaction();
		} catch (errorATF $e) {
			print_r($e->getMessage());
			log::logger($e->getMessage(), "erreur_import_client");
			log::logger($cs, "erreur_import_client");
			log::logger("----------------------", "erreur_import_client");
			ATF::db()->rollback_transaction();
		}


	}
?>
