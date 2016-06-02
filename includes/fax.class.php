<?
/**
* @package Optima
*/
class fax extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'fax.id_user'
			,'fax.JobID'=>array("width"=>100,"align"=>"center")
			,'fax.fax'
			,'fax.numero'=>array("width"=>120,"align"=>"center","renderer"=>"tel")
			,'fax.date'=>array("width"=>100,"align"=>"center")
			,'fax.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
		);
		$this->fieldstructure();
	}
};
?>