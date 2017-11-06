<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../affaire.class.php";
class affaire_cleodis extends affaire {
	function __construct($table_or_id=NULL) {
		$this->table = "affaire";
		parent::__construct($table_or_id);
		$this->actions_by = array("insert"=>"devis","update"=>"devis");

		$this->colonnes['fields_column'] = array(
			'affaire.ref'
			,'affaire.date'
			,'affaire.affaire'
			,'affaire.id_societe'
			,'affaire.type_affaire'
			,'affaire.forecast'=>array("aggregate"=>array("min","avg","max"),"width"=>100,"renderer"=>"progress",'align'=>"center")
			,'affaire.nature'=>array("width"=>80,'align'=>"center")
			,'affaire.etat'=>array("renderer"=>"etatAffaire","width"=>30)
			,'commande.etat'=>array("width"=>30,"renderer"=>"etat")
			,'parentes'=>array("custom"=>true,"nosort"=>true)
			,'mail_signature'
			,'mail_document'
			,'cni'=>array("custom"=>true,"nosort"=>true,"type"=>"file")
			,'cniVerso'=>array("custom"=>true,"nosort"=>true,"type"=>"file")
			,'contrat_signe'=>array("custom"=>true,"nosort"=>true,"type"=>"file")
			,'pouvoir'=>array("custom"=>true,"nosort"=>true,"type"=>"file")

		);

		$this->colonnes['primary'] = array(
			"ref"=>array("disabled"=>true)
			,"affaire"
			,"etat"
			,"date"
			,"id_societe"
			,"id_filiale"
			,"nature"
			,"forecast"
			,"parentes"=>array("custom"=>true)
			,"filles"=>array("custom"=>true)
			,'RIB'
			,'IBAN'
			,'BIC'
			,'RUM'
			,'nom_banque'
			,'ville_banque'
			,'date_previsionnelle'
			,"compte_t"=>array("custom"=>true)
			,"type_affaire"
		);

		$this->colonnes['panel']['date_affaire'] = array(
			"specificDate"=>array("custom"=>true)
		);
		$this->panels['date_affaire'] = array("visible"=>true, 'nbCols'=>1);

		$this->colonnes['panel']['rib_facturation'] = array(
			"specificFacturation"=>array("custom"=>true)
		);
		$this->panels['rib_facturation'] = array("visible"=>true, 'nbCols'=>1);

		$this->colonnes['panel']['refRefi'] = array(
			"specificRefRefi"=>array("custom"=>true)
		);
		$this->panels['refRefi'] = array("visible"=>true, 'nbCols'=>1);


		$this->colonnes['panel']['chiffres'] = array(
			'total_depense'
			,'total_recette'
			,'assurance_fixe'
			,'assurance_portable'
			,'valeur_residuelle'
			,'taux_refi_reel'
			,'apporteur'
		);
		$this->panels['chiffres'] = array("visible"=>true, 'nbCols'=>1);

		$this->fieldstructure();

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array('data','nature');
		$this->colonnes['bloquees']['select'] = array('id_parent','data','RIB','BIC','IBAN','RUM','nom_banque','ville_banque','date_previsionnelle');

		$this->onglets = array(
			'loyer'
			,'devis'=>array('opened'=>true)
			,'comite'=>array('opened'=>true)
			,'commande'=>array('opened'=>true)
			,'prolongation'
			,'loyer_prolongation'
			,'bon_de_commande'
			,'demande_refi'
			,'facture'=>array('opened'=>true)
			,'facture_fournisseur'
			,'facture_non_parvenue'
			,'facturation'
			,'intervention'
			,'parc'
			,'livraison'
			,'suivi'
			,'tache'
			,"pdf_affaire"
		);

		$this->autocomplete = array(
			"view"=>array("affaire.id_affaire","societe.societe")
		);
		$this->files["cni"] = array("type"=>"pdf","preview"=>true,"no_upload"=>false,"no_generate"=>true);
		$this->files["cniVerso"] = array("type"=>"pdf","preview"=>true,"no_upload"=>false,"no_generate"=>true);

		$this->files["contrat_signe"] = array("type"=>"pdf","preview"=>true,"no_upload"=>false,"no_generate"=>true);
		$this->files["pouvoir"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"force_generate"=>true);

		$this->files["facturation"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"force_generate"=>true);
		$this->field_nom="ref";
		$this->foreign_key['id_fille'] =  "affaire";
		$this->foreign_key['id_parent'] =  "affaire";
		$this->foreign_key['id_filiale'] =  "societe";
		$this->addPrivilege("updateDate","update");
		$this->addPrivilege("update_forecast","update");
		$this->addPrivilege("updateFacturation","update");
		$this->addPrivilege("getCompteT");
		$this->addPrivilege("getCompteTLoyerActualise");
		$this->no_delete = true;
		$this->no_update = true;
		$this->no_insert = true;
		$this->can_insert_from = array("societe");
	}

	/**
	* Permet de formater les données pour l'insertion d'une affaire
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param array $infos
	* @return array
	*/
	public function formateInsertUpdate($infos){
		$affaire["id_societe"]=$infos["id_societe"];
		$affaire["nature"]=$infos["nature"];
		$affaire["affaire"]=$infos["devis"];
		$affaire["RIB"]=$infos["RIB"];
		$affaire["BIC"]=$infos["BIC"];
		$affaire["IBAN"]=$infos["IBAN"];
		$affaire["nom_banque"]=$infos["nom_banque"];
		$affaire["ville_banque"]=$infos["ville_banque"];
		$affaire["date"]=$infos["date"];
		$affaire["ref"]=$infos["ref"];

		// On passe les date d'installation et de livraison sur l'affaire puisque l'opportunité va passer en état fini.
		if ($infos["id_opportunite"]) {
			$infos["id_opportunite"]=ATF::opportunite()->decryptId($infos["id_opportunite"]);
			$opportunite=ATF::opportunite()->select($infos["id_opportunite"]);
			$affaire["date_installation_prevu"] = $opportunite["date_installation_prevu"];
			$affaire["date_installation_reel"] =  $opportunite["date_installation_reel"];
			$affaire["date_livraison_prevu"] = $opportunite["date_livraison_prevu"];
		}

		if($affaire["nature"]=="avenant"){
			$affaire["date_garantie"]=$this->select($infos["id_parent"],"date_garantie");
			if (!$affaire["ref"] || !(strstr($affaire["ref"],"AVT"))) {
				$affaire["ref"]=$this->getRefAvenant($infos["id_parent"]);
			}
		}else{
			$affaire["id_parent"]=NULL;
			$affaire["date_garantie"]=NULL;
			if (!$affaire["ref"] || strstr($affaire["ref"],"AVT")) {
				$affaire["ref"]=$this->getRef($affaire["date"]);
			}
		}

		if($affaire["nature"]=="avenant" || $affaire["nature"]=="vente"){
			$affaire["id_parent"]=$infos["id_parent"];
		}

		return $affaire;
	}


	/**
	* Retourne la ref d'un avenant
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param int $id_parent
	* @return string ref
	*/
	function getRefAvenant($id_parent){
		//Récup du dernier avenant de cette affaire
		$ref=substr($this->select($id_parent,"ref"),0,7);
		$this->q->reset()
		   ->addField('MAX(`ref`)','max')
		   ->addCondition("ref",$ref."AVT%",NULL,false,"LIKE")
		   ->setStrict()
		   ->setDimension("row");

		$ref_avenant=$this->sa();
		$nb_avenant=1;
		//S'il y a déjà un avenant alors on incrémente
		if($ref_avenant["max"]){
			$nb_avenant=substr($ref_avenant["max"],-1) +1;
		}

		return $ref."AVT".$nb_avenant;
	}

