
<?php

define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

error_reporting(E_ALL);

$logFile = 'cleodis-batch-maj-facture';
$path = './test.csv';

	
log::logger('==================================Initialisation du batch=================================', $logFile);
	

//script de sauvegarde facture 

//sauvegardeFacture();

function sauvegardeFacture(){
	// $file = './sauvegarde_facture_'.date('YmdHis').'.csv';
	$file = './sauvegarde_facture.csv';
    echo "script de sauvegarde de la table facture\n\r";
	touch($file);
	$fp = fopen($file,'w+');
	if (!$fp) {
		echo "Ouverture du fichier ".$file." impossible\n\r";
	} else {
		echo "Ouverture du fichier ".$file." WELL DONE \n\r";

		ATF::facture()->q->reset()->setLimit(5);
		$factures = ATF::facture()->sa();
		
		if ($factures) {
			// print_r($factures);

			$entetes = array_keys($factures[0]);
			echo "Tentative d'insertion des entêtes\n\r";
			print_r($entetes);
			if (!fputcsv($fp, $entetes)) {
				echo "IMPOSSIBLE D'ECRIRE\n\r";
			}
			foreach ($factures as $facture) {
				
				fputcsv($fp, $facture);
			}
		
		}
		fclose($fp);
		
	}

}

log::logger('==================================Fin du script de sauvegarde de la table =================================', $logFile);


// /*
// *import d'un fichier CSV UTF-8  des impayées ,
// *avec les colonnes "numero client", "numero facture","date de rejet","motif rejet"
// */


$fichier_impayees = "./test.csv";

log::logger('Fichier a traiter : '.$fichier_impayees, $logFile);

//import_csv_impayee($fichier_impayees, $logFile);

function import_csv_impayee(string $path = '', $logFile){
	 
	try {

		log::logger("FOPEN du fichier ".$path, $logFile);
		
		$fileFactureImpayee = fopen($path, 'rb');
   
    
    if(!$fileFactureImpayee) {
      echo "Ouverture du fichier ".$fichier_impayees." impossible\n\r";
    }else{

      $entetes = fgetcsv($fileFactureImpayee,0,";");
      log::logger("=== ENTETES ===", $logFile);
      log::logger($entetes, $logFile);

      $lines_count = 1;
      
      $id_facture_csv = [];
      $motifs = [];
      $date_rejet = [];
    
      while ($ligne = fgetcsv($fileFactureImpayee,0,";")) {
        log::logger('Traitement ligne n°'.$lines_count, $logFile);
      
        if(count($lines_count)>2)  continue;

        array_push($motifs,$ligne[9]);
        array_push($date_rejet,$ligne[8]);
        array_push($id_facture_csv,$ligne[0]);
        
        $lines_count++;

    }
    
		foreach($id_facture_csv as $key=>$value){

      ATF::facture()->u(array('id_facture'=>$id_facture_csv[$key],'etat'=>'impayee','rejet'=>$motifs[$key],'date_rejet'=>$date_rejet[$key],'date_paiement'=>NULL,'date_regularisation'=>NULL));
      
      $commande = ATF::facture()->select($id_facture_csv[$key] , "facture.id_commande");

      ATF::commande()->checkEtatContentieux($commande);
      
      log::logger("--> Appel Mauvais payeur" , "mauvais_payeur");

      ATF::societe()->checkMauvaisPayeur(ATF::facture()->select(ATF::facture()->decryptId($id_facture_csv[$key]),'id_societe'));

		}

		fclose($fileFactureImpayee);

    }
		
	} catch (errorATF $e) {
		ATF::db()->rollback_transaction();
		
		throw $e;
	}

   
}

log::logger("==================================Fin du script import d\'un fichier CSV UTF-8  des impayées   =================================", $logFile);


// comparaison fichier XLS et table facture

