<?
class facturation_fournisseur extends classes_optima {
	function __construct($table_or_id=NULL) {
		$this->table="facturation_fournisseur";
		parent::__construct($table_or_id);
		$this->colonnes['fields_column'] = array(
			  'facturation_fournisseur.id_fournisseur'
			 ,'facturation_fournisseur.id_affaire'
			 ,'facturation_fournisseur.id_facture_fournisseur'
			 ,'facturation_fournisseur.date_periode_debut'
			 ,'facturation_fournisseur.date_periode_fin'
			 ,'facturation_fournisseur.montant'
			 ,'facturation_fournisseur.envoye'
		);
		$this->fieldstructure();

		$this->onglets = array(
			 'facturation_fournisseur_detail'
		);

		$this->foreign_key["id_fournisseur"] = "societe";
	}

	/**
	* Génère les factures d'achat groupées par fournisseur pour une affaire qui vient d'être créée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param object $affaire
	*/
	public function createFactureFournisseurAchat($affaire) {
		/*$commande = $affaire->getCommande();


		// Récupérer les lignes de commande
		ATF::commande_ligne()->q->reset()
			->addField("id_produit")
			->select("id_fournisseur")
			->select("quantite")
			->where("id_commande",$commande->get("id_commande"));
		$lignes_commande = ATF::commande_ligne()->sa();

		// Pour chaque ligne, récupérer le montant d'achat correspondant sur le departement de l'adresse de livraison
		$departement = ATF::db()->escape_string(substr($affaire->get("cp_adresse_livraison"),0,2));

		$factures = array();

		foreach ($lignes_commande as $ligne_commande) {

			// Trouver les loyers du fournisseur du produit et du département concerné
			ATF::produit_fournisseur()->q->reset()
				->where('produit_fournisseur.id_fournisseur',$ligne_commande['id_fournisseur'])
				->where('produit_fournisseur.recurrence','achat')
				->where('produit_fournisseur.prix_prestation',0,"AND",false,'>')
				->where('produit_fournisseur.id_produit',$ligne_commande['id_produit'])
					->andWhere('produit_fournisseur.departement','(^|,)'.$departement.'($|,)','dep','REGEXP')
					->whereIsNull('produit_fournisseur.departement','OR','dep');
			if ($fournisseur_achat = ATF::produit_fournisseur()->sa()) {
				// Boucle de calcul de facture d'achat (
				//$echeance_courante = strtotime($commande->getDateDebut());
				foreach ($fournisseur_achat as $achat) {
					$factures[$achat["id_fournisseur"]][] = array("achat" => $achat , "ligne_commande"=>$ligne_commande);

				}
			}
		}

		if($factures){
			$id_societe = $commande->get("id_societe");
			$id_affaire = $commande->get("id_affaire");
			$aff = ATF::affaire()->select($id_affaire);
			$i=1;
			foreach ($factures as $key => $value) {

				$fournisseur = "";
				$bdc_achat = $bdc_ligne_achat = array();

				switch($value[0]["achat"]["id_fournisseur"]){
					case "1" : $fournisseur="LMA";
					break;
					case "2" : $fournisseur="LMF";
					break;
					case "3" : $fournisseur="MVAD";
					break;
					case "11" : $fournisseur="SPEF";
					break;
					case "12" : $fournisseur="BDOM";
					break;
					default : $fournisseur= ATF::societe()->select($value[0]["achat"]["id_fournisseur"],"societe");
					break;
				}

				//Il faut créer le bon_de_commande
				$bdc_achat = array( "ref" => $fournisseur."-".$aff["ref"]."-".$i,
							  "num_bdc" => NULL,
							  "id_societe"=> $id_societe,
							  "id_fournisseur" => $value[0]["achat"]["id_fournisseur"],
							  "bon_de_commande" => $aff["affaire"],
							  "prix" => 0,
							  "id_affaire"=> $id_affaire,
							  "etat"=>"fnp",
							  "date"=>$commande->get("date_debut"),
							  "id_user"=>ATF::$usr->get('id_user'),
							  "id_commande"=>$commande->get("id_commande"),
							  "destinataire"=> ATF::societe()->select($id_societe, "nom")." ".ATF::societe()->select($id_societe, "prenom"),
							  "adresse" => $aff["adresse_facturation"],
							  "adresse_2" => $aff["adresse_facturation_2"],
							  "adresse_3" => $aff["adresse_facturation_3"],
							  "cp" => $aff["cp_adresse_facturation"],
							  "ville" => $aff["ville_adresse_facturation"],
							  "id_pays" => $aff["pays_facturation"],
							  "livraison_destinataire"=> ATF::societe()->select($id_societe, "nom")." ".ATF::societe()->select($id_societe, "prenom"),
							  "livraison_adresse" => $aff["adresse_livraison"],
							  "livraison_cp" => $aff["cp_adresse_livraison"],
							  "livraison_ville" => $aff["ville_adresse_livraison"],
							  "tva" => 1.200
							);

				$id_bdc = ATF::bon_de_commande()->i($bdc_achat);

				foreach ($value as $klignes => $vlignes) {
					$bdc_ligne_achat = array();
					$bdc_achat["prix"] += ($vlignes["ligne_commande"]["quantite"] * $vlignes["achat"]["prix_prestation"]);
					$bdc_ligne_achat = array("id_bon_de_commande" => $id_bdc,
										"ref" => ATF::produit()->select($vlignes["achat"]["id_produit"], "ref_lm"),
										"produit" => ATF::produit()->select($vlignes["achat"]["id_produit"], "produit"),
										"quantite" => $vlignes["ligne_commande"]["quantite"],
										"prix" => $vlignes["achat"]["prix_prestation"],
										"id_commande_ligne" => $vlignes["ligne_commande"]["commande_ligne.id_commande_ligne"]
										);
					log::logger($bdc_ligne_achat , "mfleurquin");
					ATF::bon_de_commande()->u(array("id_bon_de_commande"=> $id_bdc, "prix"=> $bdc_achat["prix"]));
					ATF::bon_de_commande_ligne()->i($bdc_ligne_achat);
				}
				//Créer la facture fournisseur par la suite

				$i++;
			}
		}*/
	}

