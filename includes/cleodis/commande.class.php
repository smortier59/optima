<?
/**
* Classe commande
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../commande.class.php";
class commande_cleodis extends commande {
	/**
	* Constructeur
	*/
	function __construct($table_or_id=NULL) {
		$this->table="commande";
		parent::__construct($table_or_id);
		$this->colonnes['fields_column'] = array(
			'commande.ref'
			,'specificDate'=>array("custom"=>true,"nosort"=>true,"renderer"=>"dateCleCommande","width"=>330)
//			,'specificDateRestitution'=>array("custom"=>true,"nosort"=>true,"renderer"=>"dateCleCommandeRestitution","width"=>290)
			,'commande.id_affaire'
			,'code_client'=>array("custom"=>true)
			//,'commande.etat'=>array("renderer"=>"etat","width"=>40)
			,'commande.etat'
			,'files'=>array("custom"=>true,"nosort"=>true,"renderer"=>"pdfCommande","width"=>90) //PDF en Fraçcais
			,'courriers'=>array("custom"=>true,"nosort"=>true,"renderer"=>"pdfCourriers","width"=>90)
			,'retour'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>50)
			,'retourPV'=>array("custom"=>true,"nosort"=>true,"type"=>"file","renderer"=>"uploadFile","width"=>50)
			,'demandeResi'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile","width"=>50)
			,'actions'=>array("custom"=>true,"nosort"=>true,"renderer"=>"cmdAction","width"=>180)
		);

		$this->colonnes['primary'] = array(
			"ref"=>array("disabled"=>true),
			"id_societe"=>array("disabled"=>true),
			"id_affaire"=>array("disabled"=>true),
			"id_devis",
			"commande",
			"etat",
			"date",
			"clause_logicielle",
			"type",
			"tva",
			"date_arret",
			'prolongation'=>array("custom"=>true,"nosort"=>true)
			,'bdc'=>array("custom"=>true,"nosort"=>true)
			,'autreFacture'=>array("custom"=>true,"nosort"=>true)
			,'stop'=>array("custom"=>true,"nosort"=>true)
		);

		$this->colonnes['panel']['loyer_lignes'] = array(
			"loyer"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_repris'] = array(
			"produits_repris"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['lignes_non_visible'] = array(
			"produits_non_visible"=>array("custom"=>true)
		);

		$this->colonnes['panel']['total'] = array(
			 "prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"prix_achat"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"marge"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
			,"marge_absolue"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield","null"=>true)
		);

		$this->colonnes['panel']['courriel'] = array(
			"email"=>array("custom"=>true,'null'=>true)
			,"emailCopie"=>array("custom"=>true,'null'=>true)
			,"emailTexte"=>array("custom"=>true,'null'=>true,"xtype"=>"htmleditor")
		);

		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['dates'] = array("visible"=>true, 'nbCols'=>4);
		$this->panels['lignes_repris'] = array('nbCols'=>1);
		$this->panels['lignes'] = array('nbCols'=>1);
		$this->panels['lignes_non_visible'] = array('nbCols'=>1);
		$this->panels['loyer_lignes'] = array('nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);
		$this->panels['courriel'] = array('nbCols'=>2,"checkboxToggle"=>true);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] = array('stop','autreFacture','bdc','prolongation','date_debut','id_devis','etat','date_evolution','id_user','tva',"retour_prel","mise_en_place","retour_pv","retour_contrat","date_debut","date_evolution","date_arret");
		$this->colonnes['bloquees']['select'] = array('produits','loyer','total');

		$this->fieldstructure();
		$this->noTruncateSA = true;
		$this->no_insert = true;
		$this->no_update = true;
		$this->onglets = array('commande_ligne','bon_de_commande');



		$this->files["contratA3"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"force_generate"=>true);
		$this->files["contratA4"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"force_generate"=>true);
		$this->files["contratAP"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"force_generate"=>true);
		$this->files["contratPV"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"force_generate"=>true);

		//$this->files["contratA4NL"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"force_generate"=>true);


		$this->files["retour"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->files["retourPV"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

        /*
		Les courriers ne doivent pas se générer à la création du contrat
        $this->files["envoiContratEtBilan"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
        $this->files["envoiContratSsBilan"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
        $this->files["envoiAvenant"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
        $this->files["contratTransfert"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
        $this->files["ctSigne"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
        $this->files["CourrierRestitution"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);
        $this->files["envoiCourrierClassique"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true);*/

        $this->files["demandeResi"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);

		$this->addPrivilege("majMail","update");
		$this->addPrivilege("updateDate","update");
		$this->addPrivilege("uploadScanDocument","update");
		$this->addPrivilege("getCommande_ligne");
		$this->addPrivilege("stopCommande","update");
		$this->addPrivilege("reactiveCommande","update");
		$this->addPrivilege("generateCourrierType");
//		$this->addPrivilege("updateDateResiliation","update");
//		$this->addPrivilege("updateDateRestitution","update");
//		$this->addPrivilege("updateDateRestitution_effective","update");
		$this->addPrivilege("export_loyer_assurance");
		$this->addPrivilege("export_contrat_refinanceur_loyer");
		$this->addPrivilege("getDateResti");
		$this->addPrivilege("export_contrat_pas_mep");

		/* Important pour patcher le problème de FK FROM relevé dans le ticket : 7775 */
		$this->foreign_key["id_fournisseur"] = "societe";
		$this->foreign_key["id_apporteur"] = "societe";
		$this->selectAllExtjs=true;
	}


	/**
	 * [_contratPartenaire retourne les contrats du contact loggué]
	 * @param  [type] $get  [description]
	 * @param  [type] $post [description]
	 * @return [array]       [description]
	 */
	public function _contratPartenaire($get,$post) {
		if ($apporteur = ATF::$usr->get("contact")) {

			// Gestion du tri
			if (!$get['tri'] || $get['tri'] == 'action') $get['tri'] = "commande.ref";
			if (!$get['trid']) $get['trid'] = "desc";

			ATF::commande()->q->reset()
				//->addField('affaire.*, loyer.*')
				->addJointure("commande","id_societe","societe","id_societe")
				->addJointure("commande","id_affaire","affaire","id_affaire")
				->addJointure("commande","id_affaire","loyer","id_affaire")

				//->where("affaire.provenance", "partenaire")


				->where("commande.etat", "non_loyer","AND", false, "!=")
				->where("commande.etat", "AR","AND", false, "!=")
				->where("commande.etat", "arreter","AND", false, "!=")
				->where("commande.etat", "vente","AND", false, "!=")


				->where("affaire.id_partenaire", $apporteur["id_societe"]); // en attendant la resolution du probleme de session

			if($get["search"]) {
				ATF::commande()->q->where("affaire.ref", "%".$get["search"]."%" , "OR", "search", "LIKE")
								->where("societe.societe", "%".$get["search"]."%" , "OR", "search", "LIKE")
								->where("societe.code_client", "%".$get["search"]."%" , "OR", "search", "LIKE")
								->where("societe.cp", "%".$get["search"]."%" , "OR", "search", "LIKE")
								->where("societe.siret", "%".$get["search"]."%" , "OR", "search", "LIKE")
								->where("affaire.affaire", "%".$get["search"]."%" , "OR", "search", "LIKE");
			}
			// filtre sur les dates
			if ($get["filters"] && $get["filters"]["startdate"]){
				ATF::commande()->q->where("commande.date_debut", $get["filters"]["startdate"]);
			}

			if ($get["filters"] && $get["filters"]["enddate"]){
				ATF::commande()->q->where("commande.date_arret", $get["filters"]["enddate"]);
			}

			if($commande = ATF::commande()->sa($get['tri'],$get['trid'])){
				$limitTime = date("Y-m-d", strtotime("-13 month", time())); // date du jour - 13 mois
				foreach ($commande as $key => $cmd) {
					$commande[$key]["solde_renouvelant"] = new DateTime($limitTime) < new DateTime($cmd["date_debut"]) ? "Non" : "Oui";
					$commande[$key]["somme_loyer"] = $cmd["loyer"] * $cmd["duree"];
				}
				header("ts-total-row: ".count($commande));
				return $commande;
			}
			else {
				header("ts-total-row: 0");
				return array();
			}
		}
	}



	/**
	 * Permet de stocker le PDF signé de Sell&Sign
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  array $get
	 * @param  array $post
	 * @return
	 */
	public function _getSignedContract($get, $post){
		if (!$post['data']) throw new Exception("Il manque le base64", 500);
		if (!$post['contract_id']) throw new Exception("Il manque l'id du contract", 500);

		$data = $post['data'];
		$contract_id = $post['contract_id'];

		// Récupérer l'id_commande a partir de l'id_contract_sellandsign
		ATF::affaire()->q->reset()->where("id_contract_sellandsign",$post['contract_id'])->setStrict()->addField('commande.id_affaire')->setDimension('cell');
		$id_affaire = ATF::affaire()->select_all();
		$commande = ATF::affaire()->getCommande($id_affaire);

		$file = $this->filepath($commande->get('id_commande'), 'retour', null, 'cleodis');
		try {
			util::file_put_contents($file,base64_decode($data));
			//On met à jour la date de retour et retourPV du contrat
			ATF::commande()->u(array("id_commande"=>$commande->get('id_commande'),
									 "retour_prel"=> date("Y-m-d"),
									 "retour_contrat"=>date("Y-m-d")
									)
								);
			$return = true;
		} catch (Exception $e) {
			$return  = array("error"=>true, "data"=>$e);
		}

		return $return;
	}


	public function _setIdContractor($get, $post){
		try {
			ATF::affaire()->u(array("id_affaire"=>$post['id_affaire'], "id_contract_sellandsign"=>$post['id_contract']));
		} catch (Exception $e) {
			return false;
		}
		return true;
	}


	public function uploadFileFromSA(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$infos['display'] = true;
		$class = ATF::getClass($infos['extAction']);
		if (!$class) return false;
		if (!$infos['id']) return false;
		if (!$files) return false;

		$id = $class->decryptID($infos['id']);

		$id_affaire = $class->select($id, "id_affaire");

		foreach ($files as $k=>$i) {
			if (!$i['size']) return false;


			$id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$id_affaire, "provenance"=>ATF::$usr->trans($class->name(), "module")." ".$k." ref : ".$class->select($id, "ref")));
			$this->store($s,$id,$k,$i);

			copy($class->filepath($id,$k), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));

		}
		ATF::$cr->block('generationTime');
		ATF::$cr->block('top');



		$o = array ('success' => true );
		return json_encode($o);
	}

	/**
    * Retourne la valeur par défaut spécifique aux données des formulaires
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return string
    */
	public function default_value($field,&$s,&$request){

		if($id_commande = ATF::_r('id_commande')){
			$commande=$this->select($id_commande);
		}elseif ($id_devis = ATF::_r('id_devis')) {
			$commande=ATF::devis()->select($id_devis);
		}

		if($commande){
			switch ($field) {
				case "ref":
					return ATF::affaire()->select($commande['id_affaire'],"ref");
				case "id_societe":
					return $commande['id_societe'];
				case "id_affaire":
					return $commande['id_affaire'];
				case "commande":
						if($commande['commande']){
							$resume = $commande['commande'];
						}elseif($commande['devis']){
							$resume = $commande['devis'];
						}
						return $resume;
				case "date":
					return $commande['id_commande']?$commande['date']:date("Y-m-d");
				case "clause_logicielle":
						if($commande['commande']){
							$clause_logicielle = $commande['clause_logicielle'];
						}elseif($commande['devis']){
							$clause_logicielle = "non";
						}
						return $clause_logicielle;
				case "type":
						if($commande['commande']){
							$type = $commande['type'];
						}elseif($commande['devis']){
							$type = "prelevement";
						}
						return $type;
				case "email":
					if($commande["id_contact"]){
						return ATF::contact()->select($commande["id_contact"],"email");
					}
					break;
				case "emailCopie":
					return ATF::$usr->get("email");
				case "emailTexte":
					return $this->majMail($commande["id_societe"]);
				case "prix_achat":
					return $commande['prix_achat'];
				case "prix":
					return $commande['prix'];
				case "id_devis":
					return $commande['id_devis'];
			}
		}

		return parent::default_value($field,$s,$request);
	}

	/**
	* Surcharge de l'insert
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @param array $nolog True si on ne désire par voir de logs générés par la méthode
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			if ($infos['file']) $fileToPreview = $infos['file'];
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$infos_ligne_repris = json_decode($infos["values_".$this->table]["produits_repris"],true);
		$infos_ligne_non_visible = json_decode($infos["values_".$this->table]["produits_non_visible"],true);
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		unset($infos["values_".$this->table]["produits"]);

		$envoyerEmail = $infos["panel_courriel-checkbox"];
		$this->infoCollapse($infos);

		//Gestion mail
		if($envoyerEmail){
			$email["email"]=$infos["email"];
			$email["emailCopie"]=$infos["emailCopie"];
			$email["texte"]=$infos["emailTexte"];
		}else{
			$email=false;
		}

		unset($infos["email"],$infos["emailCopie"],$infos["emailTexte"],$infos["AR_societe"]);
		$devis=ATF::devis()->select($infos["id_devis"]);
		$infos["id_affaire"]=$devis["id_affaire"];
		$infos["tva"]=$devis["tva"];
		$this->q->reset()->addCondition("ref",ATF::affaire()->select($infos["id_affaire"],"ref"))->setCount();
		$countRef=$this->sa();
		if($countRef["count"]>0){
			throw new errorATF("Cette Ref de commande existe déjà !",878);
		}
		$infos["ref"]=ATF::affaire()->select($infos["id_affaire"],"ref");
		$infos["etat"]="non_loyer";
		$infos["id_user"] = ATF::$usr->getID();

		$this->check_field($infos);

		ATF::db($this->db)->begin_transaction();

		//*****************************Transaction********************************
		if((ATF::affaire()->select($devis["id_affaire"], "site_associe") || ATF::affaire()->select($devis["id_affaire"], "provenance")) && ATF::affaire()->select($devis["id_affaire"], "provenance") != "partenaire"){
			//On ne crée pas la tache pour les affaires du portail partenaire, car elle est crée lors de la création de l'affaire
			ATF::affaire()->createTacheAffaireFromSite($devis["id_affaire"]);
		}


		if(ATF::$codename != "bdomplus"){
			$tache = array("tache"=>array("id_societe"=> $devis["id_societe"],
									   "id_user"=>$infos["id_user"],
									   "origine"=>"societe_commande",
									   "tache"=>"Relancer le contrat ",
									   "id_affaire"=>$infos["id_affaire"],
									   "type_tache"=>"creation_contrat",
									   "horaire_fin"=>date('Y-m-d h:i:s', strtotime('+3 day')),
									   "no_redirect"=>"true"
									  ),
						"dest"=>$dest
					  );
			$id_tache = ATF::tache()->insert($tache);

		}


		unset($infos["marge"],$infos["marge_absolue"]);
		$last_id = parent::insert($infos,$s,NULL,$var=NULL,NULL,true);

		$dest = NULL;
		if($infos["id_user"] == 18) $dest = 21;
		elseif($infos["id_user"] == 93) $dest = 103;
		else $dest = ATF::$usr->getID();



		// Mise à jour du forecast
		if(ATF::$codename == "cleodisbe") $affaire = new affaire_cleodisbe($infos['id_affaire']);
		elseif(ATF::$codename == "bdomplus") $affaire = new affaire_bdomplus($infos['id_affaire']);
		else $affaire = new affaire_cleodis($infos['id_affaire']);

		$affaire->majForecastProcess();

		////////////////Commande Ligne
		//Lignes reprise
		if($infos_ligne_repris){
			foreach($infos_ligne_repris as $key=>$item){
				$infos_ligne[]=$infos_ligne_repris[$key];
			}
		}

		//Lignes non visibles
		if($infos_ligne_non_visible){
			foreach($infos_ligne_non_visible as $key=>$item){
				$infos_ligne_non_visible[$key]["commande_ligne__dot__visible"]="non";
				$infos_ligne_non_visible[$key]["commande_ligne__dot__visible_pdf"]="non";
				$infos_ligne[]=$infos_ligne_non_visible[$key];
			}
		}

		//Lignes visibles
		if($infos_ligne){
			$infos_ligne=ATF::devis()->extJSUnescapeDot($infos_ligne,"commande_ligne");
			foreach($infos_ligne as $key=>$item){
				if($item["id_commande_ligne"]){
					$devis_ligne=ATF::devis_ligne()->select($item["id_commande_ligne"]);
					$item["id_affaire_provenance"]=$devis_ligne["id_affaire_provenance"];
					$item["serial"]=$devis_ligne["serial"];
					$item["neuf"]=$devis_ligne["neuf"];
					unset($item["id_commande_ligne"]);
				}
				$item["id_commande"]=$last_id;
				ATF::commande_ligne()->i($item);
			}
		}else{
			ATF::db($this->db)->rollback_transaction();
			throw new errorATF("Commande sans produits",877);
		}

		////////////////Devis
		$devis["etat"]="gagne";

        if (!$devis['first_date_accord']){ 	$devis['first_date_accord'] = date('Y-m-d'); }
        else{ $devis["date_accord"] = date('Y-m-d'); }
		ATF::devis()->u($devis);

		////////////////Affaire
		ATF::affaire()->u(array("id_affaire"=>$devis['id_affaire'],"etat"=>"commande"));

//*****************************************************************************
		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			/* MAIL */
			//Seulement si le profil le permet
			if($email){
				$path=array("A3"=>"contratA3","A4"=>"contratA4");
				ATF::affaire()->mailContact($email,$last_id,"commande",$path);
			}
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){
			ATF::affaire()->redirection("select",$infos["id_affaire"]);
		}
		return $this->cryptId($last_id);

	}

	/**
    * Permet de mettre a jour une date en ajax
    * @author Quentin JANON <qjanon@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* @param array &$s La session
	* @param array &$request Paramètres disponibles (clés étrangères)
	* @return bool
    */
	public function updateDate($infos,&$s,&$request){
		if (!$infos['id_commande']) return false;

		if ($infos['value'] == "undefined") $infos["value"] = "";
		switch ($infos['key']) {
			// Sécurité, n'exécuter une action que pour ces champs
			case "date_debut":
			case "date_evolution":
			case "retour_contrat":
			case "retour_prel":
			case "retour_pv":
			case "date_demande_resiliation":
			case "date_prevision_restitution":
			case "date_restitution_effective":

				//Il ne faut pas que la date début soit un 29 30 ou 31 car sinon cela pause problème lors de la création de l'échéancier
				if($infos['key']=="date_debut"){
					if(date("d",strtotime($infos['value']))=="29" || date("d",strtotime($infos['value']))=="30" || date("d",strtotime($infos['value']))=="31"){
						throw new errorATF("Un contrat ne peut pas avoir pour ".ATF::$usr->trans($infos['key'],$this->table)." une 29, 30, 31 (ici ".date("d",strtotime($infos['value'])).")",880);
					}
					ATF::devis()->u(array("id_devis"=> $this->select($infos['id_commande'] , "id_devis"), "date_accord"=>date("Y-m-d")));

					ATF::facture_fournisseur()->q->reset()->where("id_affaire", $this->select($infos['id_commande'] , "id_affaire"), "AND")
														  ->where("type", "achat");

					$id_societe = $this->select($infos["id_commande"] , "id_societe");
					if(ATF::societe()->select($id_societe, "relation") !== "client"){ ATF::societe()->u(array("id_societe"=> $id_societe, "relation"=>"client")); }

				}
				//Mode transactionel
				ATF::db($this->db)->begin_transaction();
				try {
					$cmdBefore = $this->select($infos['id_commande']);


					//Si on modifie la fin de contrat, on modifie en meme temps la date de restitution prévue
					if($infos['key'] === "date_evolution"){
						$d = array("id_commande"=>$infos['id_commande']
								   ,$infos['key']=>($infos['value']?date("Y-m-d",strtotime($infos['value'])):NULL)
								   );

					}else{
						$d = array("id_commande"=>$infos['id_commande']
								   ,$infos['key']=>($infos['value']?date("Y-m-d",strtotime($infos['value'])):NULL));
					}

					$this->u($d);
					$commande = $this->select($infos['id_commande']);

					$this->checkAndUpdateDates(array(
						"id_commande"=>$infos['id_commande']
						,"field"=>$infos['key']
						,"date"=>$d[$infos['key']]
					));

					if($infos['key'] === "date_debut" && $infos['value']){
						//Creation de la facture prorata si besoin
						$id_affaire = $this->select($infos['id_commande'] , "id_affaire");
						$affaire = ATF::affaire()->select($id_affaire);
						if($affaire["date_installation_reel"]){
							$data = array("date_installation_reel" => $affaire["date_installation_reel"],
										  "id_affaire" => $affaire["id_affaire"],
										  "date_debut_contrat" => $infos['value'],
										  "id_commande"=> $infos["id_commande"]
										);

							ATF::facture()->createFactureProrata($data);
						}

						$data = array(  "id_affaire" => $affaire["id_affaire"],
										"date_debut_contrat" => $infos['value'],
										"id_commande"=> $infos["id_commande"]
									 );

						//Creation de la premiere facture
						ATF::facture()->createPremiereFacture($data);

					}

					$cmd = $this->select($infos['id_commande']);
					if($infos['value']){
						if(ATF::$usr->get('id_user')){
							$client = ATF::societe()->select($cmd['id_societe']);
							$num_contrat = $cmd['ref'].($client["code_client"]?"-".$client["code_client"]:"");

							$notifies = array(ATF::societe()->select($cmd['id_societe'],'id_owner'));

							//On met Jennifer en notifié pour les MEP de contrat
							if($infos['key']=="date_debut"){
								if(ATF::$codename == "cleodis") $notifies[] = 106;
								if(ATF::$codename == "cleodisbe") $notifies[] = 107;
							}

							$suivi = array(
								"id_user"=>ATF::$usr->get('id_user')
								,"id_societe"=>$cmd['id_societe']
								,"id_affaire"=>$cmd['id_affaire']
								,"type_suivi"=>'Contrat'
								,"texte"=>"Affaire n°".ATF::affaire()->select($cmd['id_affaire'],'ref')." - Modification de la '".ATF::$usr->trans($infos['key'],"suivi")."', nouvelle valeur : ".$infos['value']
								,'public'=>'oui'
								,'suivi_contact'=>array(ATF::devis()->select($cmd['id_devis'],'id_contact'))
								,'suivi_societe'=>array(ATF::$usr->getID())
								,'suivi_notifie'=>$notifies
								,'champsComplementaire'=>$infos['key']
							);
							if(($infos['key'] == "date_prevision_restitution") || ($infos['key'] == "date_prevision_restitution")){	$suivi["type_suivi"] = "Restitution";	}

							ATF::suivi()->insert($suivi);
							ATF::$msg->addNotice(ATF::$usr->trans("suivi_plus_email_envoye_to_owner",$this->table));
						}
					}else{
						ATF::suivi()->q->reset()
									   ->addCondition("texte","%".ATF::affaire()->select($cmd['id_affaire'],'ref')."%","AND","cle",'LIKE')
									   ->addCondition("texte","%".ATF::$usr->trans($infos['key'],"commande")."%","AND","cle",'LIKE');
						$suivis=ATF::suivi()->sa();
						foreach($suivis as $key => $item){
							ATF::suivi()->d($item["id_suivi"]);
						}
					}
				} catch(errorATF $e) {
					//On rollback le tout
					ATF::db($this->db)->rollback_transaction();
					throw $e;
				}

				//On commit le tout
				ATF::db($this->db)->commit_transaction();

				ATF::$msg->addNotice(loc::mt(
					ATF::$usr->trans("dates_modifiee",$this->table)
					,array("date"=>ATF::$usr->trans($infos['key'],$this->table))
				));
				break;

			default:
				throw new errorATF("date_invalide",987);
		}

		if($infos["table"]!="commande"){
//			ATF::commande()->redirection("select_all",NULL,"commande.html");
//		}else{
			ATF::affaire()->redirection("select",$cmd["id_affaire"]);
		}
		return true;
	}

