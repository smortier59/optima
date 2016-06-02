<?php
/**
* @package Asterisk
*/
namespace asterisk;
class extension extends \classes_optima {
	function __construct() {
		parent::__construct();
		
		// Gestion des namespace
		if (__NAMESPACE__) {
			$this->namespace = __NAMESPACE__;
			$class = explode("\\",__CLASS__);
			$this->db = $class[0];
			$this->table = $class[1];
		} else {
			$this->table = __CLASS__;
		}
		
		//$this->controlled_by = "accueil";
		$this->colonnes['fields_column']  = array('extension.context'
												  ,'extension.exten'
												  ,'extension.priority'
												  ,'extension.app'
												  ,'extension.appdata');
		$this->fieldstructure();
	}
}
?>