	/**
	* Retourne la ref d'une affaire autre qu'avenant
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param int $id_parent
	* @return string ref
	*/
	function getRef($date){
		$prefix=date("ym",strtotime($date));
		$this->q->reset()
				->addCondition("ref",$prefix."%","AND",false,"LIKE")
				->addCondition("LENGTH(`ref`)",3,"AND",false,">")
				->addCondition("ref","%AVT%","AND",false,"NOT LIKE")
				->addField('SUBSTRING(`ref`,5)+1',"max_ref")
				->addOrder('ref',"DESC")
				->setDimension("row")
				->setLimit(1);

		$nb=$this->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="001";
		}
		return $prefix.$suffix;
	}


	/**
	* Permet de mettre a jour une date en ajax
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Quentin JANON <qjanon@absystech.fr>
	* @param array $infos
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return bool
	*/
	public function updateDate($infos,&$s,&$request){
		if ($infos['value'] == "undefined") $infos["value"] = "";

		if(isset($infos['value']) && $infos['id_affaire']){
			//Mode transactionel
			ATF::db($this->db)->begin_transaction();
			$affaire = new affaire_cleodis($infos['id_affaire']);
			try {
				switch ($infos["field"]) {
					case "date_installation_prevu":
						if(!$affaire->get("date_livraison_prevu")){
							$affaire->set("date_livraison_prevu",$this->getDateLivraison($infos['value']));



							ATF::$msg->addNotice(ATF::$usr->trans("date_livraison_prevu_modifiee",$this->table));
						}
						$aff = $this->select($infos["id_affaire"]);

						$suivi = array(
								"id_user"=>ATF::$usr->get('id_user')
								,"id_societe"=>$aff['id_societe']
								,"id_affaire"=>$aff['id_affaire']
								,"type_suivi"=>'Installation'
								,"texte"=>"Affaire n°".$aff['ref']." - Modification de la '".ATF::$usr->trans($infos['field'],"suivi")."', nouvelle valeur : ".$infos['value']
								,'public'=>'oui'
								,'id_contact'=>NULL
								,'suivi_societe'=>array(0=>ATF::$usr->getID())
								,'suivi_notifie'=>NULL
								,'champsComplementaire'=>$infos['key']
							);
							$suivi["no_redirect"] = true;

							ATF::suivi()->insert($suivi);

						break;
					case "date_installation_reel":
						$devis = $affaire->getDevis();
						if (!$devis) {
							throw new errorATF("aucun_devis_trouve",856);
						}
						if($affaire->get("nature")!="avenant" && !$affaire->get("date_garantie")){
							$affaire->set("date_garantie",$devis->getDateFinPrevue($infos['value']));
							ATF::$msg->addNotice(ATF::$usr->trans("date_garantie",$this->table));
						}
						break;

					case "date_garantie": break;

					case "date_livraison_prevu": break;

					case "date_ouverture": break;

					default:
						throw new errorATF("date_invalide",988);
				}
				$affaire->set($infos["field"], $infos["value"]?date("Y-m-d",strtotime($infos["value"])):NULL);
				$affaire->majForecastProcess();
				//On commit le tout
				ATF::db($this->db)->commit_transaction();
				ATF::$msg->addNotice(ATF::$usr->trans($infos['field']."_modifiee",$this->table));


				$this->redirection("select",$infos['id_affaire']);

				return true;

			} catch(errorATF $e) {
				//On commit le tout
				ATF::db($this->db)->rollback_transaction();
				throw $e;
			}
		}else{
			return false;
		}
	}

	/**
	* Permet de mettre a jour RIB BIC IBAN
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return bool
	*/
	public function updateFacturation($infos,&$s,&$request){
		if (!$infos['id_affaire']) return false;

		//Mode transactionel
		ATF::db($this->db)->begin_transaction();
		$id_affaire = $infos['id_affaire'];
		$infos['id_affaire'] = $this->decryptId($infos['id_affaire']);
		$affaire = new affaire_cleodis($infos['id_affaire']);

		try {
			switch ($infos["field"]) {
				case "RIB":
				case "BIC":
				case "IBAN":
				case "RUM":
				case "nom_banque":
				case "ville_banque":
				case "date_previsionnelle":
				case "reference_refinanceur":
					if($infos["field"] == "RUM"){
						$RUM = explode("  ", $infos["value"]);
						if(isset($RUM[1])){
							$infos["value"] = "++".$infos["value"];
							$infos['value'] = str_replace(" ", "", $infos['value']);
						}
					}
					if(($infos["field"] == "RIB" || $infos["field"] == "BIC" || $infos["field"] == "IBAN" || $infos["field"] == "RUM") && !ATF::affaire()->select($infos["id_affaire"] , "RUM") ){
						$RUM = "";
						$field_value = str_replace(" ", "", $infos['value']);
						$id_societe = ATF::affaire()->select($infos["id_affaire"] , "id_societe");

						ATF::affaire()->q->reset()->where("affaire.id_societe", $id_societe)
												  ->where('replace(affaire.'.$infos["field"].', " ", "")' , $field_value)
												  ->whereIsNotNull("affaire.RUM");
						$res = ATF::affaire()->select_all();

						if($res){
							$affaire->set("RIB", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "RIB") );
							$affaire->set("RUM", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "RUM") );
							$affaire->set("BIC", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "BIC") );
							$affaire->set("IBAN", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "IBAN") );
							$affaire->set("nom_banque", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "nom_banque") );
							$affaire->set("ville_banque", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "ville_banque") );

							$esp = true;
						}
					}

					$affaire->set($infos["field"],$infos['value']);
					break;
				default:
					throw new errorATF("Problème modification",987);
			}

		} catch(errorATF $e) {
			//On commit le tout
			ATF::db($this->db)->rollback_transaction();
			throw $e;
		}


		//On commit le tout
		ATF::db($this->db)->commit_transaction();
		ATF::$msg->addNotice(ATF::$usr->trans($infos['field']."_modifiee",$this->table));

		if($esp) ATF::affaire()->redirection("select",$id_affaire);
		return true;
	}


	/**
	* Retourne la marge effectuée entre le début de l'année passée en paramètre et NOW()
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $offset Décalage de l'année demandé
	* @return int
	*/
	public function getMargeTotaleDepuisDebutAnnee($offset=0){
		return 0;
	}

	/**
	* Prédicat, retourne VRAI si l'affaire est un annule et remplace, méthode d'objet et non de singleton
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
	*/
	function isAR(){
		$this->notSingleton();
		return $this->get("nature")=="AR";
	}

	/**
	* Prédicat, retourne VRAI si l'affaire est un avenant, méthode d'objet et non de singleton
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return boolean
	*/
	function isAvenant($id_affaire){
		$this->notSingleton();
		return $this->get("nature")=="avenant";
	}

	/**
	* Retourne les id_affaire et ref des affaires filles
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_fille Affaire fille qui demande ses parents
	* @return array
	*/
	function getFilles($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire)	{
			$id_fille=$this->select($id_affaire,"id_fille");
			$this->q->reset()->addCondition("id_parent",$this->decryptId($id_affaire),"OR",1)->addField("id_affaire")->addField("ref")->setStrict();
			if($id_fille){
				$this->q->addCondition("id_affaire",$id_fille,"OR",1);
			}
			if($parent=$this->sa()){
				return $parent;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* Retourne les id_affaire et ref des affaires filles
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_fille Affaire fille qui demande ses parents
	* @return array
	*/
	function getFillesAR($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire)	{
			$id_fille=$this->select($id_affaire,"id_fille");
			if($id_fille){
				return $this->select($id_fille);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* Retourne l'objet affaire parente
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire Affaire fille qui demande sa mère
	* @return affaire_cleodis
	*/
	function getParentAvenant($id_affaire=NULL){
		if ($id_affaire) {
			$id_parent = $this->select($this->decryptId($id_affaire),"id_parent");
		} elseif (!$id_affaire && $this->infos["id_parent"]) {
			$id_parent = $this->infos["id_parent"];
		}
		if($id_parent)	{
			return new affaire_cleodis($id_parent);
		}else{
			return false;
		}
	}

	/**
	* Retourne les id_affaire et ref des affaires parents
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_fille Affaire fille qui demande ses parents
	* @return array
	*/
	function getParentAR($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire)	{
			$this->q->reset()->where("id_fille",$this->decryptId($id_affaire))->addField("id_affaire")->addField("ref")->setStrict();
			if($filles=$this->sa()){
				return $filles;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* Retouren l'objet commande associé à l'affaire passée en paramètre
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire Affaire qui demande sa commande
	* @return commande_cleodis
	*/
	function getCommande($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			ATF::commande()->q->reset()->setStrict()->addField('commande.id_commande')->addCondition("commande.id_affaire",$id_affaire)->setDimension("cell");
			if($id_commande = ATF::commande()->sa()) {
				return new commande_cleodis($id_commande);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	/**
	* Retourne l'objet devis associé à la commande passée en paramètre
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return devis_cleodis
	*/
	function getDevis($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			ATF::devis()->q->reset()->setStrict()->addField('devis.id_devis')->addCondition("devis.id_affaire",$id_affaire)->setDimension("cell");
			if($id_devis = ATF::devis()->sa()) {
				return new devis_cleodis($id_devis);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* Retourne l'objet prolongation associée à la commande passée en paramètre
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return devis_cleodis
	*/
	function getProlongation($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			ATF::prolongation()->q->reset()->setStrict()->addField('id_prolongation')->addCondition("id_affaire",$id_affaire)->setDimension("cell");
			if($id_prolongation = ATF::prolongation()->sa()) {
				return new prolongation($id_prolongation);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* Retourne l'objet demande_refi acceptée associée à la commande passée en paramètre
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return devis_cleodis
	*/
	function getDemandeRefiValidee($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			if($demande_refi = $this->refiValid($id_affaire)) {
				return new demande_refi($demande_refi["id_demande_refi"]);
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	* Met à jour une valeur d'attribut
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param string $attribute
	* @param string $value
	* @return mixed Résultat de la requête d'effacement
	*/
	function set($attribute,$value){
		$oldValue = $this->get($attribute);
		if ($return = parent::set($attribute,$value)) {
			switch ($attribute) {
				case "etat":
					ATF::$msg->addNotice(loc::mt(
						ATF::$usr->trans("etat_change",$this->table)
						,array(
							"old"=>ATF::$usr->trans($oldValue,$this->table)
							,"new"=>ATF::$usr->trans($value,$this->table)
							,"ref"=>$this->get("ref")
						)
					));
					break;

				case "nature":
					ATF::$msg->addNotice(loc::mt(
						ATF::$usr->trans("nature_change",$this->table)
						,array(
							"old"=>ATF::$usr->trans($oldValue,$this->table)
							,"new"=>ATF::$usr->trans($value,$this->table)
							,"ref"=>$this->get("ref")
						)
					));
					break;
			}
		}
	}

	/**
	* Retourne la date de livraison prévue en utilisant la date de début passée en paramètre, méthode d'objet et non de singleton
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string date (Y-m-d)
	* @param int $delai en jours
	* @return string date (Y-m-d)
	*/
	function getDateLivraison($date_debut,$delai=21){
		$date_debut = strtotime($date_debut);
		return date("Y-m-d",mktime(
			0,0,0,
			date('m',$date_debut),
			date('d',$date_debut)+$delai,
			date('Y',$date_debut)
		));
	}



	/**
	* Met à jour le forecast d'une affaire en fonction de son état, méthode d'objet et non de singleton
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $etat
	* @return affaire_cleodis
	*/
	function majForecastProcess(){
		$this->notSingleton();

		// 10% Proposition créée
		if ($devis = $this->getDevis()) {
			$forecast += 10;
		}

		// +15% Contrat créé
		if ($commande = $this->getCommande()) {
			$forecast += 15;

			//	+25% Contrat signé (retour contrat renseigné)
			if ($commande->estSigne()) {
				$forecast += 25;
			}

			// +15% Contrat démarré
			if ($commande->estEnCours()) {
				$forecast += 15;
			}
		}

		if ($demandeRefiValidee = $this->getDemandeRefiValidee()) {
			$forecast += 25;
		}

		// +10% Date d'installation prévue renseignée de moins d'un mois
		if ($date_install_prevue = $this->get("date_installation_prevu")) {
			if (strtotime($date_install_prevue) <= time()+86400*28) {
				$forecast += 10;
			}
		}

		if($forecast){
			$this->set("forecast",$forecast);
			return true;
		}else{
			return false;
		}
	}


	function num_avenant($ref){
		$tab_ref=explode("AVT",$ref);
		return ($tab_ref[1]);
	}

	/**
	* Fonction qui retourne la demande_refi d'une affaire en état valide
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int id_affaire
	* @return array demande_refi
	*/
	public function refiValid($id_affaire){
		ATF::demande_refi()->q->reset()->addCondition("id_affaire",$id_affaire)
									   ->addCondition("etat","valide")
									   ->setDimension("row");

		if($refi=ATF::demande_refi()->sa()){
			return $refi;
		}else{
			return false;
		}
	}

	/**
	* Retourne les infos du compte en T
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* 		$infos[id_affaire]
	* @return string html
	*/
	public function getCompteT(&$infos) {
		$infos["display"]=true;
		if ($infos["id_affaire"]) {
			ATF::$html->assign("id_affaire",$infos["id_affaire"]);
			ATF::$html->assign("type",$infos["type"]);
			$affaire = new affaire_cleodis($infos["id_affaire"]);
			$devis = $affaire->getDevis();
			$commande = $affaire->getCommande();
			ATF::$html->assign("affaire",$affaire);

			// Lignes
			if ($commande) {
					// Si on a une commane, on utilise les lignes du contrat
				$lignesDataVisibles = $commande->getLignes("visible");
				$lignesDataNonVisibles = $commande->getLignes("invisible");
				$lignesDataReprises = $commande->getLignes("reprise");
			} else {
				// Sinon on utilise les lignes du devis
				$lignesDataVisibles = $devis->getLignes("visible");
				$lignesDataNonVisibles = $devis->getLignes("invisible");
				$lignesDataReprises = $devis->getLignes("reprise");
			}

			// Valeurs des grids de lignes
			foreach (array("lignesDataVisibles","lignesDataNonVisibles","lignesDataReprises") as $grid) {
				ATF::$html->assign($grid,json_encode($$grid));

				// Calcul total
				$total = 0;
				foreach ($$grid as $i) {
					$total += $i["quantite"]*$i["prix_achat"];
				}
				$lignesTotal += $total;
				ATF::$html->assign($grid."Total",$total);
			}
			ATF::$html->assign("lignesTotal",$lignesTotal);

			// Factures des dépenses
			ATF::facture_fournisseur()->q->reset()
				->addField("facture_fournisseur.ref")
				->addField("facture_fournisseur.id_fournisseur")
				->addField("facture_fournisseur.date")
				->addField("facture_fournisseur.prix")
				->where("id_affaire",$affaire->get("id_affaire"));
			$facturesDataFournisseurs = util::removeTableInKeys(ATF::facture_fournisseur()->select_all()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour

			// Factures non parvenues
			ATF::facture_non_parvenue()->q->reset()
				->addField("facture_non_parvenue.ref")
				->addField("facture_non_parvenue.date")
				->addField("facture_non_parvenue.prix")
				->where("id_affaire",$affaire->get("id_affaire"));
			$facturesDataNonParvenues = util::removeTableInKeys(ATF::facture_non_parvenue()->select_all()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour

			// Factures de recettes
			ATF::facture()->q->reset()
				->addField("facture.ref")
				->addField("facture.type_facture")
				->addField("facture.date")
				->addField("facture.prix")
				->from("facture","id_demande_refi","demande_refi","id_demande_refi")
				->where("facture.type_facture","refi","OR","casRefi","!=")->where("demande_refi.etat","accepte","OR","casRefi") // Ne pas sélectionner de demande de refi sauf si elle est acceptée
				->where("facture.type_facture","ap","OR",NULL,"!=") // Ne pas sélectionner les avis de prélèvements car il ne sont pas des recettes
				->where("facture.id_affaire",$affaire->get("id_affaire"));
			$facturesCleodisData = util::removeTableInKeys(ATF::facture()->sa()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour
			foreach (array("facturesDataNonParvenues","facturesDataFournisseurs","facturesCleodisData") as $grid) {
				ATF::$html->assign($grid,json_encode($$grid));

				// Calcul total
				$total = 0;
				foreach ($$grid as $i) {
					$total += $i["prix"];
				}
				if ($grid=="facturesDataNonParvenues" && $total<0) {
					$total = 0;
				}
				if ($grid=="facturesDataNonParvenues" || $grid=="facturesDataFournisseurs") {
					$facturesTotal += $total;
				}
				${$grid."Total"}=$total;
				ATF::$html->assign($grid."Total",$total);
			}
			ATF::$html->assign("facturesTotal",$facturesTotal);

			// Taux de l'affaire
			if ($infos["type"]=="manager") { // Protection suffisante
				if ($dr = $affaire->getDemandeRefiValidee()) {
					$taux = $dr->get("taux");
				}
				if (!$taux) {
					$taux = $affaire->get("taux_refi_reel");
				}
			} else {
				$taux = $infos["taux"];
				if ($taux>=0 && strlen($taux)) {
					$affaire->set("taux_refi",$taux); // Sauvegarde du taux modifié
				} else {
					$taux = $affaire->get("taux_refi");
				}
			}
			if (!$taux) {
				$taux = 0;
			}
			ATF::$html->assign("taux",$taux);



			// Calcul du loyer actualisé
			$vr = 0;
			$loyers = $affaire->getCompteTLoyersActualises($taux,$vr);
			$loyerDataVA = $loyers[0]["pv"];
			ATF::$html->assign("vr",round($vr,2));

			// Loyers et calcul de valeur actualisée
			ATF::$html->assign("loyerData",json_encode($loyers));
			ATF::$html->assign("loyerDataVA",$loyerDataVA);

			// Calculs finaux
			ATF::$html->assign("resteAFacturer",$affaire->getResteAPayer());
			if ($infos["type"]=="manager") {
				$depensesTotal = $facturesTotal ? $facturesTotal : $lignesTotal;
			} else {
				$depensesTotal = $lignesTotal;
			}
			ATF::$html->assign("depensesTotal",$depensesTotal);
			ATF::$html->assign("marge",$marge = round($loyerDataVA - $depensesTotal,2));
			ATF::$html->assign("margePourcent",round(100*$marge/$loyerDataVA,2));

			return ATF::$html->fetch("compte_t.tpl.htm");
		}
	}

	/**
	* Retourne les loyers restant à facturer
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return cell
	*/
	public function getResteAPayer() {
		$this->notSingleton();

		return ATF::facturation()->getResteAPayer($this->infos["id_affaire"]);
	}

	/**
	* Retourne les loyers de l'affaire
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire
	* @return array
	*/
	public function getLoyers($id_affaire) {
		ATF::loyer()->q->reset()
			->addField("loyer")
			->addField("duree")
			->addField("assurance")
			->addField("frais_de_gestion")
			->addField("frequence_loyer")
			->where("id_affaire",$id_affaire)
			->addOrder("id_loyer","desc");
		return ATF::loyer()->select_all();
	}

	/**
	* Retourne les loyers actualisés
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param float $taux
	* @param float $vr
	* @param array $loyers Loyer si besoin
	* @return array
	*/
	public function getCompteTLoyersActualises($taux,&$vr=NULL,$loyers=NULL) {

		$this->notSingleton();

		// Récupérer les loyers
		if (!$loyers) {
			$loyers = $this->getLoyers($this->get("id_affaire"));
		}

		// Librairie externe Finance
		require_once 'finance.class.php';
//log::logger($loyers,ygautheron);

		// Baser les calculs sur la valeur résiduelle de la demande de refi acceptée
		if ($demandeRefi = $this->getDemandeRefiValidee()) {
			$vr = $demandeRefi->get("valeur_residuelle");
		}
//log::logger("valeur_residuelle_demande_refi => ".$vr,ygautheron);

		$f = new Financial;
		$freq = array("mois"=>12,"trimestre"=>4,"semestre"=>2,"an"=>1);
		$vr2 = $vr;
		foreach ($loyers as $i => $loyer) {
			if ($pv) {
				$vr2 = $pv;
			}
//log::logger("f->PV(".$taux."/".$freq[$loyer["frequence_loyer"]]."/100, ".$loyer["duree"].", ".$loyer["loyer"].", ".$vr." , 1);",ygautheron);
			$pv = -$f->PV($taux/$freq[$loyer["frequence_loyer"]]/100, $loyer["duree"], ($loyer["loyer"]+$loyer["frais_de_gestion"]+$loyer["assurance"]), $vr2 , 1);
			$loyers[$i]["pv"] = round($pv,2);
		}
		$loyers = array_reverse($loyers);
		return $loyers;
	}

	/**
	* Retourne le loyer actualisé total
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	*		int		$infos[id_affaire]
	*		float	$infos[taux]
	*		float	$infos[vr]  Valeur résiduelle
	* @return float
	*/
	public function getCompteTLoyerActualise(&$infos) {
		$a = new affaire_cleodis($infos["id_affaire"]);
//log::logger($infos,ygautheron);
		if ($c = $a->getCommande()) {
			$date_debut = $c->get("date_debut");
		}

		if ($date_debut && $infos["date_cession"]) {
//log::logger("date_cession=".$infos["date_cession"],ygautheron);
			$date1 = new DateTime(substr($infos["date_cession"],0,8).'01');

			if(date("m",strtotime($infos["date_cession"])) != "12"){
				$date1->modify('+1 month'); // On prend le premier jour du mois suivant ( nécessaire en cas de date de cession en dernier jour de période pleine,et à cause du pb des bisextile, et en plus a ce bug de merde : https://bugs.php.net/bug.php?id=52480 )
			}




//log::logger("date_cession=".$date1->format('Y-m-d'),mfleurquin);
//log::logger("date_debut=".$date_debut,mfleurquin);
			$date2 = new DateTime($date_debut);
//log::logger($date1->diff($date2),mfleurquin);
//log::logger("diff=".$date1->diff($date2)->format('%m'),mfleurquin);
			$duree_ecoulee_restante = $duree_ecoulee = $date1->diff($date2)->format('%y')*12 + $date1->diff($date2)->format('%m');

			if($date1->diff($date2)->format('%d')){
				$duree_ecoulee_restante = $duree_ecoulee = $duree_ecoulee+1;
			}


//log::logger("duree_ecoulee=".$duree_ecoulee,mfleurquin);
//log::logger(DateTime::getLasterrorATFs(),ygautheron);

			// On "rogne" les mois deja écoulé jusqu'àla date de cession
			$frequence_loyer=array("mois"=>1,"trimestre"=>3,"semestre"=>6,"an"=>12);
			$loyers = $this->getLoyers($infos["id_affaire"]);
			$loyers = array_reverse($loyers);
			foreach ($loyers as $k => $loyer) {
//log::logger("duree_ecoulee_restante=".$duree_ecoulee_restante,ygautheron);
				if ($duree_ecoulee_restante>0) { // Tant qu'il reste de la durée à rogner
					$duree = ceil($loyer["duree"]*$frequence_loyer[$loyer["frequence_loyer"]]);
//log::logger("duree=".$duree,ygautheron);
					$duree_max_pouvant_etre_retiree_de_ce_loyer = min($duree,$duree_ecoulee_restante);
//log::logger("duree_max_pouvant_etre_retiree_de_ce_loyer=".$duree_max_pouvant_etre_retiree_de_ce_loyer,ygautheron);
					$loyer["duree"] -= $duree_max_pouvant_etre_retiree_de_ce_loyer / $frequence_loyer[$loyer["frequence_loyer"]];
					$duree_ecoulee_restante -= $duree_max_pouvant_etre_retiree_de_ce_loyer;
//log::logger("duree_ecoulee_restante=".$duree_ecoulee_restante,mfleurquin);
					$loyers[$k] = $loyer;
				}
			}

			$loyers = array_reverse($loyers);
		}
//log::logger($loyers,mfleurquin);

		$loyers = $a->getCompteTLoyersActualises($infos["taux"],$infos["vr"],$loyers);
//log::logger($loyers,ygautheron);
		//date_default_timezone_set($fuseau);	// Fin du truc chelou	: https://bugs.php.net/bug.php?id=52480
		return $loyers[0]["pv"];
	}

	/**
	* Retourne les affaires avec les affaires parentes en plus
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		$this->q->addJointure("affaire","id_affaire","commande","id_affaire")
				->addField("commande.etat");
		$return = parent::select_all($order_by,$asc,$page,$count);
		$a = new affaire_cleodis();
		foreach ($return['data'] as $k=>$i) {
			if ($i['affaire.nature'] == 'AR') {
				foreach ($a->getParentAR($i['affaire.id_affaire']) as $k_=>$i_) {
					$return['data'][$k]['parentes'] .= '<a href="#affaire-select-'.$a->cryptId($i_['id_affaire']).'.html">'.$i_['ref'].'</a>, ';
				}
			} elseif ($i['affaire.nature'] == 'vente' || $i['affaire.nature'] == 'avenant') {
				if ($affaire=$a->getParentAvenant($i['affaire.id_affaire'])) {
					$return['data'][$k]['parentes'] .= '<a href="#affaire-select-'.$a->cryptId($affaire->get('id_affaire')).'.html">'.$affaire->get('ref').'</a>, ';
				}
			} else {
				$return['data'][$k]['parentes'] = "";
			}
		}
		return $return;
	}

	/**
	* Vérification et structuration de l'envoi des mails
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $email du client
	* 		$last_id int de l'enregistrement du module concerné
	*		$table table du module
	*		$path chemin du fichier concerné
	* @return boolean
	*/
	public function mailContact($email,$last_id,$table,$paths){
		$enregistrement = ATF::$table()->select($last_id);
		if($email["email"]){
			$recipient = $email["email"];
		}elseif($enregistrement["id_contact"]){
			if(!ATF::contact()->select($enregistrement["id_contact"],"email")){
				ATF::db($this->db)->rollback_transaction();
				throw new errorATF("Il n'y a pas d'email pour le contact ".ATF::contact()->nom($enregistrement["id_contact"]),349);
			}else{
				$recipient = ATF::contact()->select($enregistrement["id_contact"],"email");
			}
		}else{
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Il n'y a pas d'email",350);
		}

		if(ATF::$usr->getID()){
			$from = ATF::user()->nom(ATF::$usr->getID())." <".ATF::user()->select(ATF::$usr->getID(),"email").">";
		}else{
			$societe = ATF::societe()->select(246);
			$from = $societe["societe"]." <".$societe["email"].">";
		}

		if(!$email["objet"]){
			$info_mail["objet"] = "Votre ".$table." référence : ".$enregistrement["ref"];
		}else{
			$info_mail["objet"] = $email["objet"];
		}

		$info_mail["from"] = $from;
		$info_mail["html"] = false;
		$info_mail["template"] = $table;
		$info_mail["texte"] = $email["texte"];
		$info_mail["recipient"] = $recipient;
		$info_mail["return_path"] = "ludivine.bowe@cleodis.com";

		$mail = new mail($info_mail);
		foreach($paths as $key=>$item){
			$path = ATF::$table()->filepath($last_id,$item);
			$mail->addFile($path,$key.$enregistrement["ref"].".pdf",true);
		}
		$mail->send();

		if($email["emailCopie"]){
			$info_mail["recipient"] = $email["emailCopie"];
			$copy_mail = new mail($info_mail);
			foreach($paths as $key=>$item){
				$path = ATF::$table()->filepath($last_id,$item);
				$copy_mail->addFile($path,$key.$enregistrement["ref"].".pdf",true);
			}
			$copy_mail->send();
		}
		return true;
	}


	/**
	* Retourne les pourcentages materiels et immateriels
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_affaire
	* @return array
	*/
	public function getPourcentagesMateriel($id_affaire) {
		$id_affaire = ATF::affaire()->decryptId($id_affaire);
		ATF::devis()->q->reset()->where("devis.id_affaire", $id_affaire);
		$devis = ATF::devis()->select_row();

		ATF::devis_ligne()->q->reset()->where("devis_ligne.id_devis", $devis["id_devis"]);
		$lignes = ATF::devis_ligne()->select_all();
		$pourcentagesImmat = $pourcentagesMat = 0;
		foreach ($lignes as $key => $value) {
			if($value["prix_achat"]){
				$type = ATF::produit()->select($value["id_produit"], "type");
				if($type != "fixe" && $type != "portable"){
					$pourcentagesImmat = $pourcentagesImmat + ($value["prix_achat"]*$value["quantite"]);
				} else {
					$pourcentagesMat = $pourcentagesMat + ($value["prix_achat"]*$value["quantite"]);
				}
			}
		}

		$total = $pourcentagesImmat + $pourcentagesMat;

		$return = array("immat"=>$pourcentagesImmat,
						"pourcentagesImmat" => round(($pourcentagesImmat * 100)/$total, 2),
						"mat"=>$pourcentagesMat,
						"pourcentagesMat" => round(($pourcentagesMat * 100)/$total , 2));

		return $return;
	}



  /* PARTIE DES FONCTIONS POUR TELESCOPE*/

	public function _pj($get,$post) {
		$input = file_get_contents('php://input');
		if (!empty($input)) parse_str($input,$post);
		$data = base64_decode($post['base64']);
		ATF::affaire()->store(ATF::_s(),$post['id_affaire'],$post['ext'],$data,false);

		ATF::affaire_etat()->insert(array(
                                  "id_affaire"=>$post["id_affaire"],
                                  "etat"=>"reception_pj"
                              ));

		return true;
	}

	/**
	*
	* Fonctions paiementIsReceived pour determiner si une affaire a été payé ou non pour le listing cleoscope
	* @package Telescope
	* @author Anthony LAHLAH <mfleurquin@absystech.fr>
	* @param $id_affaire number contient l'id affaire qui va servir à la requete.
	* @return boolean, retourne le boolean qui represente l'etat du paiement
	*/
	public function paiementIsReceived($id_affaire, $detail = false) {
		ATF::transaction_banque()->q->reset()
			->where('id_affaire', $id_affaire)
			->where('response_code', "00")
			->setDimension('row');

		$row = ATF::transaction_banque()->sa();
		if ($row && $detail)
			return $row;
		else
			return !!$row; //on s'embete pas avec les details superflu, c'est true ou false
	}
	/**
	 * [_paiementIsReceived permet de verifier si une affaire est payée ou non pour le recap toshiba www]
	 * @author Cyril CHARLIER <ccharlier@absystech.fr>
	 * @param  [Array] $get  Array contenant les paramètres de la requete
	 * @return true or false, verifie si le paiement a bien été reçu
	 */
	public function _paiementIsReceived($get) {

		if(strlen($get["id_affaire"]) ===32){
			return self::paiementIsReceived(ATF::affaire()->decryptId($get["id_affaire"]));
		}else{
			return false;
		}
	}

	/**
	 * fonction qui retourne toutes les infos d'un signataire d'une affaire
	 * @param  $id_affaire, int contenant l'id_affaire NON crypté
	 * @return array, return un tableau contenant toutes les infos du signataire de l'affaire
	 */
	public function getInfoSignataire($id_affaire) {
		ATF::affaire()->q->reset()
			->addField('contact.*')
			->addJointure("affaire","id_societe","societe","id_societe")
			->addJointure("societe","id_contact_signataire","contact","id_contact")
			->where('id_affaire', $id_affaire)
			->setDimension('row');

			return ATF::affaire()->sa();
	}
	/**
	*
	* Fonctions _GET pour telescope
	* @package Portail Partenaire
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	* @param $get array contient le tri, page limit et potentiellement un id.
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function _affairePartenaire($get,$post) {


		$utilisateur  = ATF::$usr->get("contact");
		$apporteur = $utilisateur["id_societe"];

		if($apporteur){

		 	// Gestion du tri
		 	if (!$get['tri'] || $get['tri'] == 'action') $get['tri'] = "affaire.date";
		 	if (!$get['trid']) $get['trid'] = "desc";

		 	// Gestion du limit
		 	if (!$get['limit'] && !$get['no-limit']) $get['limit'] = 30;

		 	// Gestion de la page
		 	if (!$get['page']) $get['page'] = 0;
		 	if ($get['no-limit']) $get['page'] = false;
		 	$colsData = array("affaire.affaire",
		 		"affaire.id_affaire",
		 		"affaire.etat",
		 		'affaire.date',
		 		'affaire.ref',
		 		'affaire.etat_comite',
		 		'affaire.id_societe',
		 		'societe.societe',
		 		'societe.id_contact_signataire',
		 		'loyer.loyer');
			$this->q->reset();


			$this->q->setCount();


		 	if ($get['id_affaire']) $colsData = array("affaire.affaire","affaire.id_affaire","affaire.etat",'affaire.date','affaire.ref','affaire.etat_comite','affaire.id_societe', 'affaire.pieces', 'affaire.date_verification');

		 	$this->q->addField($colsData)
					->from("affaire","id_societe","societe","id_societe")
					->from("societe","id_contact_signataire","contact","id_contact")
					->from("affaire","id_affaire","bon_de_commande","id_affaire")
					->from("affaire","id_affaire","commande","id_affaire")
					->from("affaire","id_affaire","loyer","id_affaire")
					->from("affaire", "id_affaire", "commande", "id_affaire")
					->where("provenance",'partenaire')
					->where("id_partenaire", $apporteur)
					->addGroup("affaire.id_affaire");

			if($get["search"]){
				header("ts-search-term: ".$get['search']);
				$this->q->setSearch($get['search']);
			}

			if ($get['id_affaire']) {
			  $this->q->where("affaire.id_affaire",$this->decryptId($get["id_affaire"]))->setCount(false)->setDimension('row');
			  $data = $this->sa();
			  // on check si l'affaire est "payee"
			  $data['payee'] = $this->paiementIsReceived($data['affaire.id_affaire_fk']);
			  // on set les infos signataire pour la relance des paiements
			  $data['infos_signataire'] = $this->getInfoSignataire($data['affaire.id_affaire_fk']);
			  ATF::devis()->q->reset()->addField("CONCAT(SUBSTR(user.prenom, 1,1),'. ',user.nom)","user")
						  ->addField("devis.*")
						  ->from("devis","id_user","user","id_user")
						  ->where("devis.id_affaire",$this->decryptId($get["id_affaire"]))->addOrder('id_devis', 'desc');
			  $data["devis"] = ATF::devis()->sa();

			  ATF::loyer()->q->reset()->addField("*")
						  ->from("loyer","id_affaire","affaire","id_affaire")
						  ->where("loyer.id_affaire",$this->decryptId($get["id_affaire"]))->addOrder('id_loyer', 'desc');
			  $data["loyer"] = ATF::loyer()->sa();


			  $data["contact"] = ATF::contact()->select(ATF::societe()->select($data["affaire.id_societe_fk"], "id_contact_signataire"));
			  foreach ($data as $key => $value) {
				if (strpos($key,".")) {
					$tmp = explode(".",$key);
					$data[$tmp[1]] = $value;
					unset($data[$key]);
				}
				if($key == "affaire.id_societe_fk"){
					ATF::societe()->q->reset()
						->addField("adresse")
						->addField("adresse_2")
						->addField("adresse_3")
						->addField("code_client")
						->addField("societe")
						->addField("cp")
						->addField("ville")
						->addField("pays.pays")
						->addField("tel")
						->from("pays","id_pays","societe","id_pays")
						->where("id_societe", $value)

						->setDimension('row');

					$data["societe"] = ATF::societe()->sa();
					$data["societe"]['id_pays'] = $data["societe"]["pays.pays"];
					unset($data["societe"]["pays.pays"],$data["societe"]["pays.pays_fk"]);

				}
				if($key == "id_commercial") $data["user"] = ATF::user()->select($value);

				unset($data["id_societe"],  $data["id_commercial"]);
			  }
			  foreach ($data["devis"] as $key => $value) {
				$data['devis'][$key]["fichier_joint"] = $data['devis'][$key]["documentAnnexes"] = false;
				if (file_exists(ATF::devis()->filepath($value['id_devis'],"fichier_joint"))) $data['devis'][$key]["fichier_joint"] = true;
				if (file_exists(ATF::devis()->filepath($value['id_devis'],"documentAnnexes"))) $data['devis'][$key]["documentAnnexes"] = true;
			  }

			  ATF::loyer()->q->reset()->where("loyer.id_affaire", $get['id_affaire']);
			  $data["loyer"] = ATF::loyer()->sa();

			  $data["comites"] = $this->getComite($get["id_affaire"]);
			  //$data["etat_comite_cleodis"] = "en_attente"; //état par defaut vu que le comite est inséré automatiquement quelque soit le resultat sgef/creditSafe

			  $data["file_devis"] = file_exists($this->filepath($get['id_affaire'],"devis")) ? "oui" : "non";

			  foreach ($data["comites"] as $key => $value) {
			  	if($value['description']=== 'Comité CLEODIS'){
			  		$data["etat_comite_cleodis"] = $value['etat']; //je (Anthony) rajoute cet etat car la propriété "etat_comite" de base renvoyé ne concerne pas le comite cleodis
			  	}
			  }
			  /*$this->q->reset()->where("affaire.id_affaire", $data["id_affaire"]);
			  $data["affaireAffaire"] = $this->sa();
			  */
			  $data["idcrypted"] = $this->cryptId($get["id_affaire"]);
			  ATF::commande()->q->reset()->where("commande.id_affaire",$this->decryptId($get["id_affaire"]));
			  $commande = ATF::commande()->select_row();

			  $data['id_commande_crypt'] = ATF::commande()->cryptId($commande['commande.id_commande']);

			} else {
				// Filtre sur l'etat de l'affaire
				if ($get['filters']['accepte'] == "on") $this->q->where("affaire.etat_comite","accepte","OR","etatComite");
				if ($get['filters']['refuse'] == "on") $this->q->where("affaire.etat_comite","refuse","OR","etatComite");
				if ($get['filters']['attente'] == "on") $this->q->where("affaire.etat_comite","attente","OR","etatComite");
				if ($get['filters']['commande'] == "on") $this->q->whereIsNotNull("bon_de_commande.id_commande");
				if ($get['filters']['atraiter'] == "on") $this->q->whereIsNull("bon_de_commande.id_commande");


				if ($get['filters']['startdate']) {
					$this->q
			    	->where("affaire.date", $get['filters']['startdate'], "AND", false, ">=");
				}

				if ($get['filters']['enddate']) {
				  $this->q
			    	->where("affaire.date", $get['filters']['enddate'], "AND", false, "<=");
				}

				//filtre sur l'etat de l'affaire en fonction de la vue
				if ($get['filters']['devis']) {
				  if ($get['filters']['devis']['relancer']) {
				    //devis sans bpa -> Sans contrat  sans Premiere date accord sur le devis)
				    $this->q->from("affaire", "id_affaire", "devis", "id_affaire")
				    		->whereIsNull("devis.first_date_accord");
				  }
				  if ($get['filters']['devis']['gagnes']) {
				    //devis transformé en contrat
				    $this->q->from("affaire", "id_affaire", "devis", "id_affaire")
				    		->where("affaire.etat","commande",'OR',"affaireEtat")
				    		->where("affaire.etat","facture",'OR',"affaireEtat")
				    		->where("affaire.etat","demande_refi",'OR',"affaireEtat")
				    		->where("affaire.etat","facture_refi",'OR',"affaireEtat");
				  }
				  if ($get['filters']['devis']['perdus']) {
				    //devis perdu
				    $this->q->from("affaire", "id_affaire", "devis", "id_affaire")
				    		->where("devis.etat","perdu");
				  }
				}

				if ($get['filters']['actif']) {
				  if ($get['filters']['actif']['encours']) {
				    //contrat en cours
				    $this->q
				    	->where("commande.etat","mis_loyer","OR","etat_contrat")
				    	->where("commande.etat","mis_loyer_contentieux","OR","etat_contrat");
				  }
				  if ($get['filters']['actif']['prolongation']) {
				  	$this->q
				    	->where("commande.etat","prolongation","OR","etat_contrat")
				    	->where("commande.etat","prolongation_contentieux","OR","etat_contrat");
				  }
				  if ($get['filters']['actif']['restitution']) {
				  	$this->q
				    	->where("commande.etat","restitution","OR","etat_contrat")
				    	->where("commande.etat","restitution_contentieux","OR","etat_contrat");
				  }
				  if ($get['filters']['actif']['contentieux']) {
				  	$this->q
				    	->where("commande.etat","mis_loyer_contentieux","OR","etat_contrat")
				    	->where("commande.etat","prolongation_contentieux","OR","etat_contrat")
				    	->where("commande.etat","restitution_contentieux","OR","etat_contrat");
				  }
				}

				if ($get['filters']['finance']) {
				  $res  = $this->sa($get['tri'],$get['trid'],false, false);
				  $data["data"] = array();

				  if ($get['filters']['finance']['valider']) {
				    //emis sans reponse
				    foreach ($res["data"] as $k => $v) {
				    	ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
					    						 ->where("comite.etat", "en_attente");
				    	$comite = ATF::comite()->sa();
				    	if($comite){
				    		$data["data"][$k] = $v;
				    	}
				    }
				  }
				  if ($get['filters']['finance']['acceptes']) {
				    //au moins 1 accord comité
				    foreach ($res["data"] as $k => $v) {
				    	ATF::comite()->q->reset()->where("id_affaire",$v['affaire.id_affaire_fk'])
				    							 ->where("comite.etat", "accepte");
				    	$comite = ATF::comite()->sa();
				    	if($comite){
				    		$data["data"][$k] = $v;
				    	}
				    }
				  }
				  if ($get['filters']['finance']['refuses']) {
				    //aucun comité accepté
				    foreach ($res["data"] as $k => $v) {
					    ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
					    						 ->where("comite.etat", "accepte","OR")
					    						 ->where("comite.etat", "en_attente");
				    	$comite = ATF::comite()->sa();

				    	if(!$comite){
				    		$data["data"][$k] = $v;
				    	}
				    }
				  }
				  if ($get['filters']['finance']['faire']) {
				    //affaire sans comité
				    foreach ($res["data"] as $k => $v) {
				    	ATF::comite()->q->reset()->where("id_affaire",$v['affaire.id_affaire_fk']);
				    	$comite = ATF::comite()->sa();
				    	if(!$comite){
				    		$data["data"][$k] = $v;
				    	}
				    }
				  }

				  $d = $data["data"];
				  $data["data"] = array();
				  foreach ($d as $key => $value) {
				  	$data["data"][] = $value;
				  }


				  $data["count"] = count($data["data"]);
				}

				if ($get['filters']['administratif']) {

				  if ($get['filters']['administratif']['verifier']) {
				    $this->q
				    	->whereIsNull("affaire.date_verification");
				  }

				  if ($get['filters']['administratif']['acceptes']) {
				  	$this->q
				    	->whereIsNotNull("affaire.date_verification", "AND")
				    	->where("affaire.pieces","OK","AND");
				    //pieces verifiées avec date de verification
				  }

				  if ($get['filters']['administratif']['incomplets']) {
				    //pieces refusées ou manquantes
				    $this->q
				    	->whereIsNotNull("affaire.date_verification", "AND")
				    	->where("affaire.pieces","NOK","AND");
				  }
				}

				if ($get['filters']['commande']) {
				  $res = $this->sa($get['tri'],$get['trid'],false, false);
				  $d = array();

				  if ($get['filters']['commande']['verifier']) {
				    //pas encore de date de vérif FINANCE et/ou MONTAGE
				    //Pas de date de verif des pieces ou dernier comite pas accepte
				    foreach ($res["data"] as $k => $v) {
				    	if(!$this->select($v["affaire.id_affaire_fk"], "date_verification")){
				    		$d[$k] = $v;
				    	}else{
				    		ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
					    						   ->setLimit(1)
					    						   ->addOrder("id_comite","DESC");
					    	$comite = ATF::comite()->select_row();
					    	if($comite["etat"] == "en_attente"){
					    		$d[$k] = $v;
					    	}
					    }
				    }
				  }
				  if ($get['filters']['commande']['commander']) {
				    //finance et montage ok
				    foreach ($res["data"] as $k => $v) {
				    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
				    	$bdc = ATF::bon_de_commande()->select_row();
				    	if(!$bdc){
					    	if($this->select($v["affaire.id_affaire_fk"], "date_verification")){
					    		ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
						    						   ->setLimit(1)
						    						   ->addOrder("id_comite","DESC");
						    	$comite = ATF::comite()->select_row();
						    	if($comite["etat"] == "accepte"){
						    		$d[$k] = $v;
						    	}
					    	}
					    }
				    }
				  }
				  if ($get['filters']['commande']['commandes']) {
				    //commande passée, facture non reçue
				    //on a un bon de commande mais pas de facture fournisseur pour cette commande
				    foreach ($res["data"] as $k => $v) {
				    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
				    	if($bdc = ATF::bon_de_commande()->select_row()){
				    		ATF::facture_fournisseur()->q->reset()->where("id_bon_de_commande", $bdc["bon_de_commande.id_bon_de_commande"]);
				    		$ff = ATF::facture_fournisseur()->sa();

				    		if(!$ff){
				    			$d[$k] = $v;
				    		}
				    	}
				    }
				  }
				  if ($get['filters']['commande']['livres']) {
				  	//on a un bon de commande et une de facture fournisseur pour cette commande
				  	foreach ($res["data"] as $k => $v) {
				    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
				    	if($bdc = ATF::bon_de_commande()->select_row()){
				    		ATF::facture_fournisseur()->q->reset()->where("id_bon_de_commande", $bdc["bon_de_commande.id_bon_de_commande"]);
				    		$ff = ATF::facture_fournisseur()->sa();
				    		if($ff){
				    			$d[$k] = $v;
				    		}
				    	}
				    }
				  }

				  $data["data"] = array();
				  foreach ($d as $key => $value) {
				  	$data["data"][] = $value;
				  }
				  $data["count"] = count($d);
				}

				if ($get['filters']['contrat']) {
				   $res  = $this->sa($get['tri'],$get['trid'],false, false);
				   $data["data"] = array();

				  if ($get['filters']['contrat']['recusok']) {
				    //cad VALIDATION DOSSIER OK : FINANCE acceptés et MONTAGE acceptés + PV ok
				    //Finance -> au moins un comité accepte
				    //Montage verification des pieces
				    foreach ($res["data"] as $k => $v) {
				    	ATF::commande()->q->reset()->where("commande.id_affaire",$this->decryptId($v["affaire.id_affaire_fk"]));
							$commande = ATF::commande()->select_row();

							if(
								$commande
								&& file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour"))
								&& file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV"))
							){
								// le contrat a été signé & PV retourné
					    	if($this->select($v["affaire.id_affaire_fk"], "pieces")  === 'OK' ){
					    		// puis validation des pieces OK
					    		ATF::comite()->q->reset()->where("comite.id_affaire", $v['affaire.id_affaire_fk'])
					    								->addOrder("comite.id_comite","DESC")
					    								->setLimit(1);
					    		$comite = ATF::comite()->select_row();
					    		if($comite["etat"] == "accepte"){
					    			$d[$k]= $v;
					    		}
					    	}
							}
				    }
				  }
				  if ($get['filters']['contrat']['pv']) {
				    //cad Contrat signe OK
				   	// Commande livree OK
				    //Finance -> OK
				    //Montage verification des pieces
				    foreach ($res["data"] as $k => $v) {
				    	ATF::commande()->q->reset()->where("commande.id_affaire",$this->decryptId($v["affaire.id_affaire_fk"]));
							$commande = ATF::commande()->select_row();

							if(
								$commande
								&& file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour"))
								&& !file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV"))
							){
								// le contrat a été signé & PV non retourné
					    	if($this->select($v["affaire.id_affaire_fk"], "pieces")  === 'OK' ){
					    		// puis validation des pieces OK
					    		ATF::comite()->q->reset()->where("comite.id_affaire", $v['affaire.id_affaire_fk'])
					    								->addOrder("comite.id_comite","DESC")
					    								->setLimit(1);
					    		$comite = ATF::comite()->select_row();
					    		if($comite["etat"] == "accepte"){
					    			$d[$k]= $v;
					    		}
					    	}
							}
						}
				  }
				  if ($get['filters']['contrat']['recusko']) {
				    //Commandes passées, mais FINANCE NOK ou MONTAGE NOK
				    foreach ($res["data"] as $k => $v) {
				    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
				    	$bdc = ATF::bon_de_commande()->select_all();
				    	if($bdc){
				    		if($this->select($v["affaire.id_affaire_fk"], "pieces") === NULL){
				    			$d[$k]= $v;
				    		}else{
				    			ATF::comite()->q->reset()->where("comite.id_affaire", $v['affaire.id_affaire_fk'])
				    								->addOrder("comite.id_comite","DESC")
				    								->setLimit(1);
				    			$comite = ATF::comite()->select_row();

				    			if($comite["etat"] !== "accepte"){
				    				$d[$k]= $v;
				    			}
				    		}
				    	}
				    }
				  }

				  $data["data"] = array();
				  foreach ($d as $key => $value) {
				  	$data["data"][] = $value;
				  }
				  $data["count"] = count($d);
				}

				if(!$data){
					if (!$get['no-limit']) $this->q->setLimit($get['limit']);
					$data = $this->sa($get['tri'],$get['trid'],$get['page'],true);
				}


				foreach ($data['data'] as $key => $value) {
					foreach ($value as $k_=>$val) {
						if (strpos($k_,".")) {
							$tmp = explode(".",$k_);
							$data['data'][$key][$tmp[1]] = $val;
							unset($data['data'][$key][$k_]);
						}
					}

					// pour chaque affaire on recupere ses comites
					foreach ($this->getComite($data['data'][$key]["id_affaire_fk"]) as $k => $comite) {
						if($comite['description']=== 'Comité CLEODIS'){
							$data['data'][$key]["etat_comite_cleodis"] = $comite['etat']; //je (Anthony) rajoute cet etat car la propriété "etat_comite" de base renvoyé ne concerne pas le comite cleodis
						}
					}

			  	$data['data'][$key]["contact"] = ATF::contact()->select($value['societe.id_contact_signataire']);
				  $data['data'][$key]["cni"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"cni")) ? true : false;
					$data['data'][$key]["idcrypted"] = $this->cryptId($value['affaire.id_affaire_fk']);
					if($loyer = $this->getLoyers($value['affaire.id_affaire_fk'])){
							$data['data'][$key]["loyer"] = $loyer; // on recupere tous les loyers
							$data['data'][$key]["duree"] = 0; // la duree de l'affaire (somme des durees de tous les loyers)
							$data['data'][$key]["montant"] = 0; // montant => duree * montant loyer de tous les loyers
							foreach ($loyer as $i => $l) {
								$data['data'][$key]["duree"] += $l["duree"];
								$data['data'][$key]["montant"] += $l["loyer"]*$l["duree"];
							}
					}


				  ATF::commande()->q->reset()->where("commande.id_affaire",$value['affaire.id_affaire_fk']);
				  $commande = ATF::commande()->select_row();
				  $data['data'][$key]["retourPV"] = false;
					$data['data'][$key]["payee"] = $this->paiementIsReceived($data['data'][$key]['id_affaire_fk'], true);
				  if($commande){
					$data['data'][$key]["contrat_signe"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour")) ? true : false;

					$data['data'][$key]["retourPV"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV")) ? true : false;
				  }else{
					$data['data'][$key]["contrat_signe"] = false;
				  }
				}
			}
			if($get['id_affaire']){
				$return = $data;
			}else{
				header("ts-total-row: ".$data['count']);
				if ($get['limit']) header("ts-max-page: ".ceil($data['count']/$get['limit']));
				if ($get['page']) header("ts-active-page: ".$get['page']);
				if ($get['no-limit']) header("ts-no-limit: 1");
				$return = $data['data'];
			}

			return $return;
		} else{
			throw new errorATF("Probleme d'apporteur",500);
		}

	}

	/**
	*
	* Fonctions _GET pour telescope
	* @package Telescope
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $get array contient le tri, page limit et potentiellement un id.
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function _GET($get,$post) {
	 	// Gestion du tri
	 	if (!$get['tri'] || $get['tri'] == 'action') $get['tri'] = "affaire.date";
	 	if (!$get['trid']) $get['trid'] = "desc";

	 	// Gestion du limit
	 	if (!$get['limit'] && !$get['no-limit']) $get['limit'] = 30;

	 	// Gestion de la page
	 	if (!$get['page']) $get['page'] = 0;
	 	if ($get['no-limit']) $get['page'] = false;
	 	$colsData = array("affaire.affaire",
	 		"affaire.id_affaire",
	 		"affaire.etat",
	 		'affaire.provenance',
	 		'affaire.date',
	 		'affaire.ref',
	 		'affaire.etat_comite',
	 		'affaire.id_societe',
	 		'societe.societe',
	 		'societe.id_contact_signataire',
	 		'loyer.loyer');
		$this->q->reset();


		$this->q->setCount();


	 	if ($get['id_affaire']) $colsData = array("affaire.affaire","affaire.id_affaire","affaire.etat",'affaire.provenance','affaire.date','affaire.ref','affaire.etat_comite','affaire.id_societe', 'affaire.pieces', 'affaire.date_verification');

	 	$this->q->addField($colsData)
				->addField("Count(bon_de_commande.id_bon_de_commande)","total_bdc")
				->addField("Count(commande.id_commande)","nb_contrat")
				->from("affaire","id_societe","societe","id_societe")
				->from("societe","id_contact_signataire","contact","id_contact")
				->from("affaire","id_affaire","bon_de_commande","id_affaire")
				->from("affaire","id_affaire","commande","id_affaire")
				->from("affaire","id_affaire","loyer","id_affaire")
				->from("affaire", "id_affaire", "commande", "id_affaire");
		if($get['site_associe'] && $get['site_associe'] === 'toshiba'){
		 	$this->q->where("site_associe",'toshiba')
				->addGroup("affaire.id_affaire");
		}else if ($get['site_associe'] && $get['site_associe'] === 'cleodis'){
		 	$this->q->whereIsNull("site_associe")
		 			->orWhere("site_associe",'')
				->addGroup("affaire.id_affaire");
		}else if ($get['provenance'] && $get['provenance'] === 'true' && !$get['site_associe']){
		 	$this->q->where("provenance",'partenaire')
		 		->whereIsNotNull('affaire.id_partenaire')
				->addGroup("affaire.id_affaire");
		}

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get['search']);
		}

		if ($get['id_affaire']) {
		  $this->q->where("affaire.id_affaire",$this->decryptId($get["id_affaire"]))->setCount(false)->setDimension('row');
		  $data = $this->sa();
		  // on check si l'affaire est "payee"
		  $data['payee'] = $this->paiementIsReceived($data['affaire.id_affaire_fk']);
		  // on set les infos signataire pour la relance des paiements
		  $data['infos_signataire'] = $this->getInfoSignataire($data['affaire.id_affaire_fk']);
		  ATF::devis()->q->reset()->addField("CONCAT(SUBSTR(user.prenom, 1,1),'. ',user.nom)","user")
					  ->addField("devis.*")
					  ->from("devis","id_user","user","id_user")
					  ->where("devis.id_affaire",$this->decryptId($get["id_affaire"]))->addOrder('id_devis', 'desc');
		  $data["devis"] = ATF::devis()->sa();

		  ATF::loyer()->q->reset()->addField("*")
					  ->from("loyer","id_affaire","affaire","id_affaire")
					  ->where("loyer.id_affaire",$this->decryptId($get["id_affaire"]))->addOrder('id_loyer', 'desc');
		  $data["loyer"] = ATF::loyer()->sa();


		  $data["contact"] = ATF::contact()->select(ATF::societe()->select($data["affaire.id_societe_fk"], "id_contact_signataire"));
		  $data["retourPV"] = NULL;
		  $data["pouvoir"] = file_exists(ATF::affaire()->filepath($get['id_affaire'],"pouvoir")) ? true : false;

		  foreach ($data as $key => $value) {
			if (strpos($key,".")) {
				$tmp = explode(".",$key);
				$data[$tmp[1]] = $value;
				unset($data[$key]);
			}
			if($key == "affaire.id_societe_fk"){
				ATF::societe()->q->reset()
					->addField("adresse")
					->addField("adresse_2")
					->addField("adresse_3")
					->addField("code_client")
					->addField("societe")
					->addField("cp")
					->addField("ville")
					->addField("pays.pays")
					->addField("tel")
					->from("pays","id_pays","societe","id_pays")
					->where("id_societe", $value)

					->setDimension('row');

				$data["societe"] = ATF::societe()->sa();
				$data["societe"]['id_pays'] = $data["societe"]["pays.pays"];
				unset($data["societe"]["pays.pays"],$data["societe"]["pays.pays_fk"]);

			}
			if($key == "id_commercial") $data["user"] = ATF::user()->select($value);

			unset($data["id_societe"],  $data["id_commercial"]);
		  }
		  foreach ($data["devis"] as $key => $value) {
			$data['devis'][$key]["fichier_joint"] = $data['devis'][$key]["documentAnnexes"] = false;
			if (file_exists(ATF::devis()->filepath($value['id_devis'],"fichier_joint"))) $data['devis'][$key]["fichier_joint"] = true;
			if (file_exists(ATF::devis()->filepath($value['id_devis'],"documentAnnexes"))) $data['devis'][$key]["documentAnnexes"] = true;
		  }

		  ATF::loyer()->q->reset()->where("loyer.id_affaire", $get['id_affaire']);
		  $data["loyer"] = ATF::loyer()->sa();

		  $data["comites"] = $this->getComite($get["id_affaire"]);
		  //$data["etat_comite_cleodis"] = "en_attente"; //état par defaut vu que le comite est inséré automatiquement quelque soit le resultat sgef/creditSafe

		  $data["file_cni"] = file_exists($this->filepath($get['id_affaire'],"cni")) ? "oui" : "non";
		  $data["file_cniVerso"] = file_exists($this->filepath($get['id_affaire'],"cniVerso")) ? "oui" : "non";

		  foreach ($data["comites"] as $key => $value) {
		  	if($value['description']=== 'Comité CLEODIS'){
		  		$data["etat_comite_cleodis"] = $value['etat']; //je (Anthony) rajoute cet etat car la propriété "etat_comite" de base renvoyé ne concerne pas le comite cleodis
		  	}
		  }
		  /*$this->q->reset()->where("affaire.id_affaire", $data["id_affaire"]);
		  $data["affaireAffaire"] = $this->sa();
		  */
		  $data["idcrypted"] = $this->cryptId($get["id_affaire"]);
		  ATF::commande()->q->reset()->where("commande.id_affaire",$this->decryptId($get["id_affaire"]));
		  $commande = ATF::commande()->select_row();
		  if($commande){
			$data["contrat_signe"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour")) ? true : false;
			$data["retourPV"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV")) ? true : false;
		  }else{
			$data["contrat_signe"] = false;
		  }
		  $data['id_commande_crypt'] = ATF::commande()->cryptId($commande['commande.id_commande']);

		} else {
			// Filtre sur l'etat de l'affaire
			if ($get['filters']['accepte'] == "on") $this->q->where("affaire.etat_comite","accepte","OR","etatComite");
			if ($get['filters']['refuse'] == "on") $this->q->where("affaire.etat_comite","refuse","OR","etatComite");
			if ($get['filters']['attente'] == "on") $this->q->where("affaire.etat_comite","attente","OR","etatComite");
			if ($get['filters']['commande'] == "on") $this->q->whereIsNotNull("bon_de_commande.id_commande");
			if ($get['filters']['atraiter'] == "on") $this->q->whereIsNull("bon_de_commande.id_commande");


			if ($get['filters']['startdate']) {
				$this->q
		    	->where("affaire.date", $get['filters']['startdate'], "AND", false, ">=");
			}

			if ($get['filters']['enddate']) {
			  $this->q
		    	->where("affaire.date", $get['filters']['enddate'], "AND", false, "<=");
			}

			//filtre sur l'etat de l'affaire en fonction de la vue
			if ($get['filters']['devis']) {
			  if ($get['filters']['devis']['relancer']) {
			    //devis sans bpa -> Sans contrat  sans Premiere date accord sur le devis)
			    $this->q->from("affaire", "id_affaire", "devis", "id_affaire")
			    		->whereIsNull("devis.first_date_accord");
			  }
			  if ($get['filters']['devis']['gagnes']) {
			    //devis transformé en contrat
			    $this->q->from("affaire", "id_affaire", "devis", "id_affaire")
			    		->where("affaire.etat","commande",'OR',"affaireEtat")
			    		->where("affaire.etat","facture",'OR',"affaireEtat")
			    		->where("affaire.etat","demande_refi",'OR',"affaireEtat")
			    		->where("affaire.etat","facture_refi",'OR',"affaireEtat");
			  }
			  if ($get['filters']['devis']['perdus']) {
			    //devis perdu
			    $this->q->from("affaire", "id_affaire", "devis", "id_affaire")
			    		->where("devis.etat","perdu");
			  }
			}

			if ($get['filters']['actif']) {
			  if ($get['filters']['actif']['encours']) {
			    //contrat en cours
			    $this->q
			    	->where("commande.etat","mis_loyer","OR","etat_contrat")
			    	->where("commande.etat","mis_loyer_contentieux","OR","etat_contrat");
			  }
			  if ($get['filters']['actif']['prolongation']) {
			  	$this->q
			    	->where("commande.etat","prolongation","OR","etat_contrat")
			    	->where("commande.etat","prolongation_contentieux","OR","etat_contrat");
			  }
			  if ($get['filters']['actif']['restitution']) {
			  	$this->q
			    	->where("commande.etat","restitution","OR","etat_contrat")
			    	->where("commande.etat","restitution_contentieux","OR","etat_contrat");
			  }
			  if ($get['filters']['actif']['contentieux']) {
			  	$this->q
			    	->where("commande.etat","mis_loyer_contentieux","OR","etat_contrat")
			    	->where("commande.etat","prolongation_contentieux","OR","etat_contrat")
			    	->where("commande.etat","restitution_contentieux","OR","etat_contrat");
			  }
			}

			if ($get['filters']['finance']) {
			  $res  = $this->sa($get['tri'],$get['trid'],false, false);
			  $data["data"] = array();

			  if ($get['filters']['finance']['valider']) {
			    //emis sans reponse
			    foreach ($res["data"] as $k => $v) {
			    	ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
				    						 ->where("comite.etat", "en_attente");
			    	$comite = ATF::comite()->sa();
			    	if($comite){
			    		$data["data"][$k] = $v;
			    	}
			    }
			  }
			  if ($get['filters']['finance']['acceptes']) {
			    //au moins 1 accord comité
			    foreach ($res["data"] as $k => $v) {
			    	ATF::comite()->q->reset()->where("id_affaire",$v['affaire.id_affaire_fk'])
			    							 ->where("comite.etat", "accepte");
			    	$comite = ATF::comite()->sa();
			    	if($comite){
			    		$data["data"][$k] = $v;
			    	}
			    }
			  }
			  if ($get['filters']['finance']['refuses']) {
			    //aucun comité accepté
			    foreach ($res["data"] as $k => $v) {
				    ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
				    						 ->where("comite.etat", "accepte","OR")
				    						 ->where("comite.etat", "en_attente");
			    	$comite = ATF::comite()->sa();

			    	if(!$comite){
			    		$data["data"][$k] = $v;
			    	}
			    }
			  }
			  if ($get['filters']['finance']['faire']) {
			    //affaire sans comité
			    foreach ($res["data"] as $k => $v) {
			    	ATF::comite()->q->reset()->where("id_affaire",$v['affaire.id_affaire_fk']);
			    	$comite = ATF::comite()->sa();
			    	if(!$comite){
			    		$data["data"][$k] = $v;
			    	}
			    }
			  }

			  $d = $data["data"];
			  $data["data"] = array();
			  foreach ($d as $key => $value) {
			  	$data["data"][] = $value;
			  }


			  $data["count"] = count($data["data"]);
			}

			if ($get['filters']['administratif']) {

			  if ($get['filters']['administratif']['verifier']) {
			    $this->q
			    	->whereIsNull("affaire.date_verification");
			  }

			  if ($get['filters']['administratif']['acceptes']) {
			  	$this->q
			    	->whereIsNotNull("affaire.date_verification", "AND")
			    	->where("affaire.pieces","OK","AND");
			    //pieces verifiées avec date de verification
			  }

			  if ($get['filters']['administratif']['incomplets']) {
			    //pieces refusées ou manquantes
			    $this->q
			    	->whereIsNotNull("affaire.date_verification", "AND")
			    	->where("affaire.pieces","NOK","AND");
			  }
			}

			if ($get['filters']['commande']) {
			  $res = $this->sa($get['tri'],$get['trid'],false, false);
			  $d = array();

			  if ($get['filters']['commande']['verifier']) {
			    //pas encore de date de vérif FINANCE et/ou MONTAGE
			    //Pas de date de verif des pieces ou dernier comite pas accepte
			    foreach ($res["data"] as $k => $v) {
			    	if(!$this->select($v["affaire.id_affaire_fk"], "date_verification")){
			    		$d[$k] = $v;
			    	}else{
			    		ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
				    						   ->setLimit(1)
				    						   ->addOrder("id_comite","DESC");
				    	$comite = ATF::comite()->select_row();
				    	if($comite["etat"] == "en_attente"){
				    		$d[$k] = $v;
				    	}
				    }
			    }
			  }
			  if ($get['filters']['commande']['commander']) {
			    //finance et montage ok
			    foreach ($res["data"] as $k => $v) {
			    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
			    	$bdc = ATF::bon_de_commande()->select_row();
			    	if(!$bdc){
				    	if($this->select($v["affaire.id_affaire_fk"], "date_verification")){
				    		ATF::comite()->q->reset()->where("comite.id_affaire",$v['affaire.id_affaire_fk'])
					    						   ->setLimit(1)
					    						   ->addOrder("id_comite","DESC");
					    	$comite = ATF::comite()->select_row();
					    	if($comite["etat"] == "accepte"){
					    		$d[$k] = $v;
					    	}
				    	}
				    }
			    }
			  }
			  if ($get['filters']['commande']['commandes']) {
			    //commande passée, facture non reçue
			    //on a un bon de commande mais pas de facture fournisseur pour cette commande
			    foreach ($res["data"] as $k => $v) {
			    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
			    	if($bdc = ATF::bon_de_commande()->select_row()){
			    		ATF::facture_fournisseur()->q->reset()->where("id_bon_de_commande", $bdc["bon_de_commande.id_bon_de_commande"]);
			    		$ff = ATF::facture_fournisseur()->sa();

			    		if(!$ff){
			    			$d[$k] = $v;
			    		}
			    	}
			    }
			  }
			  if ($get['filters']['commande']['livres']) {
			  	//on a un bon de commande et une de facture fournisseur pour cette commande
			  	foreach ($res["data"] as $k => $v) {
			    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
			    	if($bdc = ATF::bon_de_commande()->select_row()){
			    		ATF::facture_fournisseur()->q->reset()->where("id_bon_de_commande", $bdc["bon_de_commande.id_bon_de_commande"]);
			    		$ff = ATF::facture_fournisseur()->sa();
			    		if($ff){
			    			$d[$k] = $v;
			    		}
			    	}
			    }
			  }

			  $data["data"] = array();
			  foreach ($d as $key => $value) {
			  	$data["data"][] = $value;
			  }
			  $data["count"] = count($d);
			}

			if ($get['filters']['contrat']) {
			   $res  = $this->sa($get['tri'],$get['trid'],false, false);
			   $data["data"] = array();

			  if ($get['filters']['contrat']['recusok']) {
			    //cad VALIDATION DOSSIER OK : FINANCE acceptés et MONTAGE acceptés + PV ok
			    //Finance -> au moins un comité accepte
			    //Montage verification des pieces
			    foreach ($res["data"] as $k => $v) {
			    	ATF::commande()->q->reset()->where("commande.id_affaire",$this->decryptId($v["affaire.id_affaire_fk"]));
						$commande = ATF::commande()->select_row();

						if(
							$commande
							&& file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour"))
							&& file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV"))
						){
							// le contrat a été signé & PV retourné
				    	if($this->select($v["affaire.id_affaire_fk"], "pieces")  === 'OK' ){
				    		// puis validation des pieces OK
				    		ATF::comite()->q->reset()->where("comite.id_affaire", $v['affaire.id_affaire_fk'])
				    								->addOrder("comite.id_comite","DESC")
				    								->setLimit(1);
				    		$comite = ATF::comite()->select_row();
				    		if($comite["etat"] == "accepte"){
				    			$d[$k]= $v;
				    		}
				    	}
						}
			    }
			  }
			  if ($get['filters']['contrat']['pv']) {
			    //cad Contrat signe OK
			   	// Commande livree OK
			    //Finance -> OK
			    //Montage verification des pieces
			    foreach ($res["data"] as $k => $v) {
			    	ATF::commande()->q->reset()->where("commande.id_affaire",$this->decryptId($v["affaire.id_affaire_fk"]));
						$commande = ATF::commande()->select_row();

						if(
							$commande
							&& file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour"))
							&& !file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV"))
						){
							// le contrat a été signé & PV non retourné
				    	if($this->select($v["affaire.id_affaire_fk"], "pieces")  === 'OK' ){
				    		// puis validation des pieces OK
				    		ATF::comite()->q->reset()->where("comite.id_affaire", $v['affaire.id_affaire_fk'])
				    								->addOrder("comite.id_comite","DESC")
				    								->setLimit(1);
				    		$comite = ATF::comite()->select_row();
				    		if($comite["etat"] == "accepte"){
				    			$d[$k]= $v;
				    		}
				    	}
						}
					}
			  }
			  if ($get['filters']['contrat']['recusko']) {
			    //Commandes passées, mais FINANCE NOK ou MONTAGE NOK
			    foreach ($res["data"] as $k => $v) {
			    	ATF::bon_de_commande()->q->reset()->where("bon_de_commande.id_affaire", $v['affaire.id_affaire_fk']);
			    	$bdc = ATF::bon_de_commande()->select_all();
			    	if($bdc){
			    		if($this->select($v["affaire.id_affaire_fk"], "pieces") === NULL){
			    			$d[$k]= $v;
			    		}else{
			    			ATF::comite()->q->reset()->where("comite.id_affaire", $v['affaire.id_affaire_fk'])
			    								->addOrder("comite.id_comite","DESC")
			    								->setLimit(1);
			    			$comite = ATF::comite()->select_row();

			    			if($comite["etat"] !== "accepte"){
			    				$d[$k]= $v;
			    			}
			    		}
			    	}
			    }
			  }

			  $data["data"] = array();
			  foreach ($d as $key => $value) {
			  	$data["data"][] = $value;
			  }
			  $data["count"] = count($d);
			}

			if(!$data){
				if (!$get['no-limit']) $this->q->setLimit($get['limit']);
				$data = $this->sa($get['tri'],$get['trid'],$get['page'],true);
			}



			foreach ($data['data'] as $key => $value) {
				foreach ($value as $k_=>$val) {
					if (strpos($k_,".")) {
						$tmp = explode(".",$k_);
						$data['data'][$key][$tmp[1]] = $val;
						unset($data['data'][$key][$k_]);
					}
				}

				// pour chaque affaire on recupere ses comites
				foreach ($this->getComite($data['data'][$key]["id_affaire_fk"]) as $k => $comite) {
					if($comite['description']=== 'Comité CLEODIS'){
						$data['data'][$key]["etat_comite_cleodis"] = $comite['etat']; //je (Anthony) rajoute cet etat car la propriété "etat_comite" de base renvoyé ne concerne pas le comite cleodis
					}
				}

		  	$data['data'][$key]["contact"] = ATF::contact()->select($value['societe.id_contact_signataire']);
			  $data['data'][$key]["cni"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"cni")) ? true : false;
		  	  $data['data'][$key]["idcrypted"] = $this->cryptId($value['affaire.id_affaire_fk']);

			  ATF::commande()->q->reset()->where("commande.id_affaire",$value['affaire.id_affaire_fk']);
			  $commande = ATF::commande()->select_row();
			  $data['data'][$key]["retourPV"] = false;
				$data['data'][$key]["payee"] = $this->paiementIsReceived($data['data'][$key]['id_affaire_fk']);
			  if($commande){
				$data['data'][$key]["contrat_signe"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour")) ? true : false;

				$data['data'][$key]["retourPV"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV")) ? true : false;
			  }else{
				$data['data'][$key]["contrat_signe"] = false;
			  }
			}
		}
		if($get['id_affaire']){
			$return = $data;
		}else{
			header("ts-total-row: ".$data['count']);
			if ($get['limit']) header("ts-max-page: ".ceil($data['count']/$get['limit']));
			if ($get['page']) header("ts-active-page: ".$get['page']);
			if ($get['no-limit']) header("ts-no-limit: 1");
			$return = $data['data'];
		}
		return $return;
	}

	/**
	*
	* Fonctions _updatePiece pour cleoscope
	* @package Telescope
	* @author Anthony LAHLAH <alahlah@absystech.fr>
	* @param $get array.
	* @param $post array qui contient ok ou notok pour la validation des pièces.
	* @return true ou false, resultat du traitement
	*/
	public function _updatePiece($get,$post) {
		if (!$post['id_affaire']) throw new Exception("Il manque l'id de l'affaire", 500);

		// au cas ou il y aurait un changement de format d'id transmis
		$id_affaire =  strlen($post["id_affaire"]) === 32 ?  ATF::affaire()->decryptId($post["id_affaire"]) : $post['id_affaire'];
		$action = "OK";
		$etat = "valide_administratif";

		try {
			ATF::affaire()->update(array(
				'id_affaire'=>$id_affaire
				,'pieces'=>$action
				,'date_verification'=>date("Y-m-d")
			));

			ATF::affaire_etat()->insert(array(
				'id_affaire'=>$id_affaire
				,'etat'=>$etat
				,'id_user'=>ATF::$usr->get('id_user')
			));

			return true;
		} catch (Exception $e) {
			return false;
		}

	}


	public function getComite($id_affaire){
		ATF::comite()->q->reset()
		->from("comite","id_refinanceur","refinanceur","id_refinanceur")
		->where("comite.id_affaire" , $id_affaire);
		return ATF::comite()->sa();
	}

	/**
	 * Retourne le premier loyer d'une affaire, utilisé pour le paiement CB Toshiba
	 * Si aucun loyer, on retourne false
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @author Anthony LAHLAH <alahlah@absystech.fr>
	 * @param  array $get  (id_affaire)
	 * @param  array $post
	 * @return float|boolean       le loyer, sinon FALSE si déjà payé
	 */
	public function _get_loyer($get,$post){

		$id_affaire = $this->decryptId($get["id_affaire"]);
		if ($this->paiementIsReceived($id_affaire)){
			return false;
		} else {
			ATF::loyer()->q->reset()->where("id_affaire", $id_affaire)
				->addOrder("id_loyer", "ASC")
				->setLimit(1);
			$loyer = ATF::loyer()->select_row();
			return $loyer;
		}
	}

	/** Fonction qui crée une affaire partenaire
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _CreateAffairePartenaire($get,$post) {
		$id_societe = $post['id_societe']; //ATF::societe()->decryptId($post['id_societe']);
		$devis = array(
	      "id_societe" => $id_societe,
	      "type_contrat" => "lld",
	      "validite" => date("d-m-Y", strtotime("+1 month")),
	      "tva" => __TVA__,
	      "devis" => $post['libelle'],
	      "date" => date("d-m-Y"),
	      "type_devis" => "normal",
	      "id_contact" => $post["gerant"],
	      "id_user"=>ATF::usr()->getId(), // + tard id de l'user loggué sur
	      "type_affaire" => "normal");
      	$values_devis =array();

      	$montantLoyer = $duree = 0;

      	$loyer = array();
      	$produits = array();
      	ATF::produit()->q->reset()->where('id_produit',$post["id_produit"]);
	    $produit = ATF::produit()->select_all();
      	$loyer[0] = array(
            "loyer__dot__loyer"=>$post["loyer"],
            "loyer__dot__duree"=>$post["duree"],
            "loyer__dot__type"=>"engagement",
            "loyer__dot__assurance"=>"",
            "loyer__dot__frais_de_gestion"=>"",
            "loyer__dot__frequence_loyer"=>"mois",
            "loyer__dot__serenite"=>"",
            "loyer__dot__maintenance"=>"",
            "loyer__dot__hotline"=>"",
            "loyer__dot__supervision"=>"",
            "loyer__dot__support"=>"",
            "loyer__dot__avec_option"=>"non"
        );
	    $produits[0] = array(
          "devis_ligne__dot__produit"=> $produit[0]["produit"],
          "devis_ligne__dot__quantite"=>1,
          "devis_ligne__dot__type"=>"sans_objet",
          "devis_ligne__dot__ref"=>$produit[0]["ref"],
          "devis_ligne__dot__prix_achat"=>$produit[0]["prix_achat"],
          "devis_ligne__dot__id_produit"=>$produit[0]["id_produit"],
          "devis_ligne__dot__id_fournisseur"=>"TOSHIBA TEC",
          "devis_ligne__dot__visibilite_prix"=>"invisible",
          "devis_ligne__dot__date_achat"=>"",
          "devis_ligne__dot__commentaire"=>"",
          "devis_ligne__dot__neuf"=>"oui",
          "devis_ligne__dot__id_produit_fk"=>$produit[0]["id_produit"],
          "devis_ligne__dot__id_fournisseur_fk"=>"5474"
        );
	    $values_devis = array("loyer"=>json_encode($loyer), "produits"=>json_encode($produits));

        $id_devis = ATF::devis()->insert(array("devis"=>$devis, "values_devis"=>$values_devis));

	    $devis = ATF::devis()->select($id_devis);
	    // récupérer dans la session l'id societe partenaire quic rée le contrat
	    // @ccharlier@absystech.fr
	    ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"],"provenance"=>"partenaire",'id_apporteur'=>28531));

        ATF::affaire_etat()->insert(array(
            "id_affaire"=>$devis["id_affaire"],
            "etat"=>"reception_demande"
        ));
		$societe = ATF::societe()->select($post["id_societe"]);
      	$comite = array  (
            "id_societe" => $id_societe,
            "id_affaire" => $devis["id_affaire"],
            "id_contact" => $post['gerant'],
            "activite" => $societe["activite"],
            "id_refinanceur" => 4,
            "date_creation" => $societe["date_creation"],
            "date_compte" => $societe["lastaccountdate"],
            "capitaux_propres" => $societe["capitaux_propres"],
            "note" => $societe["cs_score"],
            "dettes_financieres" => $societe["dettes_financieres"],
            "limite" => $societe["cs_avis_credit"],
            "ca" => $societe["ca"],
            "capital_social" => $societe["capital_social"],
            "resultat_exploitation" => $societe["resultat_exploitation"],
            "date" => date("d-m-Y"),
            "description" => "Comite CreditSafe",
            "suivi_notifie"=>array(0=>"")
        );
		$creation = new DateTime( $societe["date_creation"] );
        $creation = $creation->format("Ymd");
        $past2Years = new DateTime( date("Y-m-d", strtotime("-2 years")) );
        $past2Years = $past2Years->format("Ymd");

      	if($societe["cs_score"] > 50 && $creation < $past2Years ){
        	$comite["etat"] = "accepte";
        	$comite["decisionComite"] = "Accepté automatiquement";
     	}else{
        	$comite["etat"] = "refuse";
        	$comite["decisionComite"] = "Refusé automatiquement (Note < 50, ou ancienneté < 2ans";
      	}

        $comite["reponse"] = date("Y-m-d");
        $comite["validite_accord"] = date("Y-m-d");

        try{
            ATF::comite()->insert(array("comite"=>$comite));
        }catch (errorATF $e) {
            throw new errorATF($e->getMessage() ,500);
        }
        if($comite["etat"]== "accepte"){
            //Création du comité CLEODIS
            $comite["description"] = "Comité CLEODIS";
            $comite["etat"] = "en_attente";
            $comite["reponse"] = NULL;
            $comite["validite_accord"] = NULL;
            ATF::comite()->insert(array("comite"=>$comite));
        }
        return array(
        	"result"=>true,
            "id_crypt"=>ATF::affaire()->cryptId($devis["id_affaire"])
        );
	}
	/** Fonction qui retourne les affaires / societes liés à un id partenaire
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _AffaireParc($get,$post){
		// on recupère l'apporteur;
		$utilisateur  = ATF::$usr->get("contact");
		$apporteur = $utilisateur["id_societe"];

		if($apporteur){

			$societes = $ret= [];
			ATF::affaire()->q->reset()->addField("affaire.ref","ref")
									  ->addField("devis.id_devis",'id_devis')
									  ->from("affaire","id_affaire","devis","id_affaire")
									  ->where('affaire.id_partenaire',$apporteur)
									  ->addGroup('affaire.id_affaire');
			$affaires = ATF::affaire()->select_all();


			if($affaires){
				foreach ($affaires as $key => $value) {
					$id_soc = ATF::affaire()->select($value["affaire.id_affaire"] , "id_societe");
					$societes["data"][$id_soc] = ATF::societe()->select($id_soc);
				}

				$societes["count"] = count($societes["data"]);

				foreach ($societes['data'] as $k => $v) {
					$v["id_societe"] = ATF::societe()->cryptID($v['id_societe']);
					$parc = [];

					foreach ($affaires as $kaff => $vaff) {
						$affaires[$kaff]['parc']= ATF::parc()->getParcPartenaire($vaff['affaire.id_affaire']);
						$affaires[$kaff]['id_affaire'] = $this->cryptID($vaff['affaire.id_affaire']);
						$affaires[$kaff]["id_devis"] = ATF::devis()->cryptID($vaff['id_devis']);

						unset($affaires[$kaff]['affaire.id_affaire']);
						if(!empty($affaires[$kaff]["parc"])) {
						    foreach($affaires[$kaff]["parc"] as $kparc => $vparc){
						    	$vparc['id_parc'] = $this->cryptID($vparc['id_parc']);
		        				$parc[]= $vparc;
		        			}
						}
					}
					header("ts-total-row: ".$societes['count']);
					$ret[$v["id_societe"]]=array(
						"societe"=> $v,
						"affaires"=> $affaires,
						"parc"=> $parc
					);
				}
			}else{
				header("ts-total-row:0");
				$ret=array(	);
			}


			return $ret;
		} else {
			throw new errorATF("Probleme d'apporteur",500);

		}
 	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion affaire
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function _ac($get,$post) {
	  //$length = 25;
	  //$start = 0;
	  $this->q->reset();

	  // On ajoute les champs utiles pour l'autocomplete
	  $this->q->addField("affaire.id_affaire","id_affaire")
		  ->addField("affaire.affaire","affaire")
		  ->addField("affaire.etat","etat");

	  if ($get['q']) {
		$this->q->setSearch($get["q"]);
	  }

	  if ($get['id_societe']) {
		$this->q->where("affaire.id_societe",$get["id_societe"]);
	  }

	  //$this->q->setLimit($length,$start)->setPage($start/$length);

	  return $this->select_all();
	}

	/** Fonction qui génère les résultat pour les champs d'auto complétion affaire seulement différent de perdu pour l'echeancier
	* @author Cyril Charlier <ccharlier@absystech.fr>
	*/
	public function _acSpecial($get,$post) {
	  //$length = 25;
	  //$start = 0;

	  $this->q->reset();

	  // On ajoute les champs utiles pour l'autocomplete
	  $this->q->addField("affaire.id_affaire","id_affaire")->addField("affaire.affaire","affaire")->addField("affaire.etat","etat");

	  if ($get['q']) {
		$this->q->setSearch($get["q"]);
	  }

	  if ($get['id_societe']) {
		$this->q->where("affaire.id_societe",$get["id_societe"]);
	  }
	  $this->q->AndWhere('affaire.etat','perdue',false,'<>');
	  //$this->q->setLimit($length,$start)->setPage($start/$length);

	  return $this->select_all();
	}

};
class affaire_midas extends affaire_cleodis {
	function __construct($table_or_id=NULL) {
		$this->table = "affaire";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'affaire.affaire'
			,'affaire.id_societe'
			,'affaire.etat'=>array("renderer"=>"etatAffaire","width"=>30)
			,'commande.etat'=>array("width"=>30,"renderer"=>"etat")
			,'parentes'=>array("custom"=>true,"nosort"=>true)
			,'dernier_loyer'=>array("custom"=>true)
			,'date_dem'=>array("custom"=>true,"renderer"=>"datefield")
		);

		$this->colonnes['primary'] = array(
			"ref"=>array("disabled"=>true)
			,"affaire"
			,"etat"
			,"date"
			,"id_societe"
			,"id_filiale"
			,"nature"
			,"forecast"
			,"parentes"=>array("custom"=>true)
			,"filles"=>array("custom"=>true)
			,'RIB'
			,'IBAN'
			,'BIC'
			,'nom_banque'
			,'ville_banque'
		);


		$this->colonnes['panel']['date_affaire'] = array(
			"specificDate"=>array("custom"=>true)
		);
		$this->panels['date_affaire'] = array("visible"=>true, 'nbCols'=>1);

		unset($this->colonnes['panel']['rib_facturation'],$this->colonnes['panel']['refRefi']);
		unset($this->panels['rib_facturation'],$this->panels['refRefi']);
		unset($this->files["facturation"]);

		$this->onglets = array(
			'loyer'
			,'devis'
			,'commande'
			,'parc'
		);
		$this->filtre_ob['affaire_franc']=array("titre"=>"Franchisées","function"=>"selectAllFranch");
		$this->filtre_ob['affaire_franc_cours']=array("titre"=>"Franchisées en cours","function"=>"selectAllFranchCours");
		$this->filtre_ob['affaire_franc_cours_info']=array("titre"=>"Franchisées en cours informatique","function"=>"selectAllFranchCoursInfo");

		$this->filtre_ob['affaire_suc']=array("titre"=>"Succursales","function"=>"selectAllSuc");
		$this->filtre_ob['affaire_suc_cours']=array("titre"=>"Succursales en cours","function"=>"selectAllSucCours");
		$this->filtre_ob['affaire_suc_cours_info']=array("titre"=>"Succursales en cours informatique","function"=>"selectAllSucCoursInfo");
		$this->fieldstructure();
	}

	/** On affiche que les sociétés midas
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false) {
		ATF::loyer()->q->reset()->setToString();
		$subquery=ATF::loyer()->sa("loyer.id_loyer","desc");

		$this->q->addField("(loy.loyer+(IF(loy.assurance>0,loy.assurance,0))+(IF(loy.frais_de_gestion>0,loy.frais_de_gestion,0)))","dernier_loyer")
				->addField("commande.date_debut","date_dem")
				->addJointure("affaire","id_affaire","loy","id_affaire","loy",NULL,NULL,NULL,"left",false,$subquery)
				->addJointure("affaire","id_affaire","commande","id_affaire")
				->addJointure("affaire","id_societe","societe","id_societe")
				->addCondition("societe.code_client","M%","OR",false,"LIKE")
				->addCondition("societe.divers_3","Midas")
				->addGroup("affaire.id_affaire");
		return parent::select_all($order_by,$asc,$page,$count);
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranch(){
		$this->q->addConditionNull("societe.id_filiale");
		return $this->select_all();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranchCours(){
		$this->q->addCondition("commande.etat","prolongation","OR")->addCondition("commande.etat","mis_loyer","OR");
		return $this->selectAllFranch();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllFranchCoursInfo(){
		$this->recupCoursInfo($this->q);
		return $this->selectAllFranch();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSuc(){
		$this->q->addConditionNotNull("societe.id_filiale");
		return $this->select_all();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSucCours(){
		$this->q->addCondition("commande.etat","prolongation","OR")->addCondition("commande.etat","mis_loyer","OR");
		return $this->selectAllSuc();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function selectAllSucCoursInfo(){
		$this->recupCoursInfo($this->q);
		return $this->selectAllSuc();
	}

	/** Surcharge pour filtrer le select_all si clic sur le filtre
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function recupCoursInfo(&$q){
		ATF::parc()->q->reset()->addField("count(*)","nbre_info")
								->addField("parc.id_affaire","id_affaire")
								->addCondition("libelle","%HP%","OR",false,"LIKE")
								->addCondition("libelle","%NEC%","OR",false,"LIKE")
								->addCondition("libelle","%Brother%","OR",false,"LIKE")
								->addGroup("parc.id_affaire")
								->setToString();
		$subquery=ATF::parc()->sa();

		$q->addCondition("commande.etat","prolongation","OR")
			->addCondition("commande.etat","mis_loyer","OR")
			->addCondition("par.nbre_info",0,"OR",false,">")
			->addJointure("affaire","id_affaire","par","id_affaire","par",NULL,NULL,NULL,"left",false,$subquery);
	}

};

class affaire_cleodisbe extends affaire_cleodis { };
class affaire_cap extends affaire {
	function __construct($table_or_id=NULL) {
		$this->table = "affaire";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'affaire.ref'
			,'affaire.date'
			,'affaire.id_societe'
		);

		$this->actions_by = array("insert"=>"audit","update"=>"audit");
		$this->fieldstructure();


		$this->onglets = array(
			 "audit"
			,"mandat"
		);


		$this->field_nom="ref";

		$this->foreign_key['id_societe'] =  "societe";

		$this->no_delete = true;
		$this->no_update = true;
		$this->no_insert = true;
		$this->can_insert_from = array("societe");
	}


};

?>