<?
require_once dirname(__FILE__)."/../societe.class.php";
/**
* @package Optima
* @subpackage Alerteo
*/
class societe_alerteo extends societe {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = "societe";
		
		$this->colonnes['fields_column'] = array(	
			'societe.societe'
			,'societe.tel' => array("tel"=>true)
			,'societe.email'
			,'societe.ville'
		);
		$this->fieldstructure();
		
		$this->checkAndRemoveBadFields('facturation_fs');
		
		$this->onglets[]="opportunite";
	}
};

?>