	/**
	* Création de l'échéancier de toutes les factures fournisseur attendues des loyers de factures fournisseur d'une affaire
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param object $affaire
	* @param array $facturation
	* 			date $facturation[date_debut]
	* 			date $facturation[date_fin]
	*/
	public function createEcheancier($affaire/*,$nature="engagement"*/) {
		$commande = $affaire->getCommande();

		// Récupérer les lignes de commande
		ATF::commande_ligne()->q->reset()
			->addField("id_produit")
			->select("id_fournisseur")
			->select("quantite")
			->where("id_commande",$commande->get("id_commande"));
		$lignes_commande = ATF::commande_ligne()->sa();

		// Pour chaque ligne, récupérer le loyer correspondant sur le departement de l'adresse de livraison
		$departement = ATF::db()->escape_string(substr($affaire->get("cp_adresse_livraison"),0,2));

		$mapping_periodicites = array("mois"=>1, "trimestre"=>3, "semestre"=>6, "an"=>12);

		foreach ($lignes_commande as $ligne_commande) {

			// Trouver les loyers du fournisseur du produit et du département concerné
			ATF::produit_fournisseur_loyer()->q->reset()
				->where('produit_fournisseur_loyer.id_fournisseur',$ligne_commande['id_fournisseur'])
				->where('produit_fournisseur_loyer.id_produit',$ligne_commande['id_produit'])
					->andWhere('produit_fournisseur_loyer.departement','(^|,)'.$departement.'($|,)','dep','REGEXP')
					->whereIsNull('produit_fournisseur_loyer.departement','OR','dep')
				//->where("produit_fournisseur_loyer.nature","engagement")
				->addOrder('ordre','asc');
			if ($fournisseur_loyer = ATF::produit_fournisseur_loyer()->sa()) {
				// Boucle de calcul de chaque échéances (cumul de chaque loyers pour une meme date)
				// Pour chaque occurence de la durée
				$echeance_courante = strtotime($commande->getDateDebut());
				foreach ($fournisseur_loyer as $loyer) {

					for ($numLoyer=0; $numLoyer<$loyer["nb_loyer"]; $numLoyer++) {
						// Créer le facturation_fournisseur s'il n'existe pas déjà pour cette période et ce fournisseur
						$facturation_fournisseur = $this->getFacturationFournisseurFromDate($affaire->get('id_affaire'), $ligne_commande['id_fournisseur'], date("Y-m-d", $echeance_courante));

						// Ajoute le détail
						$facturation_fournisseur->addDetail($loyer["id_produit_fournisseur_loyer"],$loyer["loyer"],$ligne_commande["quantite"]);

						// On passe à la période suivante
						$echeance_courante = strtotime("+".$mapping_periodicites[$loyer["periodicite"]]." month", $echeance_courante);
					}
				}
			}
		}
	}

	/**
	* Suppression masisve sur un seul critere
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $field
	* @param string $value
	* @return int
	*/
	public function delete_special($field,$value) {
		$query = "DELETE FROM `".$this->table."`WHERE `".ATF::db()->real_escape_string($field)."`='".ATF::db()->real_escape_string($value)."'";
		return ATF::db()->query($query);
	}

	/**
	* Retourne l'échéance de facturation_fournisseur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_produit_fournisseur_loyer
	* @param decimal $loyer
	* @param int $quantite
	*/
	public function addDetail($id_produit_fournisseur_loyer, $loyer, $quantite) {
		$this->notSingleton();
		//throw new errorATF(__FILE__.":".__LINE__);

		// Augmenter le montant
		$this->increase($this->infos["id_facturation_fournisseur"],"montant",$quantite*$loyer);

		// Ajout du détail dans la base
		ATF::facturation_fournisseur_detail()->insert(array(
			"id_facturation_fournisseur"=>$this->infos["id_facturation_fournisseur"],
			"id_produit_fournisseur_loyer"=>$id_produit_fournisseur_loyer,
			"quantite"=>$quantite
		));
	}

	/**
	* Retourne l'échéance de facturation_fournisseur
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire
	* @param int $id_fournisseur
	* @param string $date_debut
	* @return facturation_fournisseur
	*/
	public function getFacturationFournisseurFromDate($id_affaire, $id_fournisseur, $date_debut) {
		$this->q->reset()
			->select('id_facturation_fournisseur')
			->where('id_affaire',$id_affaire)
			->where('id_fournisseur',$id_fournisseur)
			->where('date_periode_debut',$date_debut)
			->setDimension('cell')
		;
		if (!($id = $this->sa())) {
			$id = $this->insert(array(
				"id_affaire"=>$id_affaire,
				"id_fournisseur"=>$id_fournisseur,
				"date_periode_debut"=>$date_debut
			));
		}

		return new facturation_fournisseur($id);
	}
}