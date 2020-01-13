<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);



$files_recup = array("xml"=>0 , "pdf"=>0 , "autre"=>0, "error"=>0);
$files_to_move = array();
$report = array("fichier_lu"=>0, "bdc_find"=>0, "bdc_nofind"=>0, "ff_deja_present"=>0, "ff_insert"=>0, "try"=>0, "treated"=>0, "error"=>0);
$dir = __ABSOLUTE_PATH__."www/readsoft/".$_SERVER["argv"][1]."/";



echo "--------  Batch import READSOFT -------\n";
echo "Le rapport est disponible dans /log/readsoft.log\n";

log::logger("======= Début de la récuperation des fichiers via FTP", "readsoft");


// Mise en place d'une connexion basique
$conn_id  = ftp_connect(__READSOFT_FTP_HOST__ );
// Identification avec un nom d'utilisateur et un mot de passe
$login = ftp_login($conn_id ,__READSOFT_FTP_LOGIN__,__READSOFT_FTP_PASS__);
$mode = ftp_pasv($conn_id, TRUE);

// Ouverture du fichier pour écriture

$remote_file = __READSOFT_FTP_FOLDER__.'/FG ACHATS';

if ((!$conn_id ) || (!$login)) {
	log::logger('Echec de connection FTP sur '. __READSOFT_FTP_HOST__ . ' pour utilisateur '.__READSOFT_FTP_LOGIN__.'.', "readsoft");
}else{
	log::logger('Connection FTP réussie.', "readsoft");
	$fichiers_ftp_readsoft = ftp_nlist ($conn_id, $remote_file );
	log::logger("Lecture du repertoire ".$remote_file , "readsoft");
	log::logger($fichiers_ftp_readsoft , "readsoft");

	if (!in_array(__READSOFT_FTP_FOLDER__.'/move', ftp_nlist($conn_id, __READSOFT_FTP_FOLDER__)) ) {
		log::logger('Le dossier '.__READSOFT_FTP_FOLDER__."/move n'existe pas, on le crée", "readsoft");
		ftp_mkdir($conn_id, __READSOFT_FTP_FOLDER__."/move");
	}
	if (!in_array(__READSOFT_FTP_FOLDER__.'/erreur', ftp_nlist($conn_id, __READSOFT_FTP_FOLDER__)) ) {
		log::logger('Le dossier '.__READSOFT_FTP_FOLDER__."/erreur n'existe pas, on le crée", "readsoft");
		ftp_mkdir($conn_id, __READSOFT_FTP_FOLDER__."/erreur");
	}


	if(!empty($fichiers_ftp_readsoft)){
		$total = count($fichiers_ftp_readsoft);
		foreach ($fichiers_ftp_readsoft as $key => $value) {
			log::logger("##" , "readsoft");

			$handle = fopen($dir."pending".str_replace($remote_file, "", $value), 'w');
			// Tente de téléchargement le fichier $remote_file et de le sauvegarder dans repertoire local
			if (ftp_fget($conn_id, $handle, $value, FTP_ASCII, 0)) {
				log::logger("Copie du fichier vers ".$dir."pending".str_replace($remote_file, "", $value)." réussie", "readsoft");
				if( preg_match('/(\w*)\.(xml|XML)/', $value)){
					$files_recup["xml"] ++;
				}elseif( preg_match('/(\w*)\.(pdf|PDF)/', $value)){
					$files_recup["pdf"] ++;
				}else{
					$files_recup["autre"] ++;
				}

				// Tentative de renommage de fichier sur le FTP
				if (ftp_rename($conn_id, $value, __READSOFT_FTP_FOLDER__."/move".str_replace($remote_file, "", $value))) {
					log::logger("Copie du fichier sur FTP vers ".__READSOFT_FTP_FOLDER__."/move".str_replace($remote_file, "", $value)." réussie", "readsoft");
				} else {
					log::logger("Problème lors du Copie de ".$value." en ".__READSOFT_FTP_FOLDER__."/move".str_replace($remote_file, "", $value), "readsoft");
				}

			} else {
				// Tentative de renommage de $old_file en $new_file
				if (ftp_rename($conn_id, $value, __READSOFT_FTP_FOLDER__."/erreur".str_replace($remote_file, "", $value))) {
					log::logger("Copie du fichier sur FTP vers ".__READSOFT_FTP_FOLDER__."/erreur".str_replace($remote_file, "", $value)." réussie", "readsoft");
				} else {
					log::logger("Problème lors du Copie de ".$value." en ".__READSOFT_FTP_FOLDER__."/erreur".str_replace($remote_file, "", $value), "readsoft");
				}
				log::logger("Il y a un problème lors du téléchargement du fichier ".$value." vers ".__READSOFT_FTP_FOLDER__."/erreur".str_replace($remote_file, "", $value), "readsoft");
				$files_recup["error"] ++;

			}
			fclose($handle);
		}
	}else{
		$total = 0;
	}

}
// Fermeture de la connexion et du pointeur de fichier
ftp_close($conn_id);

