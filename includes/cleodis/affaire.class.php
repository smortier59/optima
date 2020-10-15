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
			,'affaire.mail_signature'
			,'affaire.mail_document'
			,'cni'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>50)
			,'cniVerso'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>50)
			,'contrat_signe'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>50)
			,'pouvoir'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>50)
			,'facture_fournisseur'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>50)
			,'validateOrder'=>array("custom"=>true,"nosort"=>true,"align"=>"center","renderer"=>"validateOrderRenderer")

			,"affaire.commentaire_facture"=>array("rowEditor"=>"setInfos")
			,"affaire.commentaire_facture2"=>array("rowEditor"=>"setInfos")
			,"affaire.commentaire_facture3"=>array("rowEditor"=>"setInfos")


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


		$this->colonnes['panel']['commentaire_sur_facture'] = array(
			 "specifiqueCommentaireFacture"=>array("custom"=>true)
		);
		$this->panels['commentaire_sur_facture'] = array("visible"=>true, 'nbCols'=>1);

		$this->colonnes['panel']['infos_signature_contrat'] = array(
			"tel_signature",
			"mail_signataire",
			"date_signature",
			"signataire"
		);
		$this->panels['infos_signature_contrat'] = array("visible"=>false);

		$this->colonnes['panel']['chiffres'] = array(
			'total_depense'
			,'total_recette'
			,'assurance_fixe'
			,'assurance_portable'
			,'valeur_residuelle'
			,'taux_refi_reel'
			,'id_apporteur'
		);
		$this->panels['chiffres'] = array("visible"=>true, 'nbCols'=>1);

		$this->fieldstructure();

		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array('data','nature');
		$this->colonnes['bloquees']['select'] = array('id_parent','data','RIB','BIC','IBAN','RUM','nom_banque','ville_banque','date_previsionnelle','commentaire_facture','commentaire_facture2','commentaire_facture3');

		$this->onglets = array(
			'affaire_etat'
			,"sell_and_sign"
			,'loyer'
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

		$this->files["cni"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["cniVerso"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["devis_partenaire"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["devis_partenaire_2"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["devis_partenaire_3"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->files["rib_client"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["kbis_client"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);


		$this->files["facture_partenaire"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["facture_partenaire_2"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["facture_partenaire_3"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->files["bon_livraison_partenaire"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["bon_livraison_partenaire_2"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["bon_livraison_partenaire_3"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->files["pv_livraison_cleodis_signe_partenaire"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->files["autre_document_partenaire"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["autre_document_partenaire_2"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["autre_document_partenaire_3"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->files["contrat_signe"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["pouvoir"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["facture_fournisseur"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);


		$this->files["facturation"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"force_generate"=>true);
		$this->field_nom="%ref%";
		$this->foreign_key['id_fille'] =  "affaire";

		$this->foreign_key['id_parent'] =  "affaire";
		$this->foreign_key['id_filiale'] =  "societe";
		$this->foreign_key['id_partenaire'] =  "societe";
		$this->foreign_key['id_apporteur'] =  "societe";
		$this->addPrivilege("updateDate","update");
		$this->addPrivilege("update_forecast","update");
		$this->addPrivilege("updateFacturation","update");
		$this->addPrivilege("getCompteT");
		$this->addPrivilege("getCompteTLoyerActualise");
		$this->addPrivilege("updateCommentaireFacture");
		$this->addPrivilege("validateOrderPartenaire");
		$this->addPrivilege("setInfos");
		$this->no_delete = true;
		$this->no_update = true;
		$this->no_insert = true;
		$this->can_insert_from = array("societe");
	}

	public function setInfos($infos){
		if($infos["commentaire_facture"]) {
			$infos["field"] = "commentaire_facture";
			$infos["value"] = $infos["commentaire_facture"];
			unset($infos["commentaire_facture"]);
		}

		if($infos["commentaire_facture2"]) {
			$infos["field"] = "commentaire_facture2";
			$infos["value"] = $infos["commentaire_facture2"];
			unset($infos["commentaire_facture2"]);
		}

		if($infos["commentaire_facture3"]) {
			$infos["field"] = "commentaire_facture3";
			$infos["value"] = $infos["commentaire_facture3"];
			unset($infos["commentaire_facture3"]);
		}

		$this->updateCommentaireFacture($infos);

	}


	public function comiteAccepte($id_affaire){

		ATF::comite()->q->reset()->where("id_affaire",ATF::affaire()->decryptId($id_affaire))->where("etat","accepte")->setCount();
		$data = ATF::comite()->sa();
		if($data["count"]){
			return true;
		}else{
			return false;
		}
	}


	public function updateCommentaireFacture($infos){
		$id_affaire = $this->decryptId($infos["id_affaire"]);

		ATF::db($this->db)->begin_transaction();
		try {
			$this->u(array("id_affaire"=>$id_affaire,  $infos["field"]=>$infos["value"]));
			ATF::db($this->db)->commit_transaction();
			ATF::$msg->addNotice(ATF::$usr->trans($infos['field'], $this->table)." modifié avec succes");
		} catch(errorATF $e) {
						//On commit le tout
			ATF::db($this->db)->rollback_transaction();
			throw $e;
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
		$affaire["id_partenaire"]=$infos["id_partenaire"];
		$affaire["langue"]=$infos["langue"];
		$affaire["commentaire_facture"]=$infos["commentaire_facture"];
		$affaire["commentaire_facture2"]=$infos["commentaire_facture2"];
		$affaire["commentaire_facture3"]=$infos["commentaire_facture3"];

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

		// Baser les calculs sur la valeur résiduelle de la demande de refi acceptée
		if ($demandeRefi = $this->getDemandeRefiValidee()) {
			$vr = $demandeRefi->get("valeur_residuelle");
		}

		$f = new Financial;
		$freq = array("mois"=>12,"trimestre"=>4,"semestre"=>2,"an"=>1);
		$vr2 = $vr;
		foreach ($loyers as $i => $loyer) {
			if ($pv) {
				$vr2 = $pv;
			}
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
		if ($c = $a->getCommande()) {
			$date_debut = $c->get("date_debut");
		}

		if ($date_debut && $infos["date_cession"]) {
			$date1 = new DateTime(substr($infos["date_cession"],0,8).'01');

			if(date("m",strtotime($infos["date_cession"])) != "12"){
				$date1->modify('+1 month'); // On prend le premier jour du mois suivant ( nécessaire en cas de date de cession en dernier jour de période pleine,et à cause du pb des bisextile, et en plus a ce bug de merde : https://bugs.php.net/bug.php?id=52480 )
			}




			$date2 = new DateTime($date_debut);
			$duree_ecoulee_restante = $duree_ecoulee = $date1->diff($date2)->format('%y')*12 + $date1->diff($date2)->format('%m');

			if($date1->diff($date2)->format('%d')){
				$duree_ecoulee_restante = $duree_ecoulee = $duree_ecoulee+1;
			}



			// On "rogne" les mois deja écoulé jusqu'àla date de cession
			$frequence_loyer=array("mois"=>1,"trimestre"=>3,"semestre"=>6,"an"=>12);
			$loyers = $this->getLoyers($infos["id_affaire"]);
			$loyers = array_reverse($loyers);
			foreach ($loyers as $k => $loyer) {
				if ($duree_ecoulee_restante>0) { // Tant qu'il reste de la durée à rogner
					$duree = ceil($loyer["duree"]*$frequence_loyer[$loyer["frequence_loyer"]]);
					$duree_max_pouvant_etre_retiree_de_ce_loyer = min($duree,$duree_ecoulee_restante);
					$loyer["duree"] -= $duree_max_pouvant_etre_retiree_de_ce_loyer / $frequence_loyer[$loyer["frequence_loyer"]];
					$duree_ecoulee_restante -= $duree_max_pouvant_etre_retiree_de_ce_loyer;
					$loyers[$k] = $loyer;
				}
			}

			$loyers = array_reverse($loyers);
		}

		$loyers = $a->getCompteTLoyersActualises($infos["taux"],$infos["vr"],$loyers);
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
			$return['data'][$k]["site_associe"] = ATF::affaire()->select($i['affaire.id_affaire'], "site_associe");
			$return['data'][$k]["ref_externe"] = ATF::affaire()->select($i['affaire.id_affaire'], "ref_externe");
			$return['data'][$k]["etat_cmd_externe"] = ATF::affaire()->select($i['affaire.id_affaire'], "etat_cmd_externe");

			$return['data'][$k]["cni"] = file_exists(ATF::affaire()->filepath($i['affaire.id_affaire'],"cni"))? $this->files['cni']['type'] : false;
			$return['data'][$k]["cniVerso"] = file_exists(ATF::affaire()->filepath($i['affaire.id_affaire'],"cniVerso")) ? $this->files['cniVerso']['type'] : false;
			$return['data'][$k]["pouvoir"] = file_exists(ATF::affaire()->filepath($i['affaire.id_affaire'],"pouvoir")) ? $this->files['pouvoir']['type'] : false;
			$return['data'][$k]["contrat_signe"] = file_exists(ATF::affaire()->filepath($i['affaire.id_affaire'],"contrat_signe")) ? $this->files['contrat_signe']['type'] : false;
			$return['data'][$k]["facture_fournisseur"] = file_exists(ATF::affaire()->filepath($i['affaire.id_affaire'],"facture_fournisseur")) ? $this->files['facture_fournisseur']['type'] : false;


			$return['data'][$k]["dossier_preuve_sell_sign"] = file_exists(ATF::affaire()->filepath($i['affaire.id_affaire'],"dossier_preuve_sell_sign")) ? true : false;

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
				'affaire.pieces',
				'societe.societe',
				'societe.id_contact_signataire',
				'loyer.loyer');
			$this->q->reset();


			$this->q->setCount();


			if ($get['id_affaire']) $colsData = array("affaire.affaire","affaire.id_affaire","affaire.etat",'affaire.date','affaire.ref','affaire.etat_comite','affaire.id_societe', 'affaire.pieces', 'affaire.date_verification');

			$this->q->addField($colsData)
					->addField("famille.famille","famille")

					->from("affaire","id_societe","societe","id_societe")
					->from("societe","id_contact_signataire","contact","id_contact")
					->from("affaire","id_affaire","bon_de_commande","id_affaire")
					->from("affaire","id_affaire","commande","id_affaire")
					->from("affaire","id_affaire","loyer","id_affaire")
					->from("affaire", "id_affaire", "commande", "id_affaire")
					->from("societe","id_famille", "famille", "id_famille")
					//->where("provenance",'partenaire')
					->where("id_partenaire", $apporteur)

					->addGroup("affaire.id_affaire");


			if ($get['filters']['sans-suite'] == "on"){
				$this->q->where("affaire.etat","perdue", "OR", "affaire_demande");
			} else {
				$this->q->where("affaire.etat", "devis","OR","affaire_demande","=")
					->where("commande.etat", "non_loyer","OR","affaire_demande","=");
			}


			if($get["search"]){
				$this->q->where("affaire.affaire","%".$get["search"]."%","AND","searchquery","LIKE")
						->where("societe.societe","%".$get["search"]."%","OR","searchquery","LIKE");
			}

			$retour =  $this->returnGetPortail($get, $post);

			foreach ($retour as $key => $value) {
				$retour[$key]["date_paiement"] = NULL;
				$retour[$key]["date_max_validite"] = NULL;
				$retour[$key]["date_retour_pv"] = NULL;
				$retour[$key]["date_debut_contrat"] = NULL;

				ATF::commande()->q->reset()->where("commande.id_affaire", $value['id_affaire_fk']);
				$contrat = ATF::commande()->select_row();

				if($contrat){
					$retour[$key]["date_retour_pv"] = $contrat["commande.retour_pv"];
					$retour[$key]["date_debut_contrat"] = $contrat["commande.date_debut"];
				}

				ATF::comite()->q->reset()->where("comite.id_affaire", $value['id_affaire_fk']);
				$comites = ATF::comite()->sa();

				foreach ($comites as $k_comite => $vcomite) {
					if($retour[$key]["date_max_validite"] == NULL ){
						$retour[$key]["date_max_validite"] = $vcomite["validite_accord"];
					}elseif(str_replace("-", "", $retour[$key]["date_max_validite"]) <= str_replace("-", "",$vcomite["validite_accord"]) ){
						$retour[$key]["date_max_validite"] = $vcomite["validite_accord"];
					}
				}

				/*$retour[$key]["date_paiement"] = NULL;

				if($value["bon_de_commande"] === true){
					ATF::bon_de_commande()->q->reset()
				       ->addField("id_bon_de_commande")
				       ->from("bon_de_commande", "id_affaire", "affaire", "id_affaire")
				       ->where("affaire.id_affaire",$value['id_affaire_fk'], "AND")
				       ->setDimension('cell');
				    $bdc = ATF::bon_de_commande()->sa();

				    if($bdc){
				    	ATF::facture_fournisseur()->q->reset()->where("id_bon_de_commande", $bdc)->setDimension('cell');
				    	$ff = ATF::facture_fournisseur()->sa();

				    	if($ff){
				    		$ff = ATF::facture_fournisseur()->select($ff);
				    		if($ff["etat"] == "payee"){
				    			$retour[$key]["date_paiement"] = $ff["date_paiement"];
				    		}
				    	}
				    }
				}*/







			}

			return $retour;


		} else{
			throw new errorATF("Probleme d'apporteur",500);
		}

	}


	/**
	* Fonctions _GET pour telescope
	* @package Portail Partenaire
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param $get array contient le tri, page limit et potentiellement un id.
	* @param $post array Argument obligatoire mais inutilisé ici.
	* @return array un tableau avec les données
	*/
	public function _affairePortailToshiba($get,$post) {


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
				'affaire.pieces',
				'societe.societe',
				'societe.id_contact_signataire',
				'affaire.date_signature',
				'loyer.loyer');
			$this->q->reset();


			$this->q->setCount();


			if ($get['id_affaire']) $colsData = array("affaire.affaire","affaire.id_affaire","affaire.etat",'affaire.date','affaire.ref','affaire.etat_comite','affaire.id_societe', 'affaire.pieces', 'affaire.date_verification');

			$this->q->addField($colsData)
					->addField("famille.famille","famille")
					->from("affaire","id_societe","societe","id_societe")
					->from("societe","id_contact_signataire","contact","id_contact")
					->from("affaire","id_affaire","bon_de_commande","id_affaire")
					->from("affaire","id_affaire","commande","id_affaire")
					->from("affaire","id_affaire","loyer","id_affaire")
					->from("affaire", "id_affaire", "commande", "id_affaire")
					->from("societe","id_famille", "famille", "id_famille")
					->where("site_associe", "toshiba")

					->where("affaire.etat", "devis","OR","affaire_demande","=")
					->where("commande.etat", "non_loyer","OR","affaire_demande","=")

					->where("affaire.id_partenaire", $apporteur)
					->addGroup("affaire.id_affaire");

			if($get["search"]){
				$this->q->where("affaire.affaire","%".$get["search"]."%","AND","searchquery","LIKE")
						->where("societe.societe","%".$get["search"]."%","OR","searchquery","LIKE");
			}


			$retour =  $this->returnGetPortail($get, $post);

			foreach ($retour as $key => $value) {
				$retour[$key]["date_paiement"] = NULL;
				$retour[$key]["date_max_validite"] = NULL;
				$retour[$key]["date_retour_pv"] = NULL;
				$retour[$key]["date_debut_contrat"] = NULL;

				ATF::commande()->q->reset()->where("commande.id_affaire", $value['id_affaire_fk']);
				$contrat = ATF::commande()->select_row();

				if($contrat){
					$retour[$key]["date_retour_pv"] = $contrat["commande.retour_pv"];
					$retour[$key]["date_debut_contrat"] = $contrat["commande.date_debut"];
				}
			}
			return $retour;

		} else{
			throw new errorATF("Probleme d'apporteur",500);
		}

	}


	public function returnGetPortail($get, $post){

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


			if ($get['filters']['startdate']) {
				$this->q
				->where("affaire.date", $get['filters']['startdate'], "AND", false, ">=");
			}

			if ($get['filters']['enddate']) {
			  $this->q
				->where("affaire.date", $get['filters']['enddate'], "AND", false, "<=");
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




				$data['data'][$key]["sign_url"] = "mailto:".
					ATF::contact()->select(ATF::societe()->select($value['affaire.id_societe_fk'],'id_contact_signataire') , "email").
					"?subject=Votre lien de signature de contrat&body=".
					ATF::societe()->getUrlSign($value['affaire.id_affaire_fk']);




				// pour chaque affaire on recupere ses comites
				foreach ($this->getComite($data['data'][$key]["id_affaire_fk"]) as $k => $comite) {
					if($comite['description']=== 'Comité CLEODIS'){
						$data['data'][$key]["etat_comite_cleodis"] = $comite['etat']; //je (Anthony) rajoute cet etat car la propriété "etat_comite" de base renvoyé ne concerne pas le comite cleodis
					}
				}

				$data['data'][$key]["contact"] = ATF::contact()->select($value['societe.id_contact_signataire']);
				/*$data['data'][$key]["cni"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"cni")) ? true : false;
				$data['data'][$key]["contrat_signe"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"contrat_signe")) ? true : false;
				$data['data'][$key]["facture_fournisseur"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"facture_fournisseur")) ? true : false;*/



				$data['data'][$key]["idcrypted"] = $this->cryptId($value['affaire.id_affaire_fk']);
				if($loyer = $this->getLoyers($value['affaire.id_affaire_fk'])){
						$data['data'][$key]["loyer"] = $loyer; // on recupere tous les loyers
						$data['data'][$key]["duree"] = 0; // la duree de l'affaire (somme des durees de tous les loyers)
						foreach ($loyer as $i => $l) {
							$data['data'][$key]["duree"] += $l["duree"];
						}
				}

				$data['data'][$key]["montant"] = 0; // montant => Somme de tout les prix d'achat
				ATF::devis_ligne()->q->reset()->from("devis_ligne","id_devis","devis","id_devis")
											  ->where("devis.id_affaire", $value['affaire.id_affaire_fk']);
				$devis_ligne = ATF::devis_ligne()->sa();

				$utilisateur  = ATF::$usr->get("contact");
				$apporteur = $utilisateur["id_societe"];

				foreach ($devis_ligne as $k => $v) {
					// Il ne faut prendre que les lignes ou le partenaire est le fourisseur

					if($apporteur && $apporteur == $v["id_fournisseur"]){
						$data['data'][$key]["montant"] += ($v["quantite"] * $v["prix_achat"]);
					}

				}


				ATF::commande()->q->reset()->where("commande.id_affaire",$value['affaire.id_affaire_fk']);
				$commande = ATF::commande()->select_row();

				if($commande){
					$data['data'][$key]["contratExist"] = true;
				}else{
					$data['data'][$key]["contratExist"] = false;
				}

				$data['data'][$key]["date_signature"] = NULL;
				if($commande && $commande["commande.retour_contrat"]){
				  	$data['data'][$key]["date_signature"] = $commande["commande.retour_contrat"];
				}

				$data['data'][$key]["retourPV"] = false;
				$data['data'][$key]["payee"] = $this->paiementIsReceived($data['data'][$key]['id_affaire_fk'], true);
				if($commande){
					$data['data'][$key]["id_commande_crypt"] = ATF::commande()->cryptId($commande['commande.id_commande']);
					$data['data'][$key]["contrat_signe"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retour")) ? true : false;

					$data['data'][$key]["retourPV"] = file_exists(ATF::commande()->filepath($commande['commande.id_commande'],"retourPV")) ? true : false;
				}else{
					$data['data'][$key]["contrat_signe"] = false;
				}

				$data['data'][$key]["file_facture_fournisseur"] = file_exists(ATF::affaire()->filepath($data['data'][$key]['id_affaire_fk'],"facture_fournisseur"));
				$data['data'][$key]["file_cni"] = file_exists(ATF::affaire()->filepath($data['data'][$key]['id_affaire_fk'],"cni"));
				$data['data'][$key]["file_cniVerso"] = file_exists(ATF::affaire()->filepath($data['data'][$key]['id_affaire_fk'],"cniVerso"));
				$data['data'][$key]["file_pouvoir"] = file_exists(ATF::affaire()->filepath($data['data'][$key]['id_affaire_fk'],"pouvoir"));
				$data['data'][$key]["file_contrat_signe"] = file_exists(ATF::affaire()->filepath($data['data'][$key]['id_affaire_fk'],"contrat_signe"));
				$data['data'][$key]["file_facture_fournisseur"] = file_exists(ATF::affaire()->filepath($data['data'][$key]['id_affaire_fk'],"facture_fournisseur"));


				$data['data'][$key]["file_devis_partenaire"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"devis_partenaire")) ? true : false;
				$data['data'][$key]["file_devis_partenaire_2"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"devis_partenaire_2")) ? true : false;
				$data['data'][$key]["file_devis_partenaire_3"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"devis_partenaire_3")) ? true : false;

				$data['data'][$key]["file_rib_client"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"rib_client")) ? true : false;
				$data['data'][$key]["file_kbis_client"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"kbis_client")) ? true : false;


				$data['data'][$key]["file_facture_partenaire"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"facture_partenaire")) ? true : false;
				$data['data'][$key]["file_facture_partenaire_2"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"facture_partenaire_2")) ? true : false;
				$data['data'][$key]["file_facture_partenaire_3"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"facture_partenaire_3")) ? true : false;

				$data['data'][$key]["file_bon_livraison_partenaire"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"bon_livraison_partenaire")) ? true : false;
				$data['data'][$key]["file_bon_livraison_partenaire_2"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"bon_livraison_partenaire_2")) ? true : false;
				$data['data'][$key]["file_bon_livraison_partenaire_3"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"bon_livraison_partenaire_3")) ? true : false;

				$data['data'][$key]["file_pv_livraison_cleodis_signe_partenaire"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"pv_livraison_cleodis_signe_partenaire")) ? true : false;

				$data['data'][$key]["file_autre_document_partenaire"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"autre_document_partenaire")) ? true : false;
				$data['data'][$key]["file_autre_document_partenaire_2"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"autre_document_partenaire_2")) ? true : false;
				$data['data'][$key]["file_autre_document_partenaire_3"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"autre_document_partenaire_3")) ? true : false;




				ATF::bon_de_commande()->q->reset()
			       ->addField("id_bon_de_commande")
			       ->from("bon_de_commande", "id_affaire", "affaire", "id_affaire")
			       ->where("affaire.id_affaire", ATF::affaire()->decryptId($data['data'][$key]['id_affaire_fk']), "AND")
			       ->setDimension('cell');
			    if($id_bon_de_commande = ATF::bon_de_commande()->sa()){
			    	$data['data'][$key]["bon_de_commande"] = true;
			    	$data['data'][$key]["id_bon_de_commande_crypt"] = ATF::bon_de_commande()->cryptId($id_bon_de_commande);

			    }else{
			    	$data['data'][$key]["bon_de_commande"] = false;
			    	$data['data'][$key]["id_bon_de_commande_crypt"] = null;
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
			'affaire.id_partenaire',
			'affaire.date',
			"affaire.site_associe",
			'affaire.ref',
			'affaire.etat_comite',
			'affaire.pieces',
			'affaire.id_societe',
			'societe.societe',
			'societe.id_contact_signataire',
			'loyer.loyer');
		$this->q->reset();


		$this->q->setCount();


		if ($get['id_affaire']) $colsData = array("affaire.affaire","affaire.id_affaire","affaire.etat",'affaire.provenance','affaire.id_partenaire','affaire.date','affaire.ref','affaire.etat_comite','affaire.id_societe', 'affaire.pieces', 'affaire.date_verification');

		$this->q->addField($colsData)
				->addField("Count(bon_de_commande.id_bon_de_commande)","total_bdc")
				->addField("Count(commande.id_commande)","nb_contrat")
				->addField("famille.famille","famille")
				->from("affaire","id_societe","societe","id_societe")
				->from("societe","id_contact_signataire","contact","id_contact")
				->from("affaire","id_affaire","bon_de_commande","id_affaire")
				->from("affaire","id_affaire","commande","id_affaire")
				->from("affaire","id_affaire","loyer","id_affaire")
				->from("affaire","id_affaire", "commande", "id_affaire")
				->from("societe","id_famille", "famille", "id_famille");


		if($get['site_associe'] && $get['site_associe'] === 'toshiba'){
			$this->q->where("site_associe",'toshiba')
				->addGroup("affaire.id_affaire");
		}else if ($get['site_associe'] && $get['site_associe'] === 'btwin'){
			$this->q->where("site_associe",'btwin')
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

		if ($get['revendeurs']){
			$this->q->from("affaire","id_partenaire","societe","id_societe","partenaire")
							->where("partenaire.revendeur",'oui');
		}

		if($get["search"]){
			header("ts-search-term: ".$get['search']);
			$this->q->setSearch($get['search']);
		}

		if ($get['id_affaire']) {



		  $this->q->where("affaire.id_affaire",$this->decryptId($get["id_affaire"]))->setCount(false)->setDimension('row');
		  $data = $this->sa();


		  $data["partenaire"] = NULL;
		  if($data["affaire.id_partenaire_fk"]){
		  	$data["partenaire"] = ATF::societe()->select($data["affaire.id_partenaire_fk"] , "societe");
		  	$data["partenaire_revendeur"] = ATF::societe()->select($data["affaire.id_partenaire_fk"] , "revendeur");

		  }

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
					->addField("siren")
					->addField("societe")
					->addField("cp")
					->addField("ville")
					->addField("pays.pays")
					->addField("tel")
					->addField("langue")
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

		  ATF::loyer()->q->reset()->where("loyer.id_affaire", $this->decryptId($get["id_affaire"]));
		  $data["loyer"] = ATF::loyer()->sa();

		  $data["comites"] = $this->getComite($get["id_affaire"]);
		  //$data["etat_comite_cleodis"] = "en_attente"; //état par defaut vu que le comite est inséré automatiquement quelque soit le resultat sgef/creditSafe

		  $data["file_cni"] = file_exists($this->filepath($get['id_affaire'],"cni"));
		  $data["file_facture_fournisseur"] = file_exists($this->filepath($get["id_affaire"],"facture_fournisseur"));
		  $data["file_cniVerso"] = file_exists($this->filepath($get["id_affaire"],"cniVerso"));
		  $data["file_pouvoir"] = file_exists($this->filepath($get["id_affaire"],"pouvoir"));
		  $data["file_contrat_signe"] = file_exists($this->filepath($get["id_affaire"],"contrat_signe"));
		  $data["file_facture_fournisseur"] = file_exists($this->filepath($get["id_affaire"],"facture_fournisseur"));

		  $data["file_devis_partenaire"] = file_exists($this->filepath($get["id_affaire"],"devis_partenaire")) ? true : false;
		  $data["file_devis_partenaire_2"] = file_exists($this->filepath($get["id_affaire"],"devis_partenaire_2")) ? true : false;
		  $data["file_devis_partenaire_3"] = file_exists($this->filepath($get["id_affaire"],"devis_partenaire_3")) ? true : false;

		  $data["file_rib_client"] = file_exists($this->filepath($get["id_affaire"],"rib_client")) ? true : false;
		  $data["file_kbis_client"] = file_exists($this->filepath($get["id_affaire"],"kbis_client")) ? true : false;


		  $data["file_facture_partenaire"] = file_exists($this->filepath($get["id_affaire"],"facture_partenaire")) ? true : false;
		  $data["file_facture_partenaire_2"] = file_exists($this->filepath($get["id_affaire"],"facture_partenaire_2")) ? true : false;
		  $data["file_facture_partenaire_3"] = file_exists($this->filepath($get["id_affaire"],"facture_partenaire_3")) ? true : false;

		  $data["file_bon_livraison_partenaire"] = file_exists($this->filepath($get["id_affaire"],"bon_livraison_partenaire")) ? true : false;
		  $data["file_bon_livraison_partenaire_2"] = file_exists($this->filepath($get["id_affaire"],"bon_livraison_partenaire_2")) ? true : false;
		  $data["file_bon_livraison_partenaire_3"] = file_exists($this->filepath($get["id_affaire"],"bon_livraison_partenaire_3")) ? true : false;

		  $data["file_pv_livraison_cleodis_signe_partenaire"] = file_exists($this->filepath($get["id_affaire"],"pv_livraison_cleodis_signe_partenaire")) ? true : false;

		  $data["file_autre_document_partenaire"] = file_exists($this->filepath($get["id_affaire"],"autre_document_partenaire")) ? true : false;
		  $data["file_autre_document_partenaire_2"] = file_exists($this->filepath($get["id_affaire"],"autre_document_partenaire_2")) ? true : false;
		  $data["file_autre_document_partenaire_3"] = file_exists($this->filepath($get["id_affaire"],"autre_document_partenaire_3")) ? true : false;



		  foreach ($data["comites"] as $key => $value) {
		  	if(ATF::affaire()->select($get['id_affaire'],"site_associe") == 'toshiba'){
		  		if($value['description']=== 'Comité CLEODIS'){
					$data["etat_comite_cleodis"] = $value['etat'];
				}
		  	}else{
		  		if(ATF::refinanceur()->select($value["id_refinanceur"], 'refinanceur') === 'CLEODIS'){
					$data["etat_comite_cleodis"] = $value['etat'];
				}
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


		  	ATF::bon_de_commande()->q->reset()
		       ->addField("id_bon_de_commande")
		       ->from("bon_de_commande", "id_affaire", "affaire", "id_affaire")
		       ->where("affaire.id_affaire", ATF::affaire()->decryptId($data["idcrypted"]), "AND")
		       ->setDimension('cell');
		    if($id_bon_de_commande = ATF::bon_de_commande()->sa()){
		    	$data["bon_de_commande"] = true;
		    	$data["id_bon_de_commande_crypt"] = ATF::bon_de_commande()->cryptId($id_bon_de_commande);

		    }else{
		    	$data["bon_de_commande"] = false;
		    	$data["id_bon_de_commande_crypt"] = null;
		    }




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
						->whereIsNull("devis.first_date_accord")
						->where("affaire.etat","devis");
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
				$this->q->where("affaire.etat","perdu","AND",false,'!=');

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
				$this->q->where("affaire.etat","perdu","AND",false,'!=');

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
				$this->q->where("affaire.etat","perdu","AND",false,'!=');


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
				$this->q->where("affaire.etat","perdu","AND",false,'!=');

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
				$this->q->where("affaire.etat","perdu","AND",false,'!=');

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
					if($k_ === "affaire.id_partenaire"){
						$data['data'][$key]["id_partenaire_fk"] = $value;
						$data['data'][$key][$k_] = ATF::societe()->select($value , "societe");
					}


					if (strpos($k_,".")) {
						$tmp = explode(".",$k_);
						$data['data'][$key][$tmp[1]] = $val;
						unset($data['data'][$key][$k_]);
					}
				}

				// pour chaque affaire on recupere ses comites
				foreach ($this->getComite($data['data'][$key]["id_affaire_fk"]) as $k => $comite) {
					if(ATF::affaire()->select($data['data'][$key]["id_affaire_fk"],"site_associe") == 'toshiba'){
				  		if($comite['description']=== 'Comité CLEODIS'){
							$data['data'][$key]["etat_comite_cleodis"] = $comite['etat'];
						}
				  	}else{
				  		if(ATF::refinanceur()->select($comite["id_refinanceur"], 'refinanceur') === 'CLEODIS'){
							$data['data'][$key]["etat_comite_cleodis"] = $comite['etat'];
						}
				  	}
				}

				$data['data'][$key]["contact"] = ATF::contact()->select($value['societe.id_contact_signataire']);
				$data['data'][$key]["cni"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"cni")) ? true : false;
				$data['data'][$key]["contrat_signe"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"contrat_signe")) ? true : false;
				$data['data'][$key]["facture_fournisseur"] = file_exists($this->filepath($value['affaire.id_affaire_fk'],"facture_fournisseur")) ? true : false;




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
		$action = $post["action"];

		$etat = "valide_administratif";

		if($action === "NOK"){
			$etat = "refus_administratif";
		}

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
			return array("result"=>false);
		} else {
			ATF::loyer()->q->reset()->where("id_affaire", $id_affaire)
				->addOrder("id_loyer", "ASC")
				->setLimit(1);
			$loyer = ATF::loyer()->select_row();
			return array("result"=>$loyer);
		}
	}

	/** Fonction qui crée une affaire partenaire
	* @author Cyril CHARLIER <ccharlier@absystech.fr>
	*/
	public function _CreateAffairePartenaire($get,$post,$files) {
		$utilisateur  = ATF::$usr->get("contact");

		ATF::db($this->db)->begin_transaction();
		try {
			$id_societe = $post["id_societe"];
			// dans le cas d'un nouveau dirigeant
			if($post['gerant'] === "0"){
				$post['gerant'] = ATF::contact()->i(
					array(
						"nom"=>$post["nom_gerant"],
						"prenom"=>$post["prenom_gerant"],
						"tel"=>$post["phone_gerant"],
						"email"=>$post["email_gerant"],
						"fonction"=> $post["fonction_gerant"],
						"id_societe"=> $id_societe,
						"est_dirigeant"=> "oui"
					)
				);
			}

			$id_contact = $post["gerant"];
			$devis = array(
			  "id_societe" => $id_societe,
			  "type_contrat" => "lld",
			  "validite" => date("d-m-Y", strtotime("+1 month")),
			  "tva" => __TVA__,
			  "devis" => $post['libelle'],
			  "date" => date("d-m-Y"),
			  "type_devis" => "normal",
			  "id_contact" => $id_contact,
			  "id_user"=>ATF::$usr->getID(),
			  "type_affaire" => "normal",
			  "langue"=>ATF::societe()->select($id_societe, "langue")
			);

			$values_devis =array();

			$montantLoyer = $duree = 0;

			$loyer = array();
			$produits = array();
			$loyer[0] = array(
				"loyer__dot__loyer"=>0,
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
			  "devis_ligne__dot__produit"=> $post['libelle'],
			  "devis_ligne__dot__quantite"=>1,
			  "devis_ligne__dot__type"=>"sans_objet",
			  "devis_ligne__dot__ref"=>"",
			  "devis_ligne__dot__prix_achat"=>$post["loyer"],
			  "devis_ligne__dot__id_produit"=>"",
			  "devis_ligne__dot__id_fournisseur"=>"",
			  "devis_ligne__dot__visibilite_prix"=>"invisible",
			  "devis_ligne__dot__date_achat"=>"",
			  "devis_ligne__dot__commentaire"=>"",
			  "devis_ligne__dot__neuf"=>"oui",
			  "devis_ligne__dot__id_produit_fk"=>"",
			  "devis_ligne__dot__id_fournisseur_fk"=>246
			);
			$values_devis = array("loyer"=>json_encode($loyer), "produits"=>json_encode($produits));

			$id_devis = ATF::devis()->insert(array("devis"=>$devis, "values_devis"=>$values_devis));

			$devis = ATF::devis()->select($id_devis);
			// récupérer dans la session l'id societe partenaire qui crée le contrat
			if($post["site_associe"])	ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"],"site_associe"=>$post["site_associe"]));

			ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"],"provenance"=>"partenaire",'id_partenaire'=>ATF::$usr->get('contact','id_societe')));
          

			//Recupere Apporteur de ta société
            $apporteur = ATF::societe()->select(ATF::$usr->get('contact','id_societe'),'id_apporteur');

			if ($apporteur){
				ATF::affaire()->u(array('id_affaire'=>$devis["id_affaire"],'id_apporteur'=>$apporteur));
			}

			ATF::affaire()->u(array("id_affaire"=>$devis["id_affaire"],
									"provenance"=>"partenaire",
								    'id_partenaire'=>ATF::$usr->get('contact','id_societe')));

			//Envoi du mail
			ATF::affaire()->createTacheAffaireFromSite($devis["id_affaire"]);

			// une fois l'id affaire connue on peut ajouter le devis
			if ($content_file = file_get_contents($files['devis_file']['tmp_name'])) {
				$this->store(ATF::_s(),$devis["id_affaire"],'devis_partenaire',$content_file);
			}

			ATF::affaire_etat()->insert(array(
				"id_affaire"=>$devis["id_affaire"],
				"etat"=>"reception_demande"
			));
			$societe = ATF::societe()->select($id_societe);
			$comite = array  (
				"id_societe" => $id_societe,
				"id_affaire" => $devis["id_affaire"],
				"id_contact" => $id_contact,
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

			if(($societe["cs_score"] > 50 && $creation < $past2Years)){
				$comite["etat"] = "accepte";
				$comite["decisionComite"] = "Accepté automatiquement";
			}else{
				$comite["etat"] = "refuse";
				$comite["decisionComite"] = "Refusé automatiquement (Note < 50, ou ancienneté < 2ans";
			}


			$comite["reponse"] = date("Y-m-d");
			$comite["validite_accord"] = date("Y-m-d");


			ATF::user()->q->reset()->where("nom", "delattre");
			$users = ATF::user()->select_all();

			$notifie = array();
			foreach ($users as $key => $value) {
				$notifie[] = $value["id_user"];
			}


			$suivi = array(
				 "id_societe"=>$id_societe
				,"id_affaire"=>$devis["id_affaire"]
				,"id_contact"=>$utilisateur["id_contact"]
				,"type_suivi"=>'devis'
				,"texte"=>"Création de l'affaire par :\n".$utilisateur["prenom"]." ".$utilisateur["nom"]."\nSociete ".ATF::societe()->select($utilisateur["id_societe"], "societe")
				,'public'=>'oui'
				,'suivi_societe'=>NULL
				,'suivi_notifie'=>$notifie
			);
			$suivi["no_redirect"] = true;
			ATF::suivi()->insert($suivi);



			ATF::comite()->insert(array("comite"=>$comite));
			if($comite["etat"]== "accepte" || ATF::$codename=='cleodisbe'){
				//Création du comité CLEODIS
				$comite["description"] = "Comité CLEODIS";
				$comite["etat"] = "en_attente";
				$comite["reponse"] = NULL;
				$comite["validite_accord"] = NULL;
				ATF::comite()->insert(array("comite"=>$comite));
			}

			//Si on est sur partenaire CLEODIS BE, on envoi un mail à request@cleodis.com
            if(ATF::$codename=='cleodisbe'){
                $partenaire = ATF::societe()->select(ATF::$usr->get('contact','id_societe'), 'societe');
                $info_mail["from"] = ATF::$usr->get('contact','email');
                $info_mail["objet"] = "Nouvelle demande du partenaire ".$partenaire;
                $info_mail["html"] = false;
                $info_mail["template"] = "devis_partenaire";
                $info_mail["partenaire"] = $partenaire;
                $info_mail["client"] = $societe["societe"];
                $info_mail["url"] = __MANUAL_WEB_PATH__."accueil.html#affaire-select-".$this->cryptId($devis["id_affaire"]).".html";
                $info_mail["url_cleoscope"] = __CLEOSCOPE_WEB_PATH__."#!affaire/".$devis["id_affaire"];
                $info_mail["recipient"] = "request@cleodis.com";
                $mail = new mail($info_mail);
                $mail->send($info_mail["recipient"]);
            }


            $dest = array();
            if(ATF::societe()->select($id_societe , "id_owner")) $dest[] = ATF::societe()->select($id_societe , "id_owner");
            if(ATF::societe()->select($id_societe , "id_assistante")) $dest[] = ATF::societe()->select($id_societe , "id_assistante");

            //Creation d'une tache au responsable + assistant de la societe
            $tache = array("tache"=>array(
				"id_societe"=> $id_societe,
				"id_user"=>ATF::$usr->getID(),
				"origine"=>"societe_commande",
				"tache"=>"Nouvelle demande du partenaire.",
				"id_affaire"=>$devis["id_affaire"],
				"type_tache"=>"creation_contrat",
				"horaire_fin"=>date('Y-m-d h:i:s', strtotime('+3 day')),
				"no_redirect"=>"true"
			),
			"dest"=>$dest
			);


			ATF::tache()->insert($tache);


            if($post["commentaire"]){
            	//Creer un suivi pour alisson, Severine, jeanne
            	ATF::user()->q->reset()->where("login", "smazars", "OR", "filles")
            						   ->where("login", "jvasut", "OR", "filles")
            						   ->where("login", "abowe", "OR", "filles");
            	$filles = ATF::user()->sa();
            	$notifie = array();

            	foreach ($filles as $key => $value) {
            		$notifie[] = $value["id_user"];
            	}

            	$suivi = array(
					 "id_societe"=>$id_societe
					,"id_affaire"=>$devis["id_affaire"]
					,"id_contact"=>$utilisateur["id_contact"]
					,"type_suivi"=>'devis'
					,"texte"=>"Commentaire depuis l'espace partenaire : <br />".$post["commentaire"]
					,'public'=>'oui'
					,'suivi_societe'=>NULL
					,'suivi_notifie'=>$notifie
				);
				$suivi["no_redirect"] = true;
				ATF::suivi()->insert($suivi);


            }


			ATF::db($this->db)->commit_transaction();
		} catch (errorATF $e) {
			ATF::db($this->db)->rollback_transaction();
			throw $e;
		}
		return array(
			"result"=>true,
			"resultat_comite" => $comite["etat"],
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
									  ->addField("devis.id_societe",'id_societe')
									  ->from("affaire","id_affaire","devis","id_affaire")
									  ->where('affaire.id_partenaire',$apporteur)

									  ->where("affaire.etat","devis","AND",false,"!=")
									  ->where("affaire.etat","perdue","AND",false,"!=")
									  ->where("affaire.etat","terminee","AND",false,"!=")


									  ->where("affaire.nature","vente","AND",false,"!=")

									  ->addGroup('affaire.id_affaire');
			if($get["id_societe"])	ATF::affaire()->q->where("affaire.id_societe",$get['id_societe']);
			if($get["id_affaire"])  ATF::affaire()->q->where("affaire.id_affaire",$get['id_affaire']);

			$affaires = ATF::affaire()->select_all();



			if($affaires){

				$affaire_soc = $parc_soc = array();

				foreach ($affaires as $key => $value) {

					ATF::commande()->q->reset()->where("commande.id_affaire",$value['affaire.id_affaire'],"AND")
											   ->where("commande.etat", "non_loyer","AND", false, "!=")
											   ->where("commande.etat", "AR","AND", false, "!=")
											   ->where("commande.etat", "arreter","AND", false, "!=")
											   ->where("commande.etat", "arreter_contentieux","AND", false, "!=")
											   ->where("commande.etat", "vente","AND", false, "!=");
					$contrat = ATF::commande()->select_row();

					if($contrat){
						$id_soc = ATF::affaire()->select($value["affaire.id_affaire"] , "id_societe");
						$id_soc = ATF::societe()->cryptID($id_soc);

						$societes[$id_soc] = ATF::societe()->select($id_soc);

						$value['id_affaire'] = $this->cryptID($value['affaire.id_affaire']);
						$value["id_devis"] = ATF::devis()->cryptID($value['id_devis']);

						$societes[$id_soc]["show"] = false;

						if($id_soc === ATF::societe()->cryptID($get["id_societe"])){
							$societes[$id_soc]["show"] = true;
						}

						$affaire_soc[$id_soc][$value["affaire.id_affaire"]] = $value;
						$affaire_soc[$id_soc][$value["affaire.id_affaire"]]['show'] = false;

						if($get["id_affaire"] && $value["affaire.id_affaire"] == $get["id_affaire"]){
							$affaire_soc[$id_soc][$value["affaire.id_affaire"]]['show'] = true;
						}

						$parc_soc[$id_soc][$value["affaire.id_affaire"]]['parc'] = ATF::parc()->getParcPartenaire($value["affaire.id_affaire"]);
						$parc_soc[$id_soc][$value["affaire.id_affaire"]]['id_devis'] = $value["id_devis"];
					}
				}

				foreach ($affaire_soc as $key => $value) {
					$societes[$key]["affaires"] = $value;
					$societes[$key]["parc_societe"] = array();
					$societes[$key]["parc_societe"]["show"] = true;
				}

				foreach ($parc_soc as $key => $value) {
					//On stocke les parcs de la société par affaire
					$societes[$key]["parc"] = $value;

					//On stocke les parcs de la société pour l'onglet parc (dans le panel societe)
					foreach ($value as $kaffaireParc => $vaffaireParc) {
						$societes[$key]["parc"][$kaffaireParc]["show"] = false;


						if($get["id_affaire"] && $kaffaireParc == $get["id_affaire"]){
							$societes[$key]["parc_societe"]["show"] = false;
							$societes[$key]["parc"][$kaffaireParc]["show"] = true;
						}

						if($vaffaireParc["parc"]){
							foreach ($vaffaireParc["parc"] as $kparc => $vparc) {
								$societes[$key]["parc_societe"]['parc'][] = $vparc;
							}
						}
					}

				}
				header("ts-total-row: ".count($societes));

				return array("societes" => $societes);
			}else{
				$ret=array(	);
			}
			header("ts-total-row: 0");

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



	/**
	 * [repriseContratToshiba description]
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  [type] $get  [description]
	 * @param  [type] $post [description]
	 * @return [type]       [description]
	 */
	public function _repriseContratToshiba($get, $post){

		ATF::$usr->set('id_user',16);
	    ATF::$usr->set('id_agence',1);

	    $id_affaire = ATF::affaire()->decryptId($post["id_affaire"]);

		ATF::commande()->q->reset()->where("commande.id_affaire", $id_affaire);
		$contrat = ATF::commande()->select_row();


		$return = array("sign"=>false,
						"pieces"=>false,
						 "paiement"=>false);

		if($contrat) {
			//Si le contrat est déja signé
			if (file_exists(ATF::commande()->filepath($contrat['commande.id_commande'],"retour"))){
				$return["sign"] = true;

				if(ATF::affaire()->select($id_affaire , "pieces") == 'OK') {
					$return["pieces"] = true;

					ATF::transaction_banque()->q->reset()->where("transaction_banque.id_affaire", $id_affaire);

					if($transaction = ATF::transaction_banque()->select_row()){
						$return["paiement"] = true;
					}
				}
			}
		} else {
			ATF::societe()->_createContratToshiba($get , $post);
		}
		return $return;

	}


	/**
	 * Permet d'updater des champs editable depuis CLEOSCOPE
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  [type] $get  [description]
	 * @param  [type] $post [description]
	 */
	public function _set($get, $post) {

	  	$input = file_get_contents('php://input');

	  	if (!empty($input)) parse_str($input,$post);
		if (!$post['name']) throw new Exception("NAME_MISSING",1200);
		if (!isset($post['value'])) throw new Exception("VALUE_MISSING",1201);
		if (!$post['pk']) throw new Exception("IDENTIFIANT_MISSING",1202);

		switch ($post['name']) {
			default:
				$toUpdate = array($post['name']=>$post['value']);
			break;
		}

		$toUpdate['id_affaire'] = $post['pk'];

		return $this->u($toUpdate);
	}

	public function _setPartenaire($get, $post) {

	  	$input = file_get_contents('php://input');

	  	if (!empty($input)) parse_str($input,$post);
		if (!$post['name']) throw new Exception("NAME_MISSING",1200);
		if (!isset($post['value'])) throw new Exception("VALUE_MISSING",1201);
		if (!$post['pk']) throw new Exception("IDENTIFIANT_MISSING",1202);

		switch ($post['name']) {
			default:
				$toUpdate = array($post['name']=>$post['value']);
			break;
		}

		$toUpdate['id_affaire'] = $post['pk'];

		if( $this->u($toUpdate) ){
			return array('id'=> $post['value'], "name"=> ATF::societe()->select($post["value"], "societe"));
		}
	}




	/**
	 * Creer une tache à destination d'Emily, Severine et Alison pour les affaires provenant des portails Hors Optima (Toshiba, Btwin, espace partenaire ...)
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  $id_affaire
	 */
	public function createTacheAffaireFromSite($id_affaire){
		if (ATF::$codename != 'cleodis' && ATF::$codename != 'cleodisbe') return;

		ATF::user()->q->reset()->where("login", "jvasut", "OR", "filles")
								->where("login", "abowe", "OR", "filles")
								->where("login", "egerard", "OR", "filles")
								->where("login", "btronquit", "OR", "filles")
								->where("login", "pcaminel", "OR", "filles")
								->where("login", "smazars", "OR", "filles");


		$filles = ATF::user()->sa();
        $dest = array();
        foreach ($filles as $key => $value) {
        	$dest[] = $value["id_user"];
     	}


		// $dest = array("18", "21","112", "103","124");  //Pierre, Allison, Severine, Emily, Jeanne
		// $id_user = 116; //Benjamin Tronquit

		// if(ATF::$codename === "cleodisbe"){
		// 	$dest = array("18", "21", "104", "124");  //Pierre, Allison, Severine,  Jeanne
		// 	$id_user = 113;  //Benjamin Tronquit
		// }

		$affaire = ATF::affaire()->select($id_affaire);
		$societe = ATF::societe()->select($affaire["id_societe"]);

		$partenaire = "";

		if($affaire["id_partenaire"]) $partenaire = ATF::societe()->select($affaire["id_partenaire"] , "societe");

		$tache = array("tache"=>array("id_societe"=> ATF::affaire()->select($id_affaire, "id_societe"),
									   "id_user"=>$id_user,
									   "origine"=>"societe_commande",
									   "tache"=>"Nouvelle affaire créée. Merci de traiter<br />Affaire : ".$affaire["ref"]."<br />Provenance : ".$affaire["provenance"]."<br />Site : ".$affaire["site_associe"].". <br />Partenaire : ".$partenaire."<br />Données de l'entité : Score : ".$societe["cs_score"].", création : ".$societe["date_creation"].".",
									   "id_affaire"=>$id_affaire,
									   "type_tache"=>"creation_contrat",
									   "horaire_fin"=>date('Y-m-d h:i:s', strtotime('+3 day')),
									   "no_redirect"=>"true"
									  ),
						"dest"=>$dest
					  );
		ATF::tache()->insert($tache);
	}

	public function validateOrderPartenaire($infos,&$s,$files=NULL,&$cadre_refreshed){

		require __ABSOLUTE_PATH__.'includes/cleodis/boulangerpro/boulangerpro.php';

		$affaire = $this->select($infos["id_affaire"]);

		//On recupere toute les affaires avec le meme site associé et ref_externe
		$this->q->reset()->where("site_associe",$affaire["site_associe"])->where("ref_externe",$affaire["ref_externe"]);
		$affaires = $this->sa();

		$retour = boulangerpro::validateOrder($affaire["ref_externe"]);
		if($retour){
			foreach ($affaires as $key => $value) {
				ATF::affaire()->u(array("id_affaire"=>$value["id_affaire"], "etat_cmd_externe"=>"valide"));
				ATF::$msg->addNotice("Commande validée chez le partenaire pour l'affaire ".$value["ref"]);
			}
		}
	}

	/**
	 * Permet de recuperer l'affaire de depart pour un avenant ou AR
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  int $id_affaire ID de l'affaire pour laquelle on souhaite avoir l'affaire du départ
	 * @return [type]             [description]
	 */
	public function getAffaireDepart($id_affaire){
		$id_parent = ATF::affaire()->select($id_affaire, "id_parent");
		if($id_parent){
			return $this->getAffaireDepart($id_parent);
		}else{
			return $id_affaire;
		}
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


	/**
	* Retouren l'objet mandat associé à l'affaire passée en paramètre
	* @author Morgan FLEURUQUIN <mfleurquin@absystech.fr>
	* @param int $id_affaire Affaire qui demande sa commande
	* @return commande_cleodis
	*/
	function getMandat($id_affaire=NULL){
		if (!$id_affaire && $this->infos["id_affaire"])	$id_affaire = $this->infos["id_affaire"];

		if($id_affaire){
			ATF::mandat()->q->reset()->setStrict()->addField('mandat.id_mandat')->addCondition("mandat.id_affaire",$id_affaire)->setDimension("cell");
			if($id_mandat = ATF::mandat()->sa()) {
				return $id_mandat;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


};


class affaire_bdomplus extends affaire_cleodis {


	function __construct($table_or_id=NULL) {
		$this->table = "affaire";
		parent::__construct($table_or_id);

		$this->onglets = array(
			'affaire_etat'
			,"sell_and_sign"
			,"facture_magasin"
			,'loyer'
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
			,"licence"
		);

		$this->colonnes['primary']['licence'] = array("custom"=>true);
		$this->fieldstructure();
	}


	/**
	* Retourne la ref d'un avenant
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param int $id_parent
	* @return string ref
	*/
	function getRefAvenant($id_parent){
		//Récup du dernier avenant de cette affaire

		$ref=substr($this->select($id_parent,"ref"),0,9);

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
				->addCondition("LENGTH(`ref`)",5,"AND",false,">")
				->addCondition("ref","%AVT%","AND",false,"NOT LIKE")
				->addField('SUBSTRING(`ref`,5)+1',"max_ref")
				->addOrder('ref',"DESC")
				->setDimension("row")
				->setLimit(1);

		$nb=$this->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="0000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<10000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="00001";
		}
		return $prefix.$suffix;
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
		if($table == "facture"){
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
				$info_mail["objet"] = "Les solutions Zen – Votre facture référence n° ".$enregistrement["ref_externe"];
			}else{
				$info_mail["objet"] = $email["objet"];
			}

			$info_mail["from"] = $from;
			$info_mail["recipient"] = $recipient;
			$info_mail["return_path"] = "ludivine.bowe@cleodis.com";
			$info_mail["template"] = "bdomplus-".$table;
			$info_mail["html"] = true;

			$info_mail["client"] = ATF::societe()->select(ATF::facture()->select($last_id, "id_societe"));
			$info_mail["facture"] = $enregistrement;



			$mail = new mail($info_mail);
			foreach($paths as $key=>$item){
				$path = ATF::$table()->filepath($last_id,$item);
				$mail->addFile($path,$key.$enregistrement["ref_externe"].".pdf",true);
			}
			$mail->send();

			if($email["emailCopie"]){
				$info_mail["recipient"] = $email["emailCopie"];
				$copy_mail = new mail($info_mail);
				foreach($paths as $key=>$item){
					$path = ATF::$table()->filepath($last_id,$item);
					$copy_mail->addFile($path,$key.$enregistrement["ref_externe"].".pdf",true);
				}
				$copy_mail->send();
			}
			return true;
		}else{
			return parent::mailContact($email,$last_id,$table,$paths);
		}
	}


	/*
	* Pour toutes les affaires dont la date de debut = M+11 et qu'il n'y a pas de condition de prolongation
	* On envoi un mail au client pour l'avertir du renouvellement et on crée un suivi
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function process_envoi_mail_information_renouvellement(){
		ATF::commande()->q->reset()->where("etat", "arreter", "AND", false, "!=")
								   ->where("etat", "arreter_contentieux","AND", false, "!=")
								   ->where("etat", "vente", "AND", false, "!=")
								   ->where("etat", "non_loyer", "AND", false, "!=")
								   ->where("etat", "AR", "AND", false, "!=");

		$contrats_en_cours = ATF::commande()->sa();

		foreach ($contrats_en_cours as $key => $value) {
 			$a_renouveller = false;

			if(date("Y-m") == date("Y-m", strtotime("+11 month", strtotime($value["date_debut"]))) ){
				ATF::prolongation()->q->reset()->where("id_affaire", $value["id_affaire"]);
				$prolongation = ATF::prolongation()->sa();

				if(!$prolongation) $a_renouveller = true;
				if($prolongation && $prolongation[0]["duree"] == 0) $a_renouveller = true;
			}

			if($a_renouveller){

				//log::logger($value , "mfleurquin");
				$affaire = ATF::affaire()->select($value["id_affaire"]);


				if($email_pro = ATF::societe()->select($value["id_societe"], "email")){
			      $email = $email_pro;
			    }else{
			      $email = ATF::societe()->select($value["id_societe"], "  particulier_email");
			    }

			    $info_mail["from"] = "L'équipe BDOM+ <contact@abonnements.bdom.fr>";
			    $info_mail["recipient"] = $email;
			    $info_mail["html"] = true;
			    $info_mail["template"] = "mail_information_renouvellement_tel";
			    $info_mail["objet"] = "Votre abonnement ".$affaire["ref"]." ".$affaire["affaire"]." : Information importante sur votre renouvellement";

			    $info_mail["client"] = ATF::societe()->select($value["id_societe"]);
			    $info_mail["date_mise_place"] = date("d/m/Y", strtotime($value["mise_en_place"]));
			    $info_mail["affaire"] = $affaire;

			    //log::logger($info_mail , "mfleurquin");

			    $mail = new mail($info_mail);

			    $send = $mail->send();

			    ATF::contact()->q->reset()->where("id_societe", $value["id_societe"])->where("email", $email);
			    $contact = ATF::contact()->select_row();

			    $texte_suivi =  "Mail d'information sur le renouvellement envoyé à ".$info_mail["recipient"].".<br /><br />";
			    $texte_suivi .= "Le client a jusqu’au 25 pour résilier dans son espace client.<br /><br />";
			    $texte_suivi .= "Sinon, le client s’engage pour une nouvelle année et recevra ses nouvelles clés en fin de mois.";

			    $suivi = array(
			      "id_contact" => $contact["id_contact"],
			      "id_societe" => $value["id_societe"],
			      "id_affaire" => $affaire["id_affaire"],
			      "type"=> "note",
			      "type_suivi"=> "Contrat",
			      "texte" => $texte_suivi
			    );

			    if($send){
			      $suivi["texte"] =  "Envoi du ".$suivi["texte"];
			    }else{
			      $suivi["texte"] =  "Probleme lors de l'envoi du ".$suivi["texte"];
			    }
			    ATF::suivi()->i($suivi);
			}
		}

	}


	/*
	* Pour toutes les affaires dont la date de debut = M+11 et qu'il n'y a pas de condition de prolongation
	* On envoi un mail au client pour l'avertir du renouvellement et on crée un suivi
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function process_envoi_mail_non_renouvellement_client_contentieux(){
		ATF::commande()->q->reset()->where("etat", "arreter", "AND", false, "!=")
								   ->where("etat", "arreter_contentieux","AND", false, "!=")
								   ->where("etat", "vente", "AND", false, "!=")
								   ->where("etat", "non_loyer", "AND", false, "!=")
								   ->where("etat", "AR", "AND", false, "!=");
								   //->where("etat", "%contentieux", "AND", false, "LIKE");

		$contrats_en_cours = ATF::commande()->sa();


		foreach ($contrats_en_cours as $key => $value) {
 			$a_renouveller = false;

			if(date("Y-m") == date("Y-m", strtotime("+11 month", strtotime($value["date_debut"]))) ){
				ATF::prolongation()->q->reset()->where("id_affaire", $value["id_affaire"]);
				$prolongation = ATF::prolongation()->sa();

				if(!$prolongation) $a_renouveller = true;
				if($prolongation && $prolongation[0]["duree"] == 0) $a_renouveller = true;
			}

			if($a_renouveller && ATF::societe()->select($value["id_societe"], "mauvais_payeur") == "oui"){

				//log::logger($value , "mfleurquin");
				$affaire = ATF::affaire()->select($value["id_affaire"]);


				if($email_pro = ATF::societe()->select($value["id_societe"], "email")){
			      $email = $email_pro;
			    }else{
			      $email = ATF::societe()->select($value["id_societe"], "  particulier_email");
			    }

			    $info_mail["from"] = "L'équipe BDOM+ <contact@abonnements.bdom.fr>";
			    $info_mail["recipient"] = $email;
			    $info_mail["html"] = true;
			    $info_mail["template"] = "mail_information_client_contentieux";
			    $info_mail["objet"] = "Votre abonnement ".$affaire["ref"]." ".$affaire["affaire"]." : Votre renouvellement ne pourra avoir lieu";

			    $info_mail["client"] = ATF::societe()->select($value["id_societe"]);
			    $info_mail["date_signature"] = date("d/m/Y", strtotime($value["mise_en_place"]));
			    $info_mail["affaire"] = $affaire;

			    $mail = new mail($info_mail);

			    $send = $mail->send();

			    ATF::contact()->q->reset()->where("id_societe", $value["id_societe"])->where("email", $email);
			    $contact = ATF::contact()->select_row();

			    $texte_suivi =  "Mail d’information sur le renouvellement IMPOSSIBLE envoyé à ".$info_mail["recipient"].".<br /><br />";
			    $texte_suivi .= "Le client est au contentieux.<br /><br />";
			    $texte_suivi .= "Sans régularisation d’ici le 25, le contrat s’arrêtera et il ne pourra pas y avoir de renouvellement.";

			    $suivi = array(
			      "id_contact" => $contact["id_contact"],
			      "id_societe" => $value["id_societe"],
			      "id_affaire" => $affaire["id_affaire"],
			      "type"=> "note",
			      "type_suivi"=> "Contrat",
			      "texte" => $texte_suivi
			    );

			    if($send){
			      $suivi["texte"] =  "Envoi du ".$suivi["texte"];
			    }else{
			      $suivi["texte"] =  "Probleme lors de l'envoi du ".$suivi["texte"];
			    }
			    ATF::suivi()->i($suivi);
			}
		}

	}

	/*
	* Pour toutes les affaires dont la date de debut = M+11 et qu'il n'y a pas de condition de prolongation
	* Si le client est en contentieux
	* On envoi un mail au client pour l'avertir du renouvellement et on crée un suivi
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function process_arret_et_renouvellement(){
		ATF::commande()->q->reset()->where("etat", "arreter", "AND", false, "!=")
								   ->where("etat", "arreter_contentieux","AND", false, "!=")
								   ->where("etat", "vente", "AND", false, "!=")
								   ->where("etat", "non_loyer", "AND", false, "!=")
								   ->where("etat", "AR", "AND", false, "!=");

		$contrats_en_cours = ATF::commande()->sa();

		foreach ($contrats_en_cours as $key => $value) {
			try {
				ATF::db()->begin_transaction();
				$a_renouveller = false;

				if(date("Y-m") == date("Y-m", strtotime("+11 month", strtotime($value["date_debut"]))) ){
					ATF::prolongation()->q->reset()->where("id_affaire", $value["id_affaire"]);
					$prolongation = ATF::prolongation()->sa();

					if(!$prolongation) $a_renouveller = true;
					if($prolongation && $prolongation[0]["duree"] == 0) $a_renouveller = true;
				}

				if($a_renouveller){
					$affaire = ATF::affaire()->select($value["id_affaire"]);

					if($email_pro = ATF::societe()->select($value["id_societe"], "email")){
				      $email = $email_pro;
				    }else{
				      $email = ATF::societe()->select($value["id_societe"], "  particulier_email");
				    }
				    $info_mail["from"] = "L'équipe BDOM+ <contact@abonnements.bdom.fr>";
			   		$info_mail["recipient"] = $email;

			   		ATF::commande()->stopCommande(array("id_commande"=>$value["id_commande"]));

					if(ATF::societe()->select($value["id_societe"], "mauvais_payeur") == "oui"){
						//Client en contentieux, on arrete le contrat et basta


						$info_mail["template"] = "mail_non_renouvellement";
					    $info_mail["objet"] = "Votre abonnement ".$affaire["ref"]." ".$affaire["affaire"]." : Votre renouvellement n'a pas eu lieu ";
					    $info_mail["client"] = ATF::societe()->select($value["id_societe"]);
					    $info_mail["date_signature"] = date("d/m/Y", strtotime($value["mise_en_place"]));
					    $info_mail["affaire"] = $affaire;

					    $mail = new mail($info_mail);

					    $send = $mail->send();

					    ATF::contact()->q->reset()->where("id_societe", $value["id_societe"])->where("email", $email);
					    $contact = ATF::contact()->select_row();

					    $texte_suivi =  "Mail d'information sur le non renouvellement envoyé à ".$info_mail["recipient"].".<br /><br />";
					    $texte_suivi .= "Le client est au contentieux.";

					    $suivi = array(
					      "id_contact" => $contact["id_contact"],
					      "id_societe" => $value["id_societe"],
					      "id_affaire" => $affaire["id_affaire"],
					      "type"=> "note",
					      "type_suivi"=> "Contrat",
					      "texte" => $texte_suivi
					    );

					    if($send){
					      $suivi["texte"] =  "Envoi du ".$suivi["texte"];
					    }else{
					      $suivi["texte"] =  "Probleme lors de l'envoi du ".$suivi["texte"];
					    }
					    ATF::suivi()->i($suivi);
					}else{
						//Client bon payeur, on arrete le contrat et crée l'annule et remplace pour cette affaire
						$this->creationAffaireRenouvellement($value["id_affaire"]);


					}

				}
				ATF::db()->commit_transaction();
			} catch (errorATF $e) {
				ATF::db()->rollback_transaction();
				log::logger($e->getMessage(), "mfleurquin");
			}
		}

	}


	/**
	 * Creation de l'affaire d'annule et Remplace pour le renouvellement
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  int $id_affaire ID affaire de l'affaire à renouveller
	 */
	public function creationAffaireRenouvellement($id_affaire){

		$affaire = ATF::affaire()->select($id_affaire);
		$societe = ATF::societe()->select($affaire["id_societe"]);

		$snap = json_decode($affaire["snapshot_pack_produit"]);

		$prods = array();


		ATF::devis_ligne()->q->reset()->from("devis_ligne","id_devis","devis","id_devis")
									  ->where("id_affaire", $id_affaire);

		$ligne_actuelle = array();

		foreach (ATF::devis_ligne()->sa() as $key => $value) {
			ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $value["id_pack_produit"])
												 ->where("id_produit", $value["id_produit"]);
			$l = ATF::pack_produit_ligne()->select_row();

			$p = array(
				"id_pack_produit" => $value["id_pack_produit"],
				"id_pack_produit_ligne" => $l["id_pack_produit_ligne"],
				"id_produit" => $value["id_produit"],
				"ref" => $value["ref"],
				"produit" => $value["produit"],
				"quantite" => $value["quantite"]
			);
			$prods[] = $p;
		}

		$post = array(
		    "particulier_email" => $societe["particulier_email"],
		    "nom" => $societe["particulier_nom"],
		    "prenom" => $societe["particulier_prenom"],
		    "adresse" => $societe["adresse"],
		    "adresse_2" => $societe["adresse_2"],
		    "ville" => $societe["ville"],
		    "cp" => $societe["cp"],
		    "gsm_perso" => $societe["particulier_portable"],
		    "site_associe" => $affaire["site_associe"],
		    "type" => "particulier",
		    "id_magasin" => $affaire["id_magasin"],
		    "facture" => NULL,
		    "iban" => $affaire["IBAN"],
		    "bic" => $affaire["BIC"],
		    "id_contact" => $societe["id_contact_signataire"],
		    "id_societe" => $affaire["id_societe"],
		    "produits" => json_encode($prods),
		    "id_pack_produit" => array (
		        "0" => $snap->id_pack_produit
		    )
		);
		$affaires = ATF::souscription()->_devis(array(), $post);

		foreach ($affaires["ids"] as $key => $value) {
			ATF::affaire()->u(
				array(
					"id_affaire"=>$value,
					"nature"=>"AR",
					"id_parent" => $id_affaire
				)
			);
			ATF::affaire()->u(array("id_affaire"=>$id_affaire , "id_fille"=> $value));

			ATF::affaire()->q->reset()->where("affaire.id_affaire", $value);
			$new_affaire = ATF::affaire()->select_row();
			$new_affaire["affaire.id_affaire_fk"] = $value;
			$new_affaire["affaire.id_societe_fk"] = $affaire["id_societe"];

			ATF::commande()->q->reset()->where("affaire.id_affaire", $value);
			$commande = ATF::commande()->select_row();
			$commande["commande.id_commande_fk"] = $commande["commande.id_commande"];
			$commande["commande.etat"] = ATF::commande()->select($commande["commande.id_commande"], "etat");

			ATF::souscription()->demarrageContrat($new_affaire, $commande, true);
		}

	}


}
class affaire_bdom extends affaire_cleodis { };
class affaire_boulanger extends affaire_cleodis {

	function __construct($table_or_id=NULL) {
		$this->table = "affaire";
		parent::__construct($table_or_id);

		$this->onglets = array(
			 'affaire_etat'
			,"sell_and_sign"
			,'loyer'
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
			,'facturation_fournisseur'
			,'intervention'
			,'parc'
			,'livraison'
			,'suivi'
			,'tache'
			,"pdf_affaire"
		);

		$this->colonnes['primary']['licence'] = array("custom"=>true);
		$this->fieldstructure();
		$this->addPrivilege("export_boulanger_commande_client");
	}

	public function export_boulanger_commande_client(&$infos){
		$infos["display"] = true;

		ATF::commande()->q->reset()
						  ->addField("societe.particulier_nom","Nom client")
						  ->addField("societe.particulier_prenom","Prénom client")
						  ->addField("societe.code_client","Code client")
						  ->addField("societe.particulier_portable","Téléphone client")
						  ->addField("societe.particulier_email","Email client")
						  ->addField("societe.adresse","Adresse client")
						  ->addField("societe.adresse_2","Adresse 2 client")
						  ->addField("societe.adresse_3","Adresse 3 client")
						  ->addField("societe.cp","Code postal client")
						  ->addField("societe.ville","Ville client")
						  ->addField("commande.ref","Réf commande")
						  ->addField("commande_ligne.ref","Réf produit")
						  ->addField("commande_ligne.produit","Libellé produit")
						  ->addField("commande_ligne.quantite","Quantité")
						  ->from("commande","id_commande","commande_ligne","id_commande")
						  ->from("commande","id_societe","societe","id_societe");

		$data = ATF::commande()->sa();
		log::logger($data, "qjanon");
		ATF::db()->begin_transaction();
		try{
			$fn = "export_boulanger_commande_client.csv";
	        $filepath = '/tmp/'.$fn;
	        $fp = fopen($filepath, 'w');
			fputcsv($fp, array_keys($data[0]));
	        foreach ($data as $line) {
				fputcsv($fp, array_values($line));
			}
			fclose($fp);
			ATF::db()->commit_transaction();
		}catch(errorATF $e){
			ATF::db()->rollback_transaction();
			throw new errorATF("Erreur lors de la génération du fichier");
		}

    	header("Content-type: text/csv");
    	header("Content-Transfer-Encoding: UTF-8");
		header("Content-Disposition: attachment; filename=".$fn);
		header("Pragma: no-cache");
		header("Expires: 0");
        echo readfile($filepath);

	}

	/**
	* Retourne la ref d'un avenant
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param int $id_parent
	* @return string ref
	*/
	function getRefAvenant($id_parent){
		//Récup du dernier avenant de cette affaire
		$ref=substr($this->select($id_parent,"ref"),0,9);
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
				->addCondition("LENGTH(`ref`)",5,"AND",false,">")
				->addCondition("ref","%AVT%","AND",false,"NOT LIKE")
				->addField('SUBSTRING(`ref`,5)+1',"max_ref")
				->addOrder('ref',"DESC")
				->setDimension("row")
				->setLimit(1);
		$this->q->setToString();
		log::logger($this->sa(), "qjanon");
		$this->q->unsetToString();
		$nb=$this->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="0000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<10000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="00001";
		}
		log::logger($suffix, "qjanon");
		return $prefix.$suffix;
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
		if($table == "facture"){
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
				$info_mail["objet"] = "Les solutions Zen – Votre facture référence n° ".$enregistrement["ref_externe"];
			}else{
				$info_mail["objet"] = $email["objet"];
			}

			$info_mail["from"] = $from;
			$info_mail["recipient"] = $recipient;
			$info_mail["return_path"] = "ludivine.bowe@cleodis.com";
			$info_mail["template"] = "bdomplus-".$table;
			$info_mail["html"] = true;

			$info_mail["client"] = ATF::societe()->select(ATF::facture()->select($last_id, "id_societe"));
			$info_mail["facture"] = $enregistrement;



			$mail = new mail($info_mail);
			foreach($paths as $key=>$item){
				$path = ATF::$table()->filepath($last_id,$item);
				$mail->addFile($path,$key.$enregistrement["ref_externe"].".pdf",true);
			}
			$mail->send();

			if($email["emailCopie"]){
				$info_mail["recipient"] = $email["emailCopie"];
				$copy_mail = new mail($info_mail);
				foreach($paths as $key=>$item){
					$path = ATF::$table()->filepath($last_id,$item);
					$copy_mail->addFile($path,$key.$enregistrement["ref_externe"].".pdf",true);
				}
				$copy_mail->send();
			}
			return true;
		}else{
			return parent::mailContact($email,$last_id,$table,$paths);
		}
	}
};
class affaire_assets extends affaire_cleodis {

	/**
	* Retourne la ref d'un avenant
	* @author Mathieu Tribouillard <mtribouillard@absystech.fr>
	* @param int $id_parent
	* @return string ref
	*/
	function getRefAvenant($id_parent){
		//Récup du dernier avenant de cette affaire
		$ref=substr($this->select($id_parent,"ref"),0,9);
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
				->addCondition("LENGTH(`ref`)",5,"AND",false,">")
				->addCondition("ref","%AVT%","AND",false,"NOT LIKE")
				->addField('SUBSTRING(`ref`,5)+1',"max_ref")
				->addOrder('ref',"DESC")
				->setDimension("row")
				->setLimit(1);

		$nb=$this->sa();

		if($nb["max_ref"]){
			if($nb["max_ref"]<10){
				$suffix="0000".$nb["max_ref"];
			}elseif($nb["max_ref"]<100){
				$suffix="000".$nb["max_ref"];
			}elseif($nb["max_ref"]<1000){
				$suffix="00".$nb["max_ref"];
			}elseif($nb["max_ref"]<10000){
				$suffix="0".$nb["max_ref"];
			}else{
				$suffix=$nb["max_ref"];
			}
		}else{
			$suffix="00001";
		}
		return $prefix.$suffix;
	}

};