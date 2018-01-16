<?
/**
* @package Optima
*/
class document_revendeur extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(
			 'document_revendeur.id_societe'
			,'document_revendeur.site_associe'
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center","renderer"=>"uploadFile")
		);

		$this->files["fichier_joint"] = array("type"=>"pdf","no_generate"=>true);

		$this->field_nom= "%id_societe% - %site_associe% ";


		$this->fieldstructure();
	}
};
?>