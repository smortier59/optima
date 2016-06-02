<?
/** 
* Classe copieur_contrat
* @package Optima
* @subpackage AbsysTech
*/
class copieur_contrat extends classes_optima {
	/**
	* Constructeur !
	*/
	public function __construct() {
		parent::__construct();
 
		$this->table = __CLASS__; 
		$this->colonnes['fields_column'] = array(
			'copieur_contrat.ref',
			'copieur_contrat.date',
			'copieur_contrat.id_user',
			'copieur_contrat.id_societe',
			'copieur_contrat.duree',
			'copieur_contrat.etat'=>array('renderer'=>'etat',"width"=>30),
			'fichier_joint'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>60),
			'fichier_joint_signe'=>array("custom"=>true,"nosort"=>true,"align"=>"center","type"=>"file","width"=>60,"renderer"=>"uploadFile"),
			'actions'=>array("custom"=>true,"renderer"=>"actionsCopieurContrat","width"=>80)
		);

		$this->colonnes['primary'] = array(
			"id_societe"
			,"date"
			,"duree"	
		);
		$this->panels['primary'] = array("visible"=>true, 'nbCols'=>3);
		
		$this->colonnes['panel']['affaire'] = array(
			"releve_initial_C"=>array("null"=> true,"custom"=>true,"xtype"=>"numberfield"),
			"releve_initial_NB"=>array("null"=> true,"custom"=>true,"xtype"=>"numberfield"),
		);
		$this->panels['affaire'] = array("visible"=>true, 'nbCols'=>2);
		
		$this->colonnes['panel']['lignes'] = array(
			"produits"=>array("custom"=>true)
		);
		$this->panels['lignes'] = array("visible"=>true, 'nbCols'=>1);

		$this->fieldstructure();

		$this->colonnes['bloquees']['select'] = array("produits","releve_initial_C","releve_initial_NB");
		$this->colonnes['bloquees']['insert'] = 
		$this->colonnes['bloquees']['update'] = array('ref','etat','id_user',"id_affaire_cout_page","cause_perdu");
		$this->field_nom = "ref";

		$this->onglets = array('copieur_contrat_ligne');
		$this->files = array(
			"fichier_joint"=>array("type"=>"pdf","preview"=>true,"no_upload"=>true)
			,"fichier_joint_signe"=>array("type"=>"pdf","preview"=>false,"no_upload"=>true,"no_generate"=>true)
		);

