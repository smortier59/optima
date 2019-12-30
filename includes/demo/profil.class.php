<?php
/** Classe profil
* @package ATF
*/
require_once dirname(__FILE__)."/../../libs/ATF/includes/profil.class.php";
class profil_absystech extends profil {
	function __construct() { // PHP5
		parent::__construct();
	}
	
	/**
	* Modification spécifique du profil : le profil 'associé' doit toujours avoir un seuil à NULL
	* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
	* @param array $infos Simple dimension des champs à insérer, multiple dimension avec au moins un $infos[$this->table]
	* @param array &$s La session
	* @param array $files $_FILES
	* @param array $cadre_refreshed Eventuellement des cadres HTML div à rafraichir...
	* @return boolean TRUE si cela s'est correctement passé
	*/	
	public function update($infos,&$s,$files=NULL,&$cadre_refreshed=NULL,$nolog=false) {
		$this->infoCollapse($infos);
		if($infos["id_profil"]==1){
			$infos["seuil"]=NULL;
		}
		return parent::update($infos,$s,$files,$cadre_refreshed,$nolog);
	}

};
?>