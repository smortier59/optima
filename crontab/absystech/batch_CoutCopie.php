<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");




//ATF::db()->begin_transaction();


$cout_copie = ATF::affaire_cout_page()->select_all();

foreach($cout_copie as $key=>$value){

	ATF::contact()->q->reset()->where("id_societe", $value["id_societe"])
							  ->where("etat", "actif")->setLimit(1);

	$contact = ATF::contact()->select_row(); 


	$affaire = array("etat"=>$value["etat"],
					 "date"=> $value["date"],
					 "id_societe"=> $value["id_societe"],
					 "affaire"=> $value["affaire_cout_page"],
					 "forecast"=> $value["forecast"],
					 "id_commercial"=> $value["id_commercial"],
					 "nature"=>"consommable"
				);
	$id_affaire = ATF::affaire()->i($affaire);


	ATF::copieur_contrat()->q->reset()->where("id_affaire_cout_page", $value["id_affaire_cout_page"]);
	$contrat = ATF::copieur_contrat()->select_row();

	if($contrat){
		if($contrat["etat"] == "accepte"){
			$etat = "gagne";
		}elseif($contrat["etat"] == "en_attente"){
			$etat = "attente";
		}else{
			$etat = $contrat["etat"];		
		}


		$devis = array( "ref" => $contrat["ref"],
						"id_societe" => $contrat["id_societe"],
						"id_user" => $contrat["id_user"],
						"type_devis"=>"consommable",
						"date" => $contrat["date"],
						"etat" => $etat,
						"duree_contrat_cout_copie" => $contrat["duree"],					
						"id_affaire" => $id_affaire,
						"tva"=>1.200,
						"id_contact"=> $contact["id_contact"],
						"validite"=> date("Y-m-d" , strtotime($contrat["date"])),
						"resume"=> $value["affaire_cout_page"]
				 );
		$id_devis = ATF::devis()->i($devis);

		if (file_exists(ATF::copieur_contrat()->filepath($contrat["id_copieur_contrat"],"fichier_joint"))) {
			copy(ATF::copieur_contrat()->filepath($contrat["id_copieur_contrat"],"fichier_joint"), ATF::devis()->filepath($id_devis,"fichier_joint"));
		}

		if($etat == "gagne"){
			//On genere la commande
			$commande=array("ref" => $contrat["ref"],
							"id_societe" => $contrat["id_societe"],
							"id_user" => $contrat["id_user"],							
							"date" => $contrat["date"],
							"etat" => "en_cours",					
							"id_affaire" => $id_affaire,
							"tva"=>1.200,							
							"resume"=> $value["affaire_cout_page"],
							"id_devis"=>$id_devis
					 );
			$id_commande = ATF::commande()->i($commande);
			
			if (file_exists(ATF::copieur_contrat()->filepath($contrat["id_copieur_contrat"],"fichier_joint"))) {
				copy(ATF::copieur_contrat()->filepath($contrat["id_copieur_contrat"],"fichier_joint"), ATF::commande()->filepath($id_commande,"fichier_joint"));
			}
			
			if (file_exists(ATF::copieur_contrat()->filepath($contrat["id_copieur_contrat"],"fichier_joint_signe"))) {
				copy(ATF::copieur_contrat()->filepath($contrat["id_copieur_contrat"],"fichier_joint_signe") , ATF::commande()->filepath($id_commande,"fichier_joint2"));
			}


		}

		ATF::copieur_contrat_ligne()->q->reset()->where("id_copieur_contrat", $contrat["id_copieur_contrat"]);
		$lignes_contrat = ATF::copieur_contrat_ligne()->select_all();

		foreach ($lignes_contrat as $kcl => $vcl) {
			$ligne = array("id_devis"=>$id_devis,
						   "produit"=> $vcl["designation"],
						   "quantite"=> $vcl["quantite"],
						   "prix_nb"=> $vcl["prixNB"],	
						   "prix_couleur"=> $vcl["prixC"],	
						   "prix_achat_nb"=> $vcl["prix_achatNB"],	
						   "prix_achat_couleur"=> $vcl["prix_achatC"],	
						   "index_nb"=> $value["releve_initial_NB"],	
						   "index_couleur"=> $value["releve_initial_C"]
					);
			ATF::devis_ligne()->i($ligne);

			if($etat == "gagne"){
				unset($ligne["id_devis"]);
				$ligne["id_commande"] = $id_commande;
				$ligne["prix"] = 0.00;
				ATF::commande_ligne()->i($ligne);
			}
		}

		ATF::copieur_facture()->q->reset()->where("id_affaire_cout_page", $value["id_affaire_cout_page"]);
		$factures = ATF::copieur_facture()->select_all();

		foreach ($factures as $kf => $vf) {			
			$facture = array("ref"=>$vf["ref"],
							 "id_societe"=> $contrat["id_societe"] ,
							 "date"=> $vf["date"],
							 "prix"=> $vf["prix"],
							 "etat"=> $vf["etat"],
							 "id_termes" => $vf["id_termes"],
							 "date_previsionnelle" => $vf["date_previsionnelle"],
							 "date_effective" => $vf["date_effective"],
							 "id_user" => $vf["id_user"],
							 "id_affaire" => $id_affaire,
							 "tva" => $vf["tva"],
							 "date_debut_periode" => $vf["date_debut_periode"],
							 "date_fin_periode" => $vf["date_fin_periode"]
							);
			$id_facture = ATF::facture()->i($facture);

			ATF::copieur_facture_ligne()->q->reset()->where("id_copieur_facture", $vf["id_copieur_facture"]);
			$lignes_facture = ATF::copieur_facture_ligne()->select_all();

			foreach ($lignes_facture as $kfl => $vfl) {
				$ligne = array("id_facture"=>$id_facture,
							   "ref"=>"PRINTER ".$kfl,
							   "produit"=> $vfl["designation"],
							   "quantite"=> $vfl["quantite"],
							   "prix_nb"=> $vfl["prixNB"],	
							   "prix_couleur"=> $vfl["prixC"],	
							   "prix_achat_nb"=> $vfl["prix_achatNB"],	
							   "prix_achat_couleur"=> $vfl["prix_achatC"],	
							   "index_nb"=> $vf["releve_compteurNB"],	
							   "index_couleur"=> $vf["releve_compteurC"],
							   "prix"=>"0.00"
						);
				ATF::facture_ligne()->i($ligne);
				
			}
			
			if (file_exists(ATF::copieur_facture()->filepath($vf["id_copieur_facture"],"fichier_joint"))) {
				copy( ATF::copieur_facture()->filepath($vf["id_copieur_facture"],"fichier_joint") , ATF::facture()->filepath($id_facture,"fichier_joint"));
			}
		}


	}
}

//ATF::db()->rollback_transaction();