log::logger("####################    RECAP DE RECUPERATION DES FICHIERS VIA FTP", "readsoft");
log::logger("##  XML : ".$files_recup["xml"], "readsoft");
log::logger("##  PDF : ".$files_recup["pdf"], "readsoft");
log::logger("##  AUTRE : ".$files_recup["autre"], "readsoft");
log::logger("##  SUCCES : ".($files_recup["xml"] + $files_recup["pdf"] + $files_recup["autre"]), "readsoft");
log::logger("##  ERREUR : ".$files_recup["error"], "readsoft");
log::logger("##  TOTAL : ".$total,  "readsoft");
log::logger("####################", "readsoft");




log::logger("======= Fin de la récuperation des fichiers via FTP", "readsoft");



log::logger("======= Analyse du dossier pending ".$dir."pending/", "readsoft");

$files = scandir($dir."pending/");


log::logger("======= Analyse des fichiers", "readsoft");
log::logger("== ".count($files)." fichiers présent dans pending", "readsoft");
if(!empty($files)){


	foreach ($files as $key => $value) {
		//On analyse uniquement les fichier .XML
		if( preg_match('/(\w*)\.(xml|XML)/', $value)){
			$report["fichier_lu"] += 1;

			$xml = simplexml_load_file($dir."pending/".$value);
			$id_doc = (string)$xml->Batch->Documents->Document->Id[0];
			$headers = $xml->Batch->Documents->Document->HeaderFields;
			$lines = $xml->Batch->Documents->Document->Tables->Table->TableRows;

			$commande_fournisseur_concernee = array();
			log::logger("Document ID -> ".$id_doc,$id_doc.".log");
			log::logger("####################################",$id_doc.".log");
			log::logger("HEADERS",$id_doc.".log");
			$header = array();
			foreach ($headers as $kh => $vh) {
				$header = array();
				foreach ($vh->HeaderField as $khf => $vhf) {
					$header[(string)$vhf->Type[0]] = (string)$vhf->Text[0];
				}
			}
			log::logger($header,$id_doc.".log");
			log::logger("####################################",$id_doc.".log");
			log::logger("LINES",$id_doc.".log");

			foreach ($lines as $kl => $vl) {
				foreach ($vl->TableRow as $ki => $vi) {
					$item = array();

					foreach ($vi->ItemFields->ItemField as $kif => $vif) {
						$item[str_replace("LIT_", "", $vif->Type[0])] = (string)$vif->Text[0];
					}

					$commande_fournisseur_concernee[$item["OrderNumber"]]["lines"][] = $item;
				}
			}

			log::logger($commande_fournisseur_concernee,$id_doc.".log");
			if(!empty($commande_fournisseur_concernee)){
				foreach ($commande_fournisseur_concernee as $kcf => $vcf) {
					ATF::bon_de_commande()->q->reset()->where("bon_de_commande.ref", $kcf);
					$cm = ATF::bon_de_commande()->select_row();

					log::logger("====================================" , $id_doc.".log");

					if($cm["bon_de_commande.id_bon_de_commande"]){
						$report["bdc_find"] += 1;

						ATF::bon_de_commande_ligne()->q->reset()->where("id_bon_de_commande", $cm["bon_de_commande.id_bon_de_commande"]);
						$ligne_facture_fournisseur = ATF::bon_de_commande_ligne()->toFacture_fournisseurLigne();

						$cm = ATF::bon_de_commande()->select($cm["bon_de_commande.id_bon_de_commande"]);

						log::logger("Commande fournisseur ".$header["invoicenumber"]." trouvée" , $id_doc.".log");

						ATF::facture_fournisseur()->q->reset()->where("id_affaire",  $cm["id_affaire"],"AND")
															  ->where("facture_fournisseur.ref", $header["invoicenumber"]);
						$ff_exist = ATF::facture_fournisseur()->select_all();

						if(sizeof($ff_exist) == 0){
							$report["try"] += 1;

							log::logger("Il n'y a pas encore de facture fournisseur existante avec la ref ".$header["invoicenumber"]." pour l'affaire ".$cm["id_affaire"] , $id_doc.".log");

							$infos = array("extAction" => "facture_fournisseur",
										   "extMethod" => "insert",
										   "import_readsoft" => true);

							$prix = 0;
							foreach ($vcf["lines"] as $kp => $vp) {
								$prix += $vp["VatExcludedAmount"];
							}
							try {
								$infos["facture_fournisseur"] = array(
															"ref" => $header["invoicenumber"],
															"id_bon_de_commande" => $cm["id_bon_de_commande"],
															"type" => "achat",
															"id_affaire" =>  $cm["id_affaire"],
															"id_fournisseur" =>  $cm["id_fournisseur"],
															"periodicite" => null,
															"prix" => $prix,
															"etat" => "impayee",
															"tva" => $cm["tva"],
															"date" => $header["invoicedate"],
															"date_echeance" => $header["invoiceduedate"],
															"deja_exporte_cegid" => "non"
														);
								$values_facture_fournisseur = array();
								foreach ($ligne_facture_fournisseur["data"] as $kbdcl => $vbdcl) {
									foreach ($vbdcl as $k => $v) {
										$values_facture_fournisseur[$kbdcl][str_replace(".", "__dot__", $k)] = $v;
									}
								}
								$infos["values_facture_fournisseur"]["produits"] = json_encode( $values_facture_fournisseur );

								$id_ff = ATF::facture_fournisseur()->insert($infos);
								$report["ff_insert"] += 1;

								log::logger("Facture fournisseur créée ".$di_ff , $id_doc.".log");

								$dir_to = "treated";

								log::logger('Création du ZIP', $id_doc.".log");
								$zip = new ZipArchive();
								if($zip->open($dir."pending/".$id_doc.'.zip', ZipArchive::CREATE) === true){
									// Ajout d’un fichier.
									$zip->addFile($dir."pending/".$id_doc.".pdf", $id_doc.".pdf");

									// Et on referme l'archive.
									$zip->close();
									rename($dir."pending/".$id_doc.".zip", __DATA_PATH__.$_SERVER["argv"][1]."/facture_fournisseur/".$id_ff.".fichier_joint");
									log::logger('Enregistrement du ZIP en fichier joint sur la facture fournisseur créée ID '.$id_ff.".fichier_joint" , $id_doc.".log");
									unlink($dir."pending/".$id_doc.".zip");
								}else{
									log::logger('Impossible d\'ouvrir &quot'.$dir."pending/".$id_doc.'.zip', $id_doc.".log");
								}
							} catch (Exception $e) {
								log::logger("---- Erreur sur le fichier ".$id_doc, "readsoft");
								log::logger($e->getmessage(), "readsoft");

								log::logger($e->getmessage(), $id_doc.".log");
								log::logger($infos, $id_doc.".log");
								$dir_to = "error";
								$report[$dir_to] += 1;
							}

						}else{
							log::logger("Facture fournisseur déja existante pour ref ".$header["invoicenumber"]." - ID Affaire ".$cm["id_affaire"]." - BDC ".$kcf ,$id_doc.".log");
							$dir_to = "error";
							$report["ff_deja_present"] += 1;
						}
					}else{
						$report["bdc_nofind"] += 1;
						log::logger("Bon de commande non trouvé pour ref ".$kcf,$id_doc.".log");
						$dir_to = "error";
					}
					$report[$dir_to] += 1;
					$files_to_move[$dir_to][$id_doc] = true;
				}
			}else{
				log::logger("Pas de commande fournisseur concernée",$id_doc.".log");
				log::logger($lines,$id_doc.".log");
				log::logger("Pas de commande fournisseur concernée ".$id_doc, "readsoft");
				$dir_to = "error";
				$report[$dir_to] += 1;
				$files_to_move[$dir_to][$id_doc] = true;
			}


			log::logger("####################################",$id_doc.".log");
		}
	}

	// On déplace les fichiers dans le bon dossier
	foreach ($files_to_move as $dir_to => $file_array) {
		foreach ($file_array as $id_file => $value) {
			copy($dir."pending/".$id_file.".xml", $dir.$dir_to."/".$id_file.".xml");
			copy($dir."pending/".$id_file.".pdf", $dir.$dir_to."/".$id_file.".pdf");
			copy($dir."../../../log/".$id_file.".log", $dir.$dir_to."/".$id_file.".log");
		}
	}

	//on supprime les fichiers pending et log
	foreach ($files_to_move as $dir_to => $file_array) {
		foreach ($file_array as $id_file => $value) {
			unlink($dir."pending/".$id_file.".xml");
			unlink($dir."pending/".$id_file.".pdf");
			unlink($dir."../../../log/".$id_file.".log");
		}
	}


	log::logger("####################    RAPPORT FINAL", "readsoft");
	log::logger("Fichier lu dans le dossier www/".$_SERVER["argv"][1]."/pending/ --> ".$report["fichier_lu"], "readsoft");
	log::logger("Commandes fournisseur trouvées ".$report["bdc_find"], "readsoft");

	log::logger("Factures fournisseur inserées ".$report["ff_insert"], "readsoft");
	log::logger("##  Total ".$report["try"], "readsoft");
	log::logger("##  Succes ".$report["treated"], "readsoft");
	log::logger("##  Error ".$report["error"], "readsoft");
	log::logger("#### Commandes fournisseur introuvables ".$report["bdc_nofind"], "readsoft");
	log::logger("#### Facture Fournisseur déja existante ".$report["ff_deja_present"], "readsoft");
	$autre = $report["error"] - ($report["bdc_nofind"] + $report["ff_deja_present"]);
	log::logger("#### Autre ".$autre, "readsoft");

}

log::logger("======= Fin de l'analyse du dossier pending ".$dir."pending/", "readsoft");

log::logger("---------------------------------------", "readsoft");


?>