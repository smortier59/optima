<?
/**
* @package Optima
*/
class hebergement extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column']  = array(
			'hebergement.id_societe'
			,'hebergement.hebergement'=>array("width"=>100,"align"=>"center")
			,'hebergement.hostname'
			,'hebergement.date_creation'=>array("width"=>100,"align"=>"center")
			,'hebergement.date_expiration'=>array("width"=>100,"align"=>"center")
		);
		$this->fieldstructure();
	}
};
?>