compareCsvFileAndFactureTable($logFile);

 function compareCsvFileAndFactureTable($logFile){
	
 	/*
 	1°) premier partie : listing csv avec les factures contenues dans optima 
 	qui n'ont pas le meme statut que dans le fichier importé */
	
  log::logger('comparaison de fichier',$logFile);

	//recuperation du fichier importée
  $CsvImport = "./test.csv";
  

  //fichier qui contient les factures qui sont dans optima et qui n'ont pas la méme statut que dans le fichier impayées
  $fsnp = "./facture_non_present.csv";

  $fileOpen = fopen($CsvImport,'rb');

  if(!$fileOpen){
    echo "Ouverture du fichier ".$CsvImport." impossible\n\r";
  }else{
   
    echo "Ouverture du fichier ".$CsvImport." WELL DONE \n\r";

    $statut = [];
    $factures = [];
    $id_facture_csv = [];
    $lines_count=0;


    while ($ligne = fgetcsv($fileOpen,0,";")) {
			
		if(count($lines_count)>2)  continue;

        print_r($ligne[5]);
        
		array_push($statut,$ligne[5]);
		
		array_push($id_facture_csv,$ligne[0]);
			
		$lines_count++;

	}
	
	array_shift($id_facture_csv);
	
	log::logger($id_facture_csv,$logFile);

	foreach($statut as $etat){

		log::logger($etat,$logFile);
	
		ATF::facture()->q->reset()->where('etat',$etat,'AND',false,"!=")->setLimit(5);
		
		$factures = ATF::facture()->sa();
	  }
	
	  log::logger('facture qui nont pas le meme statut',$logFile);
	  log::logger($factures,$logFile);
	  
	  $fsnpOptima = fopen($fsnp,'w+');
	
	  if(!$fsnpOptima){
		echo "Ouverture du fichier ".$fsnp." impossible\n\r";
	  }else{
		echo "Ouverture du fichier ".$fsnp."WELL DONE";
	
		$entete = array('id_facture','type_facture','numero facture','numero client','prix','etat','date','mode_paiement','date_rejet','rejet');

		fputcsv($fsnpOptima,$entete);
	
		//ajouter les factures dans mon fichier csv 
	
		 foreach($factures as $facture){
			//ajout des factures optima qui ne sont pas dans le fichier importé.
			fputcsv($fsnpOptima, $facture);
     }
     
    fclose($fsnpOptima);
   

  }

  
  }

 	/*
	2°) deuxieme partie : listing csv avec les factures presentent dans le fichier importé
	mais qui ne sont pas dans optima*/

  $lscvfnp = "./listing_facture.csv";

  $listingCsv = fopen($lscvfnp,"w");

  if(!$listingCsv){
    echo "Ouverture du fichier ".$lscvfnp." impossible\n\r";
  }else{
    echo "Ouverture du fichier ".$lscvfnp." WELL DONE";

    $id_facture_optima = [];

    ATF::facture()->q->reset();

    foreach(ATF::facture()->sa() as $facture){ 
      array_push($id_facture_optima,$facture['id_facture']);
     
    }

    log::logger("test fgetcsv",$logFile);

    $csvFile = file('./test.csv');
    $data = [];

    foreach ($csvFile as $line) {
        $data[] = str_getcsv($line);
    }

    $factureCsvFile = [];

    foreach($data as $arrayItem){
      print_r($arrayItem);
      foreach ($arrayItem as  $value) {
        array_push($factureCsvFile,explode(";",$value));
      }

      log::logger($factureCsvFile,$logFile);
    }

    array_shift($factureCsvFile);

    $id_factureNonPresentOptima = array_diff($id_facture_csv,$id_facture_optima);

    //log::logger($id_factureNonPresentOptima,$logFile);

    $factureNonPresent = [];

    foreach ($factureCsvFile as $key => $value) {

      foreach($id_factureNonPresentOptima as $result){
          if($value[0]===$result){
             array_push($factureNonPresent,$value);
        }
      }

    }

    log::logger($factureNonPresent,$logFile);

    //insertion des headers dans le fichier contenant les factures non présent sur optima

    log::logger($entete,$logFile);

    fputcsv($listingCsv,$entete);

    //insertion des data dans mon fichier

    foreach ($factureNonPresent as $facture) {
       fputcsv($listingCsv,$facture);
    }

    fclose($listingCsv);
   

  }

 }


 
