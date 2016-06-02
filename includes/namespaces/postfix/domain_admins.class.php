<?php
/**
* @package Postfix
*/
namespace postfix;
class domain_admins extends \classes_optima {
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

		$this->fieldstructure();
	}
}

?>