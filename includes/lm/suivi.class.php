<?
/** Classe affaire
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../suivi.class.php";
class suivi_lm extends suivi {
	function __construct() {
		$this->table = "suivi";
		parent::__construct();
		$this->colonnes["fields_column"] = array(
			'suivi.id_user'
			,'suivi.id_societe'
			,'suivi.date'=>array("width"=>100,"align"=>"center")
			,'suivi.type_suivi'=>array("listeners"=>array("select"=>"ATF.changeType_suivi"))
			,'suivi.texte'
			,'suivi.intervenant_client'=>array("custom"=>true,"nosort"=>true)
			,'suivi.intervenant_societe'=>array("custom"=>true,"nosort"=>true)
			,'suivi.notifie'=>array("custom"=>true,"nosort"=>true)
			,'fichier_joint'=>array("width"=>50,"custom"=>true,"nosort"=>true,"type"=>"file")
		);

		//Autocomplete
		$this->affaireAutocompleteMapping=array(
			array("name"=>'id', "mapping"=>0),
			array("name"=>'nom', "mapping"=>1),
			array("name"=>'date', "mapping"=>2),
			array("name"=>'etat', "mapping"=>3)
		);

		$this->colonnes['primary'] = array(
			"id_societe"
			,"id_affaire"=>array("autocomplete"=>array(
				"mapping"=>$this->affaireAutocompleteMapping
			))
			,"date"
			,'type_suivi'=>array("obligatoire"=>true)
			,"attente_reponse"
		);


		$this->colonnes['panel']['texteSuivi'] = array(
			"texte"=>array("xtype"=>"textarea","height"=>300)
		);

		$this->colonnes['panel']['typeSuivi'] = array(
			"type"
		);

		$this->colonnes['panel']['intervenants'] = array(
			 "suivi_contact"=>array("custom"=>true)
			,"suivi_societe"=>array("custom"=>true)
		);
		$this->colonnes['panel']['notification'] = array(
			"suivi_notifie"=>array("custom"=>true)
		);

		$this->stats_types = array("user","users");
		$this->fieldstructure();
		$this->panels['primary'] = array("nbCols"=>2);
		$this->panels['texteSuivi'] = array("nbCols"=>1,"visible"=>true);
		$this->panels['typeSuivi'] = array("nbCols"=>2,"visible"=>true);
		$this->panels['intervenants'] = array("visible"=>true);
		$this->panels['notification'] = array("visible"=>true);

		$this->suivi_type=array('note'=>1,'fichier'=>1,'RDV'=>1,'appel'=>1,'courrier'=>1);
		$this->stats_filtre=array('suivi_type');


		$this->colonnes["bloquees"]["insert"]=
		$this->colonnes["bloquees"]["update"]=array("origine","public","id_contact");
		$this->colonnes["bloquees"]["select"]=array("origine","public");
	}


	public function default_value($field,&$s,&$request){

		if(ATF::_r('id_tache')){
			$id_tache = ATF::_r('id_tache');
			switch ($field) {
				case 'id_societe':
					return ATF::tache()->select($id_tache , "id_societe");
				break;

				case 'id_affaire':
					return ATF::tache()->select($id_tache , "id_affaire");
				break;

				case 'texte':
					return ATF::tache()->select($id_tache , "tache");
				break;

				default:
					return parent::default_value($field);
				break;
			}
		}
		return parent::default_value($field);

	}


	public function insert($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		if(!$infos["suivi"]){
			$infos["suivi"] = $infos;
		}
		$infos["objet"] = "Suivi ".$infos["suivi"]['type_suivi']." de la part de ".ATF::user()->nom(ATF::$usr->getID());
		$infos["champsComplementaire"] = $infos["suivi"]["champsComplementaire"];
		$infos["attente_reponse"] = $infos["suivi"]["attente_reponse"];
		unset($infos["suivi"]["champsComplementaire"]);

		return parent::insert($infos,$s,$files,$cadre_refreshed);
	}

	public function update($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){

		if(!$infos["suivi"]){
			$infos["suivi"] = $infos;
		}

		if(!$infos["suivi"]["attente_reponse"]){	$infos["attente_reponse"]= $this->select($infos["suivi"]["id_suivi"], "attente_reponse");
		}else{	$infos["attente_reponse"]= $infos["suivi"]["attente_reponse"];	}

		$infos["objet"] = "Modification du suivi ".$infos["suivi"]['type_suivi']." de la part de ".ATF::user()->nom(ATF::$usr->getID());


		return parent::update($infos,$s,$files,$cadre_refreshed);
	}





	/*********************************************************************************
	**								STATS
	********************************************************************************/


	/**
	* Statistiques
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param tab session
	* @param string annee
	* @param string id_societe
	* @param string pole
	* @param string id_user
	* return enregistrements
	*/
	public function stats_special($annee,$id_user=NULL,$groupe){
		$this->q->reset();
		$this->q->addField("CONCAT(`user`.`prenom`,' ',`user`.`nom`)","label")
				->addField("suivi.id_user","ident")
				->addField("COUNT(suivi.id_suivi)","nb");


		if($id_user){
			$this->q->addCondition("suivi.id_user",$id_user,false,"hot_id_user");
		}



		foreach($this->stats_filtre as $name){
			foreach($this->$name as $valeur=>$check){
				//if($check)$this->q->addCondition("hotline.$name",$valeur,"OR","hotline.$name");
				if($name == "suivi_type"){
					if($check)$this->q->addCondition("suivi.type",$valeur,"OR","suivi.type");
				}else{ if($check)$this->q->addCondition("suivi.$name",$valeur,"OR","suivi.$name");	}
			}
		}


		$this->q->addField("DATE_FORMAT(`".$this->table."`.`date`,'%Y')","y")
				->addField("DATE_FORMAT(`".$this->table."`.`date`,'%m')","mois")
				->setStrict()
				->addJointure($this->table,"id_user","user","id_user")
				->addCondition("YEAR( suivi.date )",$annee)
				->addCondition("suivi.id_user",NULL,NULL,false,"IS NOT NULL")
				->addGroup("ident")
				->addGroup("mois")
				->addOrder("mois")
				->addOrder("ident");
		$result=$this->sa();

		foreach (util::month() as $k=>$i) {
			$graph['categories']["category"][$k] = array("label"=>substr($i,0,4));
		}

		$graph['params']['caption'] = "Nombre de suivis";
		$graph['params']['yaxisname'] = "Total";

		/*parametres graphe*/
		$this->paramGraphe($dataset_params,$graph);

		foreach ($result as $val_) {
			$val_["mois"] = strlen($val_["mois"])<2?"0".$val_["mois"]:$val_["mois"];
			if (!$graph['dataset'][$val_["ident"]]) {
				$graph['dataset'][$val_["ident"]]["params"] = array_merge($dataset_params,array(
					"seriesname"=>preg_replace("`('|&)`","",$val_["label"])
					,"color"=>dechex(rand(0,16777216))
				));

				for ($m=1;$m<13;$m++) { /* Initialisation de tous les set à 0 */
					$graph['dataset'][$val_["ident"]]['set'][strlen($m)<2?"0".$m:$m] = array("value"=>0,"alpha"=>100,"titre"=>preg_replace("`('|&)`","",$val_["label"])." : 0");
				}
			}

			$graph['dataset'][$val_["ident"]]['set'][$val_["mois"]] = array("value"=>$val_['nb'],"alpha"=>100,"titre"=>preg_replace("`('|&)`","",$val_["label"])." : ".$val_['nb']);

		}
		return $graph;
	}




	/**
	* Permet de sauvegarder la liste des users sur lesquelles afficher la charge
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	*/
	public function changeUser($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL){
		$dif=array_diff_key($this->liste_user,array_flip($infos['tabuser']));

		foreach($this->liste_user as $key=>$item){
			if(isset($dif[$key]))$this->liste_user[$key]=0;
			else $this->liste_user[$key]=1;
		}

		$infos['current_class']=ATF::stats();
		ATF::$cr->add('main','stats_menu.tpl.htm',$infos);
	}
	/**
	* Filtre sur l'état de la requête
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param string $filtre : etat, pole ou facturation_ticket
	* @param string $nom : différents états d'une requête, facturation_ticket oui ou non, noms des pôles
	* @param bool $etat
	*/
	public function modifEtat($filtre,$nom,$etat){
		$filtres=$this->$filtre;
		$filtres[$nom]=($etat=="true"?1:0);
		$this->$filtre=$filtres;
	}

	/**
	* Retourne les users qui ont créé des suivi, seulement ceux qui ont un profil et actif
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @return array
	*/
	public function getUserActif(){
		ATF::user()->q->reset()
			->addField("user.id_user","id")
			->addField("CONCAT(`user`.`civilite`,' ',`user`.`prenom`,' ',`user`.`nom`)","nom")
			->setStrict()
			->addJointure("user","id_user","suivi","id_user",NULL,NULL,NULL,NULL,"inner")
			->addCondition('etat','normal')
			->addConditionNotNull('id_profil')
			->addGroup('id');

		foreach(ATF::user()->sa() AS $tab){
			$r[$tab['id']]  = $tab['nom'];
		}
		return $r;
	}


	/**
	* Retourne les années pour lesquelles la hotline a été utilisée
	*		pour une société donnée si elle est en param
	* @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
	* @param int id_societe
	* @return array key =year item=year
	*/
	public function get_annee(){
		$this->q->reset()
				->addField("YEAR( suivi.date )","year")
				->addJointure($this->table,"id_suivi","suivi","id_suivi",NULL,NULL,NULL,NULL,"inner")
				->addGroup("year")
				->addOrder("year");

		foreach(parent::select_all() AS $tab){
			$r[$tab['year']] = $tab['year'];
		}
		return $r;
	}

};

?>