<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


echo "--------  Batch import READSOFT -------\n";
echo "Le rapport est disponible dans /log/readsoft.log\n";

$dir = __ABSOLUTE_PATH__."www/readsoft/".$_SERVER["argv"][1]."/";

$files = scandir($dir."pending/");
$files_to_move = array();
$report = array("fichier_lu"=>0, "bdc_find"=>0, "bdc_nofind"=>0, "ff_deja_present"=>0, "ff_insert"=>0, "try"=>0, "treated"=>0, "error"=>0);

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


log::logger("#######################################", "readsoft");
log::logger("Rapport Final", "readsoft");
log::logger("Fichier lu dans le dossier www/".$_SERVER["argv"][1]."/pending/ --> ".$report["fichier_lu"], "readsoft");
log::logger("Commandes fournisseur trouvées ".$report["bdc_find"], "readsoft");

log::logger("Factures fournisseur inserées ".$report["ff_insert"], "readsoft");
log::logger("----- Total ".$report["try"], "readsoft");
log::logger("----- Succes ".$report["treated"], "readsoft");
log::logger("----- Error ".$report["error"], "readsoft");
log::logger("---------- Commandes fournisseur introuvables ".$report["bdc_nofind"], "readsoft");
log::logger("---------- Facture Fournisseur déja existante ".$report["ff_deja_present"], "readsoft");
$autre = $report["error"] - ($report["bdc_nofind"] + $report["ff_deja_present"]);
log::logger("---------- Autre ".$autre, "readsoft");

echo "---------------------------------------\n";


?>