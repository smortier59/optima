<?
/**
 * Classe commande
 * @package Optima
 * @subpackage AbsysTech
 */
require_once dirname(__FILE__)."/../commande.class.php";
class commande_absystech extends commande {

	public $user_for_rappel = array(
		54, // Emma
		57, // Gauthier
		3, // Séb
	);

	/**
	 * Constructeur
	 */
	public function __construct() {

		$this->table = "commande";
		parent::__construct();

		$this->colonnes['fields_column'] = array(
			 'commande.ref'=>array("width"=>100,"align"=>"center")
			 ,'commande.id_societe'
			 ,'commande.resume'
			 ,'commande.date'=>array("width"=>100,"align"=>"center")
			 ,'commande.prix'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			 ,'commande.prix_achat'=>array("aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			 ,'margebrute'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			 ,'marchandises'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			 ,'prestations'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			 ,'autres'=>array("custom"=>true,"aggregate"=>array("min","avg","max","sum"),"align"=>"right","renderer"=>"money","width"=>80)
			 ,'commande.id_devis'=>array("width"=>100,"align"=>"center")
			 ,'commande.etat'=>array("renderer"=>"etat","width"=>30)
			 ,'totalFacture'=>array("custom"=>true,"renderer"=>"money","width"=>80)
			 ,'fichier_joint'=>array("custom"=>true,"align"=>"center","nosort"=>true,"type"=>"file","width"=>50, "renderer"=>"scanner")
			 ,'fichier_joint2'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"scanner","width"=>70)
			 ,'actions'=>array("custom"=>true,"renderer"=>"actionsCmd","width"=>150)
			 ,'redacteurs'=>array("custom"=>true,"width"=>150)
		 );

		$this->colonnes['primary'] = array(
			"id_societe"
			,"date"=>array("obligatoire"=>true)
			,"code_commande_client"=>array("custom"=>true)
		);

		$this->colonnes['panel']['redaction'] = array(
			"resume"
		);

		$this->colonnes['panel']['maintenance'] = array(
			"rappel_annee"=>array("custom"=>true),
			"jours_inclus"=>array("custom"=>true)
		);


		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);

		$this->colonnes['panel']['total'] = array(
			"sous_total"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"frais_de_port"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"prix_achat"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
			,"marge"=>array("custom"=>true,"readonly"=>true)
			,"marge_absolue"=>array("custom"=>true,"readonly"=>true,"formatNumeric"=>true,"xtype"=>"textfield")
		);