log::logger("==================================Fin du script de comparaison entre la les factures d'optima et celles fichiers csv  =================================", $logFile);








 
log::logger("==================================Initialisation du script de mise à jours des factures =================================", $logFile);



/*
*Recuperation de tous les factures dont le mode_paiement est egale à prelevement ,
*alors on met à jour le statut en payée et la date_paiement est egale à la date de facture
*/


 ATF::db()->begin_transaction();

 try{

   echo "initialisation du batch de mise à jour des factures";

   ATF::facture()->q->reset()->where('mode_paiement','prelevement');
    
     $idFacturePrelevement = [];
    
     $facturePrelevement = ATF::facture()->sa();
    

    if($facturePrelevement){
      foreach($facturePrelevement as $facture){
       ATF::facture()->u(array('id_facture'=>$facture['id_facture'],'etat'=>'payee','date_paiement'=>$facture['date']));
        
       array_push($idFacturePrelevement,$facture['id_facture']);

      }
    }
    
    foreach($idFacturePrelevement as $id_facture){
      $commande = $this->select($id_facture["id_facture"] , "facture.id_commande");
	    ATF::commande()->checkEtatContentieux($commande);

      log::logger("Appel de la fonction checkMauvaisPayeur" , "dsarr");
      ATF::societe()->checkMauvaisPayeur($this->select($this->decryptId($id_facture["id_facture"]) , "id_societe"));

    }
}catch (errorATF $e){
     ATF::db()->rollback_transaction();
 }
 ATF::db()->commit_transaction();

 /*
 *Recuperation de tous les factures dont le mode_paiement est different de  prelevement ,
 *alors on met à jour le statut en impayée *et la date de paiement à NULL
 */

try{
     ATF::facture()->q->reset()->where('mode_paiement','prelevement','AND',false,"!=");
     $factureInfos = ATF::facture()->select_all();

     $idFacturenonPrelevement = [];
    
     foreach($factureInfos as $facture){
         ATF::facture()->u(array('id_facture'=>$facture['id_facture'],'etat'=>'impayee','date_paiement'=>NULL));
        array_push($idFacturenonPrelevement,$facture);
     }


    foreach($idFacturenonPrelevement as $id_facture){
       
      $commande = $this->select($id_facture["id_facture"] , "facture.id_commande");
 	    ATF::commande()->checkEtatContentieux($commande);

 	    log::logger("--> Appel Mauvais payeur" , "mauvais_payeur");
 	    ATF::societe()->checkMauvaisPayeur($this->select($this->decryptId($id_facture["id_facture"]) , "id_societe"));
    }

 }catch(errorATF $e){
     ATF::db()->rollback_transaction();
 }
 ATF::db()->commit_transaction();


 /*
 *Recuperation de tous les factures dont le statut est impayée , 
 *si la date de paiement est egale à la date de facture,alors on passe la facture en payee
 */

 ATF::facture()->q->reset()->where('etat','impayee');

 $factureImpayee = ATF::facture()->sa();

 foreach($factureImpayee as $facture){
 if($facture['date_paiement'] == $facture['date']){
 		ATF::facture()->u(array('id_facture'=>$facture['id_facture'],'etat'=>'payee'));
	}
 }

/*
*Recuperation de tous les factures dont le statut est impayée , 
*si la date de paiement est egale à la date de facture,
*si la date de regularisation et la date de rejet sont presentes alors on passe la facture en payee.
*/

 foreach($factureImpayee as $facture){
	if($facture['date_paiement'] == $facture['date']){
 		if($facture['date_regularisation'] && $facture['date_rejet']){
 			ATF::facture()->u(array('id_facture'=>$facture['id_facture'],'etat'=>'payee'));
 		}
	}
}


?>