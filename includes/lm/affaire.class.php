<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../affaire.class.php";
class affaire_lm extends affaire {
	function __construct($table_or_id=NULL) {
		$this->table = "affaire";
		parent::__construct($table_or_id);
		$this->actions_by = array("insert"=>"devis","update"=>"devis");

		$this->colonnes['fields_column'] = array(
			'affaire.ref'
			,'affaire.date'
			,'affaire.affaire'
			,'affaire.id_societe'
			,'ref_client'=>array("custom"=>true)
			,'email_client'=>array("custom"=>true)
			,'carte_maison'=>array("custom"=>true)
			,'affaire.type_affaire'=>array('EnumTranslate'=>true)
			,'affaire.forecast'=>array("aggregate"=>array("min","avg","max"),"width"=>100,"renderer"=>"progress",'align'=>"center")
			,'affaire.nature'=>array("width"=>80,'align'=>"center")
			,'affaire.etat'=>array("renderer"=>"etatAffaire","width"=>80)
			,'commande.etat'=>array("width"=>30,"renderer"=>"etat","width"=>80)
			,'parentes'=>array("custom"=>true,"nosort"=>true)
			,"ref_commande_lm"=>array("rowEditor"=>"setInfos")
			,'affaire.id_pack_produit'
		);

		$this->colonnes['primary'] = array(
			"ref"=>array("disabled"=>true)
			,"affaire"
			,"etat"
			,"date"
			,"id_societe"
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
			,'type_affaire'
			,"compte_t"=>array("custom"=>true)
			,'id_magasin'
			,'id_collaborateur'
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
			 'affaire_etat'
			,'loyer'
			,'devis'=>array('opened'=>true)
			,'comite'
			,'commande'=>array('opened'=>true)
			,'prolongation'
			,'loyer_prolongation'
			,'bon_de_commande'
			,'demande_refi'
			,'facture'=>array('opened'=>true)
			,'facture_fournisseur'
			,'facture_non_parvenue'
			,'facturation'
			,'facturation_fournisseur'
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

		$this->files["bon_inter"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["facture"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["courrier_information"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		//$this->files["mandat_slimpay"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);


		$this->field_nom="ref";
		$this->foreign_key['id_fille'] =  "affaire";
		$this->foreign_key['id_parent'] =  "affaire";
		$this->foreign_key['id_magasin'] =  "magasin";
		$this->foreign_key['pays_livraison'] =  "pays";
		$this->foreign_key['pays_facturation'] =  "pays";

		$this->addPrivilege("updateDate","update");
		$this->addPrivilege("update_forecast","update");
		$this->addPrivilege("updateFacturation","update");
		$this->addPrivilege("getCompteT");
		$this->addPrivilege("getCompteTLoyerActualise");
		$this->addPrivilege("relancer");
		$this->addPrivilege("setInfos","update");
		$this->no_delete = true;
		$this->no_update = true;
		$this->no_insert = true;
		$this->can_insert_from = array("societe");

		$this->addPrivilege("export_opteven");
	}


	/**
	 * Permet de modifier un champs en AJAX
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @return bool
	 */
	public function setInfos($infos){
		$res = $this->u(array("id_affaire"=> $this->decryptId($infos["id_affaire"]),
						  $infos["field"] => $infos[$infos["field"]])
					);
		if($res){
			ATF::$msg->addNotice(
				loc::mt(ATF::$usr->trans("notice_update_success"))
				,ATF::$usr->trans("notice_success_title")
			);
		}
	}

	/**
    * Permet de passer les affaires en abandonner au bout de 2 mois
    * @author Morgan Fleurquin <mfleurquin@absystech.fr>
    */
	public function aAbandonner(){

		$date = date("Y-m-d", strtotime('-2 months'));

		ATF::affaire()->q->reset()->where("affaire.etat",'devis','OR','etat_affaire',"=")
								  ->where("affaire.etat",'slimpay_en_cours','OR','etat_affaire',"=")
								  ->where("affaire.etat",'commande','OR','etat_affaire',"=")
								  ->where("affaire.date",$date,"AND",false,"<=");

		if($affaires = ATF::affaire()->select_all()){
			foreach ($affaires as $key => $value) {

				if($value["commande.etat"]){
					if($value["commande.etat"] === "pending" || $value["commande.etat"] === "non_loyer" || $value["commande.etat"] === "abandon"){

						ATF::commande()->q->reset()->where("commande.id_affaire",$value["affaire.id_affaire"]);
						$commande = ATF::commande()->select_row();

						ATF::commande()->abandonCommande(array("id_commande"=>$commande["commande.id_commande"]) ,true);
					}
				}else{
					ATF::affaire()->u(array("id_affaire"=>$value["affaire.id_affaire"], "etat"=>"abandon"));
				}

			}
		}

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
		$affaire["adresse_livraison"]=$infos["adresse_livraison"];
		$affaire["adresse_livraison_2"]=$infos["adresse_livraison_2"];
		$affaire["adresse_livraison_3"]=$infos["adresse_livraison_3"];

		$affaire["ville_adresse_livraison"]=$infos["ville_adresse_livraison"];
		$affaire["cp_adresse_livraison"]=$infos["cp_adresse_livraison"];
		$affaire["pays_livraison"]=$infos["pays_livraison"];
		$affaire["adresse_facturation"]=$infos["adresse_facturation"];
		$affaire["adresse_facturation_2"]=$infos["adresse_facturation_2"];
		$affaire["adresse_facturation_3"]=$infos["adresse_facturation_3"];
		$affaire["ville_adresse_facturation"]=$infos["ville_adresse_facturation"];
		$affaire["cp_adresse_facturation"]=$infos["cp_adresse_facturation"];
		$affaire["pays_facturation"]=$infos["pays_facturation"];
		$affaire["id_magasin"]=$infos["id_magasin"];
		$affaire["num_bdc_lm"]=$infos["num_bdc_lm"];
		$affaire["poseur"]=$infos["poseur"];
		$affaire["poseur_aggree"]=$infos["poseur_aggree"];
		$affaire["type_souscription"]=$infos["type_souscription"];
		$affaire["type_affaire"] = $infos["type_affaire"];


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
		$ref=substr($this->select($id_parent,"ref"),0,8);
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

		if(date("y") < 17){
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
		}else{
			if($nb["max_ref"]){
				if($nb["max_ref"]<10){
					$suffix="000".$nb["max_ref"];
				}elseif($nb["max_ref"]<100){
					$suffix="00".$nb["max_ref"];
				}elseif($nb["max_ref"]<1000){
					$suffix="0".$nb["max_ref"];
				}else{
					$suffix=$nb["max_ref"];
				}
			}else{
				$suffix="0001";
			}
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
			$affaire = new affaire_lm($infos['id_affaire']);
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
		$affaire = new affaire_lm($infos['id_affaire']);

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
	* @return affaire_lm
	*/
	function getParentAvenant($id_affaire=NULL){
		if ($id_affaire) {
			$id_parent = $this->select($this->decryptId($id_affaire),"id_parent");
		} elseif (!$id_affaire && $this->infos["id_parent"]) {
			$id_parent = $this->infos["id_parent"];
		}
		if($id_parent)	{
			return new affaire_lm($id_parent);
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
	* @return commande_lm
	*/
	function getCommande($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			ATF::commande()->q->reset()->setStrict()->addField('commande.id_commande')->addCondition("commande.id_affaire",$id_affaire)->setDimension("cell");
			if($id_commande = ATF::commande()->sa()) {
				return new commande_lm($id_commande);
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
	* @return devis_lm
	*/
	function getDevis($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"]) {
			$id_affaire = $this->infos["id_affaire"];
		}
		if($id_affaire){
			ATF::devis()->q->reset()->setStrict()->addField('devis.id_devis')->addCondition("devis.id_affaire",$id_affaire)->setDimension("cell");
			if($id_devis = ATF::devis()->sa()) {
				return new devis_lm($id_devis);
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
	* @return devis_lm
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
	* @return affaire_lm
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
			$affaire = new affaire_lm($infos["id_affaire"]);
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
			$factureslmData = util::removeTableInKeys(ATF::facture()->sa()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour
			foreach (array("facturesDataNonParvenues","facturesDataFournisseurs","factureslmData") as $grid) {
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

//			// Assurance
//			ATF::$html->assign("assurance_fixe",$taux);
//			ATF::$html->assign("assurance_portable",$taux);

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
		$a = new affaire_lm($infos["id_affaire"]);
//log::logger($infos,ygautheron);
		if ($c = $a->getCommande()) {
			$date_debut = $c->get("date_debut");
		}
		/*if ($date_debut && $infos["date_cession"]) {
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
//log::logger(DateTime::getLastErrors(),ygautheron);

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
		}*/
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
				->addJointure("affaire","id_societe","societe","id_societe")
				->addField("societe.ref","ref_client")
				->addField("societe.email","email_client")
				->addField("societe.id_carte_maison","carte_maison")
				->addField("commande.etat");
		$return = parent::select_all($order_by,$asc,$page,$count);
		$a = new affaire_lm();
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


	public function relancer($infos){
		$id_affaire = $this->decryptId($infos["id_affaire"]);
		$email = ATF::societe()->select(ATF::affaire()->select($id_affaire , "id_societe"), "email");


		/*
		*	Générer un lien vers une page du front avec ID Crypté
		*	Sur cette page on récupere tout les infos de l'affaire necessaire à SLIMPAY
		*	et on redirige direct vers SLIMPAY
		*/

	}



	/**
    * Parsing des mails d'expedition de commande
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $mail boite mail à parser
	* @param $host Host de la boite mail
	* @param $port Port de la boite mail
	* @param $password Password de connection boite mail
    */
	public function checkMailBoxExpeditionCommande($mail, $host, $port, $password){

		ATF::imap()->init($host, $port, $mail, $password, "INBOX", false, "/imap/ssl");
		if (ATF::imap()->error) {
			throw new errorATF(ATF::imap()->error);
		}
		$mails = ATF::imap()->imap_fetch_overview('1:*');


		if(is_array($mails)){
			foreach ($mails as $kmail => $vmail) {

				if (strpos(str_replace(" ","",$vmail->subject),"=?UTF-8?Q?Exp=C3=A9dition_de_votre_commande_n=C2=B0")!==false){
					$num_commande_lm = str_replace("=?UTF-8?Q?Exp=C3=A9dition_de_votre_commande_n=C2=B0","",$vmail->subject);
					$num_commande_lm = str_replace("?=","",$num_commande_lm);

					$date_expedition = date("Y-m-d", strtotime($vmail->date));
					$body =  ATF::imap()->returnBody($vmail->uid);

					$bdc = array();

					ATF::bon_de_commande()->q->reset()->addField("bon_de_commande.id_affaire")
													  ->where("num_bdc", "%".$num_commande_lm,"AND",false,"LIKE");
					$bdc = ATF::bon_de_commande()->select_row();


					/*$this->q->reset()->where("ref_commande_lm",$num_commande_lm);
					$affaire = $this->select_row();*/

					if($bdc){
						$affaire = $this->select($bdc["bon_de_commande.id_affaire_fk"]);
						$client = ATF::societe()->select($affaire["id_societe"]);

						$body = str_replace("\n", "", $body);
						$body = str_replace("\r", "", $body);


						$pattern_num_expedition = "/Leroy Merlin : Exp=C3=A9dition n=C2==B0 ([0-9]*) de votre commande/";
						preg_match_all($pattern_num_expedition , $body, $ids);
						$num_expedition = $ids[1][0];

						$pattern_ref_colissimo = "/Colis n=C2=B0 ([a-zA-Z0-9]*)</";
						preg_match_all($pattern_ref_colissimo , $body, $ids_colissimo);


						$ref_colissimo = $ids_colissimo[1][0];
						$lien_colissimo = "http://www.colissimo.fr/portail_colissimo/suivre.do?colispart=".$ref_colissimo;


						$pattern_produits = "/<em>Ref : ([0-9]*)<\/em><\/font>/";
						preg_match_all($pattern_produits , $body, $ids_produits);
						$ref_produits = $ids_produits[1];


						ATF::devis()->q->reset()->where("id_affaire" , $affaire["id_affaire"]);
						$devis = ATF::devis()->select_row();
						ATF::devis_ligne()->q->reset()->where("id_devis" , $devis["id_devis"]);
						$lignes = ATF::devis_ligne()->select_all();


						$produits = array();
						$i = 0;
						foreach ($ref_produits as $k_produit => $v_produit) {
							foreach ($lignes as $kl => $vl) {
								if($vl["ref"] == $v_produit){
									$produits[$i]["ref"] = $vl["ref"];
									$produits[$i]["produit"] = $vl["produit"];
									$produits[$i]["qte"] = $vl["quantite"];
									$i++;
								}
							}
						}


						$mail = new mail(array(
							"recipient"=>$client["email"]
							,"objet"=>"Expédition de votre commande n°".$num_commande_lm
							,"template"=>"expedition_commande"
							,"html"=>true
							,"affaire"=>$affaire
							,"client"=>$client
							,"produits"=>$produits
							,"num_expedition"=>$num_expedition
							,"commande_lm"=>$num_commande_lm
							,"date_envoi"=>$date_expedition
							,"colissimo_ref"=>$ref_colissimo
							,"lien_colissimo"=>$lien_colissimo
							,"from"=>"no-reply@leroymerlin.fr"));
						$mail->setCustomHeaders(array("Bcc"=>"contact@abonnement.leroymerlin.fr"));

						$mail->send();

						ATF::imap()->imap_mail_move( $vmail->uid, "Mail_Expedition_traitee");
					}

				}
			}
		}
		ATF::imap()->imap_expunge();
		return true;


	}






	/**
    * Permet l'export CSV des données OPTEVEN
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    */
	public function export_opteven($toCSV){


		if($toCSV !== false){
			// output headers so that the file is downloaded rather than displayed
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="demo.csv"');

			// do not cache the file
			header('Pragma: no-cache');
			header('Expires: 0');

			// create a file pointer connected to the output stream
			$file = fopen('php://output', 'w');
		}else{
			if (!$file = fopen('php://temp', 'w+')) return FALSE;
		}

		ATF::societe()->q->reset()->where("societe", "Opteven");
		$OPTEVEN = ATF::societe()->select_row();


		ATF::affaire()->q->reset()  ->select("affaire.id_societe, adresse_livraison")
									->from("affaire","id_affaire" , "commande","id_affaire")
									->from("commande","id_commande" , "commande_ligne","id_commande")
									->where("commande.etat", "arreter", 'AND', 'commandeEtat', "!=")
									->where("commande.etat", "vente", 'AND', 'commandeEtat', "!=")
									->where("commande.etat", "pending", 'AND', 'commandeEtat', "!=")
									->where("commande.etat", "abandon", 'AND', 'commandeEtat', "!=")
									->where("commande_ligne.id_fournisseur", $OPTEVEN["id_societe"])
									->where("affaire.etat",'commande', 'OR', 'statuAffaire')
							  		->where("affaire.etat",'facture', 'OR', 'statuAffaire')
							  		->addGroup("adresse_livraison,affaire.id_societe");

		$affaires = ATF::affaire()->sa();


		$affaire_ok = array();
		foreach ($affaires as $key => $value) {
			$affaire_societe = array();

			ATF::affaire()->q->reset()->where("id_societe", $value["affaire.id_societe_fk"]);

			$affs = ATF::affaire()->sa();

			foreach ($affs as $k => $v) {

				ATF::comite()->q->reset()->where("reponse", date("Y-m-d"), 'AND', false, '<=')
									 ->where("etat", "accepte", 'AND')
									 ->where("id_affaire", $v["id_affaire"], 'AND');
				$comite = ATF::comite()->select_row();

				if($comite){
					$adresse = ATF::affaire()->select($v["affaire.id_affaire"], "adresse_livraison");
					$affaire_ok[$value["affaire.id_societe_fk"]][$adresse][] = array("id_affaire" =>$v["id_affaire"]);
				}
			}
		}

		foreach ($affaire_ok as $ksoc => $vsoc) {
			$i = 0;
			foreach ($vsoc as $key => $affs) {
				if($i == 0){
					$a = ATF::affaire()->select($affs[0]["id_affaire"]);
					$adresse_livraison = strtolower($a["adresse_livraison"].$a["adresse_livraison_2"].$a["adresse_livraison_3"].$a["cp_adresse_livraison"].$a["ville_adresse_livraison"]);
					$adresse_livraison = str_replace(" ", "", $adresse_livraison);
					$adresse_livraison = str_replace("'", "", $adresse_livraison);
					$adresse_livraison = base64_encode($adresse_livraison);
				}
				foreach ($affs as $kaff => $v) {

					ATF::commande()->q->reset()->where("commande.id_affaire", $v["id_affaire"], 'AND')
										   ->where("commande.etat", "arreter", 'AND', 'commandeEtat', "!=")
										   ->where("commande.etat", "vente", 'AND', 'commandeEtat', "!=")
										   ->where("commande.etat", "pending", 'AND', 'commandeEtat', "!=")
										   ->where("commande.etat", "abandon", 'AND', 'commandeEtat', "!=");
					$contrat = ATF::commande()->select_row();

					if($contrat){
						ATF::commande_ligne()->q->reset()->from("commande_ligne","id_commande","commande","id_commande")
														 ->where("id_affaire", $v["id_affaire"]);
						if(ATF::commande_ligne()->sa()){

							$affaire_ok[$ksoc][$key][$kaff]["adresse"] = $adresse_livraison;

						}
					}else{
						unset($affaire_ok[$ksoc][$key][$kaff]);
					}
				}
			}
		}


		$data = array();
		$data[0] = array("ContratProduit", "ContratNum", "ContratDateDebut", "ClientLMANum", "BeneficiaireNom", "BeneficiairePrenom", "BeneficiaireTitre", "BeneficiaireDateNaissance", "BeneficiaireAdresse1", "BeneficiaireAdresse2","BeneficiaireCP", "BeneficiaireVille", utf8_decode("Produits loués"));


		$i = 1;
		foreach($affaire_ok as $ksoc => $vsoc){
			$client = ATF::societe()->select($ksoc);


			foreach ($vsoc as $key => $affaire_adresse) {
				$ligne = array();

				$data_aff = ATF::affaire()->select($affaire_adresse[0]["id_affaire"]);

				ATF::comite()->q->reset()->where("id_affaire", $affaire_adresse[0]["id_affaire"])->where("comite.etat", "accepte");
				$comite = ATF::comite()->select_row();

				$data[$i][0] = "";
				$data[$i][1] = $data_aff["ref"];
				$data[$i][2] = date("dmY", strtotime($comite["date"]));
				$data[$i][3] = strtoupper($client["ref"]);
				$data[$i][4] = strtoupper(utf8_decode($client["nom"]));
				$data[$i][5] = strtoupper(utf8_decode($client["prenom"]));
				$data[$i][6] = $client["civilite"];
				$data[$i][7] = date("dmY", strtotime($client["date_naissance"]));
				$data[$i][8] = strtoupper(utf8_decode($data_aff["adresse_livraison"]));
				$data[$i][9] = strtoupper(utf8_decode($data_aff["adresse_livraison_2"]." ".$data_aff["adresse_livraison_3"]));
				$data[$i][10] = strtoupper($data_aff["cp_adresse_livraison"]);
				$data[$i][11] = strtoupper(utf8_decode($data_aff["ville_adresse_livraison"]));

				$produits = array();
				ATF::commande_ligne()->q->reset()->from("commande_ligne","id_commande","commande","id_commande")
										->addOrder("commande_ligne.produit", "ASC");
				foreach ($affaire_adresse as $kl => $vl) {
					ATF::commande_ligne()->q->where("commande.id_affaire", $vl["id_affaire"], "OR", "id_affaire", "=");
				}
				$lignes_commande = ATF::commande_ligne()->select_all();

				foreach ($lignes_commande as $klc => $vlc) {
					$produits[$vlc["id_produit"]]["produit"] = $vlc["produit"];
					$produits[$vlc["id_produit"]]["quantite"] += $vlc["quantite"];
					$produits[$vlc["id_produit"]]["id_fournisseur"] = $vlc["id_fournisseur"];
				}

				foreach ($produits as $kp => $vp) {
					//Tout les produits dont le fournisseur est Opteven
					if($vp["id_fournisseur"] ==  $OPTEVEN["id_societe"] && $vp["quantite"] > 0){
						$ref_four = ATF::produit()->select($kp, "ref_fournisseur");
						if($ref_four){
							$data[$i][0] .= utf8_decode($ref_four);
						}else{
							$data[$i][0] .= utf8_decode(str_replace("&nbsp;", "", str_replace("&nbsp;>", "", $vp["produit"])));
						}
					}

					if(ATF::produit()->select($kp, "nature") == "produit"){
						if($data[$i][12]){
							$data[$i][12] .= "$".$vp["quantite"]." ".utf8_decode(str_replace("&nbsp;", "", str_replace("&nbsp;>", "", $vp["produit"])));
						}else{
							$data[$i][12] = $vp["quantite"]." ".utf8_decode(str_replace("&nbsp;", "", str_replace("&nbsp;>", "", $vp["produit"])));
						}
					}
				}

				$i++;
			}

		}

		// output each row of the data
		foreach ($data as $row){
			fputcsv($file, $row, ';', '"');
		}

		if($toCSV !== false){
			exit();
		}else{
			// Place stream pointer at beginning
		    rewind($file);

		    // Return the data
			return stream_get_contents($file);
		}
	}


};
?>