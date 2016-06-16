<?
/**
* Classe des couts unitaires
*
*
* @date 2009-12-21
* @package inventaire
* @version 1.0.0
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class cout_unitaire extends classes_optima {
	
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->controlled_by = "gep_projet";
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array(	
			'cout_unitaire.id_cout_catalogue'
			,'cout_unitaire.cout_unitaire'
			,'cout_unitaire.regle'
			,'cout_unitaire.id_user'
			,'cout_unitaire.date'
		);
		
		//Colonnes SELECT
		$this->colonnes['primary'] = array(
			"cout_unitaire"
			,"regle"
			,"id_cout_catalogue"
		);

		$this->no_insert = true;
		$this->no_update = true;
		$this->no_delete = true;
		$this->fieldstructure();
	}	

	/**
    * Retourne les infos d'un cout unitaire à partie d'un plan de coute, et d'un attribut (+ éventuellement PA)
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $id_cout_catalogue Plan de coût
	* @param array $id_attr A
	* @param array $id_pa PA (facultatif)
    * @return boolean|array
    */   
	function selectFromPA($id_cout_catalogue,$id_pa=NULL){
		if (!$id_cout_catalogue || !$id_pa) {
			return false;
		}
		$this->q
			->reset()
			->setDimension('row')
			->addCondition("id_cout_catalogue",$id_cout_catalogue)
			->addCondition("id_pa",$id_pa);
		return $this->select_all();
	}	

	/**
    * Retourne tous les coûts d'un PA
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $id_pa PA
    * @return boolean|array
    */   
	function selectAllFromPA($id_pa=NULL){
		if (!$id_pa) {
			return false;
		}
		$this->q
			->reset()
			->addCondition("id_pa",$id_pa);
		return $this->select_all();
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
		$cadre_refreshed = false;
		if (isset($infos["cout_catalogue"])) {
			// Création d'un nouveau plan de cotation
			if ($infos["cout_catalogue"]["cout_catalogue"] && $infos["cout_catalogue"]["cout_unitaire"]) {
				if ($infos["cout_unitaire"]["id_cout_catalogue"]) { // Mise à jour du prix catalogue existant
					ATF::cout_catalogue()->update(array(
						"id_cout_catalogue"=>$infos["cout_unitaire"]["id_cout_catalogue"]
						,"cout_catalogue"=>$infos["cout_catalogue"]["cout_catalogue"]
						,"cout_unitaire"=>$infos["cout_catalogue"]["cout_unitaire"]
						,"id_user"=>ATF::$usr->getID()
					));
				} else { // Ajout d'un prix catalogue
					$infos["cout_unitaire"]["id_cout_catalogue"] = ATF::cout_catalogue()->insert(array(
						"cout_catalogue"=>$infos["cout_catalogue"]["cout_catalogue"]
						,"cout_unitaire"=>$infos["cout_catalogue"]["cout_unitaire"]
						,"id_user"=>ATF::$usr->getID()
					));
				}
			} else {
				throw new errorATF("Remplir tous les champs !");
			}
		}
		$this->infoCollapse($infos); // $infos avec une seule dimension
		$infos["id_user"] = ATF::$usr->getID();
		if ($return = parent::insert($infos,$s,$files,$cadre_refreshed)) {
			ATF::$msg->addNotice(ATF::$usr->trans("notice_formulaire_cout_insert"),ATF::$usr->trans("notice_success_title"));
		}
		return $return;
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
		$cadre_refreshed = false;
		if (isset($infos["cout_catalogue"])) {
			// Création d'un nouveau plan de cotation
			if ($infos["cout_unitaire"]["id_cout_catalogue"] && $infos["cout_catalogue"]["cout_catalogue"] && $infos["cout_catalogue"]["cout_unitaire"]) {
				// Mise à jour du prix catalogue existant
				ATF::cout_catalogue()->update(array(
					"id_cout_catalogue"=>$infos["cout_unitaire"]["id_cout_catalogue"]
					,"cout_catalogue"=>$infos["cout_catalogue"]["cout_catalogue"]
					,"cout_unitaire"=>$infos["cout_catalogue"]["cout_unitaire"]
					,"id_user"=>ATF::$usr->getID()
				));
			} else {
				throw new errorATF("Remplir tous les champs !");
			}
		}
		$this->infoCollapse($infos); // $infos avec une seule dimension
		$infos["id_user"] = ATF::$usr->getID();
		if (is_int($return = parent::update($infos,$s,$files,$cadre_refreshed))) {
			ATF::$msg->addNotice(ATF::$usr->trans("notice_formulaire_cout_update"),ATF::$usr->trans("notice_success_title"));
		}
		return $return;
	}	
};
?>