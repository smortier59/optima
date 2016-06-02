<?
/**
* @package Optima
*/
class document extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'document.document'
			,'document.filename'
			,'fichier_joint'=>array("custom"=>true,"nosort"=>true,"type"=>"file","align"=>"center")
		);
		$this->fieldstructure();
		$this->files["fichier_joint"] = array();
	}
};
?>