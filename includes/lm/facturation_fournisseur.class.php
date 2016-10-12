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
	}

	/** 
	* Génère les factures d'achat groupées par fournisseur pour une affaire qui vient d'être créée
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param object $affaire
	*/
	public function createFactureFournisseurAchat($affaire) {

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