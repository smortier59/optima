<?
/**
* @package Optima
*/
require_once dirname(__FILE__)."/../opportunite.class.php";

class opportunite_cleodis extends opportunite {
	function __construct() { // PHP5 hé
		parent::__construct();
		$this->table = "opportunite";

		$this->colonnes["fields_column"] = array(
			'opportunite.id_societe'
			,'opportunite.opportunite'
			,'opportunite.etat'=>array("width"=>30,"align"=>"center","renderer"=>"etat")
			,'opportunite.date'=>array("width"=>100,"align"=>"center")
			,'opportunite.id_owner'
		);

		$this->colonnes['primary'] = array(
												'id_societe'
												,'opportunite'
												,'description'
												,'etat'
												,'date'
												,'id_owner'
												,'id_target' => array("width"=>100,"align"=>"center")
											);
		$this->colonnes['panel']['detail'] = array(
														"source"
														,"source_detail"
														,"ca"
														,"marge"
														,"echeance"
													);
		$this->fieldstructure();
		$this->panels['detail'] = array("visible"=>true);

		$this->foreign_key['id_target'] =  "user";
	}
};

class opportunite_cleodisbe extends opportunite { };
class opportunite_cap extends opportunite { };
?>