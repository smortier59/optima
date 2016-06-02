<?php
/** 
* Classe module : permet de gérer les modules de l'application
* @package ATF
*/
class module extends classes_optima {
	public static $absolutePathIcone = true;

	/**
	* selection optimisée, utile pour les petites tables très souvent sollicitées !
	* @var bool
	*/
	var $memory_optimisation_select = true; // 
	
	/**
	* Correspondance entre les noms de module et les ids
	* @var array
	*/
	private $nom2id=array();

	/**
	* Constructeur
	*/
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'module.module'
			,'module.id_parent'
			,'module.visible'=>array("width"=>50,"align"=>"center")
			,'module.priorite'=>array("width"=>50,"align"=>"center")
		);
		$this->colonnes["primary"] = array(	
			'module'
			,'id_parent'
			,'description'
			,'couleur'
			,'priorite'
		);
		
		$this->colonnes['panel']['privilege'] = array(
			"privilege"=>array("custom"=>true)
		);
		$this->panels["privilege"] = array("visible"=>true,"collapsible"=>true);
		
		$this->fieldstructure();
		$this->foreign_key["id_parent"] = "module";
		$this->colonnes['bloquees']['select']=array('privilege');
		$this->formExt=true;
		$this->onglets = array('module_privilege');
	}
	
	/** 
	* Surcharge pour insérer les privilèges adéquats
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function insert($infos,&$s,$files=NULL,&$cadre_refreshed){
		ATF::db($this->db)->begin_transaction();
		try{
			$id_module=parent::insert($infos,$s,$files,$cadre_refreshed);
		
			foreach($infos['privilege'] as $cle=>$valeur){
				$tab_insert[]=array('id_module'=>$id_module,'id_privilege'=>$cle);	
			}
			if($tab_insert){
				ATF::module_privilege()->multi_insert($tab_insert);
			}
		}catch(errorATF $e){
			ATF::db($this->db)->rollback_transaction();
			throw $e; 
		}
		ATF::db($this->db)->commit_transaction();
		return $id_module;
	}
	
	/** 
	* Surcharge du select pour récupérer les privilèges adéquats
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function select($id_module,$champs=NULL){
		$id_module=$this->decryptId($id_module);
		$this->resetCache(__FUNCTION__,$champs,$id_module);
		$donnees=parent::select($id_module,$champs);
		if(!$champs){
			//on va chercher les infos des privileges attachés
			foreach(ATF::module_privilege()->select_special("id_module",$id_module) as $cle=>$info_priv){
				$donnees['privilege'][$info_priv['id_privilege']]=1;
			}
		}
		return $donnees;
	}
	
	/** 
	* Surcharge pour mettre à jour les privilèges adéquats
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed){
		ATF::db($this->db)->begin_transaction();
		try{
			parent::update($infos,$s,$files,$cadre_refreshed);
		
			//reset des privileges selectionnés
			ATF::module_privilege()->q->reset()->addCondition('id_module',$infos['module']['id_module']);
			ATF::module_privilege()->delete();

			foreach($infos['privilege'] as $cle=>$valeur){
				$tab_insert[]=array('id_module'=>$infos['module']['id_module'],'id_privilege'=>$cle);	
			}
			if($tab_insert){
				ATF::module_privilege()->multi_insert($tab_insert);
			}
		}catch(errorATF $e){
			ATF::db($this->db)->rollback_transaction();
			throw $e; 
		}
		ATF::db($this->db)->commit_transaction();
	}
	
	/**
	* @author DEV AT <dev@absystech.fr>
	* @param int $id_module
	* @param array/pointer $array tableau de modules a trier.
	* @return array
	*/ 
	public function ancetres($id_module,&$array = NULL) {
		if (!$id_module) return false;
		if ($id_parent = $this->select($id_module,"id_parent")) {
			$module_parent=$this->select($id_parent);
			$array[] = array("id_module"=>$id_parent,"module"=>$module_parent['module'],"abstrait"=>$module_parent["abstrait"]);
			return $this->ancetres($id_parent,$array);
		} elseif (is_array($array)) {
			//krsort($array);
			//inverse les clés (ex: 0=>affaire,1=>commerce devient 0=>commerce,1=>affaire
			foreach($array as $key=>$item){
				$array_trier[count($array)-($key+1)]=array("id_module"=>$item['id_module'],"module"=>$item['module'],"abstrait"=>$item['abstrait']);
			}
			ksort($array_trier);
			return $array_trier;
		}
	}
	
	/** 
	* Retourne les modules enfants si un id_parent précisé, sinon les modules parents
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @param int $id_parent : id du module pour lequel on veut les enfants
	* @return array
	*/
	public function enfants($id_parent,$visible=1) {
		$c_mod=new classes('module');
		if (!$id_parent) {
			$c_mod->q->addCondition("id_parent",NULL,NULL,false,"IS NULL")->addOrder("priorite,module","desc,asc");
		} else {
			$c_mod->q->addCondition("id_parent",$id_parent)->addOrder("priorite,module","asc,asc");
		}
		
		if($visible===1){
			$c_mod->q->addCondition("visible",0,"OR",false,">");
		}
		
		return $c_mod->select_all();
	}
	
	/** 
	* Retourne le nombre d'enfants pour un module
	* @author AT DEV <dev@absystech.fr>
	* @param int $id_parent : id du module pour lequel on veut les enfants
	* @param int $visible : propriété 'visible' du module
	* @return array
	*/
	public function nb_enfants($id_parent=NULL,$visible=true) {
		$this->q->reset()->setCount(1);
		if($id_parent){
			$this->q->addCondition("id_parent",$id_parent);
		}else{
			$this->q->addConditionNull("id_parent");
		}
					
		if($visible){
			$this->q->addCondition("visible",1);
		}
		return $this->select_all();
	}
	
	/** 
	* Retourne l'id d'un module par rapport a son nom
	* @author AT DEV <dev@absystech.fr>
	* @param int $id_parent : id du module pour lequel on veut les enfants
	* @param int $visible : propriété 'visible' du module
	* @return int ID du module
	*/
	public function from_nom($nom) {
		if (!$nom) return false;
		$nom=str_replace("\\","___",$nom);
		if (!$this->nom2id[$nom]) {
			$this->q->reset()
						->addField("id_module","id_module")
						->addCondition("module",$nom)
						->setDimension('cell');
			$this->nom2id[$nom] = $this->select_all();
		}
		return $this->nom2id[$nom];
	}
						
	/** 
	* Renvoi le nom du module
	* @author AT DEV <dev@absystech.fr>
	* @param int $id: id du module 
	* @return string Nom du module
	*/
	public function nom($id) {
		$result = parent::nom($id);
		return loc::ation($result,$this->table);
	}

	/** 
	* @author Quentin JANON <qjanon@absystech.fr>
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @date 08/07/2009
	* @param string $nom
	* @return string
	*/
	public function skin_from_nom($nom) {
		if (!$nom) {
			return false;
		}
		if ($this->memory_optimisation_select && array_key_exists($nom,$this->cache[__FUNCTION__])) {
			return $this->cache[__FUNCTION__][$nom];
		} else {
			$nom = self::nameToModule($nom);
			$this->q->reset()
				->addField('couleur')
				->addCondition('module',$nom)
				->setDimension('cell');
			if ($return = $this->select_all()) {
				if ($this->memory_optimisation_select) {
					$this->cache[__FUNCTION__][$nom] = $return;
				}
				return $return;
			} else {
				return 'green'; // Couleur par défaut si le module n'est pas trouvé !
			}
		}
	}

	/**
	* Pas trouvé se solution plus simple pour le menu JS, il est perturbé par les backslash des namespaces
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $nom
	* @return string
	*/
	public static function nameToModule($nom) {
		return str_replace("\\","___",$nom);
	}

	/** 
	* Pas trouvé se solution plus simple pour le menu JS, il est perturbé par les backslash des namespaces
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param string $nom
	* @return string
	*/
	public static function moduleToName($nom) {
		return str_replace("___","\\",$nom);
	}

	/**
	* Retourne le chemin Static vers l'icone
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	* @author Jérémie GWIAZDOWSKI <jgwiazdowski@absystech.fr>
	* @param string $module
	* @param string $size en pixels
	* @return string
	*/
	public static function iconePath($module,$size=16) {
		if (self::$absolutePathIcone) {
			return ATF::$staticserver."images/module/".$size."/".$module.".png";
		} else {	
			return $module.".png";
		}
	}
	
	/**
	* Renvoie l'ensemble des modules sous une dimension (sans arbo) pour une page d'accueil
	* Utilisé sur astadmin et vodpress
	* @todo Changer le nom de la méthode : c'est pourri !
	* @author Jérémie GWIAZDOWSKI <jgw@absystech.fr>
	* @return array le super menu !
	*/
	public function getBigMenu(){
		$this->q->reset()
			->addField("module")
			->addCondition("abstrait","1","AND",false,"<>")
			->addCondition("visible","1","AND")
			->addOrder("module");
		$menu=$this->sa();

		//Gestion des privileges
		$menu_final=array();
		foreach($menu as $module){
			if(ATF::$usr->privilege(module::moduleToName($module["module"]),"select")){
				array_push($menu_final,$module);
			}
		}
		return $menu_final;
	}
	
	/**
	* @author Mathieu TRIBOUILLARD <jgw@absystech.fr>
	* @return true si le module est abstrait
	*/
	public function isAbstract($module){
		$this->q->reset()
			->addField("abstrait")
			->addCondition("module",$module)
			->setDimension('cell');
		return $this->select_all();
	}
	
	/** 
	* Renvoi la liste de tous les modules en abstrait 0 trié par noms traduits
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function listeModules(){
		$this->q->reset()->addCondition('abstrait',0);
		foreach($this->select_all() as $key=>$item){
			if(ATF::$usr->privilege($item['module'],'select')){
				$tableau_de_module[$item['id_module']]=ATF::$usr->trans($item['module'],'module');
			}
		}
		// tri du tableau de module
		asort($tableau_de_module);
		return $tableau_de_module;
	}


	
};
