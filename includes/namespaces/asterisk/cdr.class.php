<?php
/**
* @package Asterisk
*/
namespace asterisk;
class cdr extends \classes_optima {
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
		$this->colonnes['fields_column']  = array('cdr.calldate'
												  ,'cdr.clid'
												  ,'cdr.src'
												  ,'cdr.dst'
												  ,'cdr.dcontext'
												  ,'cdr.channel'
												  ,'cdr.dstchannel'
												  ,'cdr.lastapp'
												  ,'cdr.lastdata'
												  ,'cdr.duration'
												  ,'cdr.billsec'
												  ,'cdr.disposition'
												  ,'cdr.amaflags'
												  ,'cdr.accountcode'
												  ,'cdr.uniqueid'
												  ,'cdr.userfield');
		$this->fieldstructure();
	}
}

?>