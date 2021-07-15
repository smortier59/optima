
<?php

define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

error_reporting(E_ALL);

$logFile = 'cleodis-batch-maj-facture';
$pathFactureImpayesOneShot = "./factureToUpdateFromBT.csv";
// $pathFactureImpayes = ATF::facture()->filepath("gestion_impayee_csv","fichier_joint");


log::logger('==================================Initialisation du batch=================================', $logFile);

ATF::db()->begin_transaction();
try {
  // FIRST STEP : script de sauvegarde facture
  sauvegardeFacture($logFile);

  // SECOND STEP : Modification statut
  statutPayeFacture($logFile);

  // THIRD STEP : Sur base du fichier des impayées, on modifie les infos des factures
  statutImpayeFactureFromCsv($pathFactureImpayesOneShot, $logFile);

  // FOURTH STEP : MAJ Factures
  updateFacturePayeeRejeteSansDateRejetNiRegul($logFile);

} catch (errorATF $e) {
  ATF::db()->rollback_transaction();
  throw $e;
}
// ATF::db()->rollback_transaction();
ATF::db()->commit_transaction();


/**
 * Sauvegarde la table facture dans un fichier csv : sauvegarde_facture.csv, dans le dossier courant
 * @param  string $logFile Fichier de log
 */
function sauvegardeFacture($logFile){
  log::logger('==================================Début de sauvegarde de la table =================================', $logFile);
	// $file = './sauvegarde_facture_'.date('YmdHis').'.csv';
	$file = './sauvegarde_facture.csv';
  log::logger("Fichier de sauvegarde ".$file, $logFile);
	touch($file);
	$fp = fopen($file,'w+');
	if (!$fp) {
		log::logger("Ouverture du fichier ".$file." impossible", $logFile);
	} else {
		log::logger("Ouverture du fichier ".$file." WELL DONE", $logFile);

		ATF::facture()->q->addAllFields("facture")->reset();
    if (__DEV__) {
      log::logger("--- On est en DEV, on limite la sauvegarde a 5 éléments", $logFile);
      ATF::facture()->q->setLimit(5);
    }
		$factures = ATF::facture()->sa();

		if (count($factures)) {
      log::logger("Factures à sauvegarder : ".count($factures), $logFile);
			$entetes = array_keys($factures[0]);
			log::logger("Entêtes", $logFile);
			log::logger($entetes, $logFile);
			if (!fputcsv($fp, $entetes)) {
				echo "IMPOSSIBLE D'ECRIRE\n\r";
			}
			foreach ($factures as $facture) {
				fputcsv($fp, $facture);
			}

		}
		fclose($fp);

	}
  log::logger('==================================Fin de sauvegarde de la table =================================', $logFile);

}

/**
 * Passe les factures en état payés si celle ci sont en état impayées et que leur date correspond à la date de paiement
 * @param  string $logFile Fichier de log
 */
function statutPayeFacture($logFile) {
  log::logger('==================================Début de modification de statut en payée =================================', $logFile);

  $q = "SELECT * FROM facture f
    	WHERE f.etat = 'impayee'";

  $factures = ATF::db()->sql2array($q);

  log::logger('Factures en prélèvement à traiter : '.count($factures), $logFile);

  if(count($factures)){
    foreach($factures as $f){
      log::logger('Passage en état payée de la facture : '.$f['ref'].' ('.$f['id_facture'].')', $logFile);


      ATF::facture()->u(array(
        'id_facture'=>$f['id_facture'],
        'etat'=>'payee',
      ));

      if($f['date_paiement'] == NULL){
        ATF::facture()->u(array(
          'id_facture'=>$f['id_facture'],
          'date_paiement'=>$f['date']
        ));
      }

      if($f['rejet'] != "non_rejet" && $f['date_rejet'] == NULL ){
        ATF::facture()->u(array(
          'id_facture'=>$f['id_facture'],
          'date_rejet'=>$f['date']
        ));
      }

      if($f['rejet'] != "non_rejet" && $f['date_regularisation'] == NULL ){
        ATF::facture()->u(array(
          'id_facture'=>$f['id_facture'],
          'date_regularisation'=>$f['date']
        ));
      }

      if($f['id_commande']){
        log::logger('Appel au checkEtat sur commande '.$f['id_commande'], $logFile);
        ATF::commande()->checkEtatContentieux($f['id_commande']);
      }else{
        echo "Facture ".$f['ref'] . " associé a aucune commande\n";
      }

      if($f['id_societe']){
        log::logger("Appel du checkMauvaisPayeur sur société ".$f["id_societe"], $logFile);
        ATF::societe()->checkMauvaisPayeur($f["id_societe"]);
      }else{
        echo "Facture ".$f['ref'] . " associé a aucun client\n";
      }

    }
  }
  log::logger('==================================Fin de modification de statut en payée =================================', $logFile);
}