//	/**
//    * Permet de mettre a jour la date Resiliation en ajax
//    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
//	  * @param array $infos
//    */
//	public function updateDateResiliation($infos){
//		$commande = $this->select($infos['id_commande']);
//		//Il faut une date de de résiliation pour insérer une date de restitution
//		if($infos['value'] == 'undefined' && ($commande["date_restitution"] || $commande["date_restitution_effective"])){
//			throw new errorATF("Impossible de supprimer la date de résiliation si la date de restitution est renseignée",881);
//		}else{
//			return parent::updateDate($infos);
//		}
//	}
//
//	/**
//    * Permet de mettre a jour la date Resiliation en ajax
//    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
//	* @param array $infos
//    */
//	public function updateDateRestitution($infos){
//		$commande = $this->select($infos['id_commande']);
//		//Il faut une date de de résiliation pour insérer une date de restitution
//		if($infos['value'] != 'undefined' && !$commande["date_resiliation"]){
//			throw new errorATF("Il faut une date de resiliation pour pouvoir renseigner la date de restitution",882);
//		}elseif($infos['value'] == 'undefined' && $commande["date_restitution_effective"]){
//			throw new errorATF("Impossible de supprimer la date de résiliation si la restitution est effective",883);
//		}else{
//			return parent::updateDate($infos);
//		}
//	}
//
//	/**
//    * Permet de mettre a jour la date Resiliation en ajax
//    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
//	* @param array $infos
//    */
//	public function updateDateRestitution_effective($infos){
//		$commande = $this->select($infos['id_commande']);
//		parent::updateDate($infos);
//		$commande = new commande_cleodis($infos['id_commande']);
//		$this->checkEtat($commande);
//	}

	/**
    * Vérification si on peut modifier/supprimer une commande
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	public function checkUpdateAR($affaire){
		//On ne peux pas modifier les dates d'une commande qui est parente d'une autre commande
		if($affaire->get("id_fille")){
			$this->q->reset()->Where("id_affaire",$affaire->get("id_fille"))->setDimension("row");
			$commandeAR=$this->sa();
			if($commandeAR["date_debut"] || $commandeAR["date_evolution"]){
				throw new errorATF("On ne peut pas modifier/supprimer car l'affaire est Annulée et Remplacée, il faut d'abord supprimer les dates de l'AR (".ATF::affaire()->select($affaire->get("id_fille"),"ref").")",877);
			}
		}
	}

	/**
    * Vérification si on peut modifier/supprimer une commande
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	public function checkUpdateAVT($affaireEnfant){
		if($affaireEnfant["nature"]=="vente"){
			throw new errorATF("On ne peut pas modifier/supprimer car des produits de cette affaire sont vendus dans l'affaire (".$affaireEnfant["ref"].")",875);
		}elseif($affaireEnfant["nature"]=="avenant"){
			$this->q->reset()->Where("id_affaire",$affaireEnfant["id_affaire"])->setDimension("row");
			$commandeAvenant=$this->sa();
			//On ne peut pas modifier les dates d'une affaire parente tant que l'affaire avenant a une date_debut ou une date_fin (l'utilisateur doit d'abord supprimer les dates de l'avenant)
			if($commandeAvenant["date_debut"] || $commandeAvenant["date_evolution"]){
				throw new errorATF("On ne peut pas modifier/supprimer car l'affaire a un avenant, il faut d'abord supprimer les dates de l'avenant (".$affaireEnfant["ref"].")",876);
			}
		}
	}

	/**
    * Vérification des dates de la commande, et modification automatiques éventuelles
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param array $infos
	* 	infos[id_commande]
	* 	infos[field]		date_debut|date_evolution|retour_contrat|retour_prel|retour_pv|date_demande_resiliation|date_prevision_restituion|date_restitution_effective
	* 	infos[date]
	* @return bool
    */
	public function checkAndUpdateDates($infos){
		if(ATF::$codename == "cleodisbe") $commande = new commande_cleodisbe($infos['id_commande']);
		else $commande = new commande_cleodis($infos['id_commande']);

		$affaire = $commande->getAffaire();
		//On ne doit pas pouvoir modifier une commande Annulée et remplacée
		$this->checkUpdateAR($affaire);

		ATF::affaire()->q->reset()->addCondition("id_parent",$affaire->get("id_affaire"))->setDimension("row");
		$affaireParent=ATF::affaire()->sa();

		if(($infos["field"] !== "date_demande_resiliation") &&  ($infos["field"] !== "date_prevision_restitution") && ($infos["field"] !== "date_restitution_effective")){
			//On ne peux pas modifier les dates d'une commande qui est parente d'une autre commande
			if($affaireParent){
				$this->checkUpdateAVT($affaireParent);
			}
		}

		//Création du tableau des affaires parentes
		if ($ap = $affaire->getParentAR()) {
			// Parfois l'affaire a plusieurs parents car elle annule et remplace plusieurs autres affaires
			foreach ($ap as $a) {
				if(ATF::$codename == "cleodisbe") $affaires_parentes[] = new affaire_cleodisbe($a["id_affaire"]);
				else $affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);

			}
		} elseif ($affaire->get('id_parent')) {
			$affaire_parente = $affaire->getParentAvenant();
		}

		$affaireFillesAR=ATF::affaire()->getFillesAR($affaire->get("id_affaire"));

		$devis = $affaire->getDevis();
		switch ($infos["field"]) {
			case "date_debut":
				// Modification
				if ($infos["date"]) {
					// Si avenant loyer unique
					if ($affaire->isAvenant() && $devis->get("loyer_unique")==="oui") {
						$commande->set("date_evolution",$affaire_parente->getCommande()->get("date_evolution"));

						//l'affaire parente ne peut pas avoir une date début supérieur à la date début de l'affaire avenant
						if($affaire_parente->getCommande()->get("date_debut") > $infos["date"]){
							$commande->set("date_debut",$affaire_parente->getCommande()->get("date_debut"));
						//l'affaire parente ne peut pas avoir une date fin inférieur à la date début de l'affaire avenant
						}elseif($affaire_parente->getCommande()->get("date_evolution") < $infos["date"]){
							$commande->set("date_debut",$affaire_parente->getCommande()->get("date_evolution"));
						}

						ATF::prolongation()->q->reset()->addCondition("id_affaire",$affaire->get('id_affaire'));
						$prolongation=ATF::prolongation()->sa();
						if($prolongation){
							ATF::prolongation()->delete($prolongation[0]["id_prolongation"]);
						}
						// Créer une prolongation qui arrête le contrat (fréquence = « p », id_refinanceur = cléodis)
						$lastId=ATF::prolongation()->i(array(
							"id_affaire"=>$commande->get("id_affaire")
							,"ref"=>$commande->get("ref")
							,"date_debut"=>date("Y-m-d H:i:s",strtotime($commande->get("date_evolution")."+ 1 day"))
							,"date_fin"=>date("Y-m-d H:i:s",strtotime($commande->get("date_evolution")."+ 1 day"))
							,"id_societe"=>$commande->get("id_societe")
							,"id_refinanceur"=>4 // Cléodis
							,"date_arret"=>date("Y-m-d H:i:s",strtotime($commande->get("date_evolution")."+ 1 day"))
						));
					} else {
						// Sinon plusieurs loyers, alors on calcul la date d'évolution prévue à partir de la durée du devis et la date d'installation réelle
						$date_fin_calculee = $devis->getDateFinPrevue($infos["date"]);
						$commande->set("date_evolution",$date_fin_calculee);
					}

					$this->checkEtat($commande,$affaires_parentes);

					/* L'échéancier de facturation devient disponible */
					ATF::facturation()->insert_facturations($commande,$affaire,$affaires_parentes,$devis);

					if(ATF::$codename == "boulanger"){
						/*Il faut également généré l'echéancier de facturation fournisseur */
						ATF::facturation_fournisseur()->generate_echeancier($commande,$affaire,$affaires_parentes,$devis);
					}


					//Ce test doit se faire obligatoirement sous insert_facturations() car cette méthode met à jour les dates prolong
					if($commande->get("etat")=="prolongation"){
						if ($prolongation = $affaire->getProlongation($affaire->get('id_affaire'))) {
							//if (strtotime($prolongation->get("date_fin"))<strtotime(date("Y-m-d"))) {
								// Si l'on a atteint la date d'arrêt (définie dans prolongation) on passe l'état de la commande en « restitution »
								$commande->set("etat","restitution");
							//}
						}
					}

					if($commande->get("etat")=="restitution"){
						if ($prolongation = $affaire->getProlongation($affaire->get('id_affaire'))) {
							if ($prolongation->get("date_restitution_effective") !== "") {
								// Si on a une date de restitution,  on passe l'état de la commande en « arreter »
								$commande->set("etat","arreter");
							}
						}
					}

					if (!$affaire->get("date_installation_reel")) {
						// Si aucune date d'installation réelle, on la définie à la date de début de contrat
						$affaire->set("date_installation_reel",$commande->get("date_debut"));
					}

//					$affaire->majGarantieParc($affaire->get("date_garantie"));

				} else {
					// Suppression de la date de début de contrat
					$commande->set("date_evolution",NULL);

					// Si une affaire parente
					if ($affaires_parentes) {
						foreach ($affaires_parentes as $affaire_parente) {
							$commande_parente = $affaire_parente->getCommande();
							if ($commande_parente->estEnCours()) {
								// La commande parente a débuté
								$commande_parente->set("etat","mis_loyer");
							} elseif ($commande_parente->dateEvolutionDepassee()) {
								// La date prévue d'évolution de la commande parente est dépassée
								$commande_parente->set("etat","prolongation");
							} else {
								// La date prévue d'évolution de la commande parente n'est pas dépassée
								$commande_parente->set("etat","non_loyer");
							}

//							if ($affaire->get("etat") == "facture_refi") {
//								// L'affaire est en état facture_refi
//								$affaire_parente->set("etat","facture_refi");
//							} else {
//								$affaire_parente->set("etat","facture");
//							}
						}
					}

					$commande->set("etat","non_loyer");

					//Mise à jour des facturations
					ATF::facturation()->delete_special($commande->get("id_affaire"));

					if(ATF::$codename == "boulanger"){
						//Mise à jour des facturations fournisseur
						ATF::facturation_fournisseur()->delete_special($commande->get("id_affaire"));
					}

					//Mise à jour des prolongations (supprimer les dates)
					ATF::prolongation()->unsetDate($commande->get("id_affaire"));

				}
				break;
			case "retour_contrat":
				// Si aucune commande."date de prélèvement" ou "date de PV" on définie à "date de retour contrat"
				if (!$commande->get("retour_prel")) {
					$commande->set("retour_prel",$commande->get("retour_contrat"));
				}
				if (!$commande->get("retour_pv")) {
					$commande->set("retour_pv",$commande->get("retour_contrat"));
				}
				break;
			case "date_evolution":
				if (!$commande->isAR()) {
					// Si la commande n'a pas été annulée et remplacée
					if ($commande->dateEvolutionDepassee()) {
						// Si la date d'évolution a été dépassée
						$commande->set("etat","prolongation");
					} else {
						$commande->set("etat","mis_loyer");
					}
				}
// @todo A vérifier, normalement les 3 dates contrat, prel, et PV ne doivent pas modifier l'état du contrat !
				break;
			case "date_demande_resiliation":
				$this->checkEtat($commande);
			break;
			case "date_prevision_restitution":
				$affPar = $this->checkEtat($commande);

				//Si $commande est un avenant, on récupere l'affaire parente
				if($infos["date"] && $affPar){
					$data = $infos;
					$data["id_commande"] = $affPar["commande.id_commande"];
					$this->checkAndUpdateDates($data);
				}else{
					$reload = true;
				}

			break;
			case "date_restitution_effective":
				//if (!$commande->isAR()) {
				// Si la commande n'a pas été annulée et remplacée

				if($infos["date"] != NULL){
					$commande->set("date_arret",$infos["date"]);
					if($infos["date"] <= date("Y-m-d")){
						$commande->set("etat","arreter");
						$comm = ATF::commande()->select($infos["id_commande"]);

						$notifie = array(21);
						if(ATF::$usr->getID() !=21){
							$notifie[] = ATF::$usr->getID();
						}

						$suivi = array(
							"id_user"=>ATF::$usr->get('id_user')
							,"id_societe"=>$comm['id_societe']
							,"type_suivi"=>'Contrat'
							,"texte"=>"L'affaire ".$commande->get("ref")." est passée en arrêté suite à l'ajout de la date de restitution effective"
							,'public'=>'oui'
							,'suivi_societe'=>ATF::$usr->getID()
							,'suivi_notifie'=>$notifie
						);
						ATF::suivi()->insert($suivi);
					}else{
						$this->checkEtat($commande);
					}
				}else{
					//throw new errorATF("Il est impossible d'inserer une date de restitution effective nulle");
					$this->checkEtat($commande);
				}
				//}
			break;
		}
		ATF::parc()->updateExistenz($commande,$affaire,$affaire_parente,$affaires_parentes);
		//MAJ date garantie de l'affaire
		if(!$affaire->get("date_garantie") && $commande->get("date_evolution") && !$affaire_parente){
			if($devis->get("type_contrat")=="lrp"){
				//pour les devis LRP : la date fin garantie = date evolution - 3 mois
				$affaire->set("date_garantie",date("Y-m-d",strtotime($commande->get("date_evolution")."-3 month")));
			}else{
				$affaire->set("date_garantie",$commande->get("date_evolution"));
			}
		}
		$affaire->majForecastProcess();

		if($infos["field"] !== "date_prevision_restitution" || $reload){
			if($commande->get("id_affaire")){
				ATF::affaire()->redirection("select",$commande->get("id_affaire"));
			}
		}


	}

	/**
	* Met à jour l'état de la commande ainsi que l'état de la commnade des affaires parentes (dans la modif checkAndUpdateDate) ou des affaires filles (dans cleodisStatut)
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	*/
	function checkEtat($commande,$affaires_parentes=false,$affaireFillesAR=false){
		if(ATF::affaire()->select($commande->get("id_affaire"),"nature")=="vente"){
			ATF::facture()->q->reset()->addCondition("id_affaire",$commande->get("id_affaire"));
			if(ATF::facture()->sa()){
				$commande->set("etat","vente");
			}else{
				$commande->set("etat","non_loyer");
			}
		//Si la commande est une vente, si elle est AR ou si elle est terminée, il ne faut pas modifier son état
		}elseif($commande->get("etat")!="arreter" || $commande->get("etat")!="arreter_contentieux"){
			if($affaireFillesAR){
				ATF::commande()->q->reset()->addCondition("id_affaire",$affaireFillesAR["id_affaire"])->setDimension("row");
				$commandeFilleAR=ATF::commande()->sa();
			}
			// Si on est dans la période du contrat
			if ($commande->estEnCours()) {
				if($commandeFilleAR && ($commandeFilleAR["etat"]=="mis_loyer" || $commandeFilleAR["etat"]=="mis_loyer_contentieux" || $commandeFilleAR["etat"]=="prolongation" || $commandeFilleAR["etat"]=="prolongation_contentieux" || $commandeFilleAR["etat"]=="restitution" || $commandeFilleAR["etat"]=="restitution_contentieux" || $commandeFilleAR["etat"]=="AR" || $commandeFilleAR["etat"]=="arreter" || $commandeFilleAR["etat"]=="arreter_contentieux")){
					$commande->set("etat","AR");
				}else{
					//if($commande->dateEvolutionDepassee()){
						/*if(strpos( $commande->get("etat"), "contentieux")){
							$commande->set("etat","prolongation_contentieux");
						}else{
							$commande->set("etat","prolongation");
						}
					}else{*/
						if(strpos( $commande->get("etat"), "contentieux")){
							$commande->set("etat","mis_loyer_contentieux");
						}else{
							$commande->set("etat","mis_loyer");
						}
					//}
				}
				foreach ($affaires_parentes as $affaire_parente) {
					$affaire_parente->getCommande()->set("etat","AR");
				}

			// Sinon Si la période du contrat n'a pas commencé
			} elseif(!$commande->dateDebutDepassee()) {
				if($commandeFilleAR && ($commandeFilleAR["etat"]=="mis_loyer" || $commandeFilleAR["etat"]=="mis_loyer_contentieux" || $commandeFilleAR["etat"]=="prolongation" || $commandeFilleAR["etat"]=="prolongation_contentieux" || $commandeFilleAR["etat"]=="restitution" || $commandeFilleAR["etat"]=="restitution_contentieux" || $commandeFilleAR["etat"]=="AR" || $commandeFilleAR["etat"]=="arreter")){
					$commande->set("etat","AR");
				}else{
					$commande->set("etat","non_loyer");
				}
				foreach ($affaires_parentes as $affaire_parente) {
					$commande_parente = $affaire_parente->getCommande();
					// On met les commandes parentes en état Annule et Remplace
					if ($commande_parente->estEnCours()) {
						// Si le contrat de l'affaire parente est en cours
						$affaire_parente->getCommande()->set("etat","mis_loyer");
					} elseif(!$commande_parente->dateDebutDepassee()) {
						// Sinon si la date évolution de l'affaire parente n'est pas dépassée
						$affaire_parente->getCommande()->set("etat","non_loyer");
					} elseif($commande_parente->dateEvolutionDepassee()) {
						// Sinon si la date évolution de l'affaire parente est dépassée
						$affaire_parente->getCommande()->set("etat","prolongation");
					}
				}
			// Sinon Si la période du contrat est dépassé
			} elseif($commande->dateEvolutionDepassee()){
				if($commandeFilleAR && ($commandeFilleAR["etat"]=="mis_loyer" || $commandeFilleAR["etat"]=="mis_loyer_contentieux" || $commandeFilleAR["etat"]=="prolongation" || $commandeFilleAR["etat"]=="prolongation_contentieux" || $commandeFilleAR["etat"]=="restitution" || $commandeFilleAR["etat"]=="restitution_contentieux" || $commandeFilleAR["etat"]=="AR" || $commandeFilleAR["etat"]=="arreter" || $commandeFilleAR["etat"]=="arreter_contentieux")){
					$commande->set("etat","AR");
				}else{
					//Si il y a une date de restitution et qu'elle est dépassée OU que la date evolution est depassée et pas de date resti
					if($commande->dateRestitutionEffectiveDepassee()){
						if(strpos( $commande->get("etat"), "contentieux")){
							$commande->set("etat","arreter_contentieux");
						}else{
							$commande->set("etat","arreter");
						}

						$comm = ATF::commande()->select($commande->get("id_commande"));

						$notifie = array(21);
						if(ATF::$usr->getID() !=21){
							$notifie[] = ATF::$usr->getID();
						}
						$suivi = array(
							"id_user"=>ATF::$usr->get('id_user')
							,"id_societe"=>$comm['id_societe']
							,"type_suivi"=>'Contrat'
							,"texte"=>"L'affaire est passée en arrêté car la date de restitution effective est dépassée ou date Evolution dépassée sans date de restitution"
							,'public'=>'oui'
							,'suivi_societe'=>ATF::$usr->getID()
							,'suivi_notifie'=>$notifie
						);
						ATF::suivi()->insert($suivi);
					} elseif($commande->dateRestitutionPrevue()) {
						if(strpos( $commande->get("etat"), "contentieux")){
								$commande->set("etat","restitution_contentieux");
							}else{
								$commande->set("etat","restitution");
							}
							if(ATF::affaire()->select($commande->get("id_affaire"),"nature") == "avenant"){
								//Mettre en restitution l'affaire parente
								$id_aff_parent = ATF::affaire()->select($commande->get("id_affaire"),"id_parent");
								if($id_aff_parent){
									ATF::commande()->q->reset()->where("commande.id_affaire", $id_aff_parent);
									return ATF::commande()->select_row();
								}
							}
					} else{
						if(strpos( $commande->get("etat"), "contentieux")){
							$commande->set("etat","prolongation_contentieux");
						}else{
							$commande->set("etat","prolongation");
						}
					}
				}
				foreach ($affaires_parentes as $affaire_parente) {
					$affaire_parente->getCommande()->set("etat","AR");
				}
			}
		}
	}

	/**
	* Retourne l'objet affaire associé à la commande passée en paramètre, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return commande_cleodis
	*/
	function getAffaire(){
		$this->notSingleton();
		if(ATF::$codename == "cleodisbe") return new affaire_cleodisbe($this->infos['id_affaire']);
		if(ATF::$codename == "assets") return new affaire_assets($this->infos['id_affaire']);
		if(ATF::$codename == "bdomplus") return new affaire_bdomplus($this->infos['id_affaire']);
		return new affaire_cleodis($this->infos['id_affaire']);
	}

	public function getDateResti($infos){
		return $this->select($this->decryptId($infos["id_commande"]), "date_prevision_restitution");
	}

	/**
	* Impossible de supprimer une commande qui n'est pas en non_loyer
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_delete($id,$infos=false){
		if(ATF::$codename == "cleodisbe") $commande = new commande_cleodisbe($id);
		else $commande = new commande_cleodis($id);

		$affaire = $commande->getAffaire();

		//On ne doit pas pouvoir modifier une commande Annulée et remplacée
		$this->checkUpdateAR($affaire);

		ATF::affaire()->q->reset()->addCondition("id_parent",$affaire->get("id_affaire"))->setDimension("row");
		$affaireEnfant=ATF::affaire()->sa();

		//On ne peux pas modifier les dates d'une commande qui est parente d'une autre commande
		if($affaireEnfant){
			$this->checkUpdateAVT($affaireEnfant);
		}

		ATF::facture()->q->reset()->addCondition("id_commande",$commande->get("id_commande"))
								  ->addCondition("type_facture","ap","AND",false,"!=");

		if(ATF::facture()->sa()){
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il y a des factures dans cette affaire",879);
		}

		if($this->select($id,"etat")!="non_loyer"){
			throw new errorATF("Impossible de modifier/supprimer ce ".ATF::$usr->trans($this->table)." car il n'est plus en '".ATF::$usr->trans("non_loyer")."'",879);
		}

		// On ne peut pas supprimer un contrat qui a des matériels "actifs"
		ATF::parc()->q->reset()
			->where("id_affaire",$affaire->get("id_affaire"))
			->where("existence","actif")
			->setCountOnly();
		if (ATF::parc()->sa()>0) {
			throw new errorATF("On ne peut pas supprimer un contrat qui a des matériels 'actifs'",84513);
		}

		return true;
	}

	/**
	* Impossible de modifier une commande
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id
	* @return boolean
	*/
	public function can_update($id,$infos=false){
		return false;
	}

	/**
	* Surcharge de delete afin de supprimer les lignes de commande et modifier l'état de l'affaire et du devis
	* @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $infos le ou les identificateurs de l'élément que l'on désire inséré
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	*/
	public function delete($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL) {
		if (is_numeric($infos) || is_string($infos)) {
			$id=$this->decryptId($infos);
			$commande=$this->select($id);
//log::logger("delete1",'error.log');

			//Commande
			if($commande){
//*****************************Transaction********************************
//log::logger("delete2",'error.log');
				ATF::db($this->db)->begin_transaction();
				parent::delete($id,$s);

				//Affaire
				$affaire = array("id_affaire"=>$commande["id_affaire"],"etat"=>"devis");
				ATF::affaire()->u($affaire);

				//Devis
				ATF::devis()->q->reset()->addCondition("id_affaire",$commande["id_affaire"])->setDimension("row");
				$devis=ATF::devis()->sa();
				$devis_update = array("id_devis"=>$devis["id_devis"],"etat"=>"attente");
				ATF::devis()->u($devis_update);

				// Mise à jour du forecast
				if(ATF::$codename == "cleodisbe") $affaire = new affaire_cleodisbe($commande['id_affaire']);
				else $affaire = new affaire_cleodis($commande['id_affaire']);
				$affaire->majForecastProcess();

				//Suppression des facturations
				ATF::facturation()->delete_special($commande["id_affaire"]);


				ATF::tache()->q->reset()->where("id_affaire", $commande["id_affaire"])->where("type_tache","creation_contrat");
				foreach (ATF::tache()->select_all() as $key => $value) { ATF::tache()->d(array("id_tache"=>$value["id_tache"])); }

				ATF::db($this->db)->commit_transaction();
	//*****************************************************************************
//log::logger("redirection",'error.log');

				ATF::affaire()->redirection("select",$commande["id_affaire"]);

				return true;
			}
		} elseif (is_array($infos) && $infos) {

			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files,$cadre_refreshed);
			}
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
		ATF::$cr->block("top");
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

					if(ATF::$codename == "cleodisbe") $a = new affaire_cleodisbe($this->get('id_affaire'));
					elseif(ATF::$codename == "assets") $a = new affaire_assets($this->get('id_affaire'));
					elseif(ATF::$codename == "bdomplus") $a = new affaire_bdomplus($this->get('id_affaire'));
					else $a = new affaire_cleodis($this->get('id_affaire'));

					switch ($value) {
						case 'arreter':
						case 'arreter_contentieux' :
							if($value == "arreter") $a->set('etat','terminee');
							else $a->set('etat','terminee_contentieux');
						break;

						case 'non_loyer' :
						case 'non_loyer_contentieux' :
							$a->set('etat', 'commande');
						break;

						case 'vente' :
							$a->set('etat', 'facture');
						break;

						default:
							$a->set('etat', 'facture');
						break;
					}
				break;
			}
		}

		ATF::societe()->checkMauvaisPayeur($this->get("id_societe"));

	}

	/**
	* Prédicat qui retourne VRAI si le contrat est en cours, et non terminé, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire Affaire qui demande sa commande
	* @return commande_cleodis
	*/
	function estEnCours(){
		$this->notSingleton();
		return $this->get("date_debut") && $this->get("date_debut") <= date("Y-m-d") && $this->get("date_evolution") && $this->get("date_evolution") >= date("Y-m-d")  && $this->get("etat")!="arreter";
	}

	/**
	* Prédicat qui retourne VRAI si le contrat a débuté, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire Affaire qui demande sa commande
	* @return commande_cleodis
	*/
	function dateDebutDepassee(){
		$this->notSingleton();
		return $this->get("date_debut") && $this->get("date_debut") <= date("Y-m-d");
	}

	/**
	* Prédicat, retourne VRAI si la date d'évolution a été dépassée, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_affaire Affaire qui demande sa commande
	* @return commande_cleodis
	*/
	function dateEvolutionDepassee(){
		$this->notSingleton();
		return $this->get("date_evolution") && $this->get("date_evolution") < date("Y-m-d");
	}


	/**
	* Prédicat, retourne VRAI si il y a une date de demande de resiliation, méthode d'objet et non de singleton
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_affaire Affaire qui demande sa commande
	* @return commande_cleodis
	*/
	function dateRestitutionEffectiveDepassee(){
		$this->notSingleton();
		if($this->get("date_restitution_effective")){
			return $this->get("date_restitution_effective") < date("Y-m-d");
		}else{
			return false;
		}
	}

	/**
	* Prédicat, retourne VRAI si il y a une date de demande de restitution, méthode d'objet et non de singleton
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int $id_affaire Affaire qui demande sa commande
	* @return commande_cleodis
	*/
	function dateRestitutionPrevue(){
		$this->notSingleton();
		return $this->get("date_prevision_restitution") && $this->get("date_prevision_restitution") < date("Y-m-d");
	}

	/**
	* Prédicat qui retourne VRAI si le contrat a été annulé et remplacé, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return commande_cleodis
	*/
	function isAR(){
		$this->notSingleton();
		return $this->get("etat") === "AR";
	}

	/**
	* Prédicat qui retourne VRAI si le contrat est signé, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @return commande_cleodis
	*/
	function estSigne(){
		$this->notSingleton();
		return $this->get("retour_contrat") && $this->get("retour_contrat") <= date("Y-m-d");
	}

	/**
    * Avoir toujours les dates, quelle que soit la vue utilisateur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$this->q
			->addField("commande.id_affaire")
			->addField("societe.code_client","code_client")
			->addField("commande.date_debut")
			->addField("commande.date_evolution")
			->addField("commande.retour_contrat")
			->addField("commande.retour_prel")
			->addField("commande.retour_pv")
			->addField("commande.date_demande_resiliation")
			->addField("commande.date_prevision_restitution")
			->addField("commande.date_restitution_effective")
			->from("commande","id_societe","societe","id_societe")
			->from("commande","id_affaire","affaire","id_affaire");
		$return = parent::select_all($order_by,$asc,$page,$count);

		foreach ($return['data'] as $k=>$i) {
			$affaire = ATF::affaire()->select($i['commande.id_affaire_fk']);

			if (!$affaire) continue;

			$return['data'][$k]["langue"] = $affaire["langue"];

			//Check si c'est une vente
			if ($affaire['nature']=="vente") {
				$return['data'][$k]['vente'] = true;
			}else{
				$return['data'][$k]['vente'] = false;
			}
			//Check affichage de création de prolongation
			if ($i['commande.date_evolution'] && !ATF::prolongation()->existProlongation($i['commande.id_affaire_fk']) && $affaire['nature']!="vente") {
				$return['data'][$k]['prolongationAllow'] = true;
			} else {
				$return['data'][$k]['prolongationAllow'] = false;
			}

			//Check l'existence d'un comité accepte
			if(ATF::affaire()->comiteAccepte($i["commande.id_affaire_fk"])){
				$return['data'][$k]['allowBDCCreate'] = true;

				//Check l'existence de création de BDC
				if (ATF::bon_de_commande()->bdcByAffaire($i['commande.id_commande'])) {
					$return['data'][$k]['bdcExist'] = true;
				} else {
					$return['data'][$k]['bdcExist'] = false;
				}

			}else{
				$return['data'][$k]['allowBDCCreate'] = false;
			}



			//Check l'existence de création de demande refi
			if (ATF::demande_refi()->existDemandeRefi($i["commande.id_affaire_fk"], false) || $affaire['nature']=="vente") {
				$return['data'][$k]['demandeRefiExist'] = true;
			} else {
				$return['data'][$k]['demandeRefiExist'] = false;
			}
			//Check affichage de création de facture
			if(ATF::$codename == "bdomplus"){
				if (($i["commande.date_debut"] || $affaire['nature']=="vente") && $return['data'][$k]['bdcExist']) {
					$return['data'][$k]['factureAllow'] = true;
				} else {
					$return['data'][$k]['factureAllow'] = false;
				}
			}else{
				if (($i["commande.date_debut"] || $affaire['nature']=="vente") && $return['data'][$k]['bdcExist'] && $return['data'][$k]['demandeRefiExist']) {
					$return['data'][$k]['factureAllow'] = true;
				} else {
					$return['data'][$k]['factureAllow'] = false;
				}
			}

			$return['data'][$k]['id_affaireCrypt'] = ATF::affaire()->cryptId($i['commande.id_affaire_fk']);

            // check des fichiers courriers types
            if (file_exists($this->filepath($i['commande.id_commande'],"envoiContratEtBilan"))) {
                $return['data'][$k]["envoiContratEtBilanExists"] = true;
            }
            if (file_exists($this->filepath($i['commande.id_commande'],"envoiContratSsBilan"))) {
                $return['data'][$k]["envoiContratSsBilanExists"] = true;
            }
            if (file_exists($this->filepath($i['commande.id_commande'],"envoiAvenant"))) {
                $return['data'][$k]["envoiAvenantExists"] = true;
            }
            if (file_exists($this->filepath($i['commande.id_commande'],"contratTransfert"))) {
                $return['data'][$k]["contratTransfertExists"] = true;
            }
            if (file_exists($this->filepath($i['commande.id_commande'],"ctSigne"))) {
                $return['data'][$k]["ctSigneExists"] = true;
            }
            if (file_exists($this->filepath($i['commande.id_commande'],"CourrierRestitution"))) {
                $return['data'][$k]["CourrierRestitutionExists"] = true;
            }

            if (file_exists($this->filepath($i['commande.id_commande'],"lettreSGEF"))) {
                $return['data'][$k]["ctSGEFExists"] = true;
            }

             if (file_exists($this->filepath($i['commande.id_commande'],"lettreBelfius"))) {
                $return['data'][$k]["ctlettreBelfiusExists"] = true;
            }

            if (file_exists($this->filepath($i['commande.id_commande'],"envoiCourrierClassique"))) {
                $return['data'][$k]["envoiCourrierClassiqueExists"] = true;
            }
		}

		return $return;
	}

	/**
    * Récupére les lignes d'une commande pour un id_fournisseur
    * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
	* 	$infos[id_commande]
	* 	$infos[id_fournisseur]
    */
	function getCommande_ligne(&$infos){
		$id_commande=$this->decryptId($infos["id_commande"]);
		$id_fournisseur=ATF::societe()->decryptId($infos["id_fournisseur"]);
		if ($id_commande && $id_fournisseur) {
			$this->q->reset()->addCondition("id_commande",$id_commande);
			if($commandes=$this->sa()){
			foreach($commandes as $key=>$item){
					//Ligne de la commande pour le fournisseur il ne faut pas que ces lignes soient présentes dans un autre bon de commande
					ATF::commande_ligne()->q->reset()->addOrder("commande_ligne.id_commande_ligne","asc")
													 ->where("id_commande",$item["id_commande"])
													 //->whereIsNull("bon_de_commande_ligne.id_commande_ligne")
													 ->where("id_fournisseur",$id_fournisseur);
					$commande_ligne=ATF::commande_ligne()->sa();


					//On recupere les lignes déja commandées
					ATF::bon_de_commande_ligne()->q->reset()
													 ->from("bon_de_commande_ligne","id_bon_de_commande","bon_de_commande","id_bon_de_commande")
													 ->where("bon_de_commande.id_commande",$item["id_commande"])
													 //->whereIsNull("bon_de_commande_ligne.id_commande_ligne")
													 ->where("bon_de_commande.id_fournisseur",$id_fournisseur);

					foreach ($commande_ligne as $kl => $vl) {
						ATF::bon_de_commande_ligne()->q->where("bon_de_commande_ligne.id_commande_ligne", $vl["id_commande_ligne"], "OR", "commande");
					}
					ATF::bon_de_commande_ligne()->q->unsetToString();
					$lignes_bdc=ATF::bon_de_commande_ligne()->sa();

					$temp = $lignes_bdc;
					$lignes_bdc = array();
					foreach ($temp as $kt => $vt) {
						$lignes_bdc[$vt["id_commande_ligne"]] += $vt["quantite"];
					}


					if($commande_ligne){
						$id_commande=$this->cryptId($item["id_commande_fk"]);
						unset($ligne_commande);
						$cle=0;

						foreach($commande_ligne as $k=>$i){
							if(!$lignes_bdc[$i["id_commande_ligne"]]){
								if($i["quantite"] > 1){
									$n = 1;
									for($n=1; $n<=$i["quantite"];$n++){
										$cle++;
										$ligne_commande[]=array(
													 "text"=>$i["produit"]." ".$i["ref"]." (1)"
													,"id_commande_ligne"=>$i["id_commande_ligne"]
													,"id"=>$cle
													,"leaf"=>true
													,"prix"=>$i["prix_achat"]
													,"quantite"=>1
													,"icon"=>ATF::$staticserver."images/blank.gif"
													,"checked"=>false
											);
									}
								}else{
									$cle++;
									$ligne_commande[]=array(
													 "text"=>$i["produit"]." ".$i["ref"]." (".$i["quantite"].")"
													,"id_commande_ligne"=>$i["id_commande_ligne"]
													,"id"=>$cle
													,"leaf"=>true
													,"prix"=>$i["prix_achat"]
													,"quantite"=>$i["quantite"]
													,"icon"=>ATF::$staticserver."images/blank.gif"
													,"checked"=>false
											);
								}
							}else{
								$quantite_restant= $i["quantite"] - $lignes_bdc[$i["id_commande_ligne"]];
								if($quantite_restant > 0 ){
									if($quantite_restant > 1){
										$n = 1;
										for($n=1; $n<=$quantite_restant;$n++){
											$cle++;
											$ligne_commande[]=array(
														 "text"=>$i["produit"]." ".$i["ref"]." (1)"
														,"id_commande_ligne"=>$i["id_commande_ligne"]
														,"id"=>$cle
														,"leaf"=>true
														,"prix"=>$i["prix_achat"]
														,"quantite"=>1
														,"icon"=>ATF::$staticserver."images/blank.gif"
														,"checked"=>false
												);
										}
									}else{
										$cle++;
										$ligne_commande[]=array(
														 "text"=>$i["produit"]." ".$i["ref"]." (1)"
														,"id_commande_ligne"=>$i["id_commande_ligne"]
														,"id"=>$cle
														,"leaf"=>true
														,"prix"=>$i["prix_achat"]
														,"quantite"=>1
														,"icon"=>ATF::$staticserver."images/blank.gif"
														,"checked"=>false
												);
									}
								}
							}
						}




						/*foreach($commande_ligne as $k=>$i){
							$ligne_commande[]=array(
									"text"=>$i["produit"]." ".$i["ref"]." (".$i["quantite"].")"
									,"id"=>$i["id_commande_ligne"]
									,"leaf"=>true
									,"prix"=>$i["prix_achat"]
									,"quantite"=>$i["quantite"]
									,"icon"=>ATF::$staticserver."images/blank.gif"
									,"checked"=>false
							);

							/*for($n=1;$n<=$i["quantite"]; $n++){
								$ligne_commande[]=array(
										"text"=>$i["produit"]." ".$i["ref"]." (1)"
										,"id"=>$i["id_commande_ligne"]
										,"leaf"=>true
										,"prix"=>$i["prix_achat"]
										,"quantite"=>1
										,"icon"=>ATF::$staticserver."images/blank.gif"
										,"checked"=>false
								);
							}*/

						//}

						if ($ligne_commande) {
							$commande[]=array(
								"text"=>$item["ref"]." ".$item["commande"]
								,"id"=>$item["id_commande_fk"]
								,"leaf"=>false
								,"href"=>"javascript:window.open('commande-select-".$id_commande.".html');"
								,"cls"=>"folder"
								,"expanded"=>true
								,"adapter"=>NULL
								,"children"=>$ligne_commande
								,"checked"=>false
							);
						}
					}
				}
			}
			$infos['display'] = true;

			return json_encode($commande);
		}else{
			return false;
		}
	}

	/**
	* Retourne les lignes d'un type, méthode d'objet et non de singleton
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $type visible|invisible|reprise
	* @return array
	*/
	function getLignes($type){
		$this->notSingleton();
		ATF::commande_ligne()->q->reset()
			->addField("commande_ligne.ref")->addField("commande_ligne.id_fournisseur")->addField("commande_ligne.quantite")->addField("commande_ligne.prix_achat")
			->where("id_commande",$this->get("id_commande"));
		switch ($type) {
			case "visible":
				ATF::commande_ligne()->q->where("visible","oui")->whereIsNull("id_affaire_provenance");
				break;
			case "invisible":
				ATF::commande_ligne()->q->where("visible","non")->whereIsNull("id_affaire_provenance");
				break;
			case "reprise":
				ATF::commande_ligne()->q->whereIsNotNull("id_affaire_provenance");
				break;
		}
		return util::removeTableInKeys(ATF::commande_ligne()->select_all()); // On préfixe pour avoir la jointure auto des clés étrangères, mais les clés font chier ExtJS en retour
	}

	/**
    * Retourne la valeur du texte d'email, appelé en Ajax
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return string texte du mail
    */
 	public function majMail($id_societe){
		return nl2br("Bonjour,\n\nCi-joint la commande pour la société ".ATF::societe()->nom($id_societe).".\nCommande éditée le ".date("d/m/Y").".\n");
	}

	/**
    * Retourne mise à jour du statut
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
    */
	public function cleodisStatut(){
		$this->q->setCount()->addOrder("id_commande","DESC");
		$commandeSa=$this->sa();
		$i=1;
		ATF::db($this->db)->begin_transaction();
		foreach($commandeSa["data"] as $key=>$item){
			unset($affaires_parentes);

//			log::logger($i." \ ".$commandeSa["count"],'cleodis_statut.log');
			if(ATF::$codename == "cleodisbe") $commande = new commande_cleodisbe($item['id_commande']);
			else $commande = new commande_cleodis($item['id_commande']);

			$affaire = $commande->getAffaire();

			//Création du tableau des affaires parentes
			if ($ap = $affaire->getParentAR()) {
				// Parfois l'affaire a plusieurs parents car elle annule et remplace plusieurs autres affaires
				foreach ($ap as $a) {
					if(ATF::$codename == "cleodisbe") $affaires_parentes[] = new affaire_cleodisbe($a["id_affaire"]);
					else $affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);

				}
			} elseif ($affaire->get('id_parent')) {
				$affaire_parente = $affaire->getParentAvenant();
			}

			$affaireFillesAR=ATF::affaire()->getFillesAR($commande->get("id_affaire"));

			$etat=$commande->get("etat");

			$this->checkEtat($commande,false,$affaireFillesAR);
			$etat_modifie=$commande->get("etat");
			if($etat!=$etat_modifie){
				log::logger($commande->get("ref")."         ".$etat."!=".$etat_modifie,'cleodis_statut.log');
			}
			ATF::parc()->updateExistenz($commande,$affaire,$affaire_parente,$affaires_parentes);
			$i++;
		}
		ATF::db($this->db)->commit_transaction();
	}

	/**
    * Met à jour létat de la comande en 'arreter'
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return string texte du mail
    */
	public function stopCommande($infos){
		if(ATF::$codename == "cleodisbe") $commande = new commande_cleodisbe($infos['id_commande']);
		elseif(ATF::$codename == "assets") $commande = new commande_assets($infos['id_commande']);
		elseif(ATF::$codename == "bdomplus") $commande = new commande_bdomplus($infos['id_commande']);
		else $commande = new commande_cleodis($infos['id_commande']);

		if ($commande) {

			$arret = "arreter";

			if(strpos( $commande->get("etat"), "contentieux")){
				$arret = "arreter_contentieux";
			}

			$commande->set('etat',$arret);
			$commande->set('date_arret',date("Y-m-d"));
			$comm = ATF::commande()->select($infos['id_commande']);
			$affaire = $commande->getAffaire();

			$notifie = array(21);
			if(ATF::$usr->getID() !=21){
				$notifie[] = ATF::$usr->getID();
			}


			$suivi = array(	"id_user"=>ATF::$usr->get('id_user')
							,"id_societe"=>$comm['id_societe']
							,"type_suivi"=>'Contrat'
							,"texte"=>"L'affaire ".$affaire->get("ref")." est passée en ".$arret
							,'public'=>'oui'
							,'suivi_societe'=>ATF::$usr->getID()
							,'suivi_notifie'=>$notifie
						);
			ATF::suivi()->insert($suivi);



			//Création du tableau des affaires parentes
			if ($ap = $affaire->getParentAR()) {
				// Parfois l'affaire a plusieurs parents car elle annule et remplace plusieurs autres affaires
				foreach ($ap as $a) {
					if(ATF::$codename == "cleodisbe") $affaires_parentes[] = new affaire_cleodisbe($a["id_affaire"]);
					elseif(ATF::$codename == "assets") $affaires_parentes[] = new affaire_assets($a["id_affaire"]);
					elseif(ATF::$codename == "bdomplus") $affaires_parentes[] = new affaire_bdomplus($a["id_affaire"]);
					else $affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);
				}
			} elseif ($affaire->get('id_parent')) {
				$affaire_parente = $affaire->getParentAvenant();
			}

			ATF::parc()->updateExistenz($commande,$affaire,$affaire_parente,$affaires_parentes);
			ATF::affaire()->redirection("select",$affaire->get("id_affaire"));
		}
	}

	/**
    * Met à jour létat de la comande en 'arreter'
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param int $id_societe
	* @return string texte du mail
    */
	public function reactiveCommande($infos){
		if(ATF::$codename == "cleodisbe") $commande = new commande_cleodisbe($infos['id_commande']);
		elseif(ATF::$codename == "assets") $commande = new commande_assets($infos['id_commande']);
		elseif(ATF::$codename == "bdomplus") $commande = new commande_bdomplus($infos['id_commande']);
		else $commande = new commande_cleodis($infos['id_commande']);

		if ($commande) {
			if(strpos( $commande->get("etat"), "contentieux")){
				$commande->set('etat','non_loyer_contentieux');
			}else{
				$commande->set('etat','non_loyer');
			}

			$commande->set('date_arret',NULL);
			$affaire = $commande->getAffaire();

			$comm = ATF::commande()->select($infos['id_commande']);
			$suivi = array(	"id_user"=>ATF::$usr->get('id_user')
							,"id_societe"=>$comm['id_societe']
							,"type_suivi"=>'Contrat'
							,"texte"=>"L'affaire ".$affaire->get("ref")." a été réactivée "
							,'public'=>'oui'
							,'suivi_societe'=>array(0=>ATF::$usr->getID())
						);
			ATF::suivi()->insert($suivi);
			if ($ap = $affaire->getParentAR()) {
				// Parfois l'affaire a plusieurs parents car elle annule et remplace plusieurs autres affaires
				foreach ($ap as $a) {
					if(ATF::$codename == "cleodisbe") $affaires_parentes[] = new affaire_cleodisbe($a["id_affaire"]);
					elseif(ATF::$codename == "assets") $affaires_parentes[] = new affaire_assets($a["id_affaire"]);
					elseif(ATF::$codename == "bdomplus") $affaires_parentes[] = new affaire_bdomplus($a["id_affaire"]);
					else $affaires_parentes[] = new affaire_cleodis($a["id_affaire"]);
				}
			} elseif ($affaire->get('id_parent')) {
				$affaire_parente = $affaire->getParentAvenant();
			}

			$affaireFillesAR=ATF::affaire()->getFillesAR($affaire->get("id_affaire"));

			$this->checkEtat($commande,$affaires_parentes,$affaireFillesAR);
			ATF::parc()->updateExistenz($commande,$affaire,$affaire_parente,$affaires_parentes);
			ATF::affaire()->redirection("select",$affaire->get("id_affaire"));

		}
	}


    /**
    * Génère un courrier type pour une commande
    * @author Quentin JANON <qjanon@absystech.fr>
    * @param array $infos
    * @return string html
    */
    public function generateCourrierType($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
        if (!$infos['id_commande'] || !$infos['pdf']) return false;;
        $commande = $this->select($infos['id_commande']);

        if(ATF::affaire()->select($commande["id_affaire"], "type_affaire") === "NL"){
        	$data = ATF::pdf()->generic($infos['pdf']."NL",$infos['id_commande'],true,$infos,$infos["preview"]?true:false);
        }else{
        	$data = ATF::pdf()->generic($infos['pdf'],$infos['id_commande'],true,$infos,$infos["preview"]?true:false);
        }


        $this->store($s,$infos['id_commande'],$infos['pdf'],$data,$infos["preview"]?true:false);

        ATF::$json->add("fileToPrevisu",$infos['pdf']);

        return $infos['id_commande'];
    }


    public function initStyle(){

		$style_titre1 = new excel_style();
		$style_titre1->setWrap()->alignement('center')->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1",$style_titre1->getStyle());
		/*-------------------------------------------*/
		$style_titre1_right = new excel_style();
		$style_titre1_right->setWrap()->alignement("center","right")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_right",$style_titre1_right->getStyle());
		/*-------------------------------------------*/
		$style_titre1_left = new excel_style();
		$style_titre1_left->setWrap()->alignement("center", "left")->setSize(13)->setBorder("thin")->setBold();
		$this->setStyle("titre1_left",$style_titre1_left->getStyle());
		/*-------------------------------------------*/
		$style_titre2 = new excel_style();
		$style_titre2->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("titre2",$style_titre2->getStyle());
		/*-------------------------------------------*/
		$style_titre2_right = new excel_style();
		$style_titre2_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("titre2_right",$style_titre2_right->getStyle());
		/*-------------------------------------------*/
		$style_centre = new excel_style();
		$style_centre->alignement();
		$this->setStyle("centre",$style_centre->getStyle());
		/*-------------------------------------------*/
		$style_cel_c = new excel_style();
		$style_cel_c->setWrap()->alignement('center')->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel",$style_cel_c->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_right = new excel_style();
		$style_border_cel_right->setWrap()->alignement("center","right")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_right",$style_border_cel_right->getStyle());
		/*-------------------------------------------*/
		$style_border_cel_left = new excel_style();
		$style_border_cel_left->setWrap()->alignement("center","left")->setSize(11)->setBorder("thin");
		$this->setStyle("border_cel_left",$style_border_cel_left->getStyle());
		/*-------------------------------------------*/
		$style_cel_right = new excel_style();
		$style_cel_right->setWrap()->alignement("center","right")->setSize(11);
		$this->setStyle("cel_right",$style_cel_right->getStyle());
	}

	public function setStyle($nom,$objet){
		$this->style[$nom]=$objet;
	}

	public function getStyle($nom){
		return $this->style[$nom];
	}


    /** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_loyer_assurance($infos,$testUnitaire="false",$reset="true"){

	 	if($testUnitaire == "true"){
	 		$donnees = $infos;
		}else{
			if($reset == "true")	$this->q->reset();

         	if($infos['onglet']) $this->setQuerier(ATF::_s("pager")->create($infos['onglet']));

			$this->q->from("commande","id_affaire","loyer","id_affaire")
					->addAllFields("commande")
					->addAllFields("loyer")
					->where('commande.etat',"AR","AND","sous_req","!=")
					->where('commande.etat',"arreter","AND","sous_req","!=")
					->where('commande.etat',"vente","AND","sous_req","!=")
					->setLimit(-1)->unsetCount();
			$donnees = $this->select_all();
		}


		$onglet = str_replace("gsa_commande_", "", $infos["onglet"]);
		$onglet = str_replace("commande_", "", $onglet);
		if(is_numeric($onglet)){ $onglet = ATF::filtre_optima()->select($onglet , "filtre_optima"); }


		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle($onglet);
		$sheets=array("auto"=>$worksheet_auto);
		$this->initStyle();
		if($donnees){
			$this->ajoutTitreExport_loyer_assurance($sheets,$donnees);
			$this->ajoutDonneesExport_loyer_assurance($sheets,$donnees);
		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_loyer_assurance.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();

    }


    /** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
     public function ajoutTitreExport_loyer_assurance(&$sheets, $donnees){
     	$row_data = array();
     	//A =65 Z=90
     	$lettre1 = 65;
		$lettre2 = 0;


		unset($donnees[0]["commande.prix_achat"],$donnees[0]["commande.id_user"], $donnees[0]["commande.date"] );


		$donnees[0]["loyer.Total"] = "";

     	foreach ($donnees[0] as $key => $value) {
     		$data = array();
     		$data = explode(".", $key);
     		if( !strpos($data[1],"_fk") ){


     			if($lettre1 <= 90 && $lettre2 == 0){
     				$char = chr($lettre1);
     				$lettre1++;
     			}else{
     				if($lettre2 == 0){
     					$lettre1 =  $lettre2 = 65;
     					$char = chr($lettre1).chr($lettre2);
     				}elseif($lettre2 == 90){
						$lettre1++;
						$lettre2 = 65;
						$char = chr($lettre1).chr($lettre2);
					}else{
						$lettre2++;
						$char = chr($lettre1).chr($lettre2);
					}
     			}
			    $row_data[$char] = array(ATF::$usr->trans($data[1],$data[0]),20);
     		}
     	}

        foreach($sheets as $nom=>$onglet){
             foreach($row_data as $col=>$titre){
				  $sheets[$nom]->write($col.'1',$titre[0],$this->getStyle("titre1"));
				  $sheets[$nom]->sheet->getColumnDimension($col)->setWidth($titre[1]);
            }
        }
    }


	/** Mise en place du contenu
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @param array $sheets : contient les 30 onglets
    * @param array $infos : contient tous les enregistrements
    */
    public function ajoutDonneesExport_loyer_assurance(&$sheets,$infos){
        $row_auto=1;

		foreach ($infos as $key => $value) {

			unset($value["commande.prix_achat"],$value["commande.id_user"], $value["commande.date"]);

			$value["loyer.total"] = ($value["loyer.loyer"]+$value["loyer.assurance"]+$value["loyer.frais_gestion"])*$value["loyer.duree"];

			$row_data = array();
	     	//A =65 Z=90
	     	$lettre1 = 65;
			$lettre2 = 0;

			foreach ($value as $k => $v) {
	     		$data = array();
	     		$data = explode(".", $k);
	     		if( !strpos($data[1],"_fk") ){
	     			if($lettre1 <= 90 && $lettre2 == 0){
	     				$char = chr($lettre1);
	     				$lettre1++;
	     			}else{
	     				if($lettre2 == 0){
	     					$lettre1 =  $lettre2 = 65;
	     					$char = chr($lettre1).chr($lettre2);
	     				}elseif($lettre2 == 90){
							$lettre1++;
							$lettre2 = 65;
							$char = chr($lettre1).chr($lettre2);
						}else{
							$lettre2++;
							$char = chr($lettre1).chr($lettre2);
						}
	     			}
				    $row_data[$char] = array($v , "border_cel_left");
	     		}
	     	}

	     	if($row_data){
				$row_auto++;
				foreach($row_data as $col=>$valeur){
					$sheets['auto']->write($col.$row_auto, $valeur[0], $this->getStyle($valeur[1]));
				}
			}


		}
	}




	 /** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	 public function export_contrat_refinanceur_loyer($infos,$testUnitaire="false",$reset="true"){

	 	if($testUnitaire == "true"){
	 		$donnees = $infos;
		}else{
			if($reset == "true")	$this->q->reset();

         	$this->setQuerier(ATF::_s("pager")->create($infos['onglet']));

			$this->q->from("commande","id_affaire","loyer","id_affaire")
					->addAllFields("commande")
					->addAllFields("loyer")
					->setLimit(-1)->unsetCount();
			$donnees = $this->select_all();
		}


		$onglet = str_replace("gsa_commande_", "", $infos["onglet"]);
		$onglet = str_replace("commande_", "", $onglet);
		if(is_numeric($onglet)){ $onglet = ATF::filtre_optima()->select($onglet , "filtre_optima"); }


        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		//premier onglet
		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);
		$worksheet_auto->sheet->setTitle($onglet);
		$sheets=array("auto"=>$worksheet_auto);
		$this->initStyle();
		if($donnees){
			//mise en place des titres
			$this->ajoutTitreExport_contrat_refinanceur_loyer($sheets,$donnees);
			//ajout des donnÃ©es
			$this->ajoutDonneesExport_contrat_refinanceur_loyer($sheets,$donnees);
		}

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_contrat_refinanceur_loyer.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
    }


    /** Mise en place des titres
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     */
    public function ajoutTitreExport_contrat_refinanceur_loyer(&$sheets, $donnees){
    	$row_auto=0;

    	$donnees[0]["loyer.Total"] = "";
    	$donnees[0]["commande.Refincanceur"] = "";

    	$donnees[0]["comite.Comite"] = "";
    	$donnees[0]["comite.decisionComite"] = "";
    	$donnees[0]["comite.validite_accord"] = "";
    	$donnees[0]["comite.commentaire"] = "";
    	$donnees[0]["comite.observations"] = "";


		if(isset($donnees[0]["commande.ref"])){
			unset($donnees[0]["commande.ref"],
				  $donnees[0]["commande.date_debut"],
				  $donnees[0]["commande.mise_en_place"],
				  $donnees[0]["commande.id_devis"],
				  $donnees[0]["commande.tva"],
				  $donnees[0]["commande.clause_logicielle"],
				  $donnees[0]["commande.type"],
				  $donnees[0]["commande.date_arret"],
				  $donnees[0]["commande.date_evolution"],
				  $donnees[0]["commande.date_restitution_effective"],
				  $donnees[0]["commande.date_prevision_restitution"],
				  $donnees[0]["commande.date_demande_resiliation"],
				  $donnees[0]["commande.prix_achat"],
				  $donnees[0]["commande.id_user"],
				  $donnees[0]["commande.date"],
				  $donnees[0]["affaire.date_installation_reel"]
				  );
		}

		$row_data = array();
     	//A =65 Z=90
     	$lettre1 = 65;
		$lettre2 = 0;
		foreach ($donnees[0] as $key => $value) {

     		$data = array();
     		$data = explode(".", $key);
     		if( !strpos($data[1],"_fk") && !strpos($key , "loyer.id_") ){

     			if($lettre1 <= 90 && $lettre2 == 0){
     				$char = chr($lettre1);
     				$lettre1++;
     			}else{
     				if($lettre2 == 0){
     					$lettre1 =  $lettre2 = 65;
     					$char = chr($lettre1).chr($lettre2);
     				}elseif($lettre2 == 90){
						$lettre1++;
						$lettre2 = 65;
						$char = chr($lettre1).chr($lettre2);
					}else{
						$lettre2++;
						$char = chr($lettre1).chr($lettre2);
					}
     			}
			    $row_data[$char] = array(ATF::$usr->trans($data[1],$data[0]),20);
     		}
     	}


     	if($row_data){
			$row_auto++;
			foreach($row_data as $col=>$valeur){
				$sheets['auto']->write($col.$row_auto, $valeur[0], $this->getStyle("titre1"));
				$sheets['auto']->sheet->getColumnDimension($col)->setWidth(20);
			}
		}

    }



    /** Mise en place du contenu
    * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
    * @param array $sheets : contient les 30 onglets
    * @param array $infos : contient tous les enregistrements
    */
    public function ajoutDonneesExport_contrat_refinanceur_loyer(&$sheets,$infos){
        $row_auto=1;
		foreach ($infos as $key => $value) {

			if(!$value["commande.id_affaire_fk"]) $value["commande.id_affaire_fk"] = $value["commande.id_affaire"];

			$value["loyer.total"] = ($value["loyer.loyer"]+$value["loyer.assurance"]+$value["loyer.frais_gestion"])*$value["loyer.duree"];

			ATF::demande_refi()->q->reset()->where("id_affaire", $value["commande.id_affaire_fk"],"AND")
										   ->where("etat", "valide");
			$refi = ATF::demande_refi()->select_row();
			if($refi){	$refi = ATF::refinanceur()->select($refi["id_refinanceur"] , "refinanceur"); }
			$value["commande.refincanceur"] = $refi;

			ATF::comite()->q->reset()->where("id_affaire", $value["commande.id_affaire_fk"]);
			$comites = ATF::comite()->select_all();

			if($comites){
				$commentaire = $decision = $observations = "";
				foreach ($comites as $k => $v) {
					if($k !== 0){
						$decisiondate = $decisiondate."\n". $v["date"];
						$commentaire = $commentaire."\n".$v["commentaire"];
						$decision = $decision."\n".$v["decisionComite"];
						$date_accord = $date_accord."\n".$v["validite_accord"];
						$observations 	  = $observations."\n".$v["observations"];

					}else{
						$decisiondate = $v["date"];
						$commentaire  = $v["commentaire"];
						$decision 	  = $v["decisionComite"];
						$date_accord =  $v["validite_accord"];
						$observations  = $v["observations"];
					}
				}
				$value["comite"]=$decisiondate;
				$value["comite.decisionComite"]= $decision;
				$value["comite.validite_accord"] = $date_accord;
				$value["comite.commentaire"]= $commentaire;
				$value["comite.observations"]= $observations;
			}

			unset($value["commande.ref"],$value["commande.date_debut"],$value["commande.mise_en_place"],$value["commande.id_devis"],$value["commande.tva"],$value["commande.clause_logicielle"],$value["commande.type"],$value["commande.date_arret"],$value["commande.date_evolution"],$value["commande.date_restitution_effective"],$value["commande.date_prevision_restitution"],$value["commande.date_demande_resiliation"],$value["commande.prix_achat"],$value["commande.id_user"],$value["commande.date"],$value["affaire.date_installation_reel"]);

			$row_data = array();
	     	//A =65 Z=90
	     	$lettre1 = 65;
			$lettre2 = 0;

			foreach ($value as $k => $v) {
	     		$data = array();
	     		$data = explode(".",$k);
	     		if( !strpos($data[1],"_fk") ){
	     			if($lettre1 <= 90 && $lettre2 == 0){
	     				$char = chr($lettre1);
	     				$lettre1++;
	     			}else{
	     				if($lettre2 == 0){
	     					$lettre1 =  $lettre2 = 65;
	     					$char = chr($lettre1).chr($lettre2);
	     				}elseif($lettre2 == 90){
							$lettre1++;
							$lettre2 = 65;
							$char = chr($lettre1).chr($lettre2);
						}else{
							$lettre2++;
							$char = chr($lettre1).chr($lettre2);
						}
	     			}
	     			if($k == "commande.etat"){ $v = ATF::$usr->trans($v,$this->table); }
				    $row_data[$char] = array($v , "border_cel_left");
	     		}
	     	}

	     	if($row_data){
				$row_auto++;
				foreach($row_data as $col=>$valeur){
					$sheets['auto']->write($col.$row_auto,$valeur[0], $this->getStyle($valeur[1]));
				}
			}
		}
	}


	/** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	public function export_contrat_pas_mep($infos,$testUnitaire="false",$reset="true"){

	 	if($testUnitaire == "true"){
	 		$donnees = $infos;
		}else{
			$this->q->reset();

			$this->q->from("commande","id_affaire","loyer","id_affaire")
					->from("commande","id_affaire","affaire","id_affaire")
					->where("commande.etat","non_loyer","AND")
					->whereIsNull("commande.date_debut")
					->addAllFields("commande")
					->addAllFields("affaire")
					->addAllFields("loyer")
					->setLimit(-1)->unsetCount();
			$donnees = $this->sa();
		}

        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		$feuilles = array(array("title"=> "Pas MEP C", "code_client"=> "C"),
						  array("title"=> "Pas MEP H", "code_client"=> "H"),
						  array("title"=> "Pas MEP M", "code_client"=> "M"),
						  array("title"=> "Pas MEP MOA", "code_client"=> "MOA"),
						  array("title"=> "Pas MEP D", "code_client"=> "D"),
						  array("title"=> "Pas MEP B", "code_client"=> "B"),
						  array("title"=> "Pas MEP S", "code_client"=> "S"),
						  array("title"=> "Pas MEP ATOL", "code_client"=> ""),
						  array("title"=> "Pas MEP non repertorié")
						);

		$data = array();

		foreach ($donnees as $key => $value) {
			if($value["affaire.nature"] !== "avenant" && $value["affaire.nature"] !== "vente"){
				$societe = ATF::societe()->select($value["commande.id_societe_fk"]);

				$value["code_client"] = $societe["code_client"];

				$ref = preg_replace('/\-?\d+/', '',$societe["code_client"]);
				$esp = 0;

				foreach ($feuilles as $kf => $vf) {
					if($esp ===0 && $kf === count($feuilles)-1) {
						$data[$kf][] = $value;
					}else{
						if($esp === 0){
							if($vf["code_client"] === "" && strlen($ref) === 0 && is_numeric($societe["code_client"])){
								$data[$kf][] = $value;
								$esp = 1;
							}elseif(strpos($vf["code_client"], $ref) !== false && strpos($vf["code_client"], $ref) === 0){
								$data[$kf][] = $value;
								$esp = 1;
							}
						}
					}
				}
			}
		}

		$premfeuille = true;


		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);


		foreach ($feuilles as $key => $value) {
			if ($premfeuille){
				$workbook->setActiveSheetIndex($key);
			    $sheet = $workbook->getActiveSheet();
			    $sheet->setTitle($value["title"]);
			    $premfeuille = false;
			}else{
				$sheet = $workbook->createSheet($key);
				$workbook->setActiveSheetIndex($key);
				$sheet = $workbook->getActiveSheet();
				$sheet ->setTitle($value["title"]);
			}

			$cols = array(	array("title"=> "Affaire", "size"=>15),
								array("title"=> "Entité", "size"=>30),
								array("title"=> "Contrat", "size"=>60),
								array("title"=> "Code client", "size"=>15),
								array("title"=> "Installation prévue", "size"=>15),
								array("title"=> "Retour (AP)", "size"=>15),
								array("title"=> "Retour (PV)", "size"=>15),
								array("title"=> "Retour", "size"=>15),
								array("title"=> "Loyer", "size"=>15),
								array("title"=> "Durée", "size"=>15),
								array("title"=> "Fréquence du loyer", "size"=>15),
								array("title"=> "Total", "size"=>15),
								array("title"=> "Achat", "size"=>15),
								array("title"=> "Refinanceur", "size"=>30),
								array("title"=> "Comité", "size"=>15),
								array("title"=> "Décision Comité", "size"=>15),
								array("title"=> "Validité de l'accord", "size"=>15),
								array("title"=> "Commentaire", "size"=>60),
								array("title"=> "Observations", "size"=>60));

			$i=0;
	    	foreach($cols as $col=>$titre){
	    		$lettre1 = 65 +$i;

				$sheet->setCellValueByColumnAndRow($i , 1, $titre["title"]);
				$sheet->getColumnDimension(chr($lettre1))->setWidth($titre["size"]);
				$i++;
	        }

			$this->dataPasMep($sheet, $data[$key]);

	    }

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_ct_pas_mep.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
    }

    public function dataPasMep(&$sheet,$donnees){

    	$row_data = array();
    	foreach ($donnees as $key => $value) {

	    	$row_data[$key][] = $value["commande.ref"];
			$row_data[$key][] = $value["affaire.id_societe"];
			$row_data[$key][] = $value["affaire.affaire"];
			$row_data[$key][] = $value["code_client"];
			$row_data[$key][] = $value["affaire.date_installation_prevu"];
			$row_data[$key][] = $value["commande.retour_prel"];
			$row_data[$key][] = $value["commande.retour_pv"];
			$row_data[$key][] = $value["commande.retour_contrat"];
			$row_data[$key][] = $value["loyer.loyer"] +  $value["loyer.assurance"] +  $value["loyer.frais_de_gestion"];
			$row_data[$key][] = $value["loyer.duree"];
			$row_data[$key][] = $value["loyer.frequence_loyer"];
			$row_data[$key][] = ($value["loyer.loyer"] +  $value["loyer.assurance"] +  $value["loyer.frais_de_gestion"]) * $value["loyer.duree"];
			$row_data[$key][] = $value["commande.prix_achat"];

			ATF::demande_refi()->q->reset()->where("id_affaire", $value["affaire.id_affaire_fk"],"AND")
									   ->where("etat", "valide");

			$refi = ATF::demande_refi()->select_row();
			if($refi)	$row_data[$key][] = ATF::refinanceur()->select($refi["id_refinanceur"] , "refinanceur");
			else $row_data[$key][] = "";


			ATF::comite()->q->reset()->where("id_affaire", $value["affaire.id_affaire_fk"]);
			$comites = ATF::comite()->select_all();

			if($comites){
				$commentaire = $decision = $observations = "";
				foreach ($comites as $k => $v) {
					if($k !== 0){
						$decisiondate = $decisiondate."\n". $v["date"];
						$commentaire = $commentaire."\n".$v["commentaire"];
						$decision = $decision."\n".$v["decisionComite"];
						$date_accord = $date_accord."\n".$v["validite_accord"];
						$observations 	  = $observations."\n".$v["observations"];

					}else{
						$decisiondate = $v["date"];
						$commentaire  = $v["commentaire"];
						$decision 	  = $v["decisionComite"];
						$date_accord =  $v["validite_accord"];
						$observations  = $v["observations"];
					}
				}

				$row_data[$key][] = $decisiondate;
				$row_data[$key][] = $decision;
				$row_data[$key][] = $date_accord;
				$row_data[$key][] = $commentaire;
				$row_data[$key][] = $observations;

			} else {
				$row_data[$key][] = "";
				$row_data[$key][] = "";
				$row_data[$key][] = "";
				$row_data[$key][] = "";
				$row_data[$key][] = "";
			}


    	}


		$i=0;
    	$j=2;

    	foreach ($row_data as $ligne => $value){
	    	foreach($value as $col=>$val){
				$sheet->setCellValueByColumnAndRow($i , $j, $val);
				$i++;
	        }
	        $i=0;
	        $j++;
	    }
    }


	/**
	 * méthode permettant de faire les graphes des différents modules, dans statistique
	 * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 */
	public function commande_mep_stats($stats=false,$type=false,$widget=false,$date=false,$id_agence) {

		//on récupère la liste des années que l'on ne souhaite pas voir afficher sur les graphes
		//on les incorpore ensuite sur les requêtes adéquates
		$this->q->reset();
		foreach(ATF::stats()->liste_annees[$this->table] as $key_list=>$item_list){
			if($item_list)$this->q->addCondition("YEAR(`date`)",$key_list);
		}
		ATF::stats()->conditionYear(ATF::stats()->liste_annees[$this->table],$this->q,"date");

		switch ($type) {
			case "o2m":
			case "autre":
			case "les_S" :
			case "reseau" :
				if($widget){
					$this->q->reset()
						->addField("COUNT(*)","nb")
						->setStrict()
						->addJointure("commande","id_societe","societe","id_societe")
						->addJointure("commande","id_affaire","affaire","id_affaire")
						->addJointure("societe","id_owner","user","id_user")
						->where("user.id_agence",$id_agence);


					if($type == "reseau"){
						$this->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")
								->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL");
					}else{
						$this->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")
								->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL");
					}



					$this->q->addCondition("commande.etat","prolongation" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","AR" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","arreter" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","vente" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","restitution" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","mis_loyer_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","prolongation_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
							->addCondition("commande.etat","restitution_contentieux" ,"AND", "conditiondevis", "NOT LIKE")

							->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
							->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

							->addCondition("commande.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

							->addField("DATE_FORMAT(`".$this->table."`.`mise_en_place`,'%Y')","year")
							->addField("DATE_FORMAT(`".$this->table."`.`mise_en_place`,'%m')","month")

							->addGroup("year")->addGroup("month")
							->addOrder("year")->addOrder("month")

							->addCondition("`".$this->table."`.`mise_en_place`",$date."-01-01","AND",false,">");


					$result= parent::select_all();



					$annee = $date-3;
					if($date <2017) $id_agence = 1;
					ATF::stat_snap()->q->reset()->addField("stat_snap.nb","nb")
												->addField("DATE_FORMAT(`stat_snap`.`date`,'%Y')","year")
												->addField("DATE_FORMAT(`stat_snap`.`date`,'%m')","month")
												->addCondition("`stat_snap`.`date`",$annee."-01-01","AND",false,">=")
												->addCondition("`stat_snap`.`date`",$date."-01-01","AND",false,"<")
												->addCondition("`stat_snap`.`id_agence`",$id_agence)
												->addGroup("year")->addGroup("month")
												->addOrder("year")->addOrder("month")
												->where("stat_concerne", "mep-".$type);
					$res = ATF::stat_snap()->select_all();

					/*$annee = $date-3;
					for($a=$annee; $a<$date;$a++){
						for($m=1;$m<=12;$m++){

							ATF::commande()->q->reset()
								->addField("COUNT(*)","nb")
								->setStrict()
								->addJointure("commande","id_societe","societe","id_societe")
								->addJointure("commande","id_affaire","affaire","id_affaire")
								->addJointure("societe","id_owner","user","id_user")
								->where("user.id_agence",$id_agence)

								->addCondition("commande.etat","prolongation" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("commande.etat","AR" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("commande.etat","arreter" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("commande.etat","vente" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("commande.etat","restitution" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("commande.etat","mis_loyer_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("commande.etat","prolongation_contentieux" ,"AND", "conditiondevis", "NOT LIKE")
								->addCondition("commande.etat","restitution_contentieux" ,"AND", "conditiondevis", "NOT LIKE")

								//->addCondition("affaire.etat","terminee","AND","conditiondevis","!=")
								->addCondition("affaire.etat","perdue","AND","conditiondevis","!=")

								->addCondition("commande.ref","%avt%","AND", "conditiondevis", "NOT LIKE")

								->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%Y')","year")
								->addField("DATE_FORMAT(`commande`.`mise_en_place`,'%m')","month")

								->addGroup("year")->addGroup("month")
								->addOrder("year")->addOrder("month")

								->addCondition("`commande`.`mise_en_place`",$a."-".$m."-01","AND",false,">=")
								->addCondition("`commande`.`mise_en_place`",$a."-".$m."-31","AND",false,"<");


							if($type == "reseau"){
								ATF::commande()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","NOT LIKE")
									    		->addCondition("societe.code_client",NULL,"AND","nonFinie","IS NOT NULL");
							}else{
								ATF::commande()->q->addCondition("societe.code_client",'%S%',"AND","nonFinie","LIKE")
									    		->addCondition("societe.code_client",NULL,"OR","nonFinie","IS NULL");
							}
							$r[$a][$m] = ATF::commande()->select_row();
						}
					}

					foreach ($r as $ky => $value) {
						foreach ($value as $k => $v) {
							$res[] = array(
										"nb"=> $v["nb"],
										"year"=> $v["year"],
										"month"=> $v["month"]
									);
						}
					}*/
				}



				if($widget){

					$agence = ATF::agence()->select(1);


					foreach (ATF::stats()->recupMois($type) as $i) {
						$graph['categories']["category"][] = array("label"=>substr($i,0,1),"hoverText"=>$i);
					}

					$avg = $reel = $obj =array();

					if(count($result) < 12){
						for ($i=0; $i < 12; $i++) {
							if($i <10) $month = "0".$i+1;
							else $month = $i+1;
							$temp[$i] = array("nb"=>0, "year"=>date("Y"), "month"=>$month);
							foreach ($result as $key => $value) {
								if($month == $value["month"]){
									$temp[$i]["nb"] = $value["nb"];
									$temp[$i]["year"] = $value["year"];
									$temp[$i]["month"] = $value["month"];
								}
							}
						}
						$result = $temp;
					}


					foreach ($res as $i) {
						if($avg[$i["month"]]["value"]){
							$avg[$i["month"]]["value"] = $avg[$i["month"]]["value"]+$i["nb"];
						}else{
							$avg[$i["month"]]["value"] = $i["nb"];
						}
						$avg[$i["month"]]["titre"] = "Objectif MENSUEL : ".$avg[$i["month"]]["value"];

						if($graph['year'][$i['year']]["count"]){ $graph['year'][$i['year']]["count"] = $graph['year'][$i['year']]["count"] + $i["nb"]; }
						else{ $graph['year'][$i['year']]["count"] = $i["nb"]; }
						$graph['year'][$i['year']]["annee"] = $i["year"];

					}


					foreach ($result as $i) {
						$reel[$i["month"]]["value"]= $i["nb"];
						$reel[$i["month"]]["titre"] = "Objectif MENSUEL : ".$reel[$i["month"]]["value"];

						if($graph['year'][$i['year']]["count"]){ $graph['year'][$i['year']]["count"] = $graph['year'][$i['year']]["count"] + $i["nb"]; }
						else{ $graph['year'][$i['year']]["count"] = $i["nb"]; }
						$graph['year'][$i['year']]["annee"] = $i["year"];
					}

					$totalPrec = 0;
					if($type == "o2m" ||$type== 'reseau'){	$objectif = $agence["objectif_devis_reseaux"]; }
					else{ 	$objectif = $agence["objectif_devis_autre"]; }

					foreach ($avg as $key => $value) {
						$avg[$key]["value"] = round($value["value"]/3);
						$totalPrec += $avg[$key]["value"];
					}


					foreach ($avg as $key => $value) {
						$pourcentage = ($avg[$key]["value"]/$totalPrec)*100;
						$obj[$key]["value"] = round(($objectif/100)*$pourcentage);
					}


					$graph['dataset']["objectif"] = $obj;
					$graph['dataset']["moyenne"] = $avg;
					$graph['dataset']["reel"] = $reel;




				} else {
					foreach (ATF::stats()->recupMois($type) as $k=>$i) {
						$graph['categories']["category"][] = array("label"=>substr($i,0,4),"hoverText"=>$i);
					}
				}

				return $graph;


			default:
				return parent::stats($stats,$type,$widget);
		}
	}



};

class commande_cleodisbe extends commande_cleodis {

	function __construct($table_or_id=NULL) {
		$this->table="commande";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column']['filesLangue']  = array("custom"=>true,"nosort"=>true,"renderer"=>"pdfCommandeLangue","width"=>110); //PDF dans la langue de la société

		$this->fieldstructure();

		$this->files["contratA4NL"] = array("type"=>"pdf","preview"=>true,"no_upload"=>true,"force_generate"=>true);
	}


	/** Surcharge de l'export filtrÃ© pour avoir tous les champs nÃ©cessaire Ã  l'export spÃ©cifique
     * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
     * @param array $infos : contient le nom de l'onglet
     */
	public function export_contrat_pas_mep($infos,$testUnitaire="false",$reset="true"){

	 	if($testUnitaire == "true"){
	 		$donnees = $infos;
		}else{
			$this->q->reset();

			$this->q->from("commande","id_affaire","loyer","id_affaire")
					->from("commande","id_affaire","affaire","id_affaire")
					->where("commande.etat","non_loyer","AND")
					->whereIsNull("commande.date_debut")
					->addAllFields("commande")
					->addAllFields("affaire")
					->addAllFields("loyer")
					->setLimit(-1)->unsetCount();
			$donnees = $this->sa();
		}

        require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel.php";
		require_once __ABSOLUTE_PATH__."libs/ATF/libs/PHPExcel/Classes/PHPExcel/Writer/Excel5.php";
		$fname = tempnam(__TEMPORARY_PATH__, __TEMPLATE__.ATF::$usr->getID());
		$workbook = new PHPExcel;

		$feuilles = array(
						  array("title"=> "Pas MEP")
						);

		$data = array();

		foreach ($donnees as $key => $value) {
			if($value["affaire.nature"] !== "avenant" && $value["affaire.nature"] !== "vente"){
				$societe = ATF::societe()->select($value["commande.id_societe_fk"]);

				foreach ($feuilles as $kf => $vf) {
					$data[$kf][] = $value;
				}
			}
		}

		$premfeuille = true;


		$worksheet_auto = new PHPEXCEL_ATF($workbook,0);


		foreach ($feuilles as $key => $value) {
			/*if ($premfeuille){
				$workbook->setActiveSheetIndex($key);
			    $sheet = $workbook->getActiveSheet();
			    $sheet->setTitle($value["title"]);
			    $premfeuille = false;
			}else{*/
				$sheet = $workbook->createSheet($key);
				$workbook->setActiveSheetIndex($key);
				$sheet = $workbook->getActiveSheet();
				$sheet ->setTitle($value["title"]);
			//}

			$cols = array(	array("title"=> "Affaire", "size"=>15),
								array("title"=> "Entité", "size"=>30),
								array("title"=> "Contrat", "size"=>60),
								array("title"=> "Code client", "size"=>15),
								array("title"=> "Installation prévue", "size"=>15),
								array("title"=> "Retour (AP)", "size"=>15),
								array("title"=> "Retour (PV)", "size"=>15),
								array("title"=> "Retour", "size"=>15),
								array("title"=> "Loyer", "size"=>15),
								array("title"=> "Durée", "size"=>15),
								array("title"=> "Fréquence du loyer", "size"=>15),
								array("title"=> "Total", "size"=>15),
								array("title"=> "Refinanceur", "size"=>30),
								array("title"=> "Comité", "size"=>15),
								array("title"=> "Décision Comité", "size"=>15),
								array("title"=> "Validité de l'accord", "size"=>15),
								array("title"=> "Commentaire", "size"=>60),
								array("title"=> "Observations", "size"=>60));

			$i=0;
	    	foreach($cols as $col=>$titre){
	    		$lettre1 = 65 +$i;

				$sheet->setCellValueByColumnAndRow($i , 1, $titre["title"]);
				$sheet->getColumnDimension(chr($lettre1))->setWidth($titre["size"]);
				$i++;
	        }

			$this->dataPasMep($sheet, $data[$key]);

	    }

		$writer = new PHPExcel_Writer_Excel5($workbook);

		$writer->save($fname);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition:inline;filename=export_ct_pas_mep.xls');
		header("Cache-Control: private");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		PHPExcel_Calculation::getInstance()->__destruct();
    }

    public function dataPasMep(&$sheet,$donnees){

    	$row_data = array();
    	foreach ($donnees as $key => $value) {

	    	$row_data[$key][] = $value["commande.ref"];
			$row_data[$key][] = $value["affaire.id_societe"];
			$row_data[$key][] = $value["affaire.affaire"];
			$row_data[$key][] = $value["code_client"];
			$row_data[$key][] = $value["affaire.date_installation_prevu"];
			$row_data[$key][] = $value["commande.retour_prel"];
			$row_data[$key][] = $value["commande.retour_pv"];
			$row_data[$key][] = $value["commande.retour_contrat"];
			$row_data[$key][] = $value["loyer.loyer"] +  $value["loyer.assurance"] +  $value["loyer.frais_de_gestion"];
			$row_data[$key][] = $value["loyer.duree"];
			$row_data[$key][] = $value["loyer.frequence_loyer"];
			$row_data[$key][] = ($value["loyer.loyer"] +  $value["loyer.assurance"] +  $value["loyer.frais_de_gestion"]) * $value["loyer.duree"];


			ATF::demande_refi()->q->reset()->where("id_affaire", $value["affaire.id_affaire_fk"],"AND")
									   ->where("etat", "valide");

			$refi = ATF::demande_refi()->select_row();
			if($refi)	$row_data[$key][] = ATF::refinanceur()->select($refi["id_refinanceur"] , "refinanceur");
			else $row_data[$key][] = "";


			ATF::comite()->q->reset()->where("id_affaire", $value["affaire.id_affaire_fk"]);
			$comites = ATF::comite()->select_all();

			if($comites){
				$commentaire = $decision = $observations = "";
				foreach ($comites as $k => $v) {
					if($k !== 0){
						$decisiondate = $decisiondate."\n". $v["date"];
						$commentaire = $commentaire."\n".$v["commentaire"];
						$decision = $decision."\n".$v["decisionComite"];
						$date_accord = $date_accord."\n".$v["validite_accord"];
						$observations 	  = $observations."\n".$v["observations"];

					}else{
						$decisiondate = $v["date"];
						$commentaire  = $v["commentaire"];
						$decision 	  = $v["decisionComite"];
						$date_accord =  $v["validite_accord"];
						$observations  = $v["observations"];
					}
				}

				$row_data[$key][] = $decisiondate;
				$row_data[$key][] = $decision;
				$row_data[$key][] = $date_accord;
				$row_data[$key][] = $commentaire;
				$row_data[$key][] = $observations;

			} else {
				$row_data[$key][] = "";
				$row_data[$key][] = "";
				$row_data[$key][] = "";
				$row_data[$key][] = "";
				$row_data[$key][] = "";
			}


    	}


		$i=0;
    	$j=2;

    	foreach ($row_data as $ligne => $value){
	    	foreach($value as $col=>$val){
				$sheet->setCellValueByColumnAndRow($i , $j, $val);
				$i++;
	        }
	        $i=0;
	        $j++;
	    }
    }


};

class commande_midas extends commande_cleodis {
	function __construct($table_or_id=NULL) {
		$this->table="commande";
		parent::__construct($table_or_id);

		$this->colonnes['fields_column'] = array(
			'commande.ref'=>array("width"=>70)
			,'specificDate'=>array("custom"=>true,"nosort"=>true,"renderer"=>"dateCleCommande","width"=>300)
			,'commande.id_societe'
			,'commande.id_affaire'
			,'commande.id_devis'
			,'commande.etat'=>array("width"=>70,"renderer"=>"etat")
			,'files'=>array("custom"=>true,"nosort"=>true,"renderer"=>"pdfCommande","width"=>120)
			,'retour'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>70)
			,'retourPV'=>array("custom"=>true,"nosort"=>true,"type"=>"file","width"=>70)
		);

		$this->fieldstructure();
	}
};

