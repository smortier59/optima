<?
/**
* @package Optima
* @subpackage Testsuite
*/
require_once __ATF_PATH__."includes/user.class.php";
class user_testsuite extends user {
	var $memory_optimisation_select = true; // selection optimisée, utile pour les petites tables très souvent sollicitées !

	function __construct($table_or_id=NULL) {
		parent::__construct($table_or_id);
		
		$this->field_nom = "Test - %login% - %password% (%societe.nom_commercial%)";
	}
};
?>