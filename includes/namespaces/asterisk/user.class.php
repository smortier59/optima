<?php
/**
* @package Asterisk
*/
namespace asterisk;
class user extends \classes_optima {
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
		$this->colonnes['fields_column']  = array('login'
												  ,'date'
												  ,'statut'
												  ,'etat'
												  ,'date_activity'
												  ,'email'
												  ,'prenom'
												  ,'nom');
		$this->fieldstructure();
	}
}
?>