		$this->addPrivilege("annulation","update");
		$this->addPrivilege("uploadFileFromSA");
	}

	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}
		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$this->infoCollapse($infos);


		// Gestion de l'affaire
		$affaire["id_societe"]=$infos["id_societe"];
		$affaire["affaire_cout_page"]="Contrat de maintenance coût/page";
		$affaire["date"]=$infos["date"];
		$affaire["id_termes"]=NULL;
		$affaire["forecast"]=20;
		$affaire["id_commercial"]=$infos["id_user"];
		$affaire["releve_initial_C"]=$infos["releve_initial_C"];
		$affaire["releve_initial_NB"]=$infos["releve_initial_NB"];
		unset($infos["releve_initial_C"],$infos["releve_initial_NB"]);
		$infos["id_affaire_cout_page"]=ATF::affaire_cout_page()->insert($affaire,$s);

		// AJout des champs manquant dans le formulaire
		$infos['id_user'] = ATF::$usr->getId();
		$infos['ref'] = ATF::affaire()->getRef($infos["date"],"devis");

		//Vérification des champs
		$this->check_field($infos);

		if(!$infos_ligne){
			throw new error(ATF::$usr->trans("copieur_contrat_ligne_inexistant"),50);
		}

		ATF::db($this->db)->begin_transaction();

		$last_id = parent::insert($infos,$s,$files,$cadre_refreshed,$nolog);

		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("copieur_contrat_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			$item["id_copieur_contrat"]=$last_id;
			$item["index"]=util::extJSEscapeDot($key);
			if(!$item["quantite"]){
				$item["quantite"]=0;
			}
			ATF::copieur_contrat_ligne()->insert($item,$s);
		}


		if($preview){
			$this->move_files($last_id,$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
			return $this->cryptId($last_id);
		}else{
			$this->move_files($last_id,$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			ATF::db($this->db)->commit_transaction();
		}

		if(is_array($cadre_refreshed)){
			ATF::affaire_cout_page()->redirection("select",$infos["id_affaire_cout_page"]);
		}

		return $this->cryptId($last_id);			
	}

	/**
	 * Surcharge de l'insert afin d'insérer les lignes de copieur_contrat
	 * @author mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 * @param array $nolog True si on ne désire par voir de logs générés par la méthode
	 */
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false){
		if(isset($infos["preview"])){
			$preview=$infos["preview"];
		}else{
			$preview=false;
		}

		$copieur_contrat=$this->select($infos[$this->table]["id_copieur_contrat"]);
		$infos[$this->table]["ref"]=$copieur_contrat["ref"];
		$infos[$this->table]["id_affaire_cout_page"]=$copieur_contrat["id_affaire_cout_page"];

		$infos_ligne = json_decode($infos["values_".$this->table]["produits"],true);
		$this->infoCollapse($infos);

		if(!$infos_ligne){
			throw new error(ATF::$usr->trans("copieur_contrat_ligne_inexistant"),50);
		}

		ATF::db($this->db)->begin_transaction();
		//*****************************Transaction********************************

		// Gestion de l'affaire
		if ($infos["releve_initial_C"] || $infos["releve_initial_NB"]) {
			$affaire["id_affaire_cout_page"]=$copieur_contrat["id_affaire_cout_page"];
			$affaire["releve_initial_C"]=$infos["releve_initial_C"];
			$affaire["releve_initial_NB"]=$infos["releve_initial_NB"];
			unset($infos["releve_initial_C"],$infos["releve_initial_NB"]);
			ATF::affaire_cout_page()->u($affaire,$s);
		}
		unset($infos["releve_initial_C"],$infos["releve_initial_NB"]);

		$last_id=parent::update($infos,$s,$files);
		//*****************************************************************************
		ATF::copieur_contrat_ligne()->cleanAll($copieur_contrat["id_copieur_contrat"]);
		foreach($infos_ligne as $key=>$item){
			foreach($item as $k=>$i){
				$k_unescape=util::extJSUnescapeDot($k);
				$item[str_replace("copieur_contrat_ligne.","",$k_unescape)]=$i;
				unset($item[$k]);
			}

			$item["id_copieur_contrat"]=$copieur_contrat["id_copieur_contrat"];
			$item["index"]=util::extJSEscapeDot($key);
			if(!$item["quantite"]){
				$item["quantite"]=0;
			}
			ATF::copieur_contrat_ligne()->insert($item,$s);
		}




		if($preview){
			$this->move_files($infos["id_copieur_contrat"],$s,true,$infos["filestoattach"]); // Génération du PDF de preview
			ATF::db($this->db)->rollback_transaction();
		}else{
			$this->move_files($infos["id_copieur_contrat"],$s,false,$infos["filestoattach"]); // Génération du PDF avec les lignes dans la base
			ATF::db($this->db)->commit_transaction();
			ATF::affaire_cout_page()->redirection("select",$copieur_contrat["id_affaire_cout_page"]);
		}
		return $this->cryptId($infos["id_copieur_contrat"]);
	}

	public function uploadFileFromSA(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL) {	
		if ($r = parent::uploadFileFromSA($infos,$s,$files,$cadre_refreshed)) {
			$el = $this->select($infos['id']);
			$affaire = array("id_affaire_cout_page"=>$el['id_affaire_cout_page'],"etat"=>"commande","forecast"=>100);
			ATF::affaire_cout_page()->u($affaire);
			$this->u(array("id_copieur_contrat"=>$el["id_copieur_contrat"],"etat"=>"accepte"));
		}
		return $r;
	}

	/**
	 * Renvoi la permission de créer les factures en plus du reste
	 * @author Quentin JANON <qjanon@absystech.fr>
	 */
	public function select_all($order_by=false,$asc='desc',$page=false,$count=false){
		$return = parent::select_all($order_by,$asc,$page,$count);
		foreach ($return['data'] as $k=>$i) {
			if ($i['copieur_contrat.etat'] == "en_attente" || $i['copieur_contrat.etat'] == "fini") {
				$return['data'][$k]['allowFacture'] = false;
			} else {
				$return['data'][$k]['allowFacture'] = true;
			}

			if ($i['copieur_contrat.etat'] == "en_attente") {
				$return['data'][$k]['allowCancel'] = true;
			} else {
				$return['data'][$k]['allowCancel'] = false;
			}


		}
		return $return;
	}

	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @return string
    */   	
	public function default_value($field){
		if(ATF::_r('id_copieur_contrat')){
			$el=ATF::copieur_contrat()->select(ATF::_r('id_copieur_contrat'));
			$releve_initial_NB = ATF::affaire_cout_page()->select($el['id_affaire_cout_page'],"releve_initial_NB");
			$releve_initial_C = ATF::affaire_cout_page()->select($el['id_affaire_cout_page'],"releve_initial_C");
		}elseif(ATF::_r('id_affaire_cout_page')){
			$el=ATF::affaire_cout_page()->select(ATF::_r('id_affaire_cout_page'));
		}


		switch ($field) {
			case "releve_initial_C":
			case "releve_initial_NB":
				if ($$field) {
					return $$field;
				} elseif ($el[$field]) {
					return $el[$field];
				} else {
					return 0;
				}
			break;
			default:
				if ($el[$field]) {
					return $el[$field];
				} else {
					return parent::default_value($field);
				}
			break;
				
		}
	}


	/**
	 * Retourne true c'est à dire que la modification est possible
	 * @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	 * @return boolean
	 */
	public function can_update($id,$infos=false){
		if($el=$this->select($id)){			
			if(ATF::societe()->estFermee($el["id_societe"])){			
				throw new error(ATF::$usr->trans("Impossible de modifier un contrat car la société est inactive"));
			}
			log::logger($el,"qjanon");
			//Si l'état est gagné on ne peut le modifier uniquement s'il n'y a plus d'affaire et qu'elle n'est pas annulée
			log::logger($el["etat"],"qjanon");
			if(!$el["etat"] || $el["etat"]!="en_attente"){
				throw new error("Vous ne pouvez modifier un contrat que si celui ci est en cours",892);
			}else{
				return true;
			}
		}else{
			return false;
		}
	}


	/**
	 * Méthode quif ait le portail pour l'annulation d'un contrat, redirige vers perdu, annule ou remplacement.
	 * @author Quentin JANON <qjanon@absystech.fr>
	 * @param array $infos 
	 * @param array &$s La session
	 * @param array $files $_FILES
	 * @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	 */
	public function annulation($infos,&$s,$files=NULL,&$cadre_refreshed){
		if (!$infos['action'] || !$infos['id']) return false;
		$id_acp = $this->select($params['id_copieur_contrat'],"id_affaire_cout_page");
		switch ($infos['action']) {
			case "perdu":
			case "annule":
				$params = array('id_copieur_contrat'=>$infos['id']);

				ATF::db($this->db)->begin_transaction();
				//***************************Transaction************************************************
				
				$this->u(array(
					"id_copieur_contrat"=>$params["id_copieur_contrat"],
					"etat"=>$infos['action'],
					"cause_perdu"=>$infos['raison']
				),$s);
				ATF::affaire()->u(array("id_affaire"=>$id_acp ,"etat"=>"perdue","forecast"=>"0"),$s);

				ATF::db($this->db)->commit_transaction();
				////*****************************************************************************

				ATF::$msg->addNotice(
					loc::mt(ATF::$usr->trans("notice_devis_perdu"),array("record"=>$this->nom($infos["id_devis"])))
					,ATF::$usr->trans("notice_success_title")
				);

				return true;

			break;
				
			default:
				ATF::db($this->db)->rollback_transaction();
				return false;
			break;
		}
		return $result;
	}
	

};
?>