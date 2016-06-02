<?
/**
* @package Optima
*/
class vhost extends classes_optima {
	function __construct() { //hé
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column']  = array(
			'vhost.url'
			,'vhost.id_nom_de_domaine'
			,'vhost.repertoire'
			,'vhost.date_creation'=>array("width"=>100,"align"=>"center")
			,'vhost.date_expiration'=>array("width"=>100,"align"=>"center")
			,'vhost.id_hebergement'=>array("width"=>100,"align"=>"center")
		);
		$this->fieldstructure();
		$this->field_nom = "url";
	}
};
?>