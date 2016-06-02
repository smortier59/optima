<?
/**
* Classe des couts catalogue
*
*
* @date 2009-12-21
* @package inventaire
* @version 1.0.0
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*
*/ 
class cout_catalogue extends classes_optima {
	
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		
		//Colonnes SELECT ALL
		$this->colonnes['fields_column'] = array(	
			'cout_catalogue.cout_catalogue'
			,'cout_catalogue.cout_unitaire'
			,'cout_catalogue.note'
			,'cout_catalogue.id_user'
			,'cout_catalogue.date'
		);
		
		//Colonnes SELECT
		$this->colonnes['primary'] = array(
			"cout_unitaire"
			,"cout_catalogue"
			,"id_cout_categorie"
		);

//		$this->no_insert = true;
//		$this->no_update = true;
//		$this->no_delete = true;
		$this->no_update_all = false;
		$this->fieldstructure();
		$this->field_nom = "%cout_catalogue.cout_catalogue% - %cout_catalogue.cout_unitaire%€/%cout_catalogue.unite%";
		$this->addPrivilege("getOptions");
	}	

	/**
    * Retourne tous les coûts possibles d'un PA, retour pour un menu déroulant
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $id_pa PA
    * @return boolean|array
    */   
	function optionsFromPA($id_pa=NULL){
		if (!$id_pa) {
			return false;
		}
		
		$this->q
			->reset()
			->addGroup("cout_catalogue.id_cout_catalogue")
			->setArrayKeyIndex("cout_catalogue.id_cout_catalogue");
		
		// Si le projet est un projet d'accessibilité
		$id_gep_projet = ATF::pa()->select($id_pa,"id_gep_projet");
		$rapport = ATF::gep_projet()->select($id_gep_projet,"rapport");
		if ($rapport=="accessibilite") {
			// Alors ne retourner que les prix associés au cout_catalogue_accessibilite.CODE == parentPA.parentPA.CODE
			$id_pa_parent_parent = ATF::pa()->select(ATF::pa()->select($id_pa,"id_parent"),"id_parent");
			$code = ATF::pa()->select($id_pa_parent_parent,"code");
			$this->q
				->addJointure("cout_catalogue","id_cout_catalogue_accessibilite","cout_catalogue_accessibilite","id_cout_catalogue_accessibilite",NULL,NULL,NULL,NULL,"inner")
				->addCondition("cout_catalogue_accessibilite.code",$code);
		} else {
			$this->q
				->addJointure("cout_catalogue","id_cout_catalogue","cout_unitaire","id_cout_catalogue",NULL,NULL,NULL,NULL,"inner")
				->addCondition("cout_unitaire.id_pa",$id_pa)
				->addGroup("cout_catalogue.id_cout_catalogue")
				->setArrayKeyIndex("cout_catalogue.id_cout_catalogue");
		}
		return $this->options(NULL,NULL,false);
	}

	/**
    * Applique la condition de catégorie au querier
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param int id_cout_categorie
    * @return void
    */   
	function optionsFromCat($id_cout_categorie){
		$this->q->reset();
		if ($id_cout_categorie) {
			$this->q->addCondition("id_cout_categorie",$id_cout_categorie);
		} else {
			$this->q->addConditionNull("id_cout_categorie");
		}
	}	
	
	/**
    * Met à jour la categorie d'un cout catalogue, et retourne aussi la liste des cout_catalogues présents dans la catégorie.
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
	* @param array $infos
    * @return array
    */   
	function getOptions($infos,$order="cout_catalogue",$asc='desc'){
		$this->optionsFromCat($infos["id_cout_categorie"]);
		return $this->htmlOptions(NULL,NULL,NULL,false,$order,$asc);
	}
	
	/**
	* CF options classes.class.php
	* Tri par défaut par le libellé
	* @author Quentin JANON <qjanon@absystech.fr>
	*/
	public function options($fields=NULL,$id_on_key=NULL,$reset=true,$order_by='cout_catalogue',$asc='asc',$bypassvalue=false) {	
		return parent::options($fields,$id_on_key,$reset,$order_by,$asc,$bypassvalue);
	}
	
	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
    * @author Quentin JANON <qjanon@absystech.fr>
	* @param string $field
	* @return string
    */   	
	public function default_value($field){
		switch ($field) {
			case "id_user":
				return ATF::$usr->getID();
				break;
			default:
				return parent::default_value($field);
		}
	
	}	 
	
	
};
?>