		// Propriété des panels
		$this->panels['primary'] = array("visible"=>true,'nbCols'=>3);
		$this->panels['redaction'] = array("visible"=>true,'nbCols'=>1);
		$this->panels['maintenance'] = array("visible"=>false,'nbCols'=>2,"checkboxToggle"=>true);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);
		$this->panels['total'] = array("visible"=>true,'nbCols'=>4);

		// Champs masqués
		$this->colonnes['bloquees']['insert'] =
		$this->colonnes['bloquees']['cloner'] =
		$this->colonnes['bloquees']['update'] =  array('divers_1','ref','id_user','id_affaire','tva','etat','id_devis','code_commande_client');
		$this->colonnes['bloquees']['update'][] =  'rappel_annee';
		$this->colonnes['bloquees']['update'][] =  'jours_inclus';

		//		$this->colonnes_commande_ligne = array_flip(array('ref','produit','quantite','prix','prix_achat','serial')); // Ne sert plus que dans le vieux template liste_materiel qui ne sert plus

		$this->fieldstructure();
		$this->field_nom = "ref";
		$this->onglets = array('commande_ligne');
		$this->stats_types = array("CA","marge","pourcentage","user","users");
		$this->autocomplete = array(
		"field"=>array("commande.resume","commande.ref")
		,"show"=>array("commande.resume","commande.ref")
		,"popup"=>array("commande.resume","commande.ref")
		,"view"=>array("commande.resume","commande.ref")
		);


		$this->files["fichier_joint"] = array("type"=>"pdf","obligatoire"=>true);
		$this->files["fichier_joint2"] = array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true);
		$this->addPrivilege("annulee","update");
		$this->addPrivilege("UpdateListeMateriel","update");
		$this->addPrivilege("uploadFile","update");
		$this->addPrivilege("setInfos","update");
	}

	/**
	 * Retourne true c'est à dire que la modification est possible
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_update($id,$infos=false){
		if($this->select($id,"etat")!="en_cours" && ATF::$usr->get("id_profil")!=1) {
			throw new errorATF("Il est impossible de modifier une commande qui n'est pas en cours, seul un profil Associé peut faire cela.",892);
		}else{
			return true;
		}
	}

	/**
	 * Retourne true c'est à dire que la suppression est possible
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_delete($id){
		if($this->select($id,"etat")!="en_cours" && $this->select($id,"etat")!="annulee"){
			throw new errorATF("Il est impossible de supprimer une commande qui n'est pas en cours ou annulée",893);
		}else{
			return true;
		}
	}

	/**
	 * Surcharge de l'update
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){

		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$this->infoCollapse($infos);
		unset($infos["sous_total"],$infos["marge"],$infos["marge_absolue"]);


		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		ATF::commande_ligne()->q->reset()->where("id_commande",$this->decryptId($infos["id_commande"]));
		$commande_ligne=ATF::commande_ligne()->select_all();

		foreach($commande_ligne as $key=>$item){
			ATF::commande_ligne()->delete(array("id"=>$item["id_commande_ligne"]));
		}

		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("commande_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			$item["id_fournisseur"]=$item["id_fournisseur_fk"];
			$item["id_compte_absystech"]=$item["id_compte_absystech_fk"];
			unset($item["id_fournisseur_fk"],$item["id_compte_absystech_fk"],$item["marge"],$item["marge_absolue"]);
			$item["id_commande"]=$infos["id_commande"];
			$item["index"]=util::extJSEscapeDot($key);
			if(!$item["quantite"]) $item["quantite"]=0;
			ATF::commande_ligne()->q->reset();
			ATF::commande_ligne()->i($item,$s);
			$prixFinal += $item["prix"]*$item["quantite"];
		}
		$prixFinal += $infos["frais_de_port"];

		// Mise a jour du prix final du devis.
		$cu = array("id_commande"=>$item["id_commande"],"prix"=>$prixFinal);
		$this->u($cu);

		if($infos["code_commande_client"]){
			$affaire["code_commande_client"]=$infos["code_commande_client"];
			$affaire["id_affaire"]=$infos["id_affaire"];
			ATF::affaire()->u($affaire,$s);
		}
		unset($infos["code_commande_client"]);

		$update = parent::update($infos,$s,$files);

		ATF::db($this->db)->commit_transaction();
		//*****************************************************************************
		$id_affaire=$this->select($infos["id_commande"],"id_affaire");
		ATF::affaire()->redirection("select",$id_affaire);

		return $update;

	}

	/**
	 * Surcharge du cloner qui permet d'unseter les champs inutiles
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	//	public function cloner($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
	//		unset($infos[$this->table]["ref"],$infos[$this->table]["etat"]);
	//		return $this->insert($infos,$s,$files,$cadre_refreshed,$nolog);
	//	}

	/**
	 * Surcharge de l'insert afin d'insérer les lignes de commande et modifier l'état de l'affaire et du devis
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$this->infoCollapse($infos);


		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("commande_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}
			if(!$item["quantite"]) $item["quantite"]=0;
			$prixFinal += $item["prix"]*$item["quantite"];
		}
		$prixFinal += $infos["frais_de_port"];

		/*Formatage des numériques*/
		$infos["prix"]=util::stringToNumber($prixFinal);
		$infos["frais_de_port"]=util::stringToNumber($infos["frais_de_port"]);



		unset($infos["id_produit"]);

		if(!$infos["date"]){
			$infos["date"] = date("Y-m-d");
		}

		$infos["ref"] = ATF::affaire()->getRef($infos["date"],"commande");
		$infos["id_user"] = ATF::$usr->getID();

		$societe=ATF::societe()->select($infos["id_societe"]);
		if($societe["id_pays"]!="FR") $infos["tva"] =  1;
		else $infos["tva"] =  __TVA__;
		$infos["id_societe"]=$societe["id_societe"];
		//$infos["prix"]=$infos['prix'];
		//$infos["frais_de_port"]=$infos['frais_de_port'];
		//$infos["prix_achat"]=$infos['prix_achat'];
		if($infos["id_devis"]){
			$infos["id_affaire"]=ATF::devis()->select($infos['id_devis'],"id_affaire");
		}

		if ($infos['rappel_annee']) {
			$rappel_annee = $infos['rappel_annee'];
			$affaire["rappel_annee"]=$rappel_annee;
		}
		unset($infos['rappel_annee']);
		if ($infos['jours_inclus']) {
			$jours_inclus = $infos['jours_inclus'];
			$affaire["jours_inclus"]=$jours_inclus;
		}
		unset($infos['jours_inclus']);


		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		//Affaire
		if($infos["code_commande_client"]){
			$affaire["code_commande_client"]=$infos["code_commande_client"];
		}
		unset($infos["code_commande_client"]);
		$affaire["etat"]='commande';
		$affaire["forecast"]=100;

		if($infos["id_affaire"]){
			//Modification de l'affaire
			$affaire["id_affaire"]=$infos["id_affaire"];
			$affaire["affaire"]=ATF::affaire()->nom($infos["id_affaire"]);
			ATF::affaire()->u($affaire,$s);
		}else{
			//Insertion de l'affaire si elle n'existe pas
			$affaire["id_societe"]=$infos["id_societe"];
			$affaire["affaire"]=$infos["resume"];
			$affaire["date"]=$infos["date"];
			$infos["id_affaire"]=ATF::affaire()->i($affaire,$s);
		}


		//Commande
		unset($infos["sous_total"],$infos["marge"],$infos["marge_absolue"]);
		$last_id=parent::insert($infos,$s);

		$totalCom = 0;


		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("commande_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			$item["id_fournisseur"]=$item["id_fournisseur_fk"];
			$item["id_compte_absystech"]=$item["id_compte_absystech_fk"];
			unset($item["id_fournisseur_fk"],$item["id_compte_absystech_fk"],$item["marge"],$item["marge_absolue"]);
			$item["id_commande"]=$last_id;
			$item["index"]=util::extJSEscapeDot($key);
			//$item["quantite"]=1;
			if($item["prix_nb"]) $item["prix"] = 0;

			ATF::commande_ligne()->i($item,$s);
		}

		if ($rappel_annee) {
			$now = time();
			$horaire = date('Y-m-d H:i:s', strtotime("+".$rappel_annee." year", $now));
			// permettra de créer automatiquement une tâche pour dans X années afin de rappeler le client pour renouveler ce quoi porte la commande (onduleur, certif SSL, licences, box de téléphonie...)
			$tache = array(
				"tache"=>array(
					"tache"=>"Point renouvellement - Affaire ".$affaire["affaire"],
					"id_user"=>ATF::$usr->getId(),
					"id_societe"=>$infos["id_societe"],
					"horaire_fin"=>$horaire,
					"horaire_debut"=>$horaire,
					"type"=>"vtodo",
					"description"=>"Rappeler le client pour faire un point sur le contrat de maintenance relatif a l'affaire ".$affaire["affaire"].".",
					"priorite"=>"petite",
					"no_redirect"=>true
				),
				"dest"=>$this->user_for_rappel
			);

			ATF::tache()->insert($tache);

		}


		//Devis
		if($infos["id_devis"]){
			/* Mise à jour du devis */
			$devis = array("id_devis"=>$infos["id_devis"],"etat"=>"gagne","date_modification"=>date("Y-m-d H:i:s"));
			ATF::devis()->u($devis,$s);
		}

		//Societe
		ATF::societe()->u(array("id_societe"=>$infos["id_societe"],"relation"=>"client"));

		ATF::db($this->db)->commit_transaction();
		//*****************************************************************************

		//Déplacement du fichier uploadé de temp vers data
		ATF::affaire()->redirection("select",$infos["id_affaire"]);

		return $last_id;

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
			if($commande){
				ATF::db($this->db)->begin_transaction();
				//*****************************Transaction********************************
				//Commande
				parent::delete($id,$s);

				//Devis
				if($commande["id_devis"]){
					$devis = array("id_devis"=>$commande["id_devis"],"etat"=>"attente","date_modification"=>date("Y-m-d H:i:s"));
					ATF::devis()->u($devis,$s);
				}

				//Affaire
				$this->q->reset()->where("id_affaire",$commande["id_affaire"])->end();
				$tab_commande = $this->sa();
				ATF::devis()->q->reset()->where("id_affaire",$commande["id_affaire"])->end();
				$tab_devis = ATF::devis()->sa();
				ATF::facture()->q->reset()->where("id_affaire",$commande["id_affaire"])->end();
				$tab_facture = ATF::facture()->sa();
				if($commande["id_devis"]){
					$affaire = array("id_affaire"=>$commande["id_affaire"],"etat"=>"devis","forecast"=>"20");
					ATF::affaire()->u($affaire,$s);
				}elseif(!$tab_commande && !$tab_devis && !$tab_facture){
					ATF::affaire()->delete($commande["id_affaire"],$s);
					unset($commande["id_affaire"]);
				}

				ATF::db($this->db)->commit_transaction();
				//*****************************************************************************
				if($commande["id_affaire"]){
					ATF::affaire()->redirection("select",$commande["id_affaire"]);
				}else{
					$this->redirection("select_all",NULL,"commande.html");
				}

				return true;
			}else{
				throw new errorATF("commande introuvable", 893);
			}

		} elseif (is_array($infos) && $infos) {

			foreach($infos["id"] as $key=>$item){
				$this->delete($item,$s,$files,$cadre_refreshed);
			}
		}
	}


	/**
	 * Méthode permettant de passer l'état d'une commande à annulee
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function annulee($infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$commande=$this->select($infos["id_commande"]);
		if($commande["etat"]=="en_cours"){

			ATF::db($this->db)->begin_transaction();
			//*****************************Transaction********************************
			//Commande
			parent::update(array("id_commande"=>$commande["id_commande"],"etat"=>"annulee"),$s);

			//Affaire
			ATF::affaire()->u(array("id_affaire"=>$commande["id_affaire"],"etat"=>"devis","forecast"=>"20"),$s);

			//Devis
			if($commande["id_devis"]){
				$devis = array("id_devis"=>$commande["id_devis"],"etat"=>"attente","date_modification"=>date("Y-m-d H:i:s"));
				ATF::devis()->u($devis,$s);
			}

			ATF::db($this->db)->commit_transaction();
			//*****************************************************************************

			ATF::$msg->addNotice(
			loc::mt(ATF::$usr->trans("notice_commande_annulee"),array("record"=>$this->nom($commande["id_commande"])))
			,ATF::$usr->trans("notice_success_title")
			);


			return true;
		}else{
			return false;
		}
	}

	/**
	 * Select classique qui ne prend pas en compte certaines données lors du cloner
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param int id
	 * @param string field
	 * @return array
	 */
	public function select($id,$field=NULL) {
		$commande=parent::select($id,$field);
		if(ATF::_r("event")=="cloner"){
			$commande["date"]="";
		}

		return $commande;
	}

	/**
	 * Retourne la valeur par défaut spécifique aux données passées en paramètres
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param string $field
	 * @return string
	 */
	public function default_value($field){
		if(ATF::_r('id_devis')){
			$infos=ATF::devis()->select(ATF::_r('id_devis'));
		}elseif(ATF::_r('id_commande')){
			$infos=ATF::commande()->select(ATF::_r('id_commande'));
		}
		switch ($field) {
			case "id_societe":
				return $infos["id_societe"];
			case "resume":
				return $infos["resume"];
			case "prix":
				return $infos["prix"];
			case "frais_de_port":
				return $infos["frais_de_port"];
			case "sous_total":
				return $infos["prix"]-$infos["frais_de_port"];
			case "prix_achat":
				return $infos["prix_achat"];
			case "marge":
				return round(((($infos["prix"]-$infos["frais_de_port"])-$infos["prix_achat"])/($infos["prix"]-$infos["frais_de_port"]))*100,2)."%";
			case "marge_absolue":
				return ($infos["prix"]-$infos["frais_de_port"])-$infos["prix_achat"];
			default:
				return parent::default_value($field);
		}
	}

	/**
	 * Filtrage d'information selon le profil
	 * @author Yann GAUTHERON <ygautheron@absystech.fr>
	 */
	protected function saFilter(){
		if (ATF::$usr->get("id_profil")==11) {
			// Profil apporteur d'affaire
			$this->q
				->from("commande","id_affaire","affaire","id_affaire")
				->where("affaire.id_commercial",ATF::$usr->getID());
		}
	}

	/**
	 * Surcharge select all pour le renderer action
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @return array
	 */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		if (!count($this->q->field)) {
			foreach($this->colonnes['fields_column'] as $key=>$item){
				if(!$item["custom"]){
					$this->q->addField($key);
				}
			}
		}
		if (!$this->q->alias) {
			$d = new devis();
			$d->q->setAlias("d2")
			->from("d2","id_user","user","id_user","d2_user")
			->addField('GROUP_CONCAT(CONCAT(d2.revision,\':\',d2_user.nom) ORDER BY d2.revision ASC)','redacteurs')
			->addOrder('d2.revision','desc')
			->where('d2.id_affaire','commande.id_affaire','OR',false,"=",false,true)
			->setLimit(1)
			->setStrict()
			->setToString();
			$subQuery = $d->select_all();

			$this->q->addField("(".$subQuery.")","redacteurs");
		}
		$this->q
			//->from("commande","id_affaire","devis","id_affaire")
			//->from("devis","id_user","user","id_user","devis_user")
			//->addField("GROUP_CONCAT(DISTINCT CONCAT(devis.revision,':',devis_user.nom) ORDER BY devis.revision ASC)","redacteurs")
			//->addField("GROUP_CONCAT(CONCAT(commande_ligne.id_compte_absystech,':',commande_ligne.prix))","comptes")
			->addField("commande.prix-commande.prix_achat","margebrute")

			// Somme par compte comptable
			->from("commande","id_commande","commande_ligne","id_commande")
			->addField("SUM(IF(commande_ligne.id_compte_absystech IN (1),commande_ligne.quantite*(commande_ligne.prix-IF(commande_ligne.prix_achat IS NULL,0,commande_ligne.prix_achat)),0))","marchandises")
			->addField("SUM(IF(commande_ligne.id_compte_absystech IN (4,5),commande_ligne.quantite*(commande_ligne.prix-IF(commande_ligne.prix_achat IS NULL,0,commande_ligne.prix_achat)),0))","prestations")
			->addField("SUM(IF(commande_ligne.id_compte_absystech IS NULL OR commande_ligne.id_compte_absystech NOT IN (1,4,5),commande_ligne.quantite*(commande_ligne.prix-IF(commande_ligne.prix_achat IS NULL,0,commande_ligne.prix_achat)),0))","autres")

			->addGroup("commande.id_commande");
		$return = parent::select_all($order_by,$asc,$page,$count);
		$c = new commande_absystech();
		foreach ($return['data'] as $k=>$i) {
			$etat = $i['commande.etat']?$i['commande.etat']:$i['etat'];
			$id_commande = $i['commande.id_commande']?$i['commande.id_commande']:$i['id_commande'];

			// Commande fournisseur et livraison
			if ($etat!="annulee") {
				$return['data'][$k]['allowCF'] = $return['data'][$k]['allowLivraison'] = true;
			} else {
				$return['data'][$k]['allowCF'] = $return['data'][$k]['allowLivraison'] = false;
			}

			$return['data'][$k]['totalFacture'] = ATF::facture()->total_by_commande($id_commande);

			// Etendre à la facture
			if ($etat=="en_cours" && ATF::$usr->privilege('facture','insert')) {
				$return['data'][$k]['allowFacture'] = true;
			} else {
				$return['data'][$k]['allowFacture'] = false;
			}
			// Check du profil
			if ((ATF::$usr->get('id_profil')==1 || (ATF::$usr->get('id_profil')==9 && ATF::$codename == "absystech") ||  (ATF::$usr->get('id_profil')==5 && ATF::$codename == "att")) && $etat!="facturee" && $etat!="annulee") {
				$return['data'][$k]['allowCheckFacture'] = true;
			} else {
				$return['data'][$k]['allowCheckFacture'] = false;
			}

			// Annulation
			if ($etat=="en_cours" && ATF::$usr->privilege('commande','update')) {
				$return['data'][$k]['allowCancel'] = true;
			} else {
				$return['data'][$k]['allowCancel'] = false;
			}

			$return['data'][$k]['id_affaire'] = $this->cryptID($c->select($id_commande,"id_affaire"));
		}
		return $return;
	}


	/**
	 * Permet de modifier un champs en AJAX
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @return bool
	 */
	public function setInfos($infos){

		return $this->u($infos);

	}

	public function _getCaThisMonth(){
		$date = date('Y-m');
		$q = "SELECT SUM(prix) FROM commande WHERE DATE_FORMAT(date, '%Y-%m') = '".$date."' GROUP BY YEAR(date), MONTH(date)";

		$ca = ATF::db()->ffc($q);

		return $ca;
	}

};

class commande_att extends commande_absystech { };
class commande_wapp6 extends commande_absystech { };
class commande_atoutcoms extends commande_absystech { };
class commande_demo extends commande_absystech { };
class commande_nco extends commande_absystech { };
?>