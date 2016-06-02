<?
/**
* Classe attributs dont hérite ATTR et PA
*
*
* @date 2009-12-15
* @package inventaire
* @version 1.0.0
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class attributs extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		
		$this->addPrivilege("updateStyle","update");
		$this->addPrivilege("updateMulti","update");
	}	

	/**
    * Mise a jour du style
	* @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @param array $infos correspond au informations posté par l'AJAX
    * @return bool TRUE si ok, sinon FALSE
    */   
	function updateStyle($infos,&$s=NULL,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		if (parent::update(array("id_".$this->table=>$infos["id_".$this->table],"id_style"=>$infos["id_style"]),$s)) {
			$var = array(
				"current_class"=>$this
				,"infos"=>$this->select($infos['id_'.$this->table])
			);
			
			$this->fetchHTML($var,'attrStyle_'.$infos['id_attr'].'_'.$infos['id_pa'],$cadre_refreshed,'gep_projet-formulaire_attr_attrStyle');
			return true;
		}
		return false;
	}

};
?>