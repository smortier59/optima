<?
/**
* @date 2010-03-12
* @package inventaire
* @version 1.0.0
* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class cout_catalogue_gep extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array(	
			'cout_catalogue_gep.id_gep_projet'
			,'cout_catalogue_gep.id_cout_catalogue'
			,'cout_catalogue_gep.cout_unitaire'
			,'cout_catalogue_gep.id_user'
			,'cout_catalogue_gep.date'
		);
		
		//Colonnes SELECT
		$this->colonnes['primary'] = array(
			'id_gep_projet'
			,'id_cout_catalogue'
			,'cout_unitaire'
			,'id_user'
			,'date'
		);
		$this->field_nom = "%cout_catalogue_gep.id_gep_projet% - %cout_catalogue_gep.id_cout_catalogue%";
		$this->colonnes['bloquees']['insert'] = 	
		$this->colonnes['bloquees']['update'] = array(
			"date"
			,"id_user"
		);
		
		$this->fieldstructure();
	}	

	/**
	* Coûts spécifiques
	* @author Yann-Gaël GAUTHERON <ygautheron@absystech.fr>
	* @param int $id_cout_catalogue
	* @param int $id_gep_projet
	* @return int
	*/	
	public function coutSpecifique($id_cout_catalogue,$id_gep_projet) {
		$this->q->reset()
			->select("cout_unitaire")
			->where("id_cout_catalogue",$id_cout_catalogue)
			->where("id_gep_projet",$id_gep_projet)
			->setDimension("cell");
		return $this->query();
	}
	
	/**
    * Modification obligatoirement faite par un utilisateur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_suivi
    */ 	
	function update(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos); // $infos avec une seule dimension
		$infos["id_user"] = ATF::$usr->getID();
		return parent::update($infos,$s,$files,$cadre_refreshed);
	}	
	
	/**
    * Insertion obligatoirement faite par un utilisateur
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return int id_suivi
    */ 	
	function insert(&$infos,&$s,$files=NULL,&$cadre_refreshed=NULL){
		$this->infoCollapse($infos); // $infos avec une seule dimension
		$infos["id_user"] = ATF::$usr->getID();
		return parent::insert($infos,$s,$files,$cadre_refreshed);
	}	
};
?>