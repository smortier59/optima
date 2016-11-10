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
								,'suivi_notifie'=>array(0 => 35)
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
					if($infos["field"] == "RIB" && !ATF::affaire()->select($infos["id_affaire"] , "RUM") ){
						$RUM = "";
						$RIB = str_replace(" ", "", $infos['value']);
						$id_societe = ATF::affaire()->select($infos["id_affaire"] , "id_societe");						

						ATF::affaire()->q->reset()->where("affaire.id_societe", $id_societe)->where('replace(affaire.RIB, " ", "")' , $RIB)->whereIsNotNull("affaire.RUM");
						$res = ATF::affaire()->select_all();						

						if($res){ 	$affaire->set("RUM", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "RUM") ); $esp = true;}
					}
					if($infos["field"] == "IBAN" && !ATF::affaire()->select($infos["id_affaire"] , "RUM") ){
						$RUM = "";
						$IBAN = str_replace(" ", "", $infos['value']);
						$id_societe = ATF::affaire()->select($infos["id_affaire"] , "id_societe");						

						ATF::affaire()->q->reset()->where("affaire.id_societe", $id_societe)->where('replace(affaire.IBAN, " ", "")' , $IBAN)->whereIsNotNull("affaire.RUM");
						$res = ATF::affaire()->select_all();						

						if($res){ 	$affaire->set("RUM", ATF::affaire()->select($res[0]["affaire.id_affaire"] , "RUM") ); $esp = true;}
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
			$date1->modify('+1 month'); // On prend le premier jour du mois suivant ( nécessaire en cas de date de cession en dernier jour de période pleine,et à cause du pb des bisextile, et en plus a ce bug de merde : https://bugs.php.net/bug.php?id=52480 )
//log::logger("date_cession=".$date1->format('Y-m-d'),ygautheron);				
//log::logger("date_debut=".$date_debut,ygautheron);			
			$date2 = new DateTime($date_debut);
//log::logger($date1->diff($date2),ygautheron);			
//log::logger("diff=".$date1->diff($date2)->format('%m'),ygautheron);			
			$duree_ecoulee_restante = $duree_ecoulee = $date1->diff($date2)->format('%y')*12 + $date1->diff($date2)->format('%m');
//log::logger("duree_ecoulee=".$duree_ecoulee,ygautheron);		
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
//log::logger("duree_ecoulee_restante=".$duree_ecoulee_restante,ygautheron);		
					$loyers[$k] = $loyer;
				}
			}
			$loyers = array_reverse($loyers);
		}
//log::logger($loyers,ygautheron);		
	
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
		//Ajout du fichier

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
				if($value["type"] != "fixe" && $value["type"] != "portable"){ $pourcentagesImmat = $pourcentagesImmat + ($value["prix_achat"]*$value["quantite"]);	 }
				else{ $pourcentagesMat = $pourcentagesMat + ($value["prix_achat"]*$value["quantite"]);  }
			}			
		}

		$total = $pourcentagesImmat + $pourcentagesMat;

		$return = array("immat"=>$pourcentagesImmat, 
						"pourcentagesImmat" => round(($pourcentagesImmat * 100)/$total, 2),
						"mat"=>$pourcentagesMat, 
						"pourcentagesMat" => round(($pourcentagesMat * 100)/$total , 2));		

		return $return;
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