/**
 * Passe les factures en état impayés si celle ci sont présente dans le fichier CSV importé
 * @param string $fp Filepath du fichier des factures impayées
 * @param string $logFile Fichier de log
 */
function statutImpayeFactureFromCsv($fp, $logFile) {
  log::logger('==================================Début de modification de statut en impayée =================================', $logFile);

  log::logger("Fichier des impayées ".$fp, $logFile);
  $f = fopen($fp,'r');
  if (!$f) {
    log::logger("Ouverture du fichier ".$file." impossible", $logFile);
  } else {
    log::logger("Ouverture du fichier ".$file." WELL DONE", $logFile);

    // Vérification des colonnes
    $entetes = fgetcsv($f, 0, ",");
    if (count($entetes) != 3) {
      throw new errorATF("Le nombre de colonne est incorrect ".count($entetes)." au lieu de 3");
    }

    $expectedEntetes = array("ref_facture", "date_rejet", "motif_rejet");
    foreach ($entetes as $col => $name) {
      if($name != $expectedEntetes[$col]) {
        $erreurs[] = "Erreur entête colonne ".$col." : Valeur attendu : ".$expectedEntetes[$col]." / Valeur actuelle : ".$name;
        $nb_entete_manquant++;
      }
    }

    if (count($erreurs)) {
      throw new errorATF(implode("<br>\n\r", $erreurs));
    }

    while (($data = fgetcsv($f, 0, ",")) !== FALSE) {
      log::logger("Modification facture ".$data[0]." - Date rejet : ".$data[1]." / Motif rejet : ".$data[2]." / Etat : impayee", $logFile);
      $facture = ATF::facture()->ss("facture.ref", $data[0]);

      ATF::facture()->q->reset()->where("facture.ref", $data[0]);
      $facture = ATF::facture()->select_row();



      ATF::facture()->u(array(
        "id_facture"=>$facture['facture.id_facture'],
        "date_rejet"=>$data[1],
        "date_paiement"=>NULL,
        "date_regularisation"=>NULL,
        "rejet"=>$data[2],
        "etat"=>"impayee"
      ));

      $id_commande = ATF::facture()->select($facture["facture.id_facture"], "id_commande");
      $id_societe = ATF::facture()->select($facture["facture.id_facture"], "id_societe");

      if($id_commande){
        log::logger('Appel au checkEtat sur commande '.$id_commande, $logFile);
        ATF::commande()->checkEtatContentieux($id_commande);
      }else{
        echo "Facture ".$f['ref'] . " associé a aucune commande\n";
      }

      if($id_societe){
        log::logger("Appel du checkMauvaisPayeur sur société ".$id_societe, $logFile);
        ATF::societe()->checkMauvaisPayeur($id_societe);
      }else{
        echo "Facture ".$f['ref'] . " associé a aucun client\n";
      }
    }
    fclose($f);
  }

  log::logger('==================================Fin de modification de statut en impayée =================================', $logFile);
}

function updateFacturePayeeRejeteSansDateRejetNiRegul($logFile){
  log::logger('==================================Début de maj date Facture Payee avec Rejet sans date rejet ni date regul =================================', $logFile);

  $q = "SELECT * FROM facture f
    	WHERE f.etat = 'payee'
      AND rejet != 'non_rejet'
      AND (date_rejet IS NULL OR date_regularisation IS NULL)";

  $factures = ATF::db()->sql2array($q);

  foreach ($factures as $key => $f) {
    if ($f["date_rejet"] === NULL){
      ATF::facture()->u(array(
        'id_facture'=>$f['id_facture'],
        'date_rejet'=>$f["date"]
      ));
    }

    if($f["date_paiement"] && $f["date_regularisation"] === NULL) {
      ATF::facture()->u(array(
        'id_facture'=>$f['id_facture'],
        'date_rejet'=>$f["date_paiement"]
      ));
    }

    if($f['id_commande']){
      log::logger('Appel au checkEtat sur commande '.$f['id_commande'], $logFile);
      ATF::commande()->checkEtatContentieux($f['id_commande']);
    }else{
      echo "Facture ".$f['ref'] . " associé a aucune commande\n";
    }

    if($f['id_societe']){
      log::logger("Appel du checkMauvaisPayeur sur société ".$f["id_societe"], $logFile);
      ATF::societe()->checkMauvaisPayeur($f["id_societe"]);
    }else{
      echo "Facture ".$f['ref'] . " associé a aucun client\n";
    }
  }
}


?>