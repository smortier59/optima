<?
/**
* @package Optima
* @subpackage Testsuite
*/
require_once dirname(__FILE__)."/../societe.class.php";
class societe_testsuite extends societe {
	var $memory_optimisation_select = false; // selection optimisée, utile pour les petites tables très souvent sollicitées !

	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		
		$this->field_nom = NULL;
	}
};
?>