class commande_cap extends commande_cleodis {


	/**
	 * Permet de stocker le PDF signé de Sell&Sign
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
	 * @param  array $get
	 * @param  array $post
	 * @return
	*/
	public function _getSignedContract($get, $post){
		if (!$post['data']) throw new Exception("Il manque le base64", 500);
		if (!$post['contract_id']) throw new Exception("Il manque l'id du contract", 500);

		$data = $post['data'];
		$contract_id = $post['contract_id'];

		// Récupérer l'id_commande a partir de l'id_contract_sellandsign
		ATF::affaire()->q->reset()->where("id_contract_sellandsign",$post['contract_id'])->setStrict()->addField('id_affaire')->setDimension('cell');
		$id_affaire = ATF::affaire()->select_all();
		$id_mandat = ATF::affaire()->getMandat($id_affaire);

		$file = ATF::mandat()->filepath($id_mandat, 'retourBPA', null, 'cap');
		try {
			util::file_put_contents($file,base64_decode($data));
			//On met à jour la date de retour et retourPV du contrat
			ATF::mandat()->u(array("id_mandat"=>$id_mandat,
									 "date_retour"=> date("Y-m-d")
									)
								);
			$return = true;
		} catch (Exception $e) {
			$return  = array("error"=>true, "data"=>$e);
		}
		return $return;
	}


};


class commande_bdomplus extends commande_cleodis { };
class commande_boulanger extends commande_cleodis { };

class commande_assets extends commande_cleodis { };