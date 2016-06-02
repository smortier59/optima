<?php
/** Classe importer
* @package ATF
*/
class filtre_user extends classes_optima {
	/*---------------------------*/
	/*      Constructeurs        */
	/*---------------------------*/	
	public function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "my";
		$this->colonnes['fields_column'] = array(
			'filtre_user.id_user'
			,'filtre_user.id_module'
			,'filtre_user.id_filtre_optima'
		);
		
		$this->fieldstructure();
		$this->addPrivilege("saveFilter");
		$this->addPrivilege("removeFilter");
		$this->addPrivilege("fetchGridForTab");
		$this->addPrivilege("saveActiveTab");
		$this->addPrivilege("addFilterToPanel");
		ATF::tracabilite()->no_trace[$this->table]=1;
		
		$this->addPrivilege("saveFilterTab");
	}
	
	/** 
	* Sauvegarde un filtre
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function saveFilter($infos){
		if ($infos['module']) {
			$infos['id_module'] = ATF::module()->from_nom($infos['module']);
			unset($infos['module']);	
		}
		if (!$infos['id_module']) return false;
		$infos['id_user'] = ATF::$usr->getId();
		
		$id =  parent::insert($infos);
		
		$return = ATF::filtre_optima()->select($infos['id_filtre_optima']);
		$return['module'] = ATF::module()->select($return['id_module'],'module');
		return $return;
	}
	
	/** 
	* Supprime un filtre
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function removeFilter($infos){
		if ($infos['module']) {
			$infos['id_module'] = ATF::module()->from_nom($infos['module']);
			unset($infos['module']);	
		}
		if (!$infos['id_module']) return false;
		$id = $this->filterExist($infos['id_module'],$infos['id_filtre_optima']);
		$this->delete($id);
		return array("id_filtre_optima"=>$infos['id_filtre_optima'],"module"=>ATF::module()->select($infos['id_module'],'module')); 
	}
	
	/** 
	* Vérifie l'existence d'un filtre
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function filterExist($id_module,$id_filtre_optima) {
		if (!$id_module) return false;
		$this->q->reset()
					->addField("id_filtre_user")
					->where('id_module',$id_module)
					->where('id_user',ATF::$usr->getId())
					->where('id_filtre_optima',$id_filtre_optima)
					->setDimension('cell')
					->end();
		return $this->sa();
	}
	
	/** 
	* Renvoi l'HTML utile pour générer les grids correspondant aux filtres
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function fetchGridForTab(&$infos) {
		$infos['display']=true;
		
		$infos['current_class'] = ATF::getClass($infos['module']);
		
		// Retourner la création du nouvel onglet en Javascript
		ATF::$html->array_assign($infos);
		return ATF::$html->fetch("generic-gridpanel.tpl.js");
	}
	
	/**
	* Sauvegarder la selection du filtre
	* @author Quentin JANON <qjanon@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function saveActiveTab(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		ATF::$cr->block("top");
		ATF::$cr->block("generationTime");
		
		
		//si on clique sur le tab du select_all on retire l'active du filtre_user activé (si il y a)
		if(is_numeric(strpos($infos['id_filtre_user'],"gsa_"))){
			$id_module = ATF::module()->from_nom($infos['module']);
			if (!$id_module) return false;
			$this->q->reset()->addField("id_filtre_user")->setStrict()
							->addCondition('id_user',ATF::$usr->getId())
							->addCondition('id_module',$id_module)
							->addCondition('active',1);
			if($id_filtre_user=$this->select_cell()){
				return $this->update(array("id_filtre_user"=>$id_filtre_user,"active"=>0));	
			}else{
				return false;
			}			
		}elseif (!is_numeric(strpos($infos['id_filtre_user'],"gsa_")) && !is_numeric($infos['id_filtre_user'])){
			return false;
		}
		$fu = $this->select($infos['id_filtre_user']);

		if (!$fu) return false;
		
		$this->q->reset()
					->where('id_user',$fu['id_user'])
					->where('id_module',$fu['id_module'])
					->where('active',1)
					->setDimension('row');

		if ($toDesactive = $this->sa()) {
			$toDesactive['active'] = 0;
			$this->update($toDesactive);
		}
		
		$toActive = $fu;
		$toActive['active'] = 1;
		return $this->update($toActive);
	}
	
	
	/**
	* Récupère tous les filtres d'un module pour un user
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function getActiveFilters($module){
		$id_module = ATF::module()->from_nom($module);
		if (!$id_module) return false;
		$this->q->reset()
					->where('id_module',$id_module)
					->where('id_user',ATF::$usr->getId());
					
		$list = $this->sa();		
		
		return $list;
	}	
	
	/** Insert un filtrep our un module et un user
	* @author Quentin JANON <qjanon@absystech.fr>
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function addFilterToPanel(&$infos,&$s,$files=NULL,&$cadre_refreshed){
		$id = parent::insert($infos,$s,$files,$cadre_refreshed);
		unset($infos['filtre_user']);
		// Retourner la création du nouvel onglet en Javascript
		$infos["display"]=true;
		$infos['current_class'] = ATF::getClass($infos["table"]);
		//on doit récupérer l'id pour toujours savoir de quel filtre_user il s'agit
		$infos['id']=$id;
		ATF::$html->array_assign($infos);
		return ATF::$html->fetch("generic-gridpanel.tpl.js");
		
	}
	
	/**
	* Sauvegarder la présence ou l'abscence d'un onglet dans le TabGridPanel
	* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
	*/
	public function saveFilterTab(&$infos){
		if (!$infos['t']){
			return false;
		}
		
		$infos["display"]=true;
		
		return ATF::filtre_user()->delete($infos['t']);
	}